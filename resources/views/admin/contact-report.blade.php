@extends('layouts.dashtw')

@section('firstscript')
<title>Reporte de Contactos - Grupo Housing</title>
<style>
    .stat-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.10);
    }
    .badge-contact  { background:#dcfce7; color:#166534; }
    .badge-price    { background:#fef9c3; color:#854d0e; }
    .badge-status   { background:#dbeafe; color:#1e40af; }
    .badge-available{ background:#f3e8ff; color:#6b21a8; }
    .bar {
        transition: width 0.6s cubic-bezier(0.4,0,0.2,1);
    }
    @media (max-width: 768px) {
        .grid-cols-4 { grid-template-columns: repeat(2, 1fr) !important; }
        .grid-cols-3 { grid-template-columns: 1fr !important; }
        .grid-cols-2 { grid-template-columns: 1fr !important; }
    }
</style>
@endsection

@section('content')
<main class="overflow-x-hidden overflow-y-auto bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-6">

        {{-- ── HEADER ─────────────────────────────────────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📊 Reporte de Contactos</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    Actividad del
                    <span class="font-medium text-gray-700">
                        {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
                    </span>
                    al
                    <span class="font-medium text-gray-700">
                        {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                    </span>
                </p>
            </div>

            {{-- Filtro de rango --}}
            <form method="GET" action="{{ route('admin.contact.report') }}" 
                  class="flex flex-wrap items-end gap-2" id="filterForm">

                <div class="flex rounded-lg border border-gray-300 overflow-hidden bg-white text-sm">
                    @foreach(['today' => 'Hoy', '15days' => '15 días', '30days' => '30 días', 'custom' => 'Personalizado'] as $val => $label)
                    <button type="submit" name="range" value="{{ $val }}"
                        class="px-3 py-2 font-medium transition
                            {{ $range === $val ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>

                {{-- Fechas personalizadas --}}
                <div id="customDates" class="{{ $range === 'custom' ? 'flex' : 'hidden' }} gap-2 items-center">
                    <input type="date" name="from" value="{{ request('from', now()->subDays(7)->format('Y-m-d')) }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <span class="text-gray-400 text-sm">→</span>
                    <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <button type="submit"
                        class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700">
                        Aplicar
                    </button>
                </div>
            </form>
        </div>

        {{-- ── TARJETAS DE RESUMEN ─────────────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">

            <div class="stat-card bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Contactos</span>
                    <span class="text-lg">📞</span>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $totalContacts }}</p>
                <p class="text-xs text-gray-400 mt-1">registros tipo Contact</p>
            </div>

            <div class="stat-card bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cambios Precio</span>
                    <span class="text-lg">💰</span>
                </div>
                <p class="text-3xl font-bold text-yellow-600">{{ $totalPriceChanges }}</p>
                <p class="text-xs text-gray-400 mt-1">actualizaciones de precio</p>
            </div>

            <div class="stat-card bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cambios Estado</span>
                    <span class="text-lg">🔄</span>
                </div>
                <p class="text-3xl font-bold text-blue-600">{{ $totalStatusChanges }}</p>
                <p class="text-xs text-gray-400 mt-1">status / disponibilidad</p>
            </div>

            <div class="stat-card bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Actividad</span>
                    <span class="text-lg">📋</span>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $totalContacts + $totalPriceChanges + $totalStatusChanges }}</p>
                <p class="text-xs text-gray-400 mt-1">acciones en el período</p>
            </div>
        </div>

        {{-- ── COLA DE CONTACTO ────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

            {{-- Estado general de la cola --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <span>🗂️</span> Estado de la Cola
                </h3>
                @php $total = $queueStats->total ?: 1; @endphp

                <div class="space-y-3">
                    {{-- Pendientes --}}
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500 font-medium">Pendientes</span>
                            <span class="font-bold text-yellow-600">{{ $queueStats->pending ?? 0 }}</span>
                        </div>
                        <div class="bg-gray-100 rounded-full h-2">
                            <div class="bar bg-yellow-400 h-2 rounded-full"
                                 style="width: {{ round((($queueStats->pending ?? 0) / $total) * 100) }}%"></div>
                        </div>
                    </div>
                    {{-- Completadas --}}
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500 font-medium">Completadas</span>
                            <span class="font-bold text-green-600">{{ $queueStats->done ?? 0 }}</span>
                        </div>
                        <div class="bg-gray-100 rounded-full h-2">
                            <div class="bar bg-green-500 h-2 rounded-full"
                                 style="width: {{ round((($queueStats->done ?? 0) / $total) * 100) }}%"></div>
                        </div>
                    </div>
                    {{-- Saltadas --}}
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500 font-medium">Saltadas</span>
                            <span class="font-bold text-gray-400">{{ $queueStats->skipped ?? 0 }}</span>
                        </div>
                        <div class="bg-gray-100 rounded-full h-2">
                            <div class="bar bg-gray-400 h-2 rounded-full"
                                 style="width: {{ round((($queueStats->skipped ?? 0) / $total) * 100) }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t grid grid-cols-2 gap-3 text-center">
                    <div class="bg-red-50 rounded-lg p-2">
                        <p class="text-xl font-bold text-red-600">{{ $neverContacted }}</p>
                        <p class="text-xs text-red-400">Nunca contactadas</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-2">
                        <p class="text-xl font-bold text-orange-500">{{ $overdueContact }}</p>
                        <p class="text-xs text-orange-400">+30 días sin contacto</p>
                    </div>
                </div>
            </div>

            {{-- Completadas hoy por asesora --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <span>✅</span> Completadas Hoy
                </h3>
                @if($queueDoneToday->isEmpty())
                    <div class="flex flex-col items-center justify-center h-24 text-gray-400">
                        <span class="text-2xl mb-1">😴</span>
                        <p class="text-xs">Ninguna completada hoy</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($queueDoneToday as $row)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gray-800 text-white flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($row->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="text-sm text-gray-700 font-medium">{{ $row->user->name ?? 'Usuario '.$row->user_id }}</span>
                            </div>
                            <span class="text-sm font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                                {{ $row->completadas_hoy }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Pendientes por asesora --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <span>⏳</span> Pendientes por Asesora
                </h3>
                @if($queuePendingByUser->isEmpty())
                    <div class="flex flex-col items-center justify-center h-24 text-gray-400">
                        <span class="text-2xl mb-1">🎉</span>
                        <p class="text-xs">Sin pendientes</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($queuePendingByUser as $row)
                        @php $pct = $queueStats->total > 0 ? round(($row->pendientes / $queueStats->total) * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-700 font-medium">{{ $row->user->name ?? 'Usuario '.$row->user_id }}</span>
                                <span class="text-xs font-bold text-yellow-600">{{ $row->pendientes }}</span>
                            </div>
                            <div class="bg-gray-100 rounded-full h-1.5">
                                <div class="bar bg-yellow-400 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ── ACTIVIDAD POR DÍA ───────────────────────────────────────── --}}
        @if($activityByDay->count() > 1)
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mb-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <span>📈</span> Actividad por Día
            </h3>
            @php $maxDay = $activityByDay->max('total') ?: 1; @endphp
            <div class="flex items-end gap-1 h-28 overflow-x-auto pb-2">
                @foreach($activityByDay as $day)
                @php $h = round(($day->total / $maxDay) * 100); @endphp
                <div class="flex flex-col items-center gap-1 min-w-[36px] group">
                    <div class="relative w-full">
                        {{-- Tooltip --}}
                        <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap opacity-0 group-hover:opacity-100 transition z-10 pointer-events-none">
                            Total: {{ $day->total }}<br>
                            📞 {{ $day->contacts }} | 💰 {{ $day->prices }} | 🔄 {{ $day->statuses }}
                        </div>
                        <div class="bar bg-gray-800 rounded-t w-full" style="height: {{ max($h, 4) }}px"></div>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap" style="font-size:10px">
                        {{ \Carbon\Carbon::parse($day->day)->format('d/m') }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── ACTIVIDAD POR ASESORA ──────────────────────────────────── --}}
        @if($activityByUser->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mb-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <span>👤</span> Actividad por Asesora
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Asesora</th>
                            <th class="text-center py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Contactos</th>
                            <th class="text-center py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Precios</th>
                            <th class="text-center py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Estados</th>
                            <th class="text-center py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activityByUser->sortByDesc('total') as $row)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gray-800 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($row->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-700">{{ $row->user->name ?? 'Usuario '.$row->user_id }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="badge-contact text-xs font-semibold px-2 py-0.5 rounded-full">{{ $row->contacts }}</span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="badge-price text-xs font-semibold px-2 py-0.5 rounded-full">{{ $row->prices }}</span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="badge-status text-xs font-semibold px-2 py-0.5 rounded-full">{{ $row->statuses }}</span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="font-bold text-gray-900">{{ $row->total }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── DETALLE DE ACTIVIDAD RECIENTE ──────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span>🕐</span> Actividad Reciente
                    <span class="text-xs font-normal text-gray-400">(últimas 50 acciones)</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                            <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase">Propiedad</th>
                            <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Comentario</th>
                            <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivity as $item)
                        @php
                            $typeMap = [
                                'Contact'   => ['class' => 'badge-contact',  'label' => 'Contacto'],
                                'price'     => ['class' => 'badge-price',     'label' => 'Precio'],
                                'status'    => ['class' => 'badge-status',    'label' => 'Estado'],
                                'available' => ['class' => 'badge-available', 'label' => 'Disponib.'],
                            ];
                            $badgeClass = isset($typeMap[$item->type]) ? $typeMap[$item->type]['class'] : 'bg-gray-100 text-gray-600';
                            $typeLabel  = isset($typeMap[$item->type]) ? $typeMap[$item->type]['label'] : $item->type;
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition text-xs">
                            <td class="py-3 px-4">
                                <span class="{{ $badgeClass }} text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap">
                                    @if($item->type == 'Contact') 📞 
                                    @elseif($item->type == 'price') 💰 
                                    @elseif($item->type == 'status') 🔄 
                                    @elseif($item->type == 'available') 🏠 
                                    @endif
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($item->listing)
                                <a href="{{ route('home.tw.edit', $item->listing) }}"
                                   class="text-blue-600 hover:underline font-medium">
                                    {{ $item->listing->product_code }}
                                </a>
                                <p class="text-gray-400 truncate max-w-[180px]">{{ $item->listing->listing_title }}</p>
                                @else
                                <span class="text-gray-400">{{ $item->property_code }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 hidden sm:table-cell">
                                <span class="text-gray-600 truncate block max-w-[250px]">{{ $item->comment }}</span>
                            </td>
                            <td class="py-3 px-4 hidden md:table-cell text-gray-400 whitespace-nowrap">
                                {{ $item->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-gray-400 text-sm">
                                No hay actividad en este período
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /max-w-7xl --}}
</main>
@endsection

@section('endscript')
<script>
    // Mostrar/ocultar fechas personalizadas
    document.querySelectorAll('button[name="range"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const customDiv = document.getElementById('customDates');
            if (this.value === 'custom') {
                customDiv.classList.remove('hidden');
                customDiv.classList.add('flex');
            } else {
                customDiv.classList.add('hidden');
                customDiv.classList.remove('flex');
            }
        });
    });
</script>
@endsection