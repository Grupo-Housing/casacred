<?php

namespace App\Services;

class CardinalZoneService
{
    private const CITY_CENTERS = [
        'cuenca' => ['lat' => -2.9001285, 'lng' => -79.0058965, 'radius' => 0.012],
    ];

    private const DIAGONAL_THRESHOLD = 0.25;

    /**
     * Mapa explícito para zonas diagonales.
     * Evita concatenación incorrecta como 'norte'.'este' = 'norteeste'.
     * Clave: "{ns}_{eo}" => zona correcta
     */
    private const DIAGONAL_MAP = [
        'norte_este'  => 'noreste',
        'norte_oeste' => 'noroeste',
        'sur_este'    => 'sureste',
        'sur_oeste'   => 'suroeste',
    ];

    public function calculate(?float $lat, ?float $lng, ?string $city = null): ?string
    {
        if ($lat === null || $lng === null || $lat == 0 || $lng == 0) {
            return null;
        }

        $center = $this->getCityCenter($city);

        if ($center === null) {
            return null;
        }

        $diffLat = $lat - $center['lat']; // + = norte, - = sur
        $diffLng = $lng - $center['lng']; // + = este,  - = oeste
        $radius  = $center['radius'];

        // ── Zona central ──────────────────────────────────────────────────────
        if (abs($diffLat) <= $radius && abs($diffLng) <= $radius) {
            return 'centro';
        }

        $absDiffLat = abs($diffLat);
        $absDiffLng = abs($diffLng);
        $dominant   = max($absDiffLat, $absDiffLng);

        $ratioLat = $absDiffLat / $dominant;
        $ratioLng = $absDiffLng / $dominant;

        // ── Zona diagonal ─────────────────────────────────────────────────────
        if ($ratioLat >= self::DIAGONAL_THRESHOLD && $ratioLng >= self::DIAGONAL_THRESHOLD) {
            $ns  = $diffLat > 0 ? 'norte' : 'sur';
            $eo  = $diffLng > 0 ? 'este'  : 'oeste';
            $key = "{$ns}_{$eo}";  // ej: "norte_este"

            // Usar mapa explícito — nunca concatenar directamente
            return self::DIAGONAL_MAP[$key] ?? null;
        }

        // ── Eje dominante único ───────────────────────────────────────────────
        if ($absDiffLat >= $absDiffLng) {
            return $diffLat > 0 ? 'norte' : 'sur';
        }

        return $diffLng > 0 ? 'este' : 'oeste';
    }

    private function getCityCenter(?string $city): ?array
    {
        if (empty($city)) {
            return null;
        }

        $normalized = strtolower(trim($this->removeAccents($city)));

        foreach (self::CITY_CENTERS as $key => $center) {
            if (str_contains($normalized, $key) || str_contains($key, $normalized)) {
                return $center;
            }
        }

        return null;
    }

    private function removeAccents(string $string): string
    {
        return strtr($string, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'Á' => 'a',
            'É' => 'e',
            'Í' => 'i',
            'Ó' => 'o',
            'Ú' => 'u',
            'ñ' => 'n',
            'Ñ' => 'n',
        ]);
    }

    public function getLabel(string $zone): string
    {
        return self::getLabels()[$zone] ?? ucfirst($zone);
    }

    public static function getLabels(): array
    {
        return [
            'norte'    => '↑ Norte',
            'sur'      => '↓ Sur',
            'este'     => '→ Este',
            'oeste'    => '← Oeste',
            'centro'   => '⊙ Centro',
            'noreste'  => '↗ Noreste',
            'noroeste' => '↖ Noroeste',
            'sureste'  => '↘ Sureste',
            'suroeste' => '↙ Suroeste',
        ];
    }

    public static function getAllZones(): array
    {
        return ['norte', 'sur', 'este', 'oeste', 'centro', 'noreste', 'noroeste', 'sureste', 'suroeste'];
    }

    public static function getZoneColor(string $zone): string
    {
        $colors = [
            'norte'    => 'bg-blue-100 text-blue-800',
            'sur'      => 'bg-yellow-100 text-yellow-800',
            'este'     => 'bg-green-100 text-green-800',
            'oeste'    => 'bg-purple-100 text-purple-800',
            'centro'   => 'bg-gray-100 text-gray-800',
            'noreste'  => 'bg-sky-100 text-sky-800',
            'noroeste' => 'bg-indigo-100 text-indigo-800',
            'sureste'  => 'bg-lime-100 text-lime-800',
            'suroeste' => 'bg-orange-100 text-orange-800',
        ];

        return $colors[$zone] ?? 'bg-gray-100 text-gray-800';
    }

    public static function getZoneHexColor(string $zone): string
    {
        $colors = [
            'norte'    => '#3B82F6',
            'sur'      => '#EAB308',
            'este'     => '#22C55E',
            'oeste'    => '#A855F7',
            'centro'   => '#9CA3AF',
            'noreste'  => '#0EA5E9',
            'noroeste' => '#6366F1',
            'sureste'  => '#84CC16',
            'suroeste' => '#F97316',
        ];

        return $colors[$zone] ?? '#D1D5DB';
    }

    public function debug(?float $lat, ?float $lng, ?string $city = null): array
    {
        $center = $this->getCityCenter($city);

        if (!$center) {
            return ['error' => 'Ciudad no reconocida'];
        }

        $diffLat    = $lat - $center['lat'];
        $diffLng    = $lng - $center['lng'];
        $absDiffLat = abs($diffLat);
        $absDiffLng = abs($diffLng);
        $dominant   = max($absDiffLat, $absDiffLng);
        $ratioLat   = $dominant > 0 ? $absDiffLat / $dominant : 0;
        $ratioLng   = $dominant > 0 ? $absDiffLng / $dominant : 0;

        $ns  = $diffLat > 0 ? 'norte' : 'sur';
        $eo  = $diffLng > 0 ? 'este'  : 'oeste';
        $key = "{$ns}_{$eo}";

        return [
            'lat'              => $lat,
            'lng'              => $lng,
            'city'             => $city,
            'center_lat'       => $center['lat'],
            'center_lng'       => $center['lng'],
            'diffLat'          => round($diffLat, 6),
            'diffLng'          => round($diffLng, 6),
            'direction_lat'    => $ns,
            'direction_lng'    => $eo,
            'diagonal_key'     => $key,
            'diagonal_result'  => self::DIAGONAL_MAP[$key] ?? 'N/A',
            'ratioLat'         => round($ratioLat, 4),
            'ratioLng'         => round($ratioLng, 4),
            'threshold'        => self::DIAGONAL_THRESHOLD,
            'is_diagonal'      => $ratioLat >= self::DIAGONAL_THRESHOLD && $ratioLng >= self::DIAGONAL_THRESHOLD,
            'in_center_radius' => abs($diffLat) <= $center['radius'] && abs($diffLng) <= $center['radius'],
            'zone_calculated'  => $this->calculate($lat, $lng, $city),
        ];
    }
}
