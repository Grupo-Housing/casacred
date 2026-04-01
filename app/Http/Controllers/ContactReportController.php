<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\ContactQueue;
use App\Models\Listing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactReportController extends Controller
{
    public function index(Request $request)
    {
        // Rango de fechas — por defecto hoy
        $range = $request->input('range', 'today');
        $from  = $request->input('from');
        $to    = $request->input('to');

        [$dateFrom, $dateTo] = $this->resolveDates($range, $from, $to);

        // ── SECCIÓN 1: Propiedades únicas con actividad ─────────────────

        // Propiedades contactadas (unique por listing_id)
        $totalContactedListings = Comment::where('type', 'Contact')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->distinct('listing_id')
            ->count('listing_id');

        // Propiedades con cambio de precio (unique por listing_id)
        $totalPriceListings = Comment::where('type', 'price')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->distinct('listing_id')
            ->count('listing_id');

        // Propiedades desactivadas: status = 0
        $totalStatusOff = Comment::where('type', 'status')
            ->where('value', 0)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->distinct('listing_id')
            ->count('listing_id');

        // Propiedades marcadas como no disponibles: available = 2
        $totalAvailableOff = Comment::where('type', 'available')
            ->where('value', 2)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->distinct('listing_id')
            ->count('listing_id');

        // Total propiedades desactivadas únicas (unión de ambos tipos, sin duplicar)
        $totalDeactivated = Comment::whereIn('type', ['status', 'available'])
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('type', 'status')->where('value', 0);
                })->orWhere(function ($q2) {
                    $q2->where('type', 'available')->where('value', 2);
                });
            })
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->distinct('listing_id')
            ->count('listing_id');

        // ── Porcentaje de completado de la cola (para tarjeta 4) ────────
        // $queueTotalForCard  = ContactQueue::count();
        // $queueDoneForCard   = ContactQueue::where('status', 'done')->count();
        // $queueCompletionPct = $queueTotalForCard > 0
        //     ? round(($queueDoneForCard / $queueTotalForCard) * 100)
        //     : 0;

        $queueTotalForCard  = Listing::where('available', 1)->count();
        $queueDoneForCard   = ContactQueue::where('status', 'done')
            ->whereDate('completed_at', Carbon::today())
            ->count();
        $queueCompletionPct = $queueTotalForCard > 0
            ? round(($queueDoneForCard / $queueTotalForCard) * 100)
            : 0;

        // ── Propiedades desactivadas UNIFICADAS (detalle) ───────────────
        $allDeactivationComments = Comment::with([
            'listing:id,product_code,listing_title,status,available',
            'user:id,name'
        ])
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('type', 'status')->where('value', 0);
                })->orWhere(function ($q2) {
                    $q2->where('type', 'available')->where('value', 2);
                });
            })
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('listing_id', 'type', 'value', 'comment', 'created_at', 'user_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Agrupar por listing_id con estado real actual de la propiedad
        $deactivatedListings = $allDeactivationComments
            ->groupBy('listing_id')
            ->map(function ($rows) {
                $first   = $rows->first();
                $listing = $first->listing;

                $currentStatus    = $listing ? (int) $listing->status    : null;
                $currentAvailable = $listing ? (int) $listing->available : null;

                // Etiqueta de estado actual (compatible PHP 7, sin match)
                if ($currentAvailable === 2 && $currentStatus === 0) {
                    $currentState = 'baja';
                } elseif ($currentAvailable === 2) {
                    $currentState = 'no_disponible';
                } elseif ($currentStatus === 0) {
                    $currentState = 'off';
                } else {
                    $currentState = 'activa';
                }

                return (object) [
                    'listing_id'        => $first->listing_id,
                    'listing'           => $listing,
                    'user'              => $first->user,
                    'created_at'        => $first->created_at,
                    'types'             => $rows->pluck('type')->unique()->values(),
                    'comments'          => $rows->mapWithKeys(function ($r) {
                        return [$r->type => $r->comment];
                    }),
                    'current_status'    => $currentStatus,
                    'current_available' => $currentAvailable,
                    'current_state'     => $currentState,
                ];
            })
            ->values();

        // ── Propiedades con cambio de precio (detalle) ──────────────────
        $priceChangedListings = Comment::with(['listing:id,product_code,listing_title', 'user:id,name'])
            ->where('type', 'price')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('listing_id', 'comment', 'property_price_prev', 'property_price', 'created_at', 'user_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('listing_id');

        // ── Actividad por día (para gráfico) ────────────────────────────
        $activityByDay = Comment::select(
            DB::raw('DATE(created_at) as day'),
            DB::raw('COUNT(DISTINCT CASE WHEN type = "Contact" THEN listing_id END) as contacts'),
            DB::raw('COUNT(DISTINCT CASE WHEN type = "price" THEN listing_id END) as prices'),
            DB::raw('COUNT(DISTINCT CASE WHEN type IN ("status","available") THEN listing_id END) as statuses')
        )
            ->whereIn('type', ['Contact', 'price', 'status', 'available'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->get()
            ->map(function ($row) {
                $row->total = $row->contacts + $row->prices + $row->statuses;
                return $row;
            });

        // ── Actividad por asesora (propiedades únicas) ──────────────────
        $activityByUser = Comment::select(
            'user_id',
            DB::raw('COUNT(DISTINCT CASE WHEN type = "Contact" THEN listing_id END) as contacts'),
            DB::raw('COUNT(DISTINCT CASE WHEN type = "price" THEN listing_id END) as prices'),
            DB::raw('COUNT(DISTINCT CASE WHEN type IN ("status","available") THEN listing_id END) as statuses'),
            DB::raw('COUNT(DISTINCT listing_id) as total')
        )
            ->whereIn('type', ['Contact', 'price', 'status', 'available'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('user_id')
            ->with('user:id,name')
            ->get();

        // ── Últimas actualizaciones detalladas ──────────────────────────
        $recentActivity = Comment::with(['listing:id,product_code,listing_title', 'user:id,name'])
            ->whereIn('type', ['Contact', 'price', 'status', 'available'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // ── SECCIÓN 2: Estado de la Cola de Contacto ────────────────────
        $queueStats = ContactQueue::select(
            DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending'),
            DB::raw('SUM(CASE WHEN status = "done" THEN 1 ELSE 0 END) as done'),
            DB::raw('SUM(CASE WHEN status = "skipped" THEN 1 ELSE 0 END) as skipped'),
            DB::raw('COUNT(*) as total')
        )
            ->first();

        // Contactos registrados en el RANGO ACTIVO por asesora
        $contactedToday = Comment::select(
            'user_id',
            DB::raw('COUNT(DISTINCT listing_id) as contactadas_hoy')
        )
            ->where('type', 'Contact')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('user_id')
            ->with('user:id,name')
            ->get();

        // Completadas en la cola HOY por asesora
        $queueDoneToday = ContactQueue::select(
            'user_id',
            DB::raw('COUNT(*) as completadas_hoy')
        )
            ->where('status', 'done')
            ->whereDate('completed_at', Carbon::today())
            ->groupBy('user_id')
            ->with('user:id,name')
            ->get();

        // Pendientes por asesora
        $queuePendingByUser = ContactQueue::select(
            'user_id',
            DB::raw('COUNT(*) as pendientes')
        )
            ->where('status', 'pending')
            ->groupBy('user_id')
            ->with('user:id,name')
            ->get();

        // Propiedades nunca contactadas
        $neverContacted = Listing::where('available', 1)
            ->whereNull('contact_at')
            ->count();

        // Propiedades con contacto hace más de 30 días
        $overdueContact = Listing::where('available', 1)
            ->whereNotNull('contact_at')
            ->where('contact_at', '<', Carbon::now()->subDays(30))
            ->count();

        return view('admin.contact-report', compact(
            'range',
            'dateFrom',
            'dateTo',
            'totalContactedListings',
            'totalPriceListings',
            'totalStatusOff',
            'totalAvailableOff',
            'totalDeactivated',
            'queueCompletionPct',
            'queueDoneForCard',
            'queueTotalForCard',
            'deactivatedListings',
            'priceChangedListings',
            'activityByDay',
            'activityByUser',
            'recentActivity',
            'queueStats',
            'contactedToday',
            'queueDoneToday',
            'queuePendingByUser',
            'neverContacted',
            'overdueContact'
        ));
    }

    private function resolveDates(string $range, ?string $from, ?string $to): array
    {
        switch ($range) {
            case 'today':
                return [
                    Carbon::today()->startOfDay(),
                    Carbon::today()->endOfDay(),
                ];
            case 'yesterday':
                return [
                    Carbon::yesterday()->startOfDay(),
                    Carbon::yesterday()->endOfDay(),
                ];
            case '15days':
                return [
                    Carbon::now()->subDays(15)->startOfDay(),
                    Carbon::now()->endOfDay(),
                ];
            case '30days':
                return [
                    Carbon::now()->subDays(30)->startOfDay(),
                    Carbon::now()->endOfDay(),
                ];
            case 'custom':
                if ($from && $to) {
                    return [
                        Carbon::parse($from)->startOfDay(),
                        Carbon::parse($to)->endOfDay(),
                    ];
                }
                return [
                    Carbon::today()->startOfDay(),
                    Carbon::today()->endOfDay(),
                ];
            default:
                return [
                    Carbon::today()->startOfDay(),
                    Carbon::today()->endOfDay(),
                ];
        }
    }
}
