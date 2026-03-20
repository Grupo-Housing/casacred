<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing;
use App\Models\ContactQueue;
use Carbon\Carbon;

class AssignContactQueue extends Command
{
    protected $signature   = 'queue:assign-contacts';
    protected $description = 'Distribuye propiedades disponibles entre las asesoras para contactar';

    // ⚠️ Cambia estos IDs por los reales de tus asesoras
    const ADVISOR_IDS = [934, 942];

    public function handle()
    {
        // 1. Limpiar asignaciones pendientes anteriores para reasignar fresco
        ContactQueue::where('status', 'pending')->delete();

        // 2. Obtener propiedades disponibles ordenadas por prioridad
        $listings = Listing::where('available', 1)
            ->orderByRaw('ISNULL(contact_at) DESC')      // Nunca contactadas primero
            ->orderBy('contact_at', 'asc')               // Luego las más antiguas
            ->orderBy('no_answer_at', 'asc')             // Luego sin respuesta
            ->get();

        if ($listings->isEmpty()) {
            $this->info('No hay propiedades disponibles para asignar.');
            return;
        }

        // 3. Distribuir en round-robin entre asesoras
        $advisors   = self::ADVISOR_IDS;
        $total      = count($advisors);
        $now        = Carbon::now();
        $insertData = [];

        foreach ($listings as $index => $listing) {
            $assignedTo   = $advisors[$index % $total];
            $insertData[] = [
                'listing_id'  => $listing->id,
                'user_id'     => $assignedTo,
                'status'      => 'pending',
                'assigned_at' => $now,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        // Insert en lotes para rendimiento
        foreach (array_chunk($insertData, 500) as $chunk) {
            ContactQueue::insert($chunk);
        }

        $this->info("✅ {$listings->count()} propiedades asignadas entre " . $total . " asesoras.");
    }
}
