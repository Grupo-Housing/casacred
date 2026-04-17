@extends('layouts.web2')
@section('header')
    <title>{{ Str::ucfirst(Str::lower($listing->listing_title)) }}</title>
    @php
        $type = DB::table('listing_types')
            ->select('type_title')
            ->where('id', $listing->listingtype)
            ->first();

        $statusMap = [
            'en-venta'  => 'venta',
            'en venta'  => 'venta',
            'on sale'   => 'venta',
            'alquilar'  => 'alquiler',
            'proyectos' => 'proyectos',
        ];

        $status = $statusMap[strtolower($listing->listingtypestatus)] ?? '';
        
    @endphp
    <meta name="description" content="@isset($listing->meta_description){{ Str::ucfirst(Str::lower($listing->meta_description)) }} @else {{ mb_substr(trim(strip_tags($listing->listing_description)), 0, 150) }} @endif"/>

    <meta name="keywords" content="@isset($listing->keywords) {{ $listing->keywords }} @else Casas en venta en cuenca ecuador, Apartamentos en venta en cuenca ecuador, terrenos en venta en cuenca ecuador, lotes en venta en cuenca ecuador, {{ Str::lower($type->type_title) }} en {{ $status }} en {{ strtolower($listing->city . ' ' . $listing->state) }} @endisset">

    <meta name="robots" content="index,follow,snippet">
    <link rel="canonical" href="{{ Request::url() }}">

    <meta property="og:url" content="{{ route('web.detail', $listing->slug) }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $listing->listing_title }}" />
    <meta property="og:description"
        content="{{ mb_substr(trim(strip_tags($listing->listing_description)), 0, 150) }}" />

    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://polyfill.io/v2/polyfill.min.js?features=IntersectionObserver"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @php $firstImg = array_filter(explode("|", $listing->images)) @endphp
    <meta property="og:image" content="{{ url('uploads/listing/600', $firstImg[0] ?? '') }}" />

    <style>
        .images-mobile {
            display: none !important
        }

        .img-banner:hover {
            filter: brightness(80%) !important;
        }

        @media screen and (max-width: 580px) {
            .form {
                padding-left: 0px !important;
                padding-right: 0px !important;
            }

            .images-desktop {
                display: none !important
            }

            .images-mobile {
                display: block !important
            }

            .btn-modal-images {
                display: none !important
            }
        }

        .custom-text {
            font-size: 1.5rem;
            /* Tamaño similar al de h4 */
            font-style: normal;
            /* Eliminar la negrita */
            white-space: nowrap;
            color: #242B40;
        }

        .card-asesor {
            box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
        }

        .icon-phone,
        .fa-whatsapp,
        .fa-envelope,
        .img-profile {
            transition: transform 0.3s ease;
        }

        .icon-phone:hover {
            transform: scale(1.5);
        }

        .fa-whatsapp:hover {
            transform: scale(1.5);
        }

        .fa-envelope:hover {
            transform: scale(1.5);
        }

        .card-asesor:hover {
            background-color: #242B40;
            color: #ffffff
        }

        .card-asesor:hover .img-profile {
            transform: scale(1.1);
        }

        #calcularcredmobile {
            z-index: 4;
            position: fixed;
            bottom: 0px;
            left: 0px;
        }

        .bg-orange {
            background-color: #dc3545;
        }

        .tiktok-embed {
            width: 100% !important
        }

        .filter-image:hover {
            filter: brightness(70%)
        }

        .custom-container {
            width: 100%;
            max-width: 1500px;
            /* Ajusta este valor según tus necesidades */
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        .carousel-image {
            height: 600px;
        }

        .thumbnail-container {
            width: calc(100% - 80px);
            white-space: nowrap;
            overflow-x: auto;
            scroll-behavior: smooth;
            position: relative;
        }

        .thumbnail-standard {
            width: 100px;
            height: 70px;
            object-fit: cover;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .thumbnail-standard:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .thumbnail-wrapper {
            position: relative;
            display: inline-block;
        }

        .thumbnail-icon-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .thumbnail-wrapper:hover .thumbnail-icon-overlay {
            opacity: 1;
        }

        @media (min-width: 768px) {
            .thumbnail-standard {
                width: 180px;
                height: 150px;
            }
            .section-interest{
                display: none;
            }
            #map, #absolutemap{
                height: 500px !important;
            }
            .form-contact{
                padding: 0px 30px !important;
            }
        }

        @media (max-width: 768px) {
            .carousel-image {
                height: 400px;
                object-fit: contain !important;
            }
            .telf{
                display: none !important;
            }
            .whatsapp-group{
                display: none !important;
            }
            .section-interest{
                display: block;
            }
            #map, #absolutemap{
                height: 450px !important
            }
            .form-contact{
                padding: 0px 0px !important;
            }
            
            .feature-icons {
                justify-content: center !important;
            }

            .parent {
                flex: 0 0 calc(33.33% - 20px); /* 3 por fila, restando gap */
                max-width: calc(33.33% - 20px);
            }

            .feature-box {
                width: 70px;   /* más pequeños en mobile */
                height: 70px;
            }

            .feature-box img {
                width: 25px;
                height: 25px;
            }

            .parent p {
                font-size: 12px; /* texto un poco más chico en mobile */
            }
        }

        .scroll-button {
            background-color: rgba(0, 0, 0, 0.5);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .scroll-button.left {
            left: -20px;
            background-color: #242B40;
        }

        .scroll-button.right {
            right: -20px;
            background-color: #242B40;
        }

        .scroll-button i {
            font-size: 1.5rem;
            color: #ffffff;
        }



        .text-center.text-white.py-3.shadow {
            background-color: #242B40;
            border-radius: 25px 25px 0 0;
        }

        @media (max-width: 767.98px) {
            .text-center.text-white.py-3.shadow .row .col-12 {
                margin-bottom: 1rem;
            }

            .text-center.text-white.py-3.shadow .row .col-12.mb-2.mb-md-0 {
                margin-bottom: 0;
            }
        }

        .divider-desktop {
            border-left: 2px solid white;
            height: 50px;
            margin: 0 10px;
        }

        .divider-mobile {
            border-top: 2px solid white;
            width: 75%;
        }
        @keyframes slideInFromRight {
            0% {
                transform: translateX(50%);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .slide-in {
            animation: slideInFromRight 500ms ease-out;
        }
        .carousel-control-next,
        .carousel-control-prev /*, .carousel-indicators */ {
            filter: invert(100%);
        }
        /* Estilo predeterminado para la imagen */
        .zoom-img {
            transition: transform 0.3s; /* Agrega una transición suave al efecto de zoom */
        }

        .advisor-card-desktop{
            display: block;
        }

        .advisor-card-mobile{
            display: none !important;
        }

        @media (max-width: 768px) {
            .advisor-card-desktop{
                display: none !important;
            }
            .advisor-card-mobile{
                display: flex !important;
            }
            .advisor-card-mobile .advisor-info p{
                font-size: 17px;
            }
            .custom-container-property{
                padding: 120px 2% 0 2% !important;
            }
        }

        .advisor-card{
            display: flex;
            gap: 20px;
            margin-top: 40px;
        }
        .advisor-image img{
            border-radius: 10px;
        }
        .advisor-info{
            display: flex;
            align-items: center;
        }
        .advisor-info h3{
            font-weight: 700;
        }
        .advisor-info p{
            font-size: 20px;
        }
        .advisor-code{
            background-color: #142743;
            color: #ffffff;
            border-radius: 50px;
            padding: 10px 20px;
        }
        .custom-container-property{
            padding: 150px 8% 0 8%;
        }

        /* Imagen principal */
.main-carousel-img {
    border-radius: 15px;
    object-fit: cover;
    height: 700px;
    width: 100%;
}

/* Miniaturas */
.thumb-wrapper {
    overflow: hidden;
    border-radius: 12px;
    cursor: pointer;
    flex: 1;
    max-height: 160px;
}

.thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.6;
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.thumb-img:hover {
    opacity: 1;
    transform: scale(1.1); /* Efecto dentro del contenedor */
}

/* Botón ver todas */
.btn-overlay {
    position: absolute;
    bottom: 20px;
    right: 30px;
    background: #142743;
    color: white;
    border-radius: 20px;
    padding: 6px 15px;
    font-size: 14px;
}

.btn-overlay:hover{
    background-color: #142743;
    font-weight: 600;
}

/* Controles personalizados (flechas) */
.custom-control {
    top: 50%;
    transform: translateY(-50%);
    width: 45px;
    height: 45px;
    background-color: #142743;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.custom-control:hover {
    opacity: 1;
}

.custom-icon {
    color: #ffffff;
    background-color: #142743; /* para que las flechas sean blancas */
}

/* Modal estilo */
.modal-gallery {
    background: transparent; /* más amigable, tipo dark */
    border-radius: 10px;
    border: none;
}

.modal-img {
    border-radius: 10px;
    max-height: 80vh;
    object-fit: contain;
    margin: auto;
    display: block;
}

.close-modal-btn {
    color: #000000;
    font-size: 2rem;
    opacity: 0.8;
}

.close-modal-btn:hover {
    opacity: 1;
}

/* Responsividad */
@media (max-width: 992px) {
    .main-carousel-img {
        height: 350px;
    }

    .thumb-wrapper {
        max-height: 90px;
    }
    .property-title-h1{
        font-size: 1.5rem !important;   
    }
}

@media (max-width: 576px) {
    .main-carousel-img {
        height: 250px;
    }

    .thumb-wrapper {
        max-height: 70px;
    }

    .btn-overlay {
        font-size: 12px;
        padding: 4px 10px;
    }
    .container-price-and-code{
        display: inline !important;
    }
}

.image-counter {
    position: absolute;
    top: 25px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.7);
    color: #fff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 15px;
    font-weight: 600;
    z-index: 1055; /* por encima del modal y las imágenes */
    pointer-events: none; /* no bloquea clics */
}

/* Título */
.property-title-h1 {
    font-family: 'Sharp Grotesk', sans-serif;
    font-weight: 600;
    font-size: 2rem;
    color: #1a1a1a;
    margin-bottom: 10px;
}

/* Dirección */
.property-location-principal {
    font-size: 18px;
    color: #555;
}

/* Precio */
.price-container {
    background-color: #142743;
    color: #fff;
    font-weight: 600;
    font-size: 20px;
    padding: 10px 20px;
    border-radius: 30px;
    display: inline-block;
    margin: 15px 0;
}

.container-price-and-code{
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.container-price-and-code .price-container{
    background-color: #142743;
    color: #fff;
    padding: 10px 20px;
    border-radius: 30px;
    font-weight: 600;
}

.code-listing{
    border: 1px solid #142743;
    color: #142743;
    padding: 10px 20px;
    border-radius: 30px;
    font-weight: 600;
}

/* Sección de características */
.feature-icons {
    gap: 20px;
}
.feature-box {
    background-color: #f3f4f6;
    border-radius: 50%;
    width: 90px;
    height: 90px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease;
}
.feature-box img {
    width: 35px;
    height: 35px;
    margin-bottom: 5px;
}
.feature-box p {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}
.feature-box:hover {
    transform: scale(1.05);
}

/* Descripción */
.section-title {
    font-family: 'Sharp Grotesk', sans-serif;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 15px;
}
.property-description {
    font-size: 15px;
    color: #444;
    text-align: justify;
    line-height: 1.6;
}
.parent {
    display: flex;
    flex-direction: column; /* para que la imagen quede arriba y el texto abajo */
    align-items: center; /* centra horizontalmente */
    text-align: center; /* opcional para el texto */
}

.play-intro-overlay {
    cursor: pointer;
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 10;
}

.play-intro-icon {
    width: 100px;
    height: 100px;
    animation: rotate-smooth 8s linear infinite;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

@keyframes rotate-smooth {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Para hacer el ícono clickeable si es necesario */
.play-intro-overlay.clickable {
    pointer-events: auto;
    cursor: pointer;
}

.video-container {
  position: relative;
  width: 100%;
  padding-bottom: 56.25%; /* relación 16:9 */
  height: 0;
  overflow: hidden;
}

.video-container iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.modal-gallery {
            background: #000;
        }
        
        .video-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* Ratio 16:9 por defecto */
            overflow: hidden;
        }
        
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        /* Estilos específicos para móviles */
        @media (max-width: 768px) {
            .modal-dialog.modal-xl {
                max-width: 95%;
                margin: 10px auto;
            }
            
            .modal-content.modal-gallery {
                height: 90vh; /* Usar casi toda la altura de la pantalla */
            }
            
            .modal-body {
                height: calc(90vh - 60px); /* Restar altura del header */
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 10px;
            }
            
            /* Para videos verticales (shorts) en móvil */
            .video-container {
                padding-bottom: 177.78%; /* Ratio 9:16 para shorts */
                max-height: 100%;
                max-width: 56.25vh; /* Mantener proporción */
            }
            
            /* Si el contenedor es muy alto, limitamos y centramos */
            .video-container {
                height: auto;
                max-height: calc(90vh - 80px);
            }
            .play-intro-icon{
                width: 50px;
                height: 50px;
            }
        }
        
        /* Para pantallas muy pequeñas */
        @media (max-width: 480px) {
            .modal-dialog.modal-xl {
                max-width: 98%;
                margin: 5px auto;
            }
            
            .modal-content.modal-gallery {
                height: 95vh;
            }
            
            .modal-body {
                height: calc(95vh - 50px);
                padding: 5px;
            }
        }
        
        /* Estilos para el botón de cerrar */
        .close-modal-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            z-index: 1050;
            color: white;
            font-size: 2rem;
            background: rgba(0,0,0,0.5);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .close-modal-btn:hover {
            opacity: 1;
            color: white;
        }
        
        /* Clase adicional para forzar video vertical en móvil */
        .video-container.vertical-mobile {
            padding-bottom: 177.78% !important;
        }
        
        @media (min-width: 769px) {
            .video-container.vertical-mobile {
                padding-bottom: 56.25% !important; /* Volver a 16:9 en desktop */
            }
        }
    </style>
@endsection

@php
    $images = array_filter(explode('|', $listing->images));
    $filexists = false;
    if (file_exists(public_path() . '/uploads/listing/thumb/600/' . strtok($listing->images, '|'))) {
        $filexists = true;
    }
    $listingtype = DB::table('listing_types')
        ->where('id', $listing->listingtype)
        ->first();
    $isTerrain = $listingtype && str_contains(strtolower($listingtype->type_title), 'terreno');
    $isProject = $listing->listingtype == 41;
@endphp
{{-- @php
    $images = array_filter(explode('|', $listing->images));
    $filexists = false;
    if (file_exists(public_path() . '/uploads/listing/thumb/600/' . strtok($listing->images, '|'))) {
        $filexists = true;
    }
    $listingtype = DB::table('listing_types')
        ->where('id', $listing->listingtype)
        ->first();
@endphp --}}
@section('content')
    <div class="custom-container-property">

        <div class="row">
            <!-- Carrusel principal -->
            <div class="col-lg-9 col-md-12 position-relative mb-3">
                <div id="carouselImages" class="carousel slide" data-ride="carousel" style="position: relative;">
                    <div class="carousel-inner">
                        @foreach (explode('|', $listing->images) as $image)
                            <div class="carousel-item @if ($loop->first) active @endif">
                                <img src="{{ $listing->resolveImageUrl($image, 'thumb') }}"
                                    class="d-block w-100 main-carousel-img">
                            </div>
                        @endforeach
                    </div>

                    @if($listing->video && !Str::endsWith($listing->video, '.mp4'))
                        <div class="play-intro-overlay" data-toggle="modal" data-target="#videoModalProperty">
                            <img class="play-intro-icon" src="{{ asset('img/icono-reproducir-video-propiedad.webp') }}" alt="Icono de Reproducir Video" title="Icono de Reproducir Video">
                        </div>
                    @endif

                    <a class="carousel-control-prev custom-control" href="#carouselImages" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon custom-icon" aria-hidden="true"></span>
                    </a>
                    <a class="carousel-control-next custom-control" href="#carouselImages" role="button" data-slide="next">
                        <span class="carousel-control-next-icon custom-icon" aria-hidden="true"></span>
                    </a>
                </div>

                <button class="btn-overlay" data-toggle="modal" data-target="#allPhotosModal">
                    Ver todas las fotos
                </button>
            </div>

            <!-- Miniaturas -->
            <div class="col-lg-3 col-md-12 d-flex flex-lg-column flex-row justify-content-between">
                @foreach (explode('|', $listing->images) as $image)
                    @if ($loop->index < 4)
                        <div class="thumb-wrapper mb-3 mr-2">
                            <img src="{{ $listing->resolveImageUrl($image, 'thumb_600') }}"
                                class="img-fluid thumb-img"
                                data-target="#carouselImages"
                                data-slide-to="{{ $loop->index }}">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        
        <!-- Modal para todas las fotos -->
        <div class="modal fade" id="allPhotosModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
              <div class="modal-content modal-gallery">
                <div class="modal-header border-0">
                  <button type="button" class="close close-modal-btn" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body position-relative">
                    <!-- Contador -->
                    <div id="imageCounter" class="image-counter">1 / {{ count(explode('|', $listing->images)) }}</div>
                
                    <div id="modalCarousel" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            @foreach (explode('|', $listing->images) as $image)
                              <div class="carousel-item @if ($loop->first) active @endif">
                                <img 
                                    loading="lazy" 
                                    src="{{ $listing->resolveImageUrl($image) }}" 
                                    class="d-block w-100 modal-img">
                              </div>
                            @endforeach
                        </div>
                
                        <!-- Controles -->
                        <a class="carousel-control-prev custom-control" href="#modalCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon custom-icon"></span>
                        </a>
                        <a class="carousel-control-next custom-control" href="#modalCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon custom-icon"></span>
                        </a>
                    </div>
                </div>                
              </div>
            </div>
        </div>

        @if($listing->video && !Str::endsWith($listing->video, '.mp4'))
            <div class="modal fade" id="videoModalProperty" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content modal-gallery">
                        <div class="modal-header border-0">
                            <button type="button" class="close close-modal-btn" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body position-relative">
                            <div class="video-container vertical-mobile">
                                <iframe
                                    data-src="https://www.youtube.com/embed/{{$listing->video}}"
                                    src=""
                                    title="Video de Propiedad"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>             
                    </div>
                </div>
            </div>
        @endif
        
    </div>
    
    <div class="custom-container mt-5">
        <div class="row">
            <div class="col-md-7">
                
                <!-- Título -->
                <h1 class="property-title-h1">
                    {{ Str::title($listing->listing_title) }}
                </h1>

                <!-- Dirección -->
                <div class="d-flex align-items-center" style="gap: 8px">
                    <i class="fa-solid fa-location-dot me-2 text-muted fa-2x"></i>
                    <div class="property-location-principal">
                        {{ ucfirst(strtolower($listing->sector ?? $listing->address)) }}, {{ $listing->city }}, {{ $listing->state }}
                    </div>
                </div>

                <!-- Precio -->
                <div class="container-price-and-code">
                    <div class="price-container">
                        <span>
                            @if($listing->customized_price != null)
                                Precio: {{ $listing->customized_price }}
                            @else
                                Precio: ${{ number_format($listing->property_price, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>
                    <div class="code-listing">
                        <span>COD: {{ $listing->product_code }}</span>
                    </div>
                </div>

                <!-- Íconos de características -->
                <div class="d-flex justify-content-between flex-wrap mt-4 feature-icons">
                    @if ($isProject)
                        @isset($units)
                        <div class="parent">
                            <div class="feature-box">
                                <img loading="lazy" src="{{ asset('img/dormitorios.png') }}" alt="Unidades">
                            </div>
                            <p class="mt-2">{{ count($units) }} {{ count($units) == 1 ? 'Unidad' : 'Unidades' }}</p>
                        </div>
                        @endisset
                    @elseif (!$isTerrain)
                        @if ($listing->bedroom > 0)
                            <div class="parent">
                                <div class="feature-box">
                                    <img loading="lazy" src="{{ asset('img/dormitorios.png') }}" alt="Habitaciones">
                                </div>
                                <p class="mt-2">{{ $listing->bedroom }} Habitaciones</p>
                            </div>
                        @endif
                        @if ($listing->bathroom > 0)
                            <div class="parent">
                                <div class="feature-box">
                                    <img loading="lazy" src="{{ asset('img/banio.png') }}" alt="Baños">
                                </div>
                                <p class="mt-2">{{ $listing->bathroom }} {{ $listing->bathroom > 1 ? 'Baños' : 'Baño' }}</p>
                            </div>
                        @endif
                        @if ($listing->garage > 0)
                            <div class="parent">
                                <div class="feature-box">
                                    <img loading="lazy" src="{{ asset('img/estacionamiento.png') }}" alt="Garajes">
                                </div>
                                <p class="mt-2">{{ $listing->garage }} {{ $listing->garage > 1 ? 'Garajes' : 'Garaje' }}</p>
                            </div>
                        @endif
                    @endif
                    @if(isset($listing->land_area) && $listing->land_area != 0)
                        <div class="parent">
                            <div class="feature-box">
                                <img loading="lazy" src="{{ asset('img/area.png') }}" alt="Terreno">
                            </div>
                            <p class="mt-2">{{ $listing->land_area }} m² Terreno</p>
                        </div>
                    @endif
                    @if(isset($listing->construction_area) && $listing->construction_area != 0)
                        <div class="parent">
                            <div class="feature-box">
                                <img loading="lazy" src="{{ asset('img/area.png') }}" alt="Casa">
                            </div>
                            <p class="mt-2">{{ $listing->construction_area }} m² Construcción</p>
                        </div>
                    @endif
                    @if ($isTerrain && isset($listing->Front) && $listing->Front > 0)
                        <div class="parent">
                            <div class="feature-box">
                                <img loading="lazy" src="{{ asset('img/area.png') }}" alt="Frente">
                            </div>
                            <p class="mt-2">Frente: {{ $listing->Front }} m²</p>
                        </div>
                    @endif
                    @if ($isTerrain && isset($listing->Fund) && $listing->Fund > 0)
                        <div class="parent">
                            <div class="feature-box">
                                <img loading="lazy" src="{{ asset('img/area.png') }}" alt="Fondo">
                            </div>
                            <p class="mt-2">Fondo: {{ $listing->Fund }} m²</p>
                        </div>
                    @endif
                </div>

                <!-- Descripción -->
                <div class="mt-5">
                    <h2 class="section-title">Acerca de esta propiedad</h2>
                    <p class="property-description">
                        {{ $listing->listing_description }}
                    </p>
                </div>

                <!-- Años de antiguedad -->
                @if($listing->listyears > 0)
                    <div>
                        <p>
                            <strong>Años de antigüedad:</strong>
                            {{ $listing->listyears . ' ' . ($listing->listyears > 1 ? 'años' : 'año') }}
                        </p>
                    </div>
                @endif

                @if($listing->is_dual_operation)
                    <div class="row mt-3 ml-1">
                        <div class="w-auto rounded py-1 font-weight-bold" style="background-color: #242B40; color: #ffffff">
                            <i class="fas fa-info-circle"></i>
                            @if($listing->listingtypestatus == "en-venta")
                                <span>Esta propiedad también está en renta</span>
                            @else
                                <span>Esta propiedad también está a la venta</span>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-around flex-wrap mt-4">

                </div>

            @isset($units)
                <div class="mb-4">
                    @if(count($units)>0)
                        <x-units-web :units="$units"></x-units-web>
                    @endif
                </div>
            @endisset

            @if (is_array(json_decode($listing->heading_details)))
                <div style="border: none; background-color: transparent;" class="card my-4">
                    <div class="card-body" style="margin: -16px">
                        <h2 class="card-title h6 pb-2"
                            style="font-size: 23px; font-family: 'Sharp Grotesk'; font-weight: 500; border-bottom: 1px solid #B0BEC5;">
                            <i class="fas fa-home" style="font-size: 28px; color: #242B40; margin-right: 10px;"></i>
                            Detalles
                        </h2>
                        @foreach (json_decode($listing->heading_details) as $dets)
                            <div class="row" style="padding-left: 7px">
                                <?php unset($dets[0]);
                                $printControl = 0; ?>
                                @for ($i = 1; $i < count($dets); $i++)
                                    @if ($printControl == 0)
                                        <?php $printControl = 1; ?>
                                        <div class="col-lg-3 col-md-4 col-6 p-1">
                                            <span class="text-muted small"
                                                style="font-family: 'Sharp Grotesk'; font-weight: 300;">
                                                @foreach ($details as $detail)
                                                    @if ($detail->id == $dets[$i])
                                                        {{ $detail->charac_titile }} @if ($detail->id == $dets[$i] && $detail->id == 86)
                                                            <span style="background-color: #242B40"
                                                                class="text-white px-2">
                                                                {{ $dets[$i + 1] }}</span>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </span>
                                        </div>
                                    @else
                                        <?php $printControl = 0; ?>
                                    @endif
                                @endfor
                                {{-- @endforeach     --}}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @if (count(array_filter(explode(',', $listing->listinggeneralcharacteristics))) > 0)
                <div style="border: none; background-color: transparent;" class="card my-4">
                    <div class="card-body" style="margin: -16px">
                        <h2 class="card-title h6 pb-2"
                            style="font-size: 23px; font-family: 'Sharp Grotesk'; font-weight: 500; border-bottom: 1px solid #B0BEC5;">
                            <i class="fas fa-home" style="font-size: 28px; color: #242B40; margin-right: 10px;"></i>
                            Características
                            Generales
                        </h2>
                        <div class="row" style="padding-left: 7px">
                            @foreach (array_filter(explode(',', $listing->listinggeneralcharacteristics)) as $lgc)
                                <div class="col-lg-3 col-md-4 col-6 p-1">
                                    {{-- <i class="fas fa-check px-2 text-muted"></i> --}}
                                    <span class="text-muted small"
                                        style="font-family: 'Sharp Grotesk'; font-weight: 300;">
                                        @foreach ($generalcharacteristics as $gc)
                                            @if ($gc->id == $lgc)
                                                {{ $gc->title }}
                                                @endif @if ($gc->id == $lgc && $lgc == 8 && $listing->num_pisos > 0)
                                                    <b class="text-white px-1"
                                                        style="background-color: #242B40">{{ $listing->num_pisos }}</b>
                                                    @endif @if ($gc->id == $lgc && $lgc == 7 && $listing->niv_constr > 0)
                                                        <b class="text-white px-1" style="background-color: #242B40">
                                                            {{ $listing->niv_constr }}</b>
                                                        @endif @if ($gc->id == $lgc && $lgc == 15 && $listing->pisos_constr > 0)
                                                            <b class="text-white px-1" style="background-color: #242B40">
                                                                {{ $listing->pisos_constr }}</b>
                                                        @endif
                                                    @endforeach
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            @if (count(array_filter(explode(',', $listing->listingenvironments))) > 0)
                <div style="border: none; background-color: transparent;" class="card my-4">
                    <div class="card-body" style="margin: -16px">
                        <h2 class="card-title h6 pb-2"
                            style="font-size: 23px; font-family: 'Sharp Grotesk'; font-weight: 500; border-bottom: 1px solid #B0BEC5;">
                            <i class="fas fa-home" style="font-size: 28px; color: #242B40; margin-right: 10px;"></i>
                            Ambientes
                        </h2>
                        <div class="row" style="padding-left: 7px">
                            @foreach (array_filter(explode(',', $listing->listingenvironments)) as $lenv)
                                <div class="col-lg-3 col-md-4 col-6 p-1">
                                    {{-- <i class="fas fa-check px-2 text-muted"></i> --}}
                                    <span class="text-muted small"
                                        style="font-family: 'Sharp Grotesk'; font-weight: 300;">
                                        @foreach ($environments as $environment)
                                            @if ($environment->id == $lenv)
                                                {{ $environment->title }}
                                            @endif
                                        @endforeach
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            @if ($listing->status)
                <div class="container">
                    <p class="text-center" style="font-weight: 400; font-size:20px">Compartir</p>
                    <div class="d-flex justify-content-center">
                        <p title="Compartir en Facebook" style="cursor: pointer" id="shareToFacebook"><i
                                class="fab fa-facebook" style="color: #0165E1;font-size:30px"></i></p>
                        {{-- <p title="Compartir en Twitter" id="shareToTwitter" style="cursor: pointer"><i class="fab fa-twitter ml-3" style="color: #1DA1F2;font-size:30px"></i></p> --}}
                        <p title="Compartir por WhatsApp" id="shareToWpp" style="cursor: pointer"><i
                                class="fab fa-whatsapp ml-3" style="color: #25D366;font-size:30px"></i></p>
                    </div>
                </div>
            @endif

            <div class="position-relative">
                <div id="map" style="height: 400px;" class="my-3"></div>
                <div id="absolutemap" class="position-absolute d-flex justify-content-center align-items-center px-2" style="height: 400px; width: 100%; top: 0px; right: 0px; z-index: 1000; background-color: #6464643d; backdrop-filter: blur(4px)">
                    <div id="buttonOpenForm" class="bg-white rounded p-4 text-center shadow">
                        <p style="font-weight: 600">¿Deseas visitar esta propiedad?</p>
                        <button class="btn text-white rounded-pill" style="background-color: #242B40" onclick="openFormAgendarCita()">Agenda tu cita aquí</button>
                    </div>
                    <div id="formAgendarCita" class="p-4 bg-white rounded d-none slide-in">
                        <div class="d-flex justify-content-end">
                           <span style="font-size: small; cursor: pointer;" onclick="openFormAgendarCita()">Cancelar</span>
                        </div>
                        <p style="font-weight: 500">Ingresa tu información y agendaremos tu cita</p>
                        <form action="{{ route('web.sendlead') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6 col-6">
                                    <div class="form-group">
                                        <label for="name">Nombre:</label>
                                        <input type="text" class="form-control" name="fname" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-6">
                                    <div class="form-group">
                                        <label for="lastname">Apellido:</label>
                                        <input type="text" class="form-control" name="flastname" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12">
                                    <div class="form-group mt-2">
                                        <label for="phone">Teléfono:</label>
                                        <input type="number" class="form-control" name="tlf" required>
                                    </div>
                                </div>
                                <div class="col-6 col-md-12">
                                    <div class="form-group mt-2">
                                        <label for="email">Email:</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="message">Mensaje:</label>
                                <textarea name="message" rows="2" class="form-control" required>Deseo agendar una cita para esta propiedad</textarea>
                            </div>
                            <input type="hidden" name="interest" value="{{ $listing->product_code }}">
                            <div class="d-flex justify-content-center mt-4">
                                <button class="btn btn-primary rounded-pill">Agendar mi cita</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if ($user->profile_photo_path != null && $user->status == 1)
                <div class="advisor-card advisor-card-desktop">
                    <div class="advisor-image">
                        <img width="150px" height="200px" style="object-fit: cover" src="{{ asset('uploads/profiles/' . $user->profile_photo_path) }}" alt="Imagen de Asesora">
                    </div>
                    <div class="advisor-info">
                        <div>
                            @if ($user->role == 'ASESOR')
                                <h3>Asesora</h3>
                            @else
                                <h3>Gestora</h3>
                            @endif
                            <p>{{ $user->name }}</p>
                            <div class="advisor-code text-center">
                                <span>
                                    Cod: {{ $user->code != null ? $user->code : 'GH-1234'}}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <div class="col-md-5 mb-5">
            <div class="sticky-top form-contact" style="top: 120px;">

                <div class="text-center text-white py-3 shadow"
                    style="background-color: #242B40; border-radius: 25px 25px 0 0;">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-12 col-md-auto mb-2 mb-md-0">
                            <span class="fw-bold"
                                style="font-size: 40px; line-height: 50px; font-family: 'Sharp Grotesk'; font-weight: 500;">
                                @if($listing->customized_price != null)
                                    {{ $listing->customized_price }}
                                @else
                                    ${{ number_format($listing->property_price, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                        @if ($listing->aliquot && $listing->aliquot > 0)
                            <div class="col-12 d-md-none my-2 divider-mobile"></div> <!-- Spacer for mobile -->
                            <div class="col-auto d-none d-md-block divider-desktop"></div> <!-- Divider for desktop -->
                            <div class="col-12 col-md-auto">
                                <div class="row">
                                    <span class="fw-bold"
                                        style="font-size: 20px; font-family: 'Sharp Grotesk'; font-weight: 500;">Alícuota</span>
                                </div>
                                <div class="row">
                                    <span class="fw-bold"
                                        style="font-size: 20px; font-family: 'Sharp Grotesk'; font-weight: 500;">
                                        ${{ number_format($listing->aliquot, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>


                <div class="bg-white pt-4 pb-5 shadow px-5" style="border-radius: 0 0 25px 25px;">
                    <p class="text-center" style="font-size: x-large; font-weight: 600; ">¿Te interesa esta propiedad?
                    </p>
                    <p class="text-center">Proporciónanos tus datos y te contactaremos</p>
                    <div class="d-flex justify-content-center">
                        <form action="{{ route('web.sendlead') }}" method="POST" id="formDetailProp">

                            @csrf

                            <div class="form-group">
                                <input type="text" id="fname" name="fname" placeholder="Nombre" class="w-100"
                                    style="border: none; border-bottom: 1px solid #242B40" required>
                                <input type="text" id="flastname" name="flastname" placeholder="Apellido"
                                    class="w-100 mt-4" style="border: none; border-bottom: 1px solid #242B40" required>
                                <input type="hidden" id="interestDetail" name="interest">
                            </div>

                            <div class="form-group mt-4 w-100">
                                <input type="number" id="tlf" name="tlf" placeholder="Teléfono"
                                    class="w-100" style="border: none; border-bottom: 1px solid #242B40" required>
                            </div>

                            <div class="form-group mt-4 w-100">
                                <input type="email" id="email" name="email" placeholder="Correo electrónico"
                                    class="w-100" style="border: none; border-bottom: 1px solid #242B40" required>
                            </div>

                            <div class="form-group mt-4 w-100">
                                <textarea name="message" id="message" rows="3" placeholder="Mensaje" class="w-100"
                                    style="border: none; border-bottom: 1px solid #242B40" required>Hola, me interesa este inmueble y quiero que me contacten. Gracias</textarea>
                            </div>

                            <input type="hidden" name="interest" value="{{ $listing->product_code }}">

                            <div class="form-group mt-4 w-100 d-flex justify-content-center">
                                <button id="btnEnviar" type="button" class="btn text-white rounded-pill"
                                    style="background-color: #242B40;"
                                    onclick="sendFormDetail({{ $listing->product_code }}, event)">ENVIAR</button>
                            </div>

                            <p class="text-center mt-4" style="font-size: x-large; font-weight: 600">Nuestros datos de
                                contacto</p>

                            <div class="d-flex gap-3 ms-4">
                                <div class="rounded-circle d-flex justify-content-center align-items-center"
                                    style="border: 1px solid #242b40a2; width: 30px; height: 30px">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <a style="text-decoration: none"
                                    onclick="gtag_report_conversion('tel:+593967867998')"
                                    href="tel:{{ $listing->listingtypestatus == 'en-venta' ? '+593967867998' : '+593967867998' }}"
                                    class="mt-1 ml-2 text-dark">{{ $listing->listingtypestatus == 'en-venta' ? '096-786-7998' : '096-786-7998' }}</a>
                            </div>

                            <div class="d-flex gap-3 ms-4 mt-2">
                                <div class="rounded-circle d-flex justify-content-center align-items-center"
                                    style="border: 1px solid #242b40a2; width: 30px; height: 30px">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </div>
                                <a style="text-decoration: none"
                                    onclick="gtag_report_conversion_whatsapp('https://api.whatsapp.com/send?phone=593967867998&text=Hola%20*Grupo%20Housing*,%20deseo%20consultar%20por%20esta%20propiedad:%20*{{ $listing->product_code }}*')"
                                    href="https://api.whatsapp.com/send?phone={{ $listing->listingtypestatus == 'en-venta' ? '593967867998' : '593967867998' }}&text=Hola%20*Grupo%20Housing*,%20deseo%20consultar%20por%20esta%20propiedad:%20*{{ $listing->product_code }}*"
                                    class="mt-1 ml-2 text-dark">{{ $listing->listingtypestatus == 'en-venta' ? '096-786-7998' : '096-786-7998' }}</a>
                            </div>
                            <div class="d-flex gap-3 ms-4 mt-2">
                                <div class="rounded-circle d-flex justify-content-center align-items-center"
                                    style="border: 1px solid #242b40a2; width: 30px; height: 30px">
                                    <img width="15px" src="{{ asset('img/email-icon.png') }}" alt="Icono de Correo de la empresa Grupo Housing">
                                </div>
                                <a style="text-decoration: none" href="mailto:info@grupohousing.com"
                                    class="mt-2 text-dark ml-2">info@grupohousing.com</a>
                            </div>

                            <div class="d-flex gap-3 ms-4 mt-2">
                                <div class="rounded-circle d-flex justify-content-center align-items-center"
                                    style="border: 1px solid #242b40a2; width: 30px; height: 30px">
                                    <img loading="lazy" width="15px" src="{{ asset('img/location-icon.png') }}" alt="Icono de Ubicacion de la empresa Grupo Housing">
                                </div>
                                <a style="text-decoration: none" class="mt-1 text-dark ml-2"
                                    href="https://maps.app.goo.gl/WctLFek7TAYvaQ2V9">
                                    Remigio Tamariz Crespo y Av. Fray Vicente Solano
                                </a>
                            </div>

                            <div class="form-group mb-2">
                                <input type="hidden" name="g-recaptcha-response" id="recaptchaToken">

                                @error('captcha')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </form>
                    </div>
                </div>

                <div>
                    <x-whatsapp-channel-section></x-whatsapp-channel-section>
                </div>

                @if ($user->profile_photo_path != null && $user->status == 1)
                    <div class="advisor-card advisor-card-mobile">
                        <div class="advisor-image">
                            <img loading="lazy" width="150px" height="200px" style="object-fit: cover" src="{{ asset('uploads/profiles/' . $user->profile_photo_path) }}" alt="Imagen de Asesora">
                        </div>
                        <div class="advisor-info">
                            <div>
                                @if ($user->role == 'ASESOR')
                                    <h3>Asesora</h3>
                                @else
                                    <h3>Gestora</h3>
                                @endif
                                <p>{{ $user->name }}</p>
                                <div class="advisor-code text-center">
                                    <span>
                                        Cod: {{ $user->code != null ? $user->code : 'GH-1234'}}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
    

    <div>
        <x-similar-properties :city="$listing->city" :type="$listing->listingtype"></x-similar-properties>
    </div>

    <section id="contact-mobile-section" class="w-100 py-3 px-3 section-interest" style="position: fixed; bottom: 0px; background-color: #242B40; z-index: 10000">
        <div style="float: right" onclick="document.getElementById('contact-mobile-section').style.display='none'">
            <span class="text-white font-weight-bold">x</span>
        </div>
        <p style="font-size: medium" class="text-white m-0 p-0">¿Te interesa esta propiedad? <span style="font-weight: 700;">¡Contáctanos!</span></p>
        <div class="d-flex justify-content-center align-items-center mt-1" style="gap: 10px">
            <div class="w-100">
                <a class="btn btn-outline-light rounded-pill w-100 border" onclick="gtag_report_conversion_whatsapp('https://api.whatsapp.com/send?phone=593967867998&text=Hola%20Grupo%20Housing,%20estoy%20interesado/a%20en%20esta%20propiedad:%20*{{$listing->product_code}}*%0A{{Request::url()}}')" href="https://api.whatsapp.com/send?phone=593967867998&text=Hola%20Grupo%20Housing,%20estoy%20interesado/a%20en%20esta%20propiedad:%20*{{$listing->product_code}}*%0A{{Request::url()}}">WhatsApp</a>
            </div>
            <div class="w-100">
                <a class="btn btn-outline-light rounded-pill w-100 border" href="tel:+593967867998" onclick="gtag_report_conversion('tel:+593967867998')">Llamar</a>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalCarousel = document.getElementById("modalCarousel");
            const counter = document.getElementById("imageCounter");
            const totalSlides = modalCarousel.querySelectorAll(".carousel-item").length;

            modalCarousel.addEventListener("slid.bs.carousel", function (e) {
                const index = Array.from(modalCarousel.querySelectorAll(".carousel-item"))
                                .indexOf(e.relatedTarget) + 1;
                counter.textContent = `${index} / ${totalSlides}`;
            });

            const videoModal = document.getElementById('videoModalProperty');
            const iframe = videoModal.querySelector('iframe');
            const videoUrl = iframe.getAttribute('data-src');

            // Cuando se abre el modal
            videoModal.addEventListener('show.bs.modal', function () {
                iframe.src = videoUrl; // siempre asigna la URL
            });

            // Cuando se cierra el modal
            videoModal.addEventListener('hidden.bs.modal', function () {
                iframe.src = ""; // limpia para detener el video
            });

        });
    </script>
    <script>
        function capitalizeSentences(text) {
            return text.toLowerCase().replace(/(^\w{1}|\.\s*\w{1})/g, letter => letter.toUpperCase());
        }

        document.addEventListener('DOMContentLoaded', function() {
            let shortDesc = document.getElementById('short-desc').textContent;
            let fullDesc = document.getElementById('full-desc').textContent;

            //document.getElementById('short-desc').textContent = capitalizeSentences(shortDesc);
            //document.getElementById('full-desc').textContent = capitalizeSentences(fullDesc);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var carouselElement = document.querySelector('#carouselImages');
            var carousel = new bootstrap.Carousel(carouselElement);

            window.switchImage = function(index) {
                carousel.to(index);
            };

            var scrollLeftButton = document.getElementById('scrollLeft');
            var scrollRightButton = document.getElementById('scrollRight');
            var thumbnailContainer = document.querySelector('.thumbnail-container');

            scrollLeftButton.addEventListener('click', function() {
                thumbnailContainer.scrollBy({
                    left: -150,
                    behavior: 'smooth'
                });
            });

            scrollRightButton.addEventListener('click', function() {
                thumbnailContainer.scrollBy({
                    left: 150,
                    behavior: 'smooth'
                });
            });
        });
    </script>

    <script>
        function toggleDescription() {
            const shortDesc = document.getElementById('short-desc');
            const fullDesc = document.getElementById('full-desc');
            const toggleBtn = document.getElementById('desc-toggle');

            if (shortDesc.style.display === 'none') {
                shortDesc.style.display = 'inline';
                fullDesc.style.display = 'none';
                toggleBtn.innerHTML = 'Ver más';
            } else {
                shortDesc.style.display = 'none';
                fullDesc.style.display = 'inline';
                toggleBtn.innerHTML = 'Ver menos';
            }
        }
    </script>
    <script>
        const lat = {{ $listing->lat }};
        const lng = {{ $listing->lng }};

        let map = L.map('map').setView([lat + 0.003, lng], 15);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let title = '{{ $listing->listing_title }}';
        let images = '{{ $listing->images }}'.split('|')[0];

        let circle = L.circle([lat, lng], {
            color: '#242B40',
            fillColor: '#242B40',
            fillOpacity: 0.5,
            radius: 500
        }).addTo(map);

        let popup = L.popup()
            .setLatLng([lat + 0.004, lng])
            .setContent(
                `<div class="text-center w-auto"> <b style='font-weight: 700'>Sector donde se encuentra la propiedad:</b> <br> <span> ${title} </span> <br> <img width='100px' src='/uploads/listing/600/${images}' /></div>`
            )
            .addTo(map);
    </script>
    <script>
        function onScrollEvent(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var attributes = entry.target.attributes;
                    var src = attributes['data-src'].textContent;
                    entry.target.src = src;
                    entry.target.classList.add('visible');
                }
            });
        }
        var targets = document.querySelectorAll('.lazyLoad');
        // Instanciamos un nuevo observador.
        var observer = new IntersectionObserver(onScrollEvent);
        // Y se lo aplicamos a cada una de las
        // imágenes.
        targets.forEach(function(entry) {
            observer.observe(entry);
        });

        //compartir propiedad
        let shareLink = window.location.href;
        document.getElementById('shareToFacebook').addEventListener('click', () => {
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + shareLink, 'facebook-share-dialog',
                'width=626, height=436');
        });
        //document.getElementById('shareToTwitter').addEventListener('click', () => {window.open('https://twitter.com/intent/tweet?url='+shareLink)});
        document.getElementById('shareToWpp').addEventListener('click', () => {
            window.open('https://api.whatsapp.com/send?text=' + shareLink, '_blank')
        });

        const addactive = (id) => {
            let images = document.querySelectorAll('.active');
            images.forEach(element => {
                element.classList.remove('active');
            });
            let image = document.getElementById('img_' + id);
            image.classList.add('active');
        }

        const openFormAgendarCita = () => {
            let form = document.getElementById('formAgendarCita');
            form.classList.contains('d-none') ? form.classList.remove('d-none') : form.classList.add('d-none');

            let object = document.getElementById('buttonOpenForm');
            object.classList.contains('d-none') ? object.classList.remove('d-none') : object.classList.add('d-none');
        }
    </script>
@endsection
