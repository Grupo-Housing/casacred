<div class="units-section my-5">

    <h2 class="units-section-title mb-4">
        <i class="fas fa-building" style="color: #142743; font-size: 22px; margin-right: 10px;"></i>
        Unidades disponibles
        <span class="units-count-badge">{{ count($units) }}</span>
    </h2>

    <div class="units-list">
        @foreach ($units as $unit)
            <div class="unit-row">

                {{-- Columna izquierda: identificación --}}
                <div class="unit-row-identity">
                    <div class="d-flex align-items-center gap-2" style="gap: 10px;">
                        <div class="unit-icon-circle">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>
                            <p class="unit-row-name">{{ $unit->name ?? 'Unidad' }}</p>
                            @if($unit->unit_number)
                                <p class="unit-row-number"># {{ $unit->unit_number }}</p>
                            @endif
                        </div>
                    </div>
                    <span class="unit-status-pill unit-status-{{ $unit->status ?? 'available' }}">
                        @php
                            $statusLabels = [
                                'available'  => 'Disponible',
                                'sold'       => 'Vendido',
                                'vendido'    => 'Vendido',
                                'reserved'   => 'Reservado',
                                'reservado'  => 'Reservado',
                            ];
                        @endphp
                        {{ $statusLabels[$unit->status] ?? ucfirst($unit->status ?? 'N/D') }}
                    </span>
                </div>

                {{-- Columna central: características --}}
                <div class="unit-row-features">
                    @if($unit->floor)
                        <div class="unit-chip">
                            <i class="fas fa-layer-group"></i>
                            <span>Piso {{ $unit->floor }}</span>
                        </div>
                    @endif
                    @if($unit->area_m2)
                        <div class="unit-chip">
                            <i class="fas fa-ruler-combined"></i>
                            <span>{{ $unit->area_m2 }} m²</span>
                        </div>
                    @endif
                    @if($unit->bedrooms)
                        <div class="unit-chip">
                            <i class="fas fa-bed"></i>
                            <span>{{ $unit->bedrooms }} {{ $unit->bedrooms == 1 ? 'Hab.' : 'Habs.' }}</span>
                        </div>
                    @endif
                    @if($unit->bathrooms)
                        <div class="unit-chip">
                            <i class="fas fa-bath"></i>
                            <span>{{ $unit->bathrooms }} {{ $unit->bathrooms == 1 ? 'Baño' : 'Baños' }}</span>
                        </div>
                    @endif
                    @if($unit->description)
                        <p class="unit-row-desc">{{ $unit->description }}</p>
                    @endif
                </div>

                {{-- Columna derecha: precio --}}
                @if($unit->price || $unit->rent_price)
                    <div class="unit-row-price">
                        @if($unit->price)
                            <div>
                                <span class="unit-price-label">Venta</span>
                                <span class="unit-price-value">${{ number_format($unit->price, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($unit->rent_price)
                            <div>
                                <span class="unit-price-label"><i class="fas fa-key" style="font-size:10px"></i> Renta</span>
                                <span class="unit-price-value unit-rent-value">${{ number_format($unit->rent_price, 0, ',', '.') }}<span style="font-size:11px;font-weight:500">/mes</span></span>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        @endforeach
    </div>

</div>

<style>
.units-section-title {
    font-family: 'Sharp Grotesk', sans-serif;
    font-size: 22px;
    font-weight: 700;
    color: #1a1a1a;
    border-bottom: 2px solid #142743;
    padding-bottom: 12px;
    display: flex;
    align-items: center;
}

.units-count-badge {
    margin-left: 10px;
    background-color: #142743;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    padding: 2px 10px;
    border-radius: 20px;
}

/* ── Lista ──────────────────────────────────────────────── */
.units-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* ── Fila (card horizontal) ─────────────────────────────── */
.unit-row {
    display: flex;
    align-items: center;
    gap: 0;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: rgba(0, 0, 0, 0.07) 0px 2px 12px;
    overflow: hidden;
    transition: transform 0.22s ease, box-shadow 0.22s ease;
    border-left: 4px solid #142743;
}

.unit-row:hover {
    transform: translateY(-3px);
    box-shadow: rgba(20, 39, 67, 0.18) 0px 8px 24px;
    border-left-color: #1e3a5f;
}

/* ── Columna identidad ───────────────────────────────────── */
.unit-row-identity {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10px;
    padding: 16px 20px;
    min-width: 180px;
    background-color: #f8fafc;
    border-right: 1px solid #e5e7eb;
    align-self: stretch;
}

.unit-icon-circle {
    width: 36px;
    height: 36px;
    background-color: #142743;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.unit-icon-circle i {
    color: #fff;
    font-size: 14px;
}

.unit-row-name {
    font-family: 'Sharp Grotesk', sans-serif;
    font-weight: 700;
    font-size: 14px;
    color: #142743;
    margin: 0;
    line-height: 1.2;
}

.unit-row-number {
    font-size: 12px;
    color: #9ca3af;
    margin: 2px 0 0;
}

.unit-status-pill {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 12px;
    border-radius: 20px;
    white-space: nowrap;
    align-self: flex-start;
}

.unit-status-available    { background-color: #d1fae5; color: #065f46; }
.unit-status-sold,
.unit-status-vendido      { background-color: #fee2e2; color: #991b1b; }
.unit-status-reserved,
.unit-status-reservado    { background-color: #fef3c7; color: #92400e; }

/* ── Columna características ────────────────────────────── */
.unit-row-features {
    flex: 1;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    padding: 16px 20px;
}

.unit-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background-color: #f3f4f6;
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 13px;
    font-weight: 600;
    color: #142743;
    white-space: nowrap;
}

.unit-chip i {
    font-size: 12px;
    color: #142743;
}

.unit-row-desc {
    width: 100%;
    font-size: 12px;
    color: #6b7280;
    margin: 4px 0 0;
    line-height: 1.5;
}

/* ── Columna precio ─────────────────────────────────────── */
.unit-row-price {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    padding: 16px 24px;
    min-width: 160px;
    border-left: 1px solid #e5e7eb;
    align-self: stretch;
    justify-content: center;
    background-color: #f8fafc;
}

.unit-price-label {
    display: block;
    font-size: 11px;
    color: #9ca3af;
    font-weight: 500;
    text-align: right;
}

.unit-price-value {
    display: block;
    font-family: 'Sharp Grotesk', sans-serif;
    font-size: 17px;
    font-weight: 700;
    color: #142743;
    text-align: right;
}

.unit-rent-value {
    color: #059669;
}

/* ── Responsivo ─────────────────────────────────────────── */
@media (max-width: 768px) {
    .unit-row {
        flex-direction: column;
        align-items: stretch;
        border-left: none;
        border-top: 4px solid #142743;
    }

    .unit-row-identity {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        border-right: none;
        border-bottom: 1px solid #e5e7eb;
        min-width: unset;
    }

    .unit-row-price {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        border-left: none;
        border-top: 1px solid #e5e7eb;
        min-width: unset;
    }

    .unit-price-label,
    .unit-price-value {
        text-align: left;
    }
}
</style>
