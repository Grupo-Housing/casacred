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

        // Total propiedades únicas con cualquier actividad
        $totalUniqueListings = Comment::whereIn('type', ['Contact', 'price', 'status', 'available'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->distinct('listing_id')
            ->count('listing_id');

        // ── Propiedades desactivadas (detalle) ──────────────────────────

        // status = 0: desactivadas
        $deactivatedListings = Comment::with(['listing:id,product_code,listing_title', 'user:id,name'])
            ->where('type', 'status')
            ->where('value', 0)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('listing_id', 'comment', 'created_at', 'user_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('listing_id');

        // available = 2: ya no disponibles
        $unavailableListings = Comment::with(['listing:id,product_code,listing_title', 'user:id,name'])
            ->where('type', 'available')
            ->where('value', 2)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('listing_id', 'comment', 'created_at', 'user_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('listing_id');

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

        // Completadas HOY por asesora
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
            'totalUniqueListings',
            'deactivatedListings',
            'unavailableListings',
            'priceChangedListings',
            'activityByDay',
            'activityByUser',
            'recentActivity',
            'queueStats',
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
                // fallback a hoy si no vienen fechas
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
