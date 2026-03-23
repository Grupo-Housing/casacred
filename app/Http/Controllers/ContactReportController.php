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

        // ── SECCIÓN 1: Resumen de actividad de Comments ─────────────────
        $totalContacts = Comment::where('type', 'Contact')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        $totalPriceChanges = Comment::where('type', 'price')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        $totalStatusChanges = Comment::whereIn('type', ['status', 'available'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        // Actividad por día (para gráfico)
        $activityByDay = Comment::select(
            DB::raw('DATE(created_at) as day'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN type = "Contact" THEN 1 ELSE 0 END) as contacts'),
            DB::raw('SUM(CASE WHEN type = "price" THEN 1 ELSE 0 END) as prices'),
            DB::raw('SUM(CASE WHEN type IN ("status","available") THEN 1 ELSE 0 END) as statuses')
        )
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->get();

        // Actividad por asesora
        $activityByUser = Comment::select(
            'user_id',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN type = "Contact" THEN 1 ELSE 0 END) as contacts'),
            DB::raw('SUM(CASE WHEN type = "price" THEN 1 ELSE 0 END) as prices'),
            DB::raw('SUM(CASE WHEN type IN ("status","available") THEN 1 ELSE 0 END) as statuses')
        )
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('user_id')
            ->with('user:id,name')
            ->get();

        // Últimas actualizaciones detalladas
        $recentActivity = Comment::with('listing:id,product_code,listing_title')
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
            'totalContacts',
            'totalPriceChanges',
            'totalStatusChanges',
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
                return [
                    Carbon::parse($from)->startOfDay(),
                    Carbon::parse($to)->endOfDay(),
                ];
            default:
                return [
                    Carbon::today()->startOfDay(),
                    Carbon::today()->endOfDay(),
                ];
        }
    }
}
