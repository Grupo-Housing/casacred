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

    protected $description = 'Calcula y asigna la zona cardinal (9 zonas). Solo Cuenca activo.';

    public function handle()
    {
        $service    = new CardinalZoneService();
        $force      = $this->option('force');
        $cityFilter = $this->option('city');
        $dryRun     = $this->option('dry-run');
        $debugId    = $this->option('debug-id');

        // ── Modo debug de una propiedad específica ────────────────────────────
        if ($debugId) {
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
            $this->line('');
            $debug = $service->debug($lat, $lng, $prop->city);
            $rows  = array_map(fn($k, $v) => [$k, is_bool($v) ? ($v ? 'true' : 'false') : $v], array_keys($debug), $debug);
            $this->table(['Campo', 'Valor'], $rows);
            return 0;
        }

        // ── Query base usando DB::table (evita $fillable de Eloquent) ─────────
        $query = DB::table('listings')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('lat', '!=', '')
            ->where('lng', '!=', '');

        if (!$force) {
            $query->whereNull('cardinal_zone');
        }

        if ($cityFilter) {
            $query->where('city', 'LIKE', "%{$cityFilter}%");
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('No hay propiedades para procesar.');
            return 0;
        }

        $this->info("Propiedades a procesar: {$total}");

        if ($dryRun) {
            $this->warn('MODO DRY-RUN: no se guardará nada.');
        }

        $bar    = $this->output->createProgressBar($total);
        $bar->start();

        $counts  = array_fill_keys(CardinalZoneService::getAllZones(), 0);
        $counts['sin_zona']      = 0;
        $counts['limpiadas']     = 0;
        $counts['sin_cambio']    = 0;
        $updated = 0;

        $query->orderBy('id')->chunk(200, function ($listings) use (
            $service,
            $dryRun,
            $bar,
            &$counts,
            &$updated
        ) {
            foreach ($listings as $listing) {

                // ── Sanitizar coordenadas ─────────────────────────────────────
                $latRaw = str_replace(',', '.', trim((string) ($listing->lat ?? '')));
                $lngRaw = str_replace(',', '.', trim((string) ($listing->lng ?? '')));
                $lat    = (float) $latRaw;
                $lng    = (float) $lngRaw;

                $latValida = $lat !== 0.0 && $lat >= -90  && $lat <= 90;
                $lngValida = $lng !== 0.0 && $lng >= -180 && $lng <= 180;

                // ── Coords inválidas → limpiar ────────────────────────────────
                if (!$latValida || !$lngValida) {
                    $counts['limpiadas']++;
                    if (!$dryRun) {
                        DB::table('listings')->where('id', $listing->id)->update([
                            'lat'           => null,
                            'lng'           => null,
                            'cardinal_zone' => null,
                        ]);
                    }
                    $bar->advance();
                    continue;
                }

                // ── Calcular zona ─────────────────────────────────────────────
                $zone = $service->calculate($lat, $lng, $listing->city ?? null);

                if ($zone === null) {
                    // Ciudad no reconocida (no Cuenca) → dejar intacto
                    $counts['sin_zona']++;
                    $bar->advance();
                    continue;
                }

                // ── Si ya tiene la zona correcta, saltar ──────────────────────
                if (!$this->option('force') && $listing->cardinal_zone === $zone) {
                    $counts['sin_cambio']++;
                    $bar->advance();
                    continue;
                }

                // ── Guardar con DB::table (bypasa $fillable y timestamps) ─────
                if (!$dryRun) {
                    $affected = DB::table('listings')
                        ->where('id', $listing->id)
                        ->update(['cardinal_zone' => $zone]);

                    if ($affected === 0) {
                        // Esto no debería ocurrir — si ocurre, el ENUM aún bloquea
                        $this->line('');
                        $this->line('');
                        $this->error("⚠  ID {$listing->id}: update() devolvió 0 filas afectadas para zona '{$zone}'");
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

        $verb = $dryRun ? ' (DRY-RUN — nada guardado)' : '';
        $this->info("Proceso completado{$verb}:");

        $labels = CardinalZoneService::getLabels();
        $rows   = [];
        foreach (CardinalZoneService::getAllZones() as $zone) {
            $rows[] = [$labels[$zone] ?? ucfirst($zone), $counts[$zone] ?? 0];
        }
        $rows[] = ['─────────────────────────────', '───'];
        $rows[] = ['Sin zona (ciudad no reconocida)',    $counts['sin_zona']];
        $rows[] = ['Limpiadas a NULL (coords inválidas)', $counts['limpiadas']];
        $rows[] = ['Sin cambio (ya correcta)',           $counts['sin_cambio']];
        $rows[] = ['TOTAL ACTUALIZADAS',                 $updated];

        $this->table(['Zona', 'Propiedades'], $rows);

        return 0;
    }
}
