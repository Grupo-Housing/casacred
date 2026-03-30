<?php

namespace App\Console\Commands;

use App\Services\CardinalZoneService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateCardinalZones extends Command
{
    protected $signature = 'listings:update-cardinal-zones
                            {--force : Recalcular incluso las que ya tienen zona asignada}
                            {--city= : Filtrar solo por ciudad (ej: Cuenca)}
                            {--dry-run : Mostrar resultados sin guardar}
                            {--debug-id= : Mostrar cálculo detallado de una propiedad por ID}';

    protected $description = 'Calcula zonas cardinales para ciudades soportadas. Limpia a NULL las que no aplican.';

    public function handle(): int
    {
        $service    = new CardinalZoneService();
        $force      = $this->option('force');
        $cityFilter = $this->option('city');
        $dryRun     = $this->option('dry-run');
        $debugId    = $this->option('debug-id');

        // ── Modo debug de una propiedad específica ────────────────────────────
        if ($debugId) {
            return $this->runDebug($service, $debugId);
        }

        // ── Mostrar ciudades activas al inicio ────────────────────────────────
        $this->info('Ciudades con zona cardinal activa: ' . implode(', ', CardinalZoneService::getSupportedCities()));
        $this->line('Las propiedades en otras ciudades quedarán con cardinal_zone = NULL.');
        $this->line('');

        // ── Query: todas las propiedades (con o sin coordenadas) ──────────────
        // Necesitamos procesar también las que no tienen coords para limpiarlas.
        $query = DB::table('listings')->select('id', 'lat', 'lng', 'city', 'cardinal_zone');

        if ($cityFilter) {
            $query->where('city', 'LIKE', "%{$cityFilter}%");
            $this->info("Filtrando solo ciudad: {$cityFilter}");
        }

        // Sin --force, solo procesamos las que necesitan actualización:
        // - no tienen zona asignada aún, o
        // - tienen zona pero su ciudad ya no está soportada (para limpiarlas)
        if (!$force) {
            $query->where(function ($q) use ($service) {
                $q->whereNull('cardinal_zone');
                // También incluir las que tienen zona pero ciudad no soportada
                // (se detectarán en el chunk y se limpiarán)
            });
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('No hay propiedades para procesar. Usa --force para reprocesar todas.');
            return 0;
        }

        $this->info("Propiedades a revisar: {$total}");

        if ($dryRun) {
            $this->warn('MODO DRY-RUN activo: no se guardará ningún cambio.');
        }

        $this->line('');

        // ── Contadores ────────────────────────────────────────────────────────
        $counts = array_fill_keys(CardinalZoneService::getAllZones(), 0);
        $counts['limpiadas_ciudad_no_soportada'] = 0;
        $counts['limpiadas_coords_invalidas']    = 0;
        $counts['sin_cambio']                    = 0;
        $updated = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->orderBy('id')->chunk(200, function ($listings) use (
            $service,
            $dryRun,
            $bar,
            $force,
            &$counts,
            &$updated
        ) {
            foreach ($listings as $listing) {

                // ── Sanitizar coordenadas ─────────────────────────────────────
                $latRaw   = str_replace(',', '.', trim((string) ($listing->lat ?? '')));
                $lngRaw   = str_replace(',', '.', trim((string) ($listing->lng ?? '')));
                $lat      = (float) $latRaw;
                $lng      = (float) $lngRaw;

                $tieneCoords = $lat !== 0.0
                    && $lng !== 0.0
                    && $lat >= -90  && $lat <= 90
                    && $lng >= -180 && $lng <= 180;

                // ── CASO 1: Coordenadas inválidas o ausentes ──────────────────
                // Limpiar zona y coordenadas para mantener consistencia.
                if (!$tieneCoords) {
                    if ($listing->cardinal_zone !== null) {
                        $counts['limpiadas_coords_invalidas']++;
                        if (!$dryRun) {
                            DB::table('listings')->where('id', $listing->id)->update([
                                'lat'           => null,
                                'lng'           => null,
                                'cardinal_zone' => null,
                            ]);
                        }
                        $updated++;
                    } else {
                        $counts['sin_cambio']++;
                    }
                    $bar->advance();
                    continue;
                }

                // ── CASO 2: Ciudad no soportada (ej: Gualaceo, Azogues...) ────
                // No tiene sentido calcular zona relativa a Cuenca.
                // Si ya tiene zona asignada (por error previo), limpiarla.
                if (!$service->isCitySupported($listing->city)) {
                    if ($listing->cardinal_zone !== null) {
                        $counts['limpiadas_ciudad_no_soportada']++;
                        if (!$dryRun) {
                            DB::table('listings')->where('id', $listing->id)->update([
                                'cardinal_zone' => null,
                            ]);
                        }
                        $updated++;
                    } else {
                        $counts['sin_cambio']++;
                    }
                    $bar->advance();
                    continue;
                }

                // ── CASO 3: Ciudad soportada → calcular zona ──────────────────
                $zone = $service->calculate($lat, $lng, $listing->city);

                if ($zone === null) {
                    // Esto no debería ocurrir si isCitySupported() pasó,
                    // pero lo manejamos por seguridad.
                    $counts['sin_cambio']++;
                    $bar->advance();
                    continue;
                }

                // Si ya tiene la zona correcta y no es --force, saltar
                if (!$force && $listing->cardinal_zone === $zone) {
                    $counts['sin_cambio']++;
                    $bar->advance();
                    continue;
                }

                if (!$dryRun) {
                    $affected = DB::table('listings')
                        ->where('id', $listing->id)
                        ->update(['cardinal_zone' => $zone]);

                    if ($affected === 0) {
                        $this->line('');
                        $this->error("ID {$listing->id}: update devolvió 0 filas para zona '{$zone}'");
                    }
                }

                $counts[$zone] = ($counts[$zone] ?? 0) + 1;
                $updated++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->line('');
        $this->line('');

        $this->printResults($counts, $updated, $dryRun);

        return 0;
    }

    // ── Debug de propiedad individual ─────────────────────────────────────────
    private function runDebug(CardinalZoneService $service, string $debugId): int
    {
        $prop = DB::table('listings')->where('id', $debugId)->first();

        if (!$prop) {
            $this->error("No se encontró propiedad con ID {$debugId}");
            return 1;
        }

        $lat = (float) str_replace(',', '.', trim((string) ($prop->lat ?? '')));
        $lng = (float) str_replace(',', '.', trim((string) ($prop->lng ?? '')));

        $this->info("Propiedad ID {$debugId} — Cod: {$prop->product_code}");
        $this->info("lat raw: {$prop->lat}  →  float: {$lat}");
        $this->info("lng raw: {$prop->lng}  →  float: {$lng}");
        $this->info("city: {$prop->city}");
        $this->info("cardinal_zone actual en BD: " . ($prop->cardinal_zone ?? 'NULL'));
        $this->info("¿Ciudad soportada?: " . ($service->isCitySupported($prop->city) ? 'SÍ' : 'NO'));
        $this->line('');

        $debug = $service->debug($lat, $lng, $prop->city);
        $rows  = array_map(
            fn($k, $v) => [$k, is_bool($v) ? ($v ? 'true' : 'false') : $v],
            array_keys($debug),
            $debug
        );
        $this->table(['Campo', 'Valor'], $rows);

        return 0;
    }

    // ── Tabla de resultados ───────────────────────────────────────────────────
    private function printResults(array $counts, int $updated, bool $dryRun): void
    {
        $verb = $dryRun ? ' (DRY-RUN — nada guardado)' : '';
        $this->info("Proceso completado{$verb}:");

        $labels = CardinalZoneService::getLabels();
        $rows   = [];

        foreach (CardinalZoneService::getAllZones() as $zone) {
            $rows[] = [$labels[$zone] ?? ucfirst($zone), $counts[$zone] ?? 0];
        }

        $rows[] = ['─────────────────────────────────────', '───'];
        $rows[] = ['Limpiadas (ciudad no soportada → NULL)', $counts['limpiadas_ciudad_no_soportada']];
        $rows[] = ['Limpiadas (coords inválidas → NULL)',    $counts['limpiadas_coords_invalidas']];
        $rows[] = ['Sin cambio',                            $counts['sin_cambio']];
        $rows[] = ['TOTAL MODIFICADAS',                     $updated];

        $this->table(['Zona / Resultado', 'Propiedades'], $rows);
    }
}
