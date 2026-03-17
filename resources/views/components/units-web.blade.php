<div class="units-section my-5">
 
    <h2 class="section-title mb-4">
        <i class="fas fa-building me-2" style="color: #142743; font-size: 24px; margin-right: 10px;"></i>
        Unidades disponibles
    </h2>
 
    <div class="row g-3">
        @foreach ($units as $unit)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="unit-card h-100">
 
                    {{-- Header de la card --}}
                    <div class="unit-card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="unit-name mb-0">{{ $unit->name ?? 'Unidad' }}</h6>
                            <span class="unit-status-badge unit-status-{{ $unit->status ?? 'available' }}">
                                {{ $unit->status === 'available' ? 'Disponible' : ucfirst($unit->status ?? 'N/D') }}
                            </span>
                        </div>
                        @if($unit->unit_number)
                            <span class="unit-number"># {{ $unit->unit_number }}</span>
                        @endif
                    </div>
 
                    {{-- Body con características --}}
                    <div class="unit-card-body">
                        <div class="unit-features">
 
                            @if($unit->floor)
                                <div class="unit-feature-item">
                                    <div class="unit-feature-icon">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div>
                                        <span class="unit-feature-label">Piso</span>
                                        <span class="unit-feature-value">{{ $unit->floor }}</span>
                                    </div>
                                </div>
                            @endif
 
                            @if($unit->area_m2)
                                <div class="unit-feature-item">
                                    <div class="unit-feature-icon">
                                        <i class="fas fa-ruler-combined"></i>
                                    </div>
                                    <div>
                                        <span class="unit-feature-label">Área</span>
                                        <span class="unit-feature-value">{{ $unit->area_m2 }} m²</span>
                                    </div>
                                </div>
                            @endif
 
                            @if($unit->bedrooms)
                                <div class="unit-feature-item">
                                    <div class="unit-feature-icon">
                                        <i class="fas fa-bed"></i>
                                    </div>
                                    <div>
                                        <span class="unit-feature-label">Habitaciones</span>
                                        <span class="unit-feature-value">{{ $unit->bedrooms }}</span>
                                    </div>
                                </div>
                            @endif
 
                            @if($unit->bathrooms)
                                <div class="unit-feature-item">
                                    <div class="unit-feature-icon">
                                        <i class="fas fa-bath"></i>
                                    </div>
                                    <div>
                                        <span class="unit-feature-label">Baños</span>
                                        <span class="unit-feature-value">{{ $unit->bathrooms }}</span>
                                    </div>
                                </div>
                            @endif
 
                        </div>
 
                        @if($unit->description)
                            <p class="unit-description">{{ $unit->description }}</p>
                        @endif
                    </div>
 
                    {{-- Footer con precio --}}
                    @if($unit->price || $unit->rent_price)
                      <div class="unit-card-footer">
                          @if($unit->price)
                              <div class="d-flex flex-column">
                                  <span class="unit-price-label">Precio venta</span>
                                  <span class="unit-price">${{ number_format($unit->price, 0, ',', '.') }}</span>
                              </div>
                          @endif

                          @if($unit->rent_price)
                              <div class="d-flex flex-column align-items-end">
                                  <span class="unit-price-label">
                                      <i class="fas fa-key" style="font-size:10px"></i> Precio renta
                                  </span>
                                  <span class="unit-price unit-price-rent">
                                      ${{ number_format($unit->rent_price, 0, ',', '.') }}<span style="font-size:11px;font-weight:500">/mes</span>
                                  </span>
                              </div>
                          @endif
                      </div>
                  @endif
 
                </div>
            </div>
        @endforeach
    </div>
 
</div>
 
<style>
/* ── Sección ──────────────────────────────────────────── */
.units-section .section-title {
    font-family: 'Sharp Grotesk', sans-serif;
    font-size: 22px;
    font-weight: 700;
    color: #1a1a1a;
    border-bottom: 2px solid #142743;
    padding-bottom: 10px;
}
 
/* ── Card ─────────────────────────────────────────────── */
.unit-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: rgba(0, 0, 0, 0.08) 0px 4px 16px;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    display: flex;
    flex-direction: column;
}
 
.unit-card:hover {
    transform: translateY(-4px);
    box-shadow: rgba(20, 39, 67, 0.18) 0px 8px 24px;
}
 
/* ── Header ───────────────────────────────────────────── */
.unit-card-header {
    background-color: #142743;
    padding: 14px 18px;
    color: #ffffff;
}
 
.unit-name {
    font-family: 'Sharp Grotesk', sans-serif;
    font-weight: 600;
    font-size: 15px;
    color: #ffffff;
}
 
.unit-number {
    font-size: 12px;
    color: rgba(255,255,255,0.65);
    margin-top: 3px;
    display: block;
}
 
.unit-status-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    white-space: nowrap;
}
 
.unit-status-available {
    background-color: #d1fae5;
    color: #065f46;
}
 
.unit-status-sold,
.unit-status-vendido {
    background-color: #fee2e2;
    color: #991b1b;
}
 
.unit-status-reserved,
.unit-status-reservado {
    background-color: #fef3c7;
    color: #92400e;
}
 
/* ── Body ─────────────────────────────────────────────── */
.unit-card-body {
    padding: 16px 18px;
    flex: 1;
}
 
.unit-features {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 12px;
}
 
.unit-feature-item {
    display: flex;
    align-items: center;
    gap: 8px;
    background-color: #f3f4f6;
    border-radius: 10px;
    padding: 8px 10px;
}
 
.unit-feature-icon {
    width: 30px;
    height: 30px;
    background-color: #142743;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
 
.unit-feature-icon i {
    color: #ffffff;
    font-size: 12px;
}
 
.unit-feature-label {
    display: block;
    font-size: 10px;
    color: #9ca3af;
    font-weight: 500;
    line-height: 1;
}
 
.unit-feature-value {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #142743;
    line-height: 1.3;
}
 
.unit-description {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.5;
    margin: 8px 0 0;
    border-top: 1px solid #f3f4f6;
    padding-top: 8px;
}
 
/* ── Footer ───────────────────────────────────────────── */
.unit-card-footer {
    background-color: #f9fafb;
    border-top: 1px solid #e5e7eb;
    padding: 12px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
 
.unit-price-label {
    font-size: 12px;
    color: #9ca3af;
    font-weight: 500;
}
 
.unit-price {
    font-family: 'Sharp Grotesk', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: #142743;
}

.unit-price-rent {
    color: #059669; /* verde para distinguirlo del precio de venta */
}
 
/* ── Responsivo ───────────────────────────────────────── */
@media (max-width: 576px) {
    .unit-features {
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .unit-price {
        font-size: 16px;
    }
}
</style>