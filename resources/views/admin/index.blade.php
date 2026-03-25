@extends('layouts.dashtw')
@section('firstscript')
<title>Dashboard - Grupo Housing</title>

{{-- map leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- Load Esri Leaflet from CDN -->
<script src="https://unpkg.com/esri-leaflet@3.0.10/dist/esri-leaflet.js"></script>
<!-- Load Esri Leaflet Geocoder from CDN -->
<link rel="stylesheet" href="https://unpkg.com/esri-leaflet-geocoder@3.1.4/dist/esri-leaflet-geocoder.css" crossorigin="" />
<script src="https://unpkg.com/esri-leaflet-geocoder@3.1.4/dist/esri-leaflet-geocoder.js" crossorigin=""></script>

@endsection

@section('content')
<main class="overflow-x-hidden overflow-y-auto">

    <div class="grid grid-cols-1 gap-4 p-4">
    
    {{-- Estadísticas --}}
    <div class="border rounded-lg p-4 shadow-sm bg-white">
        <p class="font-semibold text-gray-700 mb-3">
            <i class="fas fa-chart-bar text-gray-600 mr-1"></i> ESTADÍSTICAS PROPIEDADES
        </p>
        <div class="flex flex-wrap gap-2 mb-3">
            <div class="border px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white transition text-sm">
                Total Disponibles: <b>{{$totalavailableproperties}}</b>
            </div>
            <div class="border px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white transition text-sm">
                Total Activas: <b>{{$totalactivatedproperties}}</b>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach([
                ['icon' => 'fa-circle', 'label' => 'Casas', 'value' => $totalcasas],
                ['icon' => 'fa-circle', 'label' => 'Departamentos', 'value' => $totaldepartamentos],
                ['icon' => 'fa-circle', 'label' => 'Casas Comerciales', 'value' => $totalcasascomer],
                ['icon' => 'fa-circle', 'label' => 'Terrenos', 'value' => $totalterrenos],
                ['icon' => 'fa-circle', 'label' => 'Quintas', 'value' => $totalquintas],
                ['icon' => 'fa-circle', 'label' => 'Haciendas', 'value' => $totalhaciendas],
                ['icon' => 'fa-circle', 'label' => 'Locales Comerciales', 'value' => $totallocalcomer],
                ['icon' => 'fa-circle', 'label' => 'Oficinas', 'value' => $totaloficinas],
                ['icon' => 'fa-circle', 'label' => 'Suites', 'value' => $totalsuites],
            ] as $item)
            <div class="border px-2 py-1 rounded-lg hover:bg-gray-800 hover:text-white transition text-xs">
                <i class="fa-sharp fa-solid {{ $item['icon'] }} text-gray-400 mr-1"></i>
                {{ $item['label'] }}: <b>{{ $item['value'] }}</b>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Carousel --}}
    {{-- <div class="border rounded-lg shadow-sm bg-white overflow-hidden">
        <x-contact-queue-carousel />
        <x-modal-update-contact-date />
    </div> --}}

</div>

    @if(Auth::user()->id)
        <div class="grid grid-cols-1 mx-4 w-max-content">
            <a class="bg-green-700 p-2 rounded-md text-white" href="{{ route('update.valid.properties') }}">Actualizar propiedades validas</a>
        </div>
    @endif

    <div class="grid grid-cols-1 mx-4 my-2 w-auto border rounded py-4">
        <div>
            <p class="font-semibold mx-4 pb-1"><i class="fas fa-location text-gray-800"></i> UBICACIÓN DE PROPIEDADES</p>
            <p class="text-xs mx-4 pb-2">*Puede hacer clic sobre la ubicación de cada propiedad para más información</p>
            {{-- <div id="map" class="mx-4 rounded" style="height: 400px"></div> --}}
            <div id="map" style="height: 600px; z-index: 3"></div>
        </div>
    </div>
    <div class="mx-4 my-3 w-auto border rounded py-4">
        <p class="font-semibold mx-4"><i class="fas fa-user text-gray-800"></i> MI USUARIO</p>
        <div class="mx-4">
            @if (count($properties_at_week)>0)
            <p>Propiedades subidas por <b class="text-gray-600">{{Auth::user()->name}}</b> durante esta semana desde el <b class="text-gray-600">{{$now->startOfWeek()->format('d-M-y')}} </b> hasta el <b class="text-gray-600">{{$now->endOfWeek()->format('d-M-y')}}</b></p>
            <div class="grid grid-cols-3 my-2">
                @foreach ($properties_at_week as $prop_at_week)
                    <div class="border mx-1 p-1 rounded">
                        <label class="text-xs text-gray-500">{{$prop_at_week->created_at->format('d-M-y')}}</label>
                        <p>{{$prop_at_week->product_code}} - {{$prop_at_week->listing_title}}</p>
                    </div>
                @endforeach
            </div>
            @else
                <p class="text-red-600"><i class="fa-sharp fa-solid fa-circle-exclamation"></i> No hemos encontrado captaciones esta semana del usuario {{Auth::user()->name}}</p>
            @endif
        </div>
    </div>
    {{-- @if(Auth::user()->role == "administrator")
    <div class="mx-4 my-3 w-auto border rounded py-4">
        <p class="font-semibold mx-4">PROPIEDADES SUBIDAS EL DÍA DE HOY</p>
        <div class="mx-4">
            @if (count($properties_today)>0)
            <div class="grid grid-cols-1 my-2">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0">
                        <tr>
                            <th class="px-4 py-2">Código</th>
                            <th class="px-4 py-2">Detalle</th>
                            <th class="px-4 py-2">Plan</th>
                            <th class="px-4 py-2">Sector</th>
                            <th class="px-4 py-2">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($properties_today as $prop_today)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 text-xs">
                            <td class="px-4 py-4">{{$prop_today->product_code}}</td>
                            <td class="px-4 py-4 text-blue-400"><a href="{{route('home.tw.edit', $prop_today)}}">{{$prop_today->listing_title}}</a></td>
                            <td class="px-4 py-4">@if($prop_today->listing_type == 2) PAGO @elseif($prop_today->listing_type == 1) GRATIS @endif</td>
                            <td class="px-4 py-4">{{$prop_today->address}}</td>
                            <td class="px-4 py-4">{{$prop_today->created_at->format('d-M-y')}}</td>
                        </tr>    
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p class="text-red-600"><i class="fa-sharp fa-solid fa-circle-exclamation"></i> No hemos encontrado propiedades subidas el día de hoy</p>
            @endif
        </div>
    </div>
    @endif --}}

    <div class="mx-4 my-3 w-auto border rounded py-4">
        <p class="font-semibold mx-4"><i class="far fa-edit text-gray-800"></i> PROPIEDADES ACTUALIZADAS POR <b class="text-gray-800 font-semibold">{{Auth::user()->name}}</b> EL DÍA <b class="text-gray-800 font-semibold">{{substr(date(now()), 0, 10)}}</b></p>
        <div class="mx-4">
            @if (count($updated_listing)>0)
            <div class="grid grid-cols-1 my-2">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0">
                        <tr>
                            <th class="px-4 py-2">Código</th>
                            <th class="px-4 py-2">Campos Actualizados</th>
                            <th class="px-4 py-2">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($updated_listing as $update_lis)
                        @php
                            $listing = \App\Models\Listing::find($update_lis->listing_id);
                        @endphp
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 text-xs">
                            <td class="px-4 py-4"><a href="{{route('home.tw.edit', $listing)}}">{{$update_lis->property_code}}</a></td>
                            <td class="px-4 py-4">{{str_replace(",", ", ", $update_lis->value_change)}}</td>
                            <td class="px-4 py-4">{{date_format(new DateTime($update_lis->created_at), 'd-M-Y')}}</td>
                        </tr>    
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p class="text-red-600"><i class="fa-sharp fa-solid fa-circle-exclamation"></i> No hemos encontrado propiedades</p>
            @endif
        </div>
    </div>

    <div class="mx-4 my-3 w-auto border rounded py-4">
        <p class="font-semibold mx-4"><i class="fas fa-arrow-down text-gray-800"></i> PROPIEDADES QUE BAJARON DE PRECIO</p>
        <div class="mx-4">
            @if (count($properties_dropped)>0)
            <div class="grid grid-cols-1 my-2">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0">
                        <tr>
                            <th></th>
                            <th class="px-4 py-2">Código</th>
                            <th class="px-4 py-2">Comentario</th>
                            <th class="px-4 py-2">Precio Anterior</th>
                            <th class="px-4 py-2">Nuevo Precio</th>
                            <th class="px-4 py-2">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($properties_dropped as $pd)
                        @php
                            $listing = \App\Models\Listing::find($pd->listing_id);
                        @endphp
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 text-xs">
                            <td class="px-4 py-4">@if($pd->property_price_prev > $pd->property_price) <i class="fas fa-arrow-down text-green-600"></i> @elseif($pd->property_price_prev < $pd->property_price) <i class="fas fa-arrow-up text-red-600"></i> @else <i class="fas fa-horizontal-rule"></i> @endif </td>
                            <td class="px-4 py-4"><a href="{{route('home.tw.edit', $listing)}}">{{$pd->property_code}}</a></td>
                            <td class="px-4 py-4">@if($pd->comment == null || $pd->comment == "") <b>Sin información</b> @else {{$pd->comment}}@endif</td>
                            <td class="px-4 py-4">${{number_format($pd->property_price_prev)}}</td>
                            <td class="px-4 py-4">${{number_format($pd->property_price)}}</td>
                            <td class="px-4 py-4">{{date_format(new DateTime($pd->created_at), 'd-M-Y')}}</td>
                        </tr>    
                        @endforeach
                    </tbody>
                </table>
                <div class="flex float-right mt-3 text-blue-600">
                    <a href="{{route('admin.properties.change.price')}}">Ver todas</a>
                </div>
            </div>
            @else
                <p class="text-red-600"><i class="fa-sharp fa-solid fa-circle-exclamation"></i> No hemos encontrado propiedades</p>
            @endif
        </div>
    </div>
</main>
@endsection

@section('endscript')
<script>
    let map = L.map('map').setView([-2.9004975301629137, -79.00414343755442], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const apiKey = "AAPK6cd0390360a34c47abb6992f612c3a4eHDSN5oz15wvKsDnnOXQAT1xiCNYDtP4B8XRcytqys3UphqELHcSD_tlTbsijCbGz";

    const searchControl = L.esri.Geocoding.geosearch({
        position: "topright",
        placeholder: "Ingrese la dirección de la propiedad",
        useMapBounds: false,
        providers: [
            L.esri.Geocoding.arcgisOnlineProvider({
                apikey: apiKey,
                nearby: {
                lat: -33.8688,
                lng: 151.2093
                }
            })
        ]
    }).addTo(map);

    // --- Colores por listingtype (ajusta colores si quieres) ---
const colorsByType = {
    23: '#ff4d4d', // Casas
    24: '#4d79ff', // Departamentos
    25: '#39b54a', // Casas Comerciales
    26: '#ff9900', // Terrenos
    29: '#9933ff', // Quintas
    30: '#009999', // Haciendas
    32: '#ff66cc', // Locales Comerciales
    35: '#666666', // Oficinas
    36: '#000000', // Suites
    37: '#ffff00', // Edificio
    38: '#996600', // Colonial
    39: '#00ffff', // Hotel
    40: '#ff0066', // En Proyecto
    41: '#006600', // Fábrica
    42: '#660000', // Parqueadero
    43: '#999999', // Bodega
    44: '#333333', // No Aplica
    45: '#ff3300', // Naves Industriales
    46: '#0066ff'  // Penthouse
};

// --- Helpers para crear SVG data-url como icono ---
function createSvgDataUrl(color, size = 40) {
    // Un ícono tipo "pin" simple (puedes modificar path si quieres otro estilo)
    const svg = `
        <svg xmlns='http://www.w3.org/2000/svg' width='${size}' height='${size}' viewBox='0 0 24 24' >
            <path d='M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z'
                  fill='${color}' stroke='#ffffff' stroke-width='1.2'/>
            <circle cx='12' cy='9' r='2.5' fill='#fff'/>
        </svg>
    `;
    return "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(svg);
}

function createColoredIcon(color, size = 40) {
    const url = createSvgDataUrl(color, size);
    return L.divIcon({
        html: `<img src="${url}" style="width:${size}px;height:${size}px;display:block;">`,
        className: '', // evita estilos por defecto del divIcon
        iconSize: [size, size],
        iconAnchor: [Math.round(size/2), size], // ancla abajo-centro
        popupAnchor: [0, -Math.round(size/2)]
    });
}

// --- Añadir propiedades al mapa ---
let newarray = @json($properties_aux) || [];

for (let i = 0; i < newarray.length; i++) {
    const item = newarray[i];
    const lat = parseFloat(item.lat);
    const lng = parseFloat(item.lng);

    if (!lat || !lng) continue; // omitir si no hay coords válidas

    const listingType = parseInt(item.listingtype, 10);
    const color = colorsByType[listingType] || '#808080'; // gris por defecto
    const icon = createColoredIcon(color, 40);

    // imagen de la propiedad (fallback si no hay)
    const images = item.images ? item.images.split('|') : [];
    const imageUrl = images.length > 0
        ? `https://grupohousing.com/uploads/listing/600/${images[0]}`
        : 'https://grupohousing.com/img/logo-azul-grupo-housing.png';

    const marker = L.marker([lat, lng], { icon }).addTo(map);

    // Popup — ajusta contenido/links según tus rutas
    marker.bindPopup(`
        <div style="width:300px; border-radius:8px; overflow:hidden;
        ">
            
            <!-- Imagen principal -->
            <div style="width:100%; height:140px; overflow:hidden;">
                <img src="${imageUrl}" alt="Imagen de propiedad" 
                    style="width:100%; height:100%; object-fit:cover; display:block;">
            </div>

            <!-- Información -->
            <div style="background:#fff;">
                <div style="font-size:14px; color:#666; margin-bottom:4px;">
                    Código: <strong style="color:#000;">${item.product_code || ''}</strong>
                </div>
                <div style="font-size:16px; font-weight:bold; color:#333; margin-bottom:8px;">
                    ${item.listing_title || ''}
                </div>

                <!-- Acciones -->
                <div style="display:flex; justify-content:space-between; margin-top:8px;">
                    <a target="_blank" 
                    href="https://wa.me/?text=Ubicación:%20${lat},${lng}" 
                    style="flex:1; text-align:center; background:#25d366; color:#fff; padding:6px 0; text-decoration:none; font-size:13px; border-radius:4px; margin-right:4px;">
                    Compartir Ubicación
                    </a>
                    <a href="/propiedad/${item.id}" 
                    style="flex:1; text-align:center; background:#007bff; color:#fff; padding:6px 0; text-decoration:none; font-size:13px; border-radius:4px; margin-left:4px;">
                    Ver Propiedad
                    </a>
                </div>
            </div>
        </div>
    `);
}
</script>
@endsection