<?php

namespace App\Http\Controllers;

use App\Models\ContactQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactQueueController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $query = ContactQueue::with(['listing' => function ($q) {
            $q->select('id', 'listing_title', 'product_code', 'contact_at', 'no_answer_at');
        }])
            ->where('status', 'pending');

        if (!$user->is_admin) {
            $query->where('user_id', $user->id);
        }

        $items = $query->orderBy('assigned_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'items'   => $items,
            'total'   => $items->count(),
        ]);
    }

    /**
     * Marca una asignación como completada (se llama al guardar el contacto).
     */
    public function markDone(Request $request)
    {
        $queueId = $request->input('queue_id');

        $item = ContactQueue::where('id', $queueId)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $item->update([
            'status'       => 'done',
            'completed_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Salta una propiedad sin contactar (opcional).
     */
    public function skip(Request $request)
    {
        $queueId = $request->input('queue_id');

        // En lugar de marcar skipped, actualiza assigned_at 
        // para que quede al final de la cola del día
        ContactQueue::where('id', $queueId)
            ->where('user_id', Auth::id())
            ->update(['assigned_at' => now()]); // va al final por fecha

        return response()->json(['success' => true]);
    }

    public function view()
    {
        return view('admin.contact-queue');
    }
}
