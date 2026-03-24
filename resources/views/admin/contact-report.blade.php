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
    .badge-status   { background:#fee2e2; color:#991b1b; }
    .badge-available{ background:#fce7f3; color:#9d174d; }
    .bar {
        transition: width 0.6s cubic-bezier(0.4,0,0.2,1);
    }
    .collapsible-body {
        transition: max-height 0.3s ease, opacity 0.3s ease;
        overflow: hidden;
    }
    .collapsible-body.collapsed {
        max-height: 0;
        opacity: 0;
    }
    .collapsible-body.expanded {
        max-height: 2000px;
        opacity: 1;
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
                    Propiedades actualizadas del
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

                {{-- Campo oculto que guarda el range activo al hacer submit --}}
                <input type="hidden" name="range" id="rangeInput" value="{{ $range }}">

                <div class="flex rounded-lg border border-gray-300 overflow-hidden bg-white text-sm">
                    @foreach(['today' => 'Hoy', 'yesterday' => 'Ayer', '15days' => '15 días', '30days' => '30 días'] as $val => $label)
                    <button type="button" onclick="applyRange('{{ $val }}')"
                        class="px-3 py-2 font-medium transition range-btn
                            {{ $range === $val ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-100' }}"
                        data-value="{{ $val }}">
                        {{ $label }}
                    </button>
                    @endforeach
                    {{-- Personalizado: solo muestra el panel, NO hace submit --}}
                    <button type="button" onclick="showCustom()"
                        class="px-3 py-2 font-medium transition range-btn
                            {{ $range === 'custom' ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-100' }}"
                        data-value="custom">
                        Personalizado
                    </button>
                </div>

                {{-- Fechas personalizadas: solo visible con "Personalizado" --}}
                <div id="customDates" class="{{ $range === 'custom' ? 'flex' : 'hidden' }} gap-2 items-center flex-wrap">
                    <input type="date" name="from" id="fromInput"
                        value="{{ $range === 'custom' && request('from') ? request('from') : now()->subDays(7)->format('Y-m-d') }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <span class="text-gray-400 text-sm">→</span>
                    <input type="date" name="to" id="toInput"
                        value="{{ $range === 'custom' && request('to') ? request('to') : now()->format('Y-m-d') }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    {{-- Solo este botón hace submit con range=custom --}}
                    <button type="submit" onclick="document.getElementById('rangeInput').value='custom'"
                        class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700">
                        Aplicar
                    </button>
                </div>
            </form>
        </div>

        {{-- ── TARJETAS DE RESUMEN (propiedades únicas) ───────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">

            <div class="stat-card bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Propiedades Contactadas</span>
                    <span class="text-lg">📞</span>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $totalContactedListings }}</p>
                <p class="text-xs text-gray-400 mt-1">propiedades únicas contactadas</p>
            </div>

            <div class="stat-card bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cambios de Precio</span>
                    <span class="text-lg">💰</span>
                </div>
                <p class="text-3xl font-bold text-yellow-600">{{ $totalPriceListings }}</p>
                <p class="text-xs text-gray-400 mt-1">propiedades con precio actualizado</p>
            </div>

            <div class="stat-card bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Desactivadas</span>
                    <span class="text-lg">🚫</span>
                </div>
                <p class="text-3xl font-bold text-red-600">{{ $totalStatusOff + $totalAvailableOff }}</p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $totalStatusOff }} estado off · {{ $totalAvailableOff }} no disp.
                </p>
            </div>

            <div class="stat-card bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Propiedades</span>
                    <span class="text-lg">🏠</span>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $totalUniqueListings }}</p>
                <p class="text-xs text-gray-400 mt-1">propiedades únicas con actividad</p>
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
                    {{-- Faltan por completar (pending) --}}
                    <div class="mt-4 pt-3 border-t text-xs text-gray-500">
                        Faltan por completar:
                        <span class="font-bold text-yellow-600 text-sm ml-1">{{ $queueStats->pending ?? 0 }}</span>
                        de
                        <span class="font-semibold text-gray-700">{{ $queueStats->total ?? 0 }}</span>
                    </div>
                </div>

                <div class="mt-3 grid grid-cols-2 gap-3 text-center">
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

        {{-- ── PROPIEDADES DESACTIVADAS ────────────────────────────────── --}}
        @if($deactivatedListings->count() > 0 || $unavailableListings->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
            <button type="button" onclick="toggleSection('deactivated')"
                class="w-full flex items-center justify-between px-5 py-4 border-b border-gray-100 text-left">
                <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span>🚫</span> Propiedades Desactivadas / No Disponibles
                    <span class="ml-2 text-xs font-normal bg-red-100 text-red-600 px-2 py-0.5 rounded-full">
                        {{ $deactivatedListings->count() + $unavailableListings->count() }} propiedades
                    </span>
                </h3>
                <span id="deactivated-icon" class="text-gray-400 text-xs">▲</span>
            </button>

            <div id="deactivated-body" class="collapsible-body expanded">
                <div class="p-5">
                    @if($deactivatedListings->count() > 0)
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                        🔴 Estado desactivado (status = 0) — {{ $deactivatedListings->count() }} propiedad(es)
                    </p>
                    <div class="overflow-x-auto mb-5">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Código</th>
                                    <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Propiedad</th>
                                    <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Comentario</th>
                                    <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deactivatedListings as $item)
                                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                    <td class="py-2 px-3">
                                        @if($item->listing)
                                        <a href="{{ route('home.tw.edit', $item->listing) }}"
                                           class="text-blue-600 hover:underline font-medium text-xs">
                                            {{ $item->listing->product_code }}
                                        </a>
                                        @else
                                        <span class="text-gray-400 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="text-gray-700 text-xs truncate block max-w-[200px]">
                                            {{ $item->listing->listing_title ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 hidden sm:table-cell">
                                        <span class="text-gray-500 text-xs truncate block max-w-[250px]">{{ $item->comment }}</span>
                                    </td>
                                    <td class="py-2 px-3 hidden md:table-cell text-gray-400 text-xs whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    @if($unavailableListings->count() > 0)
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                        🟣 No disponible (available = 2) — {{ $unavailableListings->count() }} propiedad(es)
                    </p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Código</th>
                                    <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Propiedad</th>
                                    <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Comentario</th>
                                    <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unavailableListings as $item)
                                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                    <td class="py-2 px-3">
                                        @if($item->listing)
                                        <a href="{{ route('home.tw.edit', $item->listing) }}"
                                           class="text-blue-600 hover:underline font-medium text-xs">
                                            {{ $item->listing->product_code }}
                                        </a>
                                        @else
                                        <span class="text-gray-400 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="text-gray-700 text-xs truncate block max-w-[200px]">
                                            {{ $item->listing->listing_title ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 hidden sm:table-cell">
                                        <span class="text-gray-500 text-xs truncate block max-w-[250px]">{{ $item->comment }}</span>
                                    </td>
                                    <td class="py-2 px-3 hidden md:table-cell text-gray-400 text-xs whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ── PROPIEDADES CON CAMBIO DE PRECIO ───────────────────────── --}}
        @if($priceChangedListings->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
            <button type="button" onclick="toggleSection('prices')"
                class="w-full flex items-center justify-between px-5 py-4 border-b border-gray-100 text-left">
                <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span>💰</span> Propiedades con Cambio de Precio
                    <span class="ml-2 text-xs font-normal bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                        {{ $priceChangedListings->count() }} propiedades
                    </span>
                </h3>
                <span id="prices-icon" class="text-gray-400 text-xs">▲</span>
            </button>

            <div id="prices-body" class="collapsible-body expanded">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase">Código</th>
                                <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase">Propiedad</th>
                                <th class="text-right py-2 px-4 text-xs font-semibold text-gray-500 uppercase">Precio Anterior</th>
                                <th class="text-right py-2 px-4 text-xs font-semibold text-gray-500 uppercase">Precio Nuevo</th>
                                <th class="text-right py-2 px-4 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Diferencia</th>
                                <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($priceChangedListings as $item)
                            @php
                                $diff = ($item->property_price ?? 0) - ($item->property_price_prev ?? 0);
                                $diffClass = $diff < 0 ? 'text-green-600' : ($diff > 0 ? 'text-red-500' : 'text-gray-400');
                                $diffPrefix = $diff > 0 ? '+' : '';
                            @endphp
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                <td class="py-2 px-4">
                                    @if($item->listing)
                                    <a href="{{ route('home.tw.edit', $item->listing) }}"
                                       class="text-blue-600 hover:underline font-medium text-xs">
                                        {{ $item->listing->product_code }}
                                    </a>
                                    @else
                                    <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4">
                                    <span class="text-gray-700 text-xs truncate block max-w-[180px]">
                                        {{ $item->listing->listing_title ?? '—' }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 text-right text-xs text-gray-500">
                                    {{ $item->property_price_prev ? '$'.number_format($item->property_price_prev, 0, ',', '.') : '—' }}
                                </td>
                                <td class="py-2 px-4 text-right text-xs font-semibold text-gray-800">
                                    {{ $item->property_price ? '$'.number_format($item->property_price, 0, ',', '.') : '—' }}
                                </td>
                                <td class="py-2 px-4 text-right text-xs font-bold hidden sm:table-cell {{ $diffClass }}">
                                    @if($item->property_price_prev && $item->property_price)
                                        {{ $diffPrefix }}${{ number_format($diff, 0, ',', '.') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-2 px-4 hidden md:table-cell text-gray-400 text-xs whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- ── ACTIVIDAD POR DÍA ───────────────────────────────────────── --}}
        @if($activityByDay->count() > 1)
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mb-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <span>📈</span> Actividad por Día
                <span class="text-xs font-normal text-gray-400">(propiedades únicas)</span>
            </h3>
            @php $maxDay = $activityByDay->max('total') ?: 1; @endphp
            <div class="flex items-end gap-1 h-28 overflow-x-auto pb-2">
                @foreach($activityByDay as $day)
                @php $h = round(($day->total / $maxDay) * 100); @endphp
                <div class="flex flex-col items-center gap-1 min-w-[36px] group">
                    <div class="relative w-full">
                        <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap opacity-0 group-hover:opacity-100 transition z-10 pointer-events-none">
                            📞 {{ $day->contacts }} | 💰 {{ $day->prices }} | 🚫 {{ $day->statuses }}
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
                <span class="text-xs font-normal text-gray-400">(propiedades únicas)</span>
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Asesora</th>
                            <th class="text-center py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Contactos</th>
                            <th class="text-center py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Precios</th>
                            <th class="text-center py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Desact.</th>
                            <th class="text-center py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Total Props.</th>
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
                                'Contact'   => ['class' => 'badge-contact',   'label' => 'Contacto',  'icon' => '📞'],
                                'price'     => ['class' => 'badge-price',      'label' => 'Precio',    'icon' => '💰'],
                                'status'    => ['class' => 'badge-status',     'label' => 'Estado',    'icon' => '🔄'],
                                'available' => ['class' => 'badge-available',  'label' => 'Disponib.', 'icon' => '🏠'],
                            ];
                            $badge      = $typeMap[$item->type] ?? ['class' => 'bg-gray-100 text-gray-600', 'label' => $item->type, 'icon' => ''];
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition text-xs">
                            <td class="py-3 px-4">
                                <span class="{{ $badge['class'] }} text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap">
                                    {{ $badge['icon'] }} {{ $badge['label'] }}
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
    // Aplica un rango simple (hoy, ayer, 15days, 30days): hace submit inmediato
    function applyRange(val) {
        document.getElementById('rangeInput').value = val;
        // Ocultar panel de fechas personalizadas para que no envíe from/to vacíos
        document.getElementById('customDates').classList.add('hidden');
        document.getElementById('customDates').classList.remove('flex');
        // Resaltar botón activo visualmente
        updateActiveBtn(val);
        document.getElementById('filterForm').submit();
    }

    // Muestra el panel de fechas sin hacer submit
    function showCustom() {
        document.getElementById('customDates').classList.remove('hidden');
        document.getElementById('customDates').classList.add('flex');
        updateActiveBtn('custom');
        // NO se llama a submit aquí — el usuario debe hacer click en "Aplicar"
    }

    // Actualiza el estilo del botón activo en la barra
    function updateActiveBtn(val) {
        document.querySelectorAll('.range-btn').forEach(btn => {
            const isActive = btn.dataset.value === val;
            btn.classList.toggle('bg-gray-800', isActive);
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('text-gray-600', !isActive);
            btn.classList.toggle('hover:bg-gray-100', !isActive);
        });
    }

    // Toggle secciones colapsables
    function toggleSection(id) {
        const body = document.getElementById(id + '-body');
        const icon = document.getElementById(id + '-icon');
        if (body.classList.contains('expanded')) {
            body.classList.remove('expanded');
            body.classList.add('collapsed');
            icon.textContent = '▼';
        } else {
            body.classList.remove('collapsed');
            body.classList.add('expanded');
            icon.textContent = '▲';
        }
    }
</script>
@endsection