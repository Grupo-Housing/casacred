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

    const ADVISOR_IDS = [900, 901];

    public function handle()
    {
        $hoy    = Carbon::today();
        $hace30 = Carbon::today()->subDays(30);

        // 1. Limpiar pendientes anteriores
        $deleted = ContactQueue::where('status', 'pending')->delete();
        $this->info("🗑️  {$deleted} asignaciones pendientes anteriores eliminadas.");

        // 2. Obtener propiedades que califican según las reglas de negocio
        $listings = Listing::where('available', 1)
            ->where(function ($query) use ($hace30) {
                // REGLA contact_at:
                // Califica si nunca fue contactada O si el último contacto fue hace más de 30 días
                $query->whereNull('contact_at')
                    ->orWhere('contact_at', '<', $hace30);
            })
            ->where(function ($query) use ($hoy) {
                // REGLA no_answer_at:
                // Califica si nunca tuvo no_answer O si el último no_answer fue ANTES de hoy
                // (si no contestó ayer, hoy se puede volver a intentar)
                // (si no contestó HOY, no insistir hasta mañana)
                $query->whereNull('no_answer_at')
                    ->orWhereDate('no_answer_at', '<', $hoy);
            })
            // Orden de prioridad:
            // 1. Nunca contactadas (contact_at NULL) primero
            // 2. Las contactadas hace más tiempo
            // 3. Las que no contestaron hace más tiempo
            ->orderByRaw('CASE WHEN contact_at IS NULL THEN 0 ELSE 1 END ASC')
            ->orderBy('contact_at', 'asc')
            ->orderByRaw('CASE WHEN no_answer_at IS NULL THEN 0 ELSE 1 END ASC')
            ->orderBy('no_answer_at', 'asc')
            ->get();

        if ($listings->isEmpty()) {
            $this->info('✅ No hay propiedades disponibles para asignar.');
            return;
        }

        // 3. Distribuir en round-robin entre las asesoras
        $advisors   = self::ADVISOR_IDS;
        $totalAdvisors = count($advisors);
        $now        = Carbon::now();
        $insertData = [];

        // Contadores por asesora para el reporte
        $countPerAdvisor = array_fill_keys($advisors, 0);

        foreach ($listings as $index => $listing) {
            $assignedTo = $advisors[$index % $totalAdvisors];
            $countPerAdvisor[$assignedTo]++;

            $insertData[] = [
                'listing_id'  => $listing->id,
                'user_id'     => $assignedTo,
                'status'      => 'pending',
                'assigned_at' => $now,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        // 4. Insertar en lotes de 500 para rendimiento
        foreach (array_chunk($insertData, 500) as $chunk) {
            ContactQueue::insert($chunk);
        }

        // 5. Reporte final
        $this->info("✅ {$listings->count()} propiedades asignadas entre {$totalAdvisors} asesoras:");
        foreach ($countPerAdvisor as $userId => $count) {
            $this->info("   👤 User ID {$userId}: {$count} propiedades");
        }

        // Desglose de por qué califican
        $nuncaContactadas    = $listings->whereNull('contact_at')->count();
        $masde30dias         = $listings->whereNotNull('contact_at')->count();
        $this->info("📊 Desglose:");
        $this->info("   🔴 Nunca contactadas:          {$nuncaContactadas}");
        $this->info("   🟡 Sin contacto hace +30 días: {$masde30dias}");
    }
}
