@extends('layouts.web2')
@section('header')
    <title>Grupo Housing - Propiedades en Venta en Ecuador</title>
    <meta name="description"
        content="@if (request()->segment(2) != null) En Grupo Housing contamos con {{ ucwords(str_replace('-', ' ', request()->segment(2))) }}. Accede a nuestro Sitio Web y encuentra la propiedad que estás buscando. @else Encuentre la casa de sus sueños, donde los sueños se hacen realidad 😉 Contamos con una gran variedad de propiedades disponibles ¡Contáctenos! @endif">
    <meta name="keywords" content="">
    <link rel="canonical" href="">
    <meta name="robots" content="index,follow,snippet">
    <meta property="og:url"                content="https://grupohousing.com/propiedades-en-general" />
    <meta property="og:type"               content="website" />
    <meta property="og:title"              content="@isset($meta_seo){{ ucfirst(str_replace('-', ' ', $meta_seo)) }} @else Grupo Housing Encuentra la casa de tus sueños. @endisset" />
    <meta property="og:description"
        content="@isset($meta_seo)En Grupo Housing Contamos con {{ ucfirst(str_replace('-', ' ', $meta_seo)) }}. Accede a nuestro sitio web y encuentra la propiedad que estás buscando. @else Encuentre la casa de sus sueños, donde los sueños se hacen realidad 😉 Contamos con una gran variedad de propiedades disponibles ¡Contáctenos! @endisset" />
    <meta property="og:image" content="{{ asset('img/meta-image-social-cc.jpg') }}" />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        .search-bar-container {
            position: -webkit-sticky;
            margin-top: 97px;
            z-index: 0;
            width: 100%;
            background-color: rgb(238, 238, 238) !important;
        }

        .search-bar {
            position: sticky;
            z-index: 2;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            width: 100%;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-form {
            display: flex;
            gap: 20px;
            width: 100%;
            padding: 20px;
            border-radius: 0 10px 10px 10px;
            background-color: #ffffff;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: flex-end;
            align-items: flex-start;
        }


        .search-bar form{
            gap: 20px;
            padding: 20px 20px;
            border-radius: 0 10px 10px 10px;
            background-color: #ffffff;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .dropdown-menu {
            border: 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-light {
            background-color: #ffffff;
            color: #333;
        }

        .btn-light:hover {
            background-color: #f8f9fa;
        }

        @media (max-width: 768px) {
            .form-control,
            .dropdown-toggle {
                font-size: 14px;
            }
            .header-container{
                padding-top: 0px;
            }

            .search-bar-container{
                margin-top: 0px;
            }
        }

        .container-fluid {
            padding: 0;
        }

        .mx-auto {
            margin-right: auto;
            margin-left: auto;
        }

        @media (max-width: 768px) {
            .btn-fixed {
                position: fixed;
                bottom: 10px;
                left: 10px;
                z-index: 1000;
            }
        }

        .btn-primary {
            background-color: #242B40 !important;
            border-color: #242B40 !important;
            color: #ffffff !important;
        }

        .btn-primary:hover {
            background-color: #1a1f33 !important;
            border-color: #161a28 !important;
        }

        @media screen and (max-width: 580px) {
            .redes {
                display: none !important
            }

            .text-title {
                position: relative !important;
                height: auto !important
            }

            .search {
                display: inline-block !important;
            }

            .rounded-search-mobile {
                border: 1px solid rgb(195, 195, 195) !important;
                border-radius: 5% !important;
                padding: 10px 20px 10px 20px !important
            }

            .slash {
                display: none !important
            }

            .section-search {
                padding-left: 5% !important;
                padding-right: 5% !important
            }

            .margin-bottom-mobile {
                margin-bottom: 9px !important
            }

            .border-tabs-mobile {
                border-radius: 25px !important;
                border: .5px solid rgb(202, 202, 202) !important;
                box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;
            }

            .btn-search-mobile {
                text-align: center !important;
                margin-top: 20px !important
            }

            .label-filter {
                display: block !important
            }

            .img-filters {
                display: inline-block !important
            }

            .logo-housing {
                width: 250px;
            }

            .title {
                font-size: 35px !important;
                line-height: 45px !important;
            }

            .filters {
                box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px, rgba(10, 37, 64, 0.35) 0px -2px 6px 0px inset;
                display: inline !important;
                width: 100% !important;
                margin-left: 5% !important;
                margin-right: 5% !important;
            }

            .filters>select,
            .filters>input,
            .filters>button {
                width: 100% !important;
                margin-top: 2% !important;
                margin-bottom: 2% !important;
            }

            .padding-mobile-0 {
                padding-left: 0px !important;
                padding-right: 0px !important;
            }

            .characteristics {
                display: block !important;
                text-align: center !important;
            }

            .characteristics>p {
                padding-top: 0px !important;
            }

            .image_thumbnail {
                height: 250px !important;
            }

            .card-body {
                padding-top: 50px !important;
            }

            .card-body>a>h2 {
                padding-right: 0px !important;
            }
        }

        .border-end {
            border-right: 1px solid #dee2e6 !important;
        }

        .border-end-0 {
            border-right: 0 !important;
        }

        .carousel-image {
            object-fit: cover;
            width: 100%;
        }

        .property-item {
            transition: transform 0.3s ease;
        }

        .property-item:hover {
            transform: scale(1.05);
        }

        .btn-outline-primary {
            color: #242B40;
            border-color: #242B40;
        }

        .btn-outline-primary:hover {
            color: #ffffff;
            border-color: #242B40;
            background-color: #242B40;
        }

        .switch-container {
            display: inline-block;
            position: relative;
        }

        .switch-input {
            display: none;
        }

        .switch-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 60px;
            height: 30px;
            background-color: #ccc;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .switch-input:checked+.switch-label {
            background-color: #007bff;
        }

        .switch-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background-color: #fff;
            border-radius: 50%;
            box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        #dynamic_content h3{
            font-size: 18px;
            font-weight: 200;
        }

        .switch-input:checked+.switch-label .switch-icon:first-child {
            transform: translateX(30px);
        }

        .switch-input:checked+.switch-label .switch-icon:last-child {
            transform: translateX(-30px);
        }

        @media (max-width: 767.98px) {
            .switch-container {
                display: none;
            }
        }
        .description-clamp {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            font-family: 'Sharp Grotesk', sans-serif;
            font-weight: 100;
        }

        .inline-filters{
            width: 1px;
            background-color: #1427433c;
            height: 80px;
            margin: 0 10px;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
        }

        input, select, .dropdown-toggle {
            border: 1px solid #ccc;
            padding: 8px;
            transition: all 0.3s ease;
        }

        input:focus, select:focus, .dropdown-toggle:focus {
            outline: none;
            border-color: #337ab7;
            box-shadow: 0 0 5px rgba(51, 122, 183, 0.5);
        }

        .list-group-item{
            cursor: pointer;
        }

        .custom-number-input {
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            overflow: hidden;
            width: 80px;
        }

        .custom-number-input input[type="number"] {
            -webkit-appearance: none;
            -moz-appearance: textfield;
            appearance: none;
            border: none;
            outline: none;
            box-shadow: none;
            padding: 0.375rem 0.5rem;
            height: 38px;
            width: 100%;
            text-align: center;
            font-size: 13px;
        }

        .custom-number-input input::-webkit-outer-spin-button,
        .custom-number-input input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .custom-number-input .input-buttons {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .custom-number-input button {
            background-color: #f8f9fa;
            border: none;
            border-left: 1px solid #ced4da;
            color: #495057;
            cursor: pointer;
            font-size: 12px;
            width: 20px;
            height: 19px;
            padding: 0;
            line-height: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background-color 0.2s ease;
        }

        .custom-number-input .btn-up {
            border-bottom: 1px solid #ced4da;
            outline: none;
        }
        .custom-number-input .btn-down {
            border-top: none;
            outline: none;
        }

        .custom-number-input button:hover {
            background-color: #e9ecef;
        }

        @media (max-width: 1399.98px) {
            .search-bar form {
                flex-wrap: wrap;
                gap: 15px;
            }

            .search-bar form > div {
                flex: 1 1 30%;
                min-width: 250px;
            }

            .inline-filters {
                display: none;
            }

            .search-bar form > div:last-child {
                flex: 1 1 100%;
                display: flex;
                justify-content: center;
                gap: 10px;
            }

            select, .dropdown-toggle{
                width: 250px !important;
            }
        }

        @media (max-width: 991.98px) {
            .search-bar {
                padding: 20px;
            }

            .search-bar form {
                flex-wrap: wrap;
                gap: 15px;
                padding: 15px;
            }

            .search-bar form > div {
                flex: 1 1 45%;
                min-width: 200px;
            }

            .inline-filters {
                display: none;
            }

            select,
            .dropdown-toggle {
                width: auto !important;
            }

            .custom-input{
                width: 100% !important;
            }

            #minPrice, #maxPrice{
                width: 100px !important;
            }
        }

        @media (max-width: 767.98px) {
            .search-bar {
                display: none !important;
            }

            .btn-fixed {
                display: block;
            }
        }

        .modal.show {
            z-index: 6000;
        }

        /* Empieza estilos de filtros para moviles */

        .mobile-filters {
            background-color: #f8f9fa;
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            margin-top: 90px;
        }
        
        .custom-input {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 13px;
            width: 100%;
            height: 38px;
        }
        
        .custom-input:focus {
            border-color: #142743;
            box-shadow: 0 0 0 0.2rem rgba(20, 39, 67, 0.25);
            outline: none;
        }
        
        .search-button {
            background-color: #142743;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 8px;
            font-size: 12px;
            height: 38px;
            min-width: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .search-button:hover {
            background-color: #0f1e34;
            color: white;
        }
        
        .more-filters-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 6px;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .more-filters-btn:hover {
            background-color: #5a6268;
            color: white;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #495057;
        }
        
        .form-check-label {
            font-size: 13px;
            color: #495057;
        }
        
        .results-container {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .results-container .list-group-item {
            padding: 8px 12px;
            font-size: 13px;
            cursor: pointer;
        }
        
        .results-container .list-group-item:hover {
            background-color: #f8f9fa;
        }
        
        .custom-number-input {
            position: relative;
            width: 50px;
        }
        
        .custom-number-input input {
            text-align: center;
            font-size: 12px;
            height: 22px;
        }

        .input-number-bedrooms, .input-number-bathrooms, .input-number-garage{
            width: auto !important;
        }
        
        .input-buttons {
            position: absolute;
            right: 3px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
        }
        
        .btn-up, .btn-down {
            background: none;
            border: none;
            font-size: 10px;
            padding: 0;
            width: 12px;
            height: 8px;
            line-height: 1;
            color: #666;
        }
        
        .btn-up:hover, .btn-down:hover {
            color: #142743;
        }


        .number-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .number-control {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .number-control{
            width: min-content;
        }

        .age-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .age-inputs {
            display: flex;
            gap: 10px;
        }

        .close-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
        }

        .close-btn:hover {
            background-color: #c82333;
        }

        .filter-btn {
            background-color: #d8dadb;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 8px;
            cursor: pointer;
        }

        .search-btn {
            background-color: #142743;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .search-btn:hover {
            background-color: #0f1e34;
        }

        .label-bathrooms,
        .label-garage,
        .label-bedrooms{
            font-size: 12px !important;
        }

        .custom-input-modal{
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 13px;
            width: 100% !important;
            height: 38px;
        }

        .characteristics-details summary::-webkit-details-marker {
            display: none;
        }

        .characteristics-details summary::marker {
            display: none;
        }

        .characteristics-details[open] .dropdown-arrow {
            transform: rotate(180deg);
            transition: transform 0.2s ease;
        }

        .characteristics-details .dropdown-arrow {
            transition: transform 0.2s ease;
        }

        .characteristics-content {
            animation: slideDown 0.3s ease;
        }

        #garageModal, #bedroomsModal, #bathroomsModal{
            padding: 0 4px;
            text-align: left;  
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
                padding-top: 0;
                padding-bottom: 0;
            }
            to {
                opacity: 1;
                max-height: 200px;
                padding-top: 15px;
                padding-bottom: 15px;
            }
        }

        /* ===== ZONA CARDINAL ===== */
        .zone-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            vertical-align: middle;
        }
        .zone-norte  { background: #e8f4fd; color: #1565c0; border: 1px solid #bbdefb; }
        .zone-sur    { background: #fce4ec; color: #c62828; border: 1px solid #f8bbd9; }
        .zone-este   { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .zone-oeste  { background: #fff3e0; color: #e65100; border: 1px solid #ffe0b2; }
        .zone-centro { background: #ede7f6; color: #4527a0; border: 1px solid #d1c4e9; }

        .zone-noreste  { background: #e3f2fd; color: #0d47a1; border: 1px solid #bbdefb; }
        .zone-noroeste { background: #e8eaf6; color: #283593; border: 1px solid #c5cae9; }
        .zone-sureste  { background: #f1f8e9; color: #33691e; border: 1px solid #dcedc8; }
        .zone-suroeste { background: #fff8e1; color: #e65100; border: 1px solid #ffecb3; }

        /* Mapa modal */
        #zoneMap { height: 400px; width: 100%; border-radius: 8px; z-index: 1; }
        .zone-map-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            padding: 8px 0 10px;
        }
        .zone-map-legend span {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            user-select: none;
        }
        .zone-map-legend span:hover { opacity: 0.8; transform: translateY(-1px); }
        .zone-map-legend span.active {
            box-shadow: 0 0 0 2px #142743;
            transform: translateY(-1px);
        }

        /* Botón mapa inline */
        .map-btn {
            background: none;
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 5px 9px;
            font-size: 12px;
            color: #495057;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
            white-space: nowrap;
            height: 38px;
        }
        .map-btn:hover {
            background-color: #142743;
            color: white;
            border-color: #142743;
        }

        /* Tooltip Leaflet personalizado */
        .zone-tooltip {
            background: rgba(20,39,67,0.92) !important;
            border: none !important;
            color: white !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            padding: 4px 10px !important;
            border-radius: 4px !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3) !important;
        }
        .zone-tooltip::before { display: none !important; }
        .leaflet-tooltip.zone-tooltip { margin: 0 !important; }
    </style>
@endsection

@section('content')
    <section class="container d-none">
        <section class="p-5">
            <p style="font-family: 'Sharp Grotesk'" class="text-center display-6 fw-bold"><span
                    style="font-weight: 100">Prueba nuestro</span> <span style="font-weight: 500">buscador avanzado</span>
            </p>
        </section>
    </section>

    {{-- Empieza filtros para moviles --}}

    <section class="mobile-filters d-md-none">
        <form id="searchFormModal" class="row g-2">
            <!-- Primera fila: Tipo de Propiedad y Operación -->
            <div class="col-6">
                <label for="propertyTypeModal" class="form-label">Tipo de Propiedad</label>
                <select class="custom-input" id="propertyTypeModal">
                    <option value="">Elije tipo de propiedad</option>
                    <option data-ids="[23,1]" value="1">Casas</option>
                    <option data-ids="[24,3]" value="2">Departamentos</option>
                    <option data-ids="[26,10]" value="15">Terrenos</option>
                    <option data-ids="[25,5]" value="3">Casas Comerciales</option>
                    <option data-ids="[32,6]" value="4">Locales Comerciales</option>
                    <option data-ids="[37]" value="5">Edificios</option>
                    <option data-ids="[39]" value="6">Hoteles</option>
                    <option data-ids="[41]" value="7">Fábricas</option>
                    <option data-ids="[42]" value="8">Parqueaderos</option>
                    <option data-ids="[43]" value="9">Bodegas</option>
                    <option data-ids="[35,7]" value="10">Oficinas</option>
                    <option data-ids="[36,8]" value="11">Suites</option>
                    <option data-ids="[29,9]" value="12">Quintas</option>
                    <option data-ids="[30,30]" value="13">Haciendas</option>
                    <option data-ids="[45]" value="14">Naves Industriales</option>
                </select>
            </div>
            
            <div class="col-6">
                <label for="propertyStatusModal" class="form-label">Operación</label>
                <select class="custom-input" id="propertyStatusModal">
                    <option data-ids="general" value="general">Todas</option>
                    <option data-ids="venta" value="venta">Venta</option>
                    <option data-ids="renta" value="renta">Renta</option>
                    <option data-ids="proyectos" value="proyectos">Proyectos</option>
                </select>
            </div>
            
            <!-- Segunda fila: Ubicación, Buscar y Más Filtros -->
            <div class="col-8">
                <label for="searchTermModal" class="form-label">Ubicación o código</label>
                <div style="position: relative;">
                    <input type="text" id="searchTermModal" class="custom-input" 
                           placeholder="Sector, Parroquia, Provincia" autocomplete="off">
                    <div id="resultsContainerModal" class="results-container list-group shadow-sm"></div>
                </div>
            </div>
            
            <div class="col-2 d-flex align-items-end">
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            
            <div class="col-2 d-flex align-items-end">
                <button type="button" class="more-filters-btn" data-bs-toggle="modal" data-bs-target="#filtersModal" title="Más filtros">
                    <i class="fas fa-sliders-h"></i>
                </button>
            </div>
        </form>
    </section>

    <!-- Modal para filtros adicionales (mobile) -->
    <div class="modal fade" id="filtersModal" tabindex="-1" aria-labelledby="filtersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <!-- Zona y Radio -->
                    <div class="row mb-3">
                        <div class="col-6 col-md-6">
                            <label class="form-label">Zona</label>
                            <div class="d-flex align-items-center" style="gap: 6px;">
                                <select id="zonaModal" class="custom-input-modal border" style="flex: 1;">
                                    <option value="">Todas las zonas</option>
                                    <option value="norte">Norte</option>
                                    <option value="noreste">Noreste</option>
                                    <option value="este">Este</option>
                                    <option value="sureste">Sureste</option>
                                    <option value="sur">Sur</option>
                                    <option value="suroeste">Suroeste</option>
                                    <option value="oeste">Oeste</option>
                                    <option value="noroeste">Noroeste</option>
                                    <option value="centro">Centro</option>
                                </select>
                                <button type="button" class="map-btn" data-bs-toggle="modal" data-bs-target="#zoneMapModal" title="Ver mapa de zonas">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/>
                                        <line x1="9" y1="3" x2="9" y2="18"/>
                                        <line x1="15" y1="6" x2="15" y2="21"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <label class="form-label">Radio</label>
                            <input type="text" id="radioModal" class="custom-input border" placeholder="0km">
                        </div>
                    </div>

                    <!-- Seleccione características -->
                    <div class="mb-3">
                        <details class="characteristics-details">
                            <summary class="custom-input d-flex justify-content-between align-items-center" 
                                    style="width: 100%; background-color: white; border: 1px solid #ced4da; cursor: pointer; list-style: none;">
                                Seleccione características
                                <span class="dropdown-arrow">▼</span>
                            </summary>
                            <div class="characteristics-content mt-1" style="background-color: #f8f9fa; padding: 15px; border-radius: 6px;">
                                <div class="row">
                                    <div class="col-6 mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="gymModal">
                                            <label class="form-check-label" for="gymModal">Gimnasio</label>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="wifiModal">
                                            <label class="form-check-label" for="wifiModal">Internet/Wifi</label>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="poolModal">
                                            <label class="form-check-label" for="poolModal">Piscina</label>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="cisternModal">
                                            <label class="form-check-label" for="cisternModal">Cisterna</label>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="terraceModal">
                                            <label class="form-check-label" for="terraceModal">Terraza</label>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="gardenModal">
                                            <label class="form-check-label" for="gardenModal">Jardín</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </details>
                    </div>

                    <!-- Controles numéricos -->
                    <div class="number-controls mb-3">
                        <div class="number-control">
                            <label class="form-label mb-0 label-bathrooms">Baños:</label>
                            <div class="custom-number-input">
                                <input type="number" id="bathroomsModal" value="0" min="0">
                                <div class="input-buttons">
                                    <button type="button" class="btn-up">+</button>
                                    <button type="button" class="btn-down">-</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="number-control">
                            <label class="form-label mb-0 label-garage">Garaje:</label>
                            <div class="custom-number-input">
                                <input type="number" id="garageModal" value="0" min="0">
                                <div class="input-buttons">
                                    <button type="button" class="btn-up">+</button>
                                    <button type="button" class="btn-down">-</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="number-control">
                            <label class="form-label mb-0 label-bedrooms">Habit:</label>
                            <div class="custom-number-input">
                                <input type="number" id="bedroomsModal" value="0" min="0">
                                <div class="input-buttons">
                                    <button type="button" class="btn-up">+</button>
                                    <button type="button" class="btn-down">-</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Antigüedad -->
                    <div class="mb-3">
                        <label class="form-label">Antigüedad:</label>
                        <div class="age-control">
                            <div class="form-check">
                                <label class="form-check-label" for="nuevaModal">Nueva</label>
                                <input class="form-check-input" type="checkbox" id="nuevaModal">
                            </div>
                            <span class="mx-2">Años</span>
                            <div class="age-inputs">
                                <input type="text" id="listyearsmin" class="custom-input" placeholder="Mínimo" style="width: 80px;">
                                <input type="text" id="listyearsmax" class="custom-input" placeholder="Máximo" style="width: 80px;">
                            </div>
                        </div>
                    </div>

                    <!-- Área de construcción -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Área de construcción</label>
                        </div>
                        <div class="col-6">
                            <input type="text" id="constructionAreaMinModal" class="custom-input" placeholder="Mínimo m2">
                        </div>
                        <div class="col-6">
                            <input type="text" id="constructionAreaMaxModal" class="custom-input" placeholder="Máximo m2">
                        </div>
                    </div>

                    <!-- Área de terreno -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Área de terreno</label>
                        </div>
                        <div class="col-6">
                            <input type="text" id="landAreaMinModal" class="custom-input" placeholder="Mínimo m2">
                        </div>
                        <div class="col-6">
                            <input type="text" id="landAreaMaxModal" class="custom-input" placeholder="Máximo m2">
                        </div>
                    </div>

                    <!-- Precio -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Precio</label>
                        </div>
                        <div class="col-6">
                            <input type="text" id="minPriceModal" class="custom-input" placeholder="Mínimo $">
                        </div>
                        <div class="col-6">
                            <input type="text" id="maxPriceModal" class="custom-input" placeholder="Máximo $">
                        </div>
                    </div>
                </div>
                
                <!-- Footer con botones -->
                <div class="modal-footer border-0 pt-0">
                    <div class="row">
                        <div class="col-6 col-sm-6">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">✕ <span style="color: #fff; font-size: 14px;">Cerrar</span></button>
                        </div>
                        <div class="col-6 col-sm-6 d-flex" style="gap: 5px">
                            <button type="button" class="filter-btn" onclick="clearSearchModal()">
                                <img width="25px" height="25px" src="{{ asset('img/icono-de-filtros.png') }}" alt="Icono de filtros">
                            </button>
                            <button type="submit" form="searchFormModal" class="search-btn" data-bs-dismiss="modal">Buscar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Termina filtros para moviles --}}

    <section class="search-bar-container">
        <!-- Contenido para desktop -->
        <div class="d-none d-md-block mx-auto">
            <div class="search-bar rounded-0">
                <form id="searchFormDesktop" class="search-form">
                    
                    <!-- Provincia -->
                    <div class="filter-item">
                        <label for="state">Provincia</label>
                        <select id="state" class="custom-input">
                            <option value="">Seleccione provincia</option>
                            @foreach ($provinces as $province)
                                <option data-id="{{ $province->id }}" value="{{ $province->name }}">{{ $province->name }}</option>
                            @endforeach
                        </select>
                    </div>
    
                    <!-- Ciudad -->
                    <div class="filter-item">
                        <label for="city">Ciudad</label>
                        <select id="city" class="custom-input">
                            <option value="">Seleccione ciudad</option>
                        </select>
                    </div>
    
                    <!-- Sector -->
                    <div class="filter-item">
                        <label for="sector">Sector o Código</label>
                        <input type="text" id="searchTerm" class="custom-input" placeholder="Ej: El Vergel, Cumbayá, Samborondón">
                    </div>
    
                    <!-- Tipo de Propiedad -->
                    <div class="filter-item">
                        <label for="propertyType">Propiedad</label>
                        <select class="custom-input" id="propertyType">
                            <option value="">Elije tipo de propiedad</option>
                            <option data-ids="[23,1]" value="1">Casas</option>
                            <option data-ids="[24,3]" value="2">Departamentos</option>
                            <option data-ids="[26,10]" value="15">Terrenos</option>
                            <option data-ids="[25,5]" value="3">Casas Comerciales</option>
                            <option data-ids="[32,6]" value="4">Locales Comerciales</option>
                            <option data-ids="[37]" value="5">Edificios</option>
                            <option data-ids="[39]" value="6">Hoteles</option>
                            <option data-ids="[41]" value="7">Fábricas</option>
                            <option data-ids="[42]" value="8">Parqueaderos</option>
                            <option data-ids="[43]" value="9">Bodegas</option>
                            <option data-ids="[35,7]" value="10">Oficinas</option>
                            <option data-ids="[36,8]" value="11">Suites</option>
                            <option data-ids="[29,9]" value="12">Quintas</option>
                            <option data-ids="[30,30]" value="13">Haciendas</option>
                            <option data-ids="[45]" value="14">Naves Industriales</option>
                        </select>
                    </div>
    
                    <!-- Operación -->
                    <div class="filter-item">
                        <label for="propertyStatus">Operación</label>
                        <select class="custom-input" id="propertyStatus">
                            <option data-ids="general" value="general">Todas</option>
                            <option data-ids="venta" value="venta">Venta</option>
                            <option data-ids="renta" value="renta">Renta</option>
                            <option data-ids="proyectos" value="proyectos">Proyectos</option>
                        </select>
                    </div>

                    <!-- ===== ZONA CARDINAL (DESKTOP) ===== -->
                    <div class="filter-item">
                        <label for="cardinalZone">Zona</label>
                        <div class="d-flex align-items-center" style="gap: 6px;">
                            <select id="cardinalZone" class="custom-input">
                                <option value="">Todas las zonas</option>
                                <option value="norte">Norte</option>
                                <option value="noreste">Noreste</option>
                                <option value="este">Este</option>
                                <option value="sureste">Sureste</option>
                                <option value="sur">Sur</option>
                                <option value="suroeste">Suroeste</option>
                                <option value="oeste">Oeste</option>
                                <option value="noroeste">Noroeste</option>
                                <option value="centro">Centro</option>
                            </select>
                            <button type="button" class="map-btn" data-bs-toggle="modal" data-bs-target="#zoneMapModal" title="Seleccionar zona en mapa">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/>
                                    <line x1="9" y1="3" x2="9" y2="18"/>
                                    <line x1="15" y1="6" x2="15" y2="21"/>
                                </svg>
                                Mapa
                            </button>
                        </div>
                    </div>
    
                    <!-- Precio -->
                    <div class="filter-item">
                        <label for="minPrice">Precio</label>
                        <div class="d-flex" style="gap: 10px">
                            <input type="number" id="minPrice" class="custom-input" placeholder="Mínimo">
                            <input type="number" id="maxPrice" class="custom-input" placeholder="Máximo">
                        </div>
                    </div>
    
                    <!-- Más Filtros -->
                    <div class="filter-item">
                        <label for="bedrooms">Más Filtros</label>
                        <button class="dropdown-toggle custom-input" type="button" id="featuresInput"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Características
                        </button>
                        <div class="dropdown-menu p-3" aria-labelledby="featuresInput" style="max-width: 300px;">
                            <!-- Dropdown hijo -->
                            <div class="dropdown dropend w-100">
                                <button class="dropdown-toggle btn btn-sm w-100 mb-2" type="button"
                                    id="btnCharacteristics" data-bs-toggle="dropdown" aria-expanded="false">
                                    Seleccione características
                                </button>
                    
                                <!-- Menú hijo -->
                                <div class="dropdown-menu p-2" aria-labelledby="btnCharacteristics" style="width: 250px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gym">
                                        <label class="form-check-label" for="gym">Gimnasio</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="wifi">
                                        <label class="form-check-label" for="wifi">Internet/Wifi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pool">
                                        <label class="form-check-label" for="pool">Piscina</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="cistern" checked>
                                        <label class="form-check-label" for="cistern">Cisterna</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terrace">
                                        <label class="form-check-label" for="terrace">Terraza</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="garden">
                                        <label class="form-check-label" for="garden">Jardín</label>
                                    </div>
                                </div>
                            </div>
                    
                            <!-- Inputs adicionales -->
                            <div class="row g-2 mt-2">
                                <div class="col-6 d-flex align-items-center" style="gap: 5px">
                                    <label for="bathrooms">Baños:</label>
                                    <div class="custom-number-input input-number-bathrooms">
                                        <input type="number" min="0" id="bathrooms" class="form-control form-control-sm" value="0">
                                        <div class="input-buttons">
                                            <button type="button" class="btn-up">+</button>
                                            <button type="button" class="btn-down">-</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 d-flex align-items-center" style="gap: 5px">
                                    <label for="bedrooms">Habit:</label>
                                    <div class="custom-number-input input-number-bedrooms">
                                        <input type="number" min="0" id="bedrooms" class="form-control form-control-sm" value="0">
                                        <div class="input-buttons">
                                            <button type="button" class="btn-up">+</button>
                                            <button type="button" class="btn-down">-</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 d-flex align-items-center" style="gap: 5px">
                                    <label for="garage">Garaje:</label>
                                    <div class="custom-number-input input-number-garage">
                                        <input type="number" min="0" id="garage" class="form-control form-control-sm" value="0">
                                        <div class="input-buttons">
                                            <button type="button" class="btn-up">+</button>
                                            <button type="button" class="btn-down">-</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                            <hr>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label mb-1">Área de construcción</label>
                                    <div class="d-flex" style="gap: 10px">
                                        <input type="number" class="custom-input" style="width: 100px" placeholder="Mínimo" id="constructionAreaMin">
                                        <input type="number" class="custom-input" style="width: 100px" placeholder="Máximo" id="constructionAreaMax">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-1">Área de terreno</label>
                                    <div class="d-flex" style="gap: 10px">
                                        <input type="number" class="custom-input" style="width: 100px" placeholder="Mínimo" id="landAreaMin">
                                        <input type="number" class="custom-input" style="width: 100px" placeholder="Máximo" id="landAreaMax">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Botones -->
                    <div class="filter-actions">
                        <button type="button" class="btn btn-sm rounded-pill" onclick="clearSearch(false)" style="background-color: #14274311;">
                            <img width="20" src="{{ asset('img/icono-filtrar.webp') }}" alt="Icono de Limpiar Filtros">
                            Limpiar
                        </button>
                        <button type="submit" class="btn btn-sm rounded-pill" style="background-color: #142743; color: #ffffff">
                            <img width="20" src="{{ asset('img/icono-limpiar-filtros.webp') }}" alt="Icono de filtrar">
                            Buscar
                        </button>
                    </div>
    
                </form>
            </div>
        </div>
    </section>

    <!-- ===== MODAL MAPA DE ZONAS ===== -->
    <div class="modal fade" id="zoneMapModal" tabindex="-1" aria-labelledby="zoneMapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold" id="zoneMapModalLabel" style="display:flex; align-items:center; gap:8px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#142743" stroke-width="2.5">
                                <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/>
                                <line x1="9" y1="3" x2="9" y2="18"/>
                                <line x1="15" y1="6" x2="15" y2="21"/>
                            </svg>
                            Selecciona la zona en Cuenca
                        </h5>
                        <p class="text-muted mb-0" style="font-size: 13px;">Haz clic en una zona del mapa o en la leyenda para filtrar propiedades</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body pt-2">
                    <!-- Leyenda clickeable -->
                    <div class="zone-map-legend">
                        <span class="zone-norte"    data-zone="norte"    onclick="selectZoneFromLegend('norte')">🔵 Norte</span>
                        <span class="zone-noreste"  data-zone="noreste"  onclick="selectZoneFromLegend('noreste')">🔷 Noreste</span>
                        <span class="zone-este"     data-zone="este"     onclick="selectZoneFromLegend('este')">🟢 Este</span>
                        <span class="zone-sureste"  data-zone="sureste"  onclick="selectZoneFromLegend('sureste')">🟩 Sureste</span>
                        <span class="zone-sur"      data-zone="sur"      onclick="selectZoneFromLegend('sur')">🔴 Sur</span>
                        <span class="zone-suroeste" data-zone="suroeste" onclick="selectZoneFromLegend('suroeste')">🟧 Suroeste</span>
                        <span class="zone-oeste"    data-zone="oeste"    onclick="selectZoneFromLegend('oeste')">🟠 Oeste</span>
                        <span class="zone-noroeste" data-zone="noroeste" onclick="selectZoneFromLegend('noroeste')">🔹 Noroeste</span>
                        <span class="zone-centro"   data-zone="centro"   onclick="selectZoneFromLegend('centro')">🟣 Centro</span>
                        <span style="background:#f1f3f5; color:#495057; border:1px solid #dee2e6;" data-zone="" onclick="selectZoneFromLegend('')">✕ Todas</span>
                    </div>
                    <!-- Mapa Leaflet -->
                    <div id="zoneMap"></div>
                    <!-- Feedback zona seleccionada -->
                    <div id="zoneMapFeedback" class="mt-2 text-center" style="font-size: 13px; min-height: 24px;"></div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" onclick="selectZoneFromLegend('')">
                        Limpiar zona
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill" style="background-color:#142743; color:#fff;" data-bs-dismiss="modal" onclick="applyZoneAndSearch()">
                        <i class="fas fa-search me-1"></i> Buscar en esta zona
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- ===== FIN MODAL MAPA ===== -->


    <div class="container d-flex justify-content-between align-items-center header-container" style="margin-top: 40px">
        <div>
            <h1 style="font-family: 'Sharp Grotesk'; text-align: left;" class="h3 fw-bold">
                <span style="font-weight: 500">Total</span>
                <span style="font-weight: 100"> propiedades</span>
            </h1>
            <p id="description_banner"></p>
        </div>
        <div class="switch-container">
            <input type="checkbox" id="toggleViewBtn" class="switch-input">
            <label for="toggleViewBtn" class="switch-label">
                <span class="switch-icon"><i id="toggleIcon" class="fas fa-th-large"></i></span>
                <span class="switch-icon"><i id="toggleIcon" class="fas fa-bars"></i></span>
            </label>
        </div>
    </div>

    <p id="dynamic-description-paragraph" class="container"></p>

    <section class="container mt-5" id="propertiesContainer">

        <section class="row">
            <section class="col-sm-12">
                <section class="row justify-content-center" id="propertiesList">
                    <!-- Los resultados de la búsqueda se insertarán aquí -->
                </section>
            </section>
        </section>
        <div class="row justify-content-center">
            <div id="pagination" class="mt-4"></div>
        </div>

        <div id="dynamic_content" class="row justify-content-center align-items-center mb-4">

        </div>
    </section>
@endsection

@section('script')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>

        document.querySelectorAll('.dropdown-menu').forEach(function (element) {
            element.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        });

        var typeIdsArray = [];
        var typeIdsArrayModal = [];
        let useCardView = false;
        let pagegobal = 1;
        document.getElementById('toggleViewBtn').addEventListener('change', function() {
            useCardView = this.checked;
            searchProperties(pagegobal, false);
        });
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.custom-number-input').forEach(container => {
                const input = container.querySelector('input[type="number"]');
                const btnUp = container.querySelector('.btn-up');
                const btnDown = container.querySelector('.btn-down');

                btnUp.addEventListener('click', () => {
                    input.stepUp();
                    input.dispatchEvent(new Event('change'));
                });

                btnDown.addEventListener('click', () => {
                    input.stepDown();
                    input.dispatchEvent(new Event('change'));
                });
            });

            if (window.innerWidth <= 767) {
                document.getElementById('toggleViewBtn').checked = true;
                useCardView = true;
            }

                        const initialState        = '{{ $state ?? '' }}';
            const initialStatus       = '{{ $status ?? '' }}';
            const initialCity         = '{{ $city ?? '' }}';
            const initialParish       = '{{ $parish ?? '' }}';
            const initialMinPrice     = '{{ $minPrice ?? '' }}';
            const initialMaxPrice     = '{{ $maxPrice ?? '' }}';
            const initialTypeIds      = JSON.parse('{{ json_encode($typeId ?? []) }}');
            const initialPropertyCode = '{{ $propertyCode ?? '' }}';
 
            const searchTerm = new URLSearchParams(window.location.search).get('searchTerm') || '';
 
            // Rellenar campos desktop
            if (initialCity || initialParish || searchTerm)
                document.getElementById('searchTerm').value = searchTerm || initialCity || initialParish || '';
            if (initialMinPrice) document.getElementById('minPrice').value = initialMinPrice;
            if (initialMaxPrice) document.getElementById('maxPrice').value = initialMaxPrice;
            setInitialPropertyType(initialTypeIds, 'propertyType');
            setInitialPropertyStatus(initialStatus, 'propertyStatus');
 
            // Rellenar campos mobile
            if (initialCity || initialParish || searchTerm)
                document.getElementById('searchTermModal').value = searchTerm || initialCity || initialParish || '';
            if (initialMinPrice) document.getElementById('minPriceModal').value = initialMinPrice;
            if (initialMaxPrice) document.getElementById('maxPriceModal').value = initialMaxPrice;
            setInitialPropertyType(initialTypeIds, 'propertyTypeModal');
            setInitialPropertyStatus(initialStatus, 'propertyStatusModal');
 
            // Si viene un código de propiedad, buscar por código directamente
            // sin disparar el submit del formulario normal
            if (initialPropertyCode) {
                searchProperties(1, false, initialPropertyCode);
            } else {
                document.querySelector('#searchFormDesktop button[type="submit"]').click();
            }


            const myModalEl = document.getElementById('searchFormModal');
            myModalEl.addEventListener('shown.bs.modal', event => {
                const searchButton = document.querySelector('#searchFormModal button[type="submit"]');
                if (searchButton) {
                    searchButton.click();
                } else {
                    console.error("No se encontró el botón de búsqueda en el modal.");
                }
            });

            const provinceSelect = document.getElementById('state');
            const citySelect = document.getElementById('city');

            provinceSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const provinceId = selectedOption.getAttribute('data-id');

                citySelect.innerHTML = '<option value="">Seleccione ciudad</option>';

                if (provinceId) {
                    const url = `/getcities/${provinceId}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(cities => {
                            if (cities.length === 0) {
                                const option = document.createElement('option');
                                option.textContent = "No se encontraron ciudades";
                                citySelect.appendChild(option);
                            } else {
                                cities.forEach(city => {
                                    const option = document.createElement('option');
                                    option.setAttribute('data-id', city.id);
                                    option.value = city.name;
                                    option.textContent = city.name;
                                    citySelect.appendChild(option);
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error al obtener las ciudades:', error);
                            alert('Error al cargar las ciudades. Intente de nuevo más tarde.');
                        });
                }
            });
        });

        function setInitialPropertyType(typeIds, propertyTypeId) {
            const selectElement = document.getElementById(propertyTypeId);
            const options = selectElement.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].getAttribute('data-ids') === JSON.stringify(typeIds)) {
                    options[i].selected = true;
                    if (propertyTypeId === 'propertyType') {
                        typeIdsArray = typeIds;
                    } else if (propertyTypeId === 'propertyTypeModal') {
                        typeIdsArrayModal = typeIds;
                    }
                    const event = new Event('change');
                    selectElement.dispatchEvent(event);
                    break;
                }
            }
        }

        function setInitialPropertyStatus(initialStatus, propertyStatusId) {
            const selectElement = document.getElementById(propertyStatusId);
            const options = selectElement.options;
            for (let option of options) {
                if (option.value === initialStatus) {
                    option.selected = true;
                    selectElement.dispatchEvent(new Event('change'));
                    break;
                }
            }
        }

        document.getElementById('propertyType').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var typeIds = selectedOption.getAttribute('data-ids');
            typeIdsArray = JSON.parse(typeIds);
        });

        document.getElementById('propertyTypeModal').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var typeIds = selectedOption.getAttribute('data-ids');
            typeIdsArrayModal = JSON.parse(typeIds);
        });

        document.getElementById('propertyStatus').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var statusselect = selectedOption.getAttribute('data-ids');
            status = statusselect;
        });
        document.getElementById('propertyStatusModal').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var statusselect = selectedOption.getAttribute('data-ids');
            status = statusselect;
        });

        // =====================================================================
        // ===== LÓGICA MAPA DE ZONAS CUENCA (LEAFLET) =========================
        // =====================================================================
        let zoneMap = null;
        let selectedZoneValue = '';
        let zoneLayerMap = {};

        // Polígonos aproximados de las zonas de Cuenca
        // NOTA: Ajusta estas coordenadas según los límites reales de tu empresa
        const cuencaZones = {
            norte: {
                label: 'Norte', color: '#1565c0', fillColor: '#90caf9',
                coords: [[-2.8550,-79.0200],[-2.8550,-78.9600],[-2.8950,-78.9600],[-2.8950,-79.0200]]
            },
            noreste: {
                label: 'Noreste', color: '#0d47a1', fillColor: '#bbdefb',
                coords: [[-2.8550,-78.9600],[-2.8550,-78.9000],[-2.8950,-78.9000],[-2.8950,-78.9600]]
            },
            este: {
                label: 'Este', color: '#2e7d32', fillColor: '#a5d6a7',
                coords: [[-2.8950,-78.9600],[-2.8950,-78.8800],[-2.9250,-78.8800],[-2.9250,-78.9600]]
            },
            sureste: {
                label: 'Sureste', color: '#33691e', fillColor: '#dcedc8',
                coords: [[-2.9250,-78.9600],[-2.9250,-78.8800],[-2.9650,-78.8800],[-2.9650,-78.9600]]
            },
            sur: {
                label: 'Sur', color: '#c62828', fillColor: '#ef9a9a',
                coords: [[-2.9250,-79.0200],[-2.9250,-78.9600],[-2.9650,-78.9600],[-2.9650,-79.0200]]
            },
            suroeste: {
                label: 'Suroeste', color: '#e65100', fillColor: '#ffcc80',
                coords: [[-2.9250,-79.0900],[-2.9250,-79.0200],[-2.9650,-79.0200],[-2.9650,-79.0900]]
            },
            oeste: {
                label: 'Oeste', color: '#e65100', fillColor: '#ffcc80',
                coords: [[-2.8950,-79.0900],[-2.8950,-79.0200],[-2.9250,-79.0200],[-2.9250,-79.0900]]
            },
            noroeste: {
                label: 'Noroeste', color: '#283593', fillColor: '#c5cae9',
                coords: [[-2.8550,-79.0900],[-2.8550,-79.0200],[-2.8950,-79.0200],[-2.8950,-79.0900]]
            },
            centro: {
                label: 'Centro', color: '#4527a0', fillColor: '#ce93d8',
                coords: [[-2.8950,-79.0200],[-2.8950,-78.9600],[-2.9250,-78.9600],[-2.9250,-79.0200]]
            }
        };

        function initZoneMap() {
            if (zoneMap) {
                zoneMap.invalidateSize();
                return;
            }

            zoneMap = L.map('zoneMap').setView([-2.9001, -79.0059], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
                maxZoom: 18
            }).addTo(zoneMap);

            Object.entries(cuencaZones).forEach(([key, zone]) => {
                const polygon = L.polygon(zone.coords, {
                    color: zone.color,
                    fillColor: zone.fillColor,
                    fillOpacity: 0.40,
                    weight: 2.5
                }).addTo(zoneMap);

                polygon.bindTooltip(`<b>${zone.label}</b>`, {
                    permanent: true,
                    direction: 'center',
                    className: 'zone-tooltip'
                });

                polygon.on('click', function() {
                    selectZoneFromMap(key);
                });
                polygon.on('mouseover', function() {
                    if (selectedZoneValue !== key) {
                        this.setStyle({ fillOpacity: 0.65 });
                    }
                });
                polygon.on('mouseout', function() {
                    if (selectedZoneValue !== key) {
                        this.setStyle({ fillOpacity: 0.40 });
                    }
                });

                zoneLayerMap[key] = polygon;
            });

            // Si ya había una zona seleccionada, reflejarla
            if (selectedZoneValue && zoneLayerMap[selectedZoneValue]) {
                highlightZoneOnMap(selectedZoneValue);
            }
        }

        function highlightZoneOnMap(zoneKey) {
            Object.entries(zoneLayerMap).forEach(([k, poly]) => {
                poly.setStyle({ fillOpacity: 0.40, weight: 2.5 });
            });
            if (zoneKey && zoneLayerMap[zoneKey]) {
                zoneLayerMap[zoneKey].setStyle({ fillOpacity: 0.75, weight: 3.5 });
            }
            // Actualizar leyenda
            document.querySelectorAll('.zone-map-legend span').forEach(el => {
                el.classList.toggle('active', el.dataset.zone === zoneKey);
            });
        }

        function selectZoneFromMap(zoneKey) {
            selectedZoneValue = zoneKey;
            highlightZoneOnMap(zoneKey);

            const feedback = document.getElementById('zoneMapFeedback');
            if (feedback) {
                if (zoneKey) {
                    const zone = cuencaZones[zoneKey];
                    feedback.innerHTML = `<span class="zone-badge zone-${zoneKey}">✓ Zona ${zone.label} seleccionada — haz clic en "Buscar en esta zona" para aplicar</span>`;
                } else {
                    feedback.innerHTML = '<span style="color:#6c757d">Sin zona seleccionada — se mostrarán todas las propiedades</span>';
                }
            }

            syncZoneSelects(zoneKey);
        }

        function selectZoneFromLegend(zoneKey) {
            selectZoneFromMap(zoneKey);
        }

        function syncZoneSelects(zoneKey) {
            const desktopSelect = document.getElementById('cardinalZone');
            const mobileSelect  = document.getElementById('zonaModal');
            if (desktopSelect) desktopSelect.value = zoneKey;
            if (mobileSelect)  mobileSelect.value  = zoneKey;
        }

        function applyZoneAndSearch() {
            const isMobile = window.innerWidth < 768;
            searchProperties(1, isMobile);
        }

        // Inicializar mapa al abrir el modal
        document.addEventListener('DOMContentLoaded', function() {
            const zoneMapModalEl = document.getElementById('zoneMapModal');
            if (zoneMapModalEl) {
                zoneMapModalEl.addEventListener('shown.bs.modal', function() {
                    initZoneMap();
                    // Reflejar zona actual al abrir
                    const desktopVal = document.getElementById('cardinalZone')?.value || '';
                    const mobileVal  = document.getElementById('zonaModal')?.value   || '';
                    const currentZone = desktopVal || mobileVal || '';
                    if (currentZone !== selectedZoneValue) {
                        selectedZoneValue = currentZone;
                    }
                    highlightZoneOnMap(selectedZoneValue);
                    if (selectedZoneValue) {
                        const zone = cuencaZones[selectedZoneValue];
                        const feedback = document.getElementById('zoneMapFeedback');
                        if (feedback && zone) {
                            feedback.innerHTML = `<span class="zone-badge zone-${selectedZoneValue}">✓ Zona ${zone.label} seleccionada</span>`;
                        }
                    }
                });
            }
        });
        // =====================================================================
        // ===== FIN LÓGICA MAPA ===============================================
        // =====================================================================


        function getImageUrl(property) {
            const imageList = property.images.split('|');
            if (imageList.length > 0 && imageList[0]) {
                return `/uploads/listing/${imageList[0]}`;
            }
        }

        function replaceFirstEnWithDe(titleSuffix) {
            return titleSuffix.replace(/\ben\b/i, 'de');
        }

        function updatePagination(pagination, isModal) {
            let paginationHtml =
                '<nav aria-label="Page navigation" class="pagination-nav"><ul class="pagination justify-content-center">';
            if (pagination.prev_page_url) {
                paginationHtml +=
                    `<li class="page-item"><button class="page-link" style="border: 1px solid #242B40; border-radius: 50%; color: #242B40; width: 36px; height: 36px; padding: 0 12px; display: flex; align-items: center; justify-content: center;" onclick="searchProperties(${pagination.current_page - 1}, ${isModal})"><i class="fas fa-angle-left"></i></button></li>`;
            } else {
                paginationHtml +=
                    '<li class="page-item disabled"><span class="page-link" style="border: 1px solid #242B40; border-radius: 50%; color: #242B40; width: 36px; height: 36px; padding: 0 12px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-angle-left"></i></span></li>';
            }

            let startPage = Math.max(1, pagination.current_page - 2);
            let endPage = Math.min(pagination.current_page + 2, pagination.last_page);
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = pagination.current_page === i ? 'active' : '';
                const activeStyle = activeClass ?
                    'background-color: #242B40; color: white; border: 1px solid #242B40; border-radius: 50%; width: 36px; height: 36px; padding: 0 12px; display: flex; align-items: center; justify-content: center;' :
                    'border: 1px solid #242B40; color: #242B40; border-radius: 50%; width: 36px; height: 36px; padding: 0 12px; display: flex; align-items: center; justify-content: center;';
                paginationHtml +=
                    `<li class="page-item ${activeClass}"><button class="page-link" style="${activeStyle}" onclick="searchProperties(${i}, ${isModal})">${i}</button></li>`;
            }

            if (pagination.next_page_url) {
                paginationHtml +=
                    `<li class="page-item"><button class="page-link" style="border: 1px solid #242B40; border-radius: 50%; color: #242B40; width: 36px; height: 36px; padding: 0 12px; display: flex; align-items: center; justify-content: center;" onclick="searchProperties(${pagination.current_page + 1}, ${isModal})"><i class="fas fa-angle-right"></i></button></li>`;
            } else {
                paginationHtml +=
                    '<li class="page-item disabled"><span class="page-link" style="border: 1px solid #242B40; border-radius: 50%; color: #242B40; width: 36px; height: 36px; padding: 0 12px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-angle-right"></i></span></li>';
            }
            paginationHtml += '</ul></nav>';
            document.getElementById('pagination').innerHTML = paginationHtml;

            pagegobal = pagination.current_page;
        }

        function strTitle(cadena) {
            return cadena.split(' ').map(function(palabra) {
                return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
            }).join(' ');
        }

        // Helper para generar el badge de zona cardinal
        function buildZoneBadge(cardinalZone) {
            if (!cardinalZone) return '';
            const zoneLabels = {
                norte:'Norte', sur:'Sur', este:'Este', oeste:'Oeste', centro:'Centro',
                noreste:'Noreste', noroeste:'Noroeste', sureste:'Sureste', suroeste:'Suroeste'
            };
            const zoneKey = cardinalZone.toLowerCase().trim();
            if (!zoneLabels[zoneKey]) return '';
            return `<span class="zone-badge zone-${zoneKey}" style="margin-left: 6px;">
                <svg width="7" height="7" viewBox="0 0 10 10" fill="currentColor" style="flex-shrink:0;"><circle cx="5" cy="5" r="5"/></svg>
                ${zoneLabels[zoneKey]}
            </span>`;
        }


        function buildTypeBadge(typeName, listingtype) {
            if (!typeName) return '';
            const lowerName = typeName.toLowerCase();
            let color = '#242B40';
            if (lowerName.includes('terreno')) color = '#28a745';
            else if (listingtype == 40) color = '#fd7e14';
            return `<span style="background:${color};color:#fff;font-size:11px;padding:2px 8px;border-radius:10px;font-weight:500;vertical-align:middle;">${typeName}</span>`;
        }

        function buildHorizontalPropertyHTML(property, indexProperty) {
            const isTerrain = property.type_name && property.type_name.toLowerCase().includes('terreno');
            const isProject = property.listingtype == 40;

            let aliquotInfo = property.aliquot > 0 ?
                `<p class="card-text" style="font-family: 'Sharp Grotesk', sans-serif;"><strong>Alícuota:</strong> $${property.aliquot}</p>` :
                '';
            let phoneNumber = '593967867998';
            let phoneNumberWhatsapp = '593967867998';
            let transactionType = "venta";
            if (property.listingtypestatus.includes('rent') || property.listingtypestatus.includes('alquilar')) {
                phoneNumber = '593967867998';
                transactionType = "alquiler";
            }

            let whatsappMessage = encodeURIComponent(
                `Hola, Grupo Housing estoy interesado en ${transactionType === "venta" ? "comprar" : "rentar"} esta propiedad: ${property.product_code}`
            );

            let images = property.images.split('|');
            let carouselItems = '';

            images.forEach((image, index) => {
                let activeClass = index === 0 ? 'active' : '';
                carouselItems += `
                <div class="carousel-item ${activeClass}">
                    <img src="${image}" class="d-block w-100 carousel-image" style="height:330px" loading="lazy" alt="${property.listing_title} - img ${index+1}">
                </div>`;
            });

            let areaInfo = '';
            if (property.construction_area > 0) {
                areaInfo = `${property.construction_area} m<sup>2</sup>`;
            }
            let landArea = '';
            if (property.land_area > 0) {
                landArea = `${property.land_area} m<sup>2</sup>`;
            }
            let frontArea = '';
            if (property.Front > 0) {
                frontArea = `${property.Front} m<sup>2</sup>`;
            }
            let fundArea = '';
            if (property.Fund > 0) {
                fundArea = `${property.Fund} m<sup>2</sup>`;
            }

            let property_price = "";
            let formattedPrice = new Intl.NumberFormat('es-EC', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(property.property_price);

            if(property.customized_price != null){
                property_price = property.customized_price;
            } else {
                property_price = formattedPrice;
            }

            // Badge de zona cardinal
            const zoneBadge = buildZoneBadge(property.cardinal_zone);
            const typeBadge = buildTypeBadge(property.type_name, property.listingtype);

            return `<article class="col-12 my-1 property-item" style="padding-left: 0px !important; padding-right: 0px !important;">
        <div class="card mb-3 rounded-0">
            <div class="row g-0 d-flex">
                <div class="col-md-4">
                    <a href="/propiedad/${property.slug}" style="text-decoration: none;">
                        <div id="carousel${property.id}" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                ${carouselItems}
                            </div>
                            <a class="carousel-control-prev" href="#carousel${property.id}" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Anterior</span>
                            </a>
                            <a class="carousel-control-next" href="#carousel${property.id}" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Siguiente</span>
                            </a>
                        </div>
                    </a>
                </div>
                <div class="col-md-8 px-5 py-3 padding-mobile-0 position-relative">
                    <div class="position-absolute" style="font-family: 'Sharp Grotesk', sans-serif;top: 0px; right: 0px; background-color: #242B40; color: #ffffff; border-radius: 0px 0px 0px 25px !important;">
                        <p class="m-0 py-3 px-3 h5">Cod: ${property.product_code}</p>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <h2 class="h5 text-muted order-2" style="font-family: 'Sharp Grotesk', sans-serif; font-weight: 300;">
                                <i class="fas fa-map-marker-alt"></i>${property.sector ? ` ${property.sector},` : ''} ${property.city}, ${property.state}
                            </h2>
                            <p class="order-3">
                                ${zoneBadge}
                            </p>
                            <p class="order-4 ml-1">
                                ${typeBadge}
                            </p>
                        </div>
                        <a href="/propiedad/${property.slug}" class="text-dark order-1" style="text-decoration: none;">
                            <h3 class="card-title" style="font-family: 'Sharp Grotesk', sans-serif; font-size: 1.4rem; padding-right: 60px; font-weight: 500;">${property.listing_title.charAt(0).toUpperCase() + property.listing_title.slice(1).toLowerCase()}</h3>
                        </a>
                        <p class="card-text" style="font-weight:500; font-size: 23px; font-family: 'Sharp Grotesk', sans-serif;">${property_price}</p>
                        ${aliquotInfo}
                        <h4 class="h6 description-clamp">${property.listing_description}</h4>
                        <hr>
                        <div class="row align-items-center">
                            <div class="col-sm-8 d-flex justify-content-around">
                                ${isProject ? `<div class="d-flex align-items-center justify-content-center w-100 border-end characteristics">
                                                <div>
                                                    <img width="50px" height="50px" src="{{ asset('img/dormitorios.png') }}" alt="Unidades del proyecto ${property.product_code}" title="Unidades del proyecto ${property.product_code}">
                                                    <h4 class="p-0 m-0" style="font-weight: 600; font-size: 15px">${property.units_count} unid.</h4>
                                                </div>
                                            </div>` :
                                  (!isTerrain && property.bedroom > 0 ? `<div class="d-flex align-items-center justify-content-center w-100 border-end characteristics">
                                                <div>
                                                    <img width="50px" height="50px" src="{{ asset('img/dormitorios.png') }}" alt="Icono dormitorios de propiedad ${property.product_code}" title="Icono dormitorios de propiedad ${property.product_code}">
                                                    <h4 class="p-0 m-0" style="font-weight: 600; font-size: 15px">${property.bedroom} hab.</h4>
                                                </div>
                                            </div>` : '')}
                                ${!isTerrain && property.bathroom > 0 ? `<div class="d-flex align-items-center justify-content-center w-100 border-end characteristics">
                                                <div>
                                                    <img width="50px" height="50px" src="{{ asset('img/banio.png') }}" alt="Icono de baños de propiedad ${property.product_code}" title="Icono de baños de la propiedad ${property.product_code}">
                                                    <h4 class="p-0 m-0" style="font-weight: 600; font-size: 15px">${property.bathroom} bañ.</h4>
                                                </div>
                                            </div>` : ''}
                                ${!isTerrain && property.garage > 0 ? `<div class="d-flex align-items-center justify-content-center w-100 border-end characteristics">
                                                <div>
                                                    <img width="50px" height="50px" src="{{ asset('img/estacionamiento.png') }}" alt="Icono de estacionamientos de la propiedad ${property.product_code}" title="Icono de estacionamientos de la propiedad ${property.product_code}">
                                                    <h4 class="p-0 m-0" style="font-weight: 600; font-size: 15px">${property.garage} estac.</h4>
                                                </div>
                                            </div>` : ''}
                                ${areaInfo ? `<div class="d-flex align-items-center justify-content-center w-100 border-end characteristics">
                                                <div>
                                                    <img width="40px" height="40px" src="{{ asset('img/icono-de-area-de-construccion.png') }}" alt="Icono de área de construcción de la propiedad ${property.product_code}" title="Icono de área de construcción de la propiedad ${property.product_code}">
                                                    <h4 class="p-0 m-0 pt-2" style="font-weight: 600; font-size: 15px">${areaInfo}</h4>
                                                </div>
                                            </div>` : ''}
                                ${landArea ? `<div class="d-flex align-items-center justify-content-center w-100 characteristics">
                                                <div>
                                                    <img width="50px" height="50px" src="{{ asset('img/area.png') }}" alt="Icono de área de terreno de la propiedad ${property.product_code}" title="Icono de área de terreno de la propiedad ${property.product_code}">
                                                    <h4 class="p-0 m-0" style="font-weight: 600; font-size: 15px">${landArea}</h4>
                                                </div>
                                            </div>` : ''}
                                ${isTerrain && frontArea ? `<div class="d-flex align-items-center justify-content-center w-100 characteristics">
                                                <div>
                                                    <img width="50px" height="50px" src="{{ asset('img/area.png') }}" alt="">
                                                    <h4 class="p-0 m-0" style="font-weight: 600; font-size: 15px">Frente: ${frontArea}</h4>
                                                </div>
                                            </div>` : ''}
                                ${isTerrain && fundArea ? `<div class="d-flex align-items-center justify-content-center w-100 characteristics">
                                                <div>
                                                    <img width="50px" height="50px" src="{{ asset('img/area.png') }}" alt="">
                                                    <h4 class="p-0 m-0" style="font-weight: 600; font-size: 15px">Fondo: ${fundArea}</h4>
                                                </div>
                                            </div>` : ''}
                            </div>
                            <div class="col-sm-4 d-flex gap-3">
                                <div class="w-100 d-flex align-items-center">
                                <a href="tel:${phoneNumber}" onclick="gtag_report_conversion('tel:${phoneNumber}')" class="btn btn-outline-primary rounded-pill w-100 d-flex align-items-center">
                                    <i class="fas fa-phone-alt me-2 mr-1"></i>Llamar
                                </a>
                            </div>
                            <div class="w-100 d-flex align-items-center ml-2">
                                <a onclick="gtag_report_conversion_whatsapp('https://wa.me/${phoneNumberWhatsapp}?text=${whatsappMessage}')" href="https://wa.me/${phoneNumberWhatsapp}?text=${whatsappMessage}" class="btn btn-outline-success rounded-pill w-100 d-flex align-items-center">
                                    <i class="fab fa-whatsapp me-2 mr-1"></i> WhatsApp
                                </a>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>`;
        }


        function buildCardPropertyHTML(property, indexProperty) {
            const isTerrain = property.type_name && property.type_name.toLowerCase().includes('terreno');
            const isProject = property.listingtype == 40;

            let aliquotInfo = property.aliquot > 0 ?
                `<p class="card-text" style="font-family: 'Sharp Grotesk', sans-serif;"><strong>Alícuota:</strong> $${property.aliquot}</p>` :
                '';

            let phoneNumber = '593967867998';
            let phoneNumberWhatsapp = '593967867998';
            let transactionType = "venta";
            if (property.listingtypestatus.includes('rent') || property.listingtypestatus.includes('alquilar')) {
                phoneNumber = '593967867998';
                transactionType = "alquiler";
            }

            let whatsappMessage = encodeURIComponent(
                `Hola, Grupo Housing estoy interesado en ${transactionType === "venta" ? "comprar" : "rentar"} esta propiedad: ${property.product_code}`
            );

            let images = property.images.split('|');
            let carouselItems = '';

            images.forEach((image, index) => {
                let activeClass = index === 0 ? 'active' : '';
                carouselItems += `
                <div class="carousel-item ${activeClass}">
                    <img src="${image}" class="d-block w-100 carousel-image" style="height:330px" loading="lazy">
                </div>`;
            });

            let areaInfo = '';
            if (property.construction_area > 0) {
                areaInfo = `${property.construction_area} m<sup>2</sup>`;
            }

            let landArea = '';
            if (property.land_area > 0) {
                landArea = `${property.land_area} m<sup>2</sup>`;
            }

            let frontArea = '';
            if (property.Front > 0) {
                frontArea = `${property.Front} m<sup>2</sup>`;
            }
            let fundArea = '';
            if (property.Fund > 0) {
                fundArea = `${property.Fund} m<sup>2</sup>`;
            }

            let formattedDescription = property.listing_description ?
                property.listing_description.toLowerCase().replace(/(^\w{1})|(\.\s*\w{1})/g, letter => letter.toUpperCase())
                .substring(0, 120) + '...' :
                'Descripción no disponible.';

            let property_price = "";
            let formattedPrice = new Intl.NumberFormat('es-EC', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(property.property_price);

            if(property.customized_price != null){
                property_price = property.customized_price;
            } else {
                property_price = formattedPrice;
            }

            // Badge de zona cardinal
            const zoneBadge = buildZoneBadge(property.cardinal_zone);
            const typeBadge = buildTypeBadge(property.type_name, property.listingtype);

            return `
    <article class="col-12 col-md-4 mb-4 property-item">
        <div class="card h-100">
            <a href="/propiedad/${property.slug}" style="text-decoration: none;">
                <div id="carousel${property.id}" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        ${carouselItems}
                    </div>
                    <a class="carousel-control-prev" href="#carousel${property.id}" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Anterior</span>
                    </a>
                    <a class="carousel-control-next" href="#carousel${property.id}" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Siguiente</span>
                    </a>
                </div>
            </a>
            <div class="card-body flex-grow-1 d-flex flex-column">
                <div class="position-absolute" style="top: 0px; right: 0px; background-color: #242B40; color: #ffffff; border-radius: 0px 0px 0px 25px;">
                    <p class="m-0 py-2 px-2 h6" style="font-family: 'Sharp Grotesk', sans-serif;">Cod: ${property.product_code}</p>
                </div>
                <h2 class="h6" style="font-family: 'Sharp Grotesk', sans-serif; font-weight: 300;">
                    <i class="fas fa-map-marker-alt"></i> ${property.sector ? `${property.sector},` : ''} ${property.city}, ${property.state}${zoneBadge}
                </h2>
                <a href="/propiedad/${property.slug}" class="text-dark" style="text-decoration: none;">
                    <h3 class="card-title" style="font-family: 'Sharp Grotesk', sans-serif; font-size: 1.2rem; font-weight: 500;">
                        ${property.listing_title.charAt(0).toUpperCase() + property.listing_title.slice(1).toLowerCase()}
                    </h3>
                </a>
                ${typeBadge ? `<div class="mb-2">${typeBadge}</div>` : ''}
                ${aliquotInfo}
                <h4 class="card-text h6" style="font-family: 'Sharp Grotesk', sans-serif; font-weight: 100; font-size: 15px; text-align: justify">${formattedDescription}</h4>
                <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-wrap">
                            ${isProject ? `<div class="characteristics text-center pl-2">
                                <img width="30px" height="30px" src="{{ asset('img/dormitorios.png') }}" alt="Unidades del proyecto ${property.product_code}" title="Unidades del proyecto ${property.product_code}">
                                <p style="font-weight: 600; font-size: 15px">${property.units_count} unid.</p>
                            </div>` :
                              (!isTerrain && property.bedroom > 0 ? `<div class="characteristics text-center pl-2">
                                <img width="30px" height="30px" src="{{ asset('img/dormitorios.png') }}" alt="Icono de dormitorios de la propiedad ${property.product_code}" title="Icono de dormitorios de la propiedad ${property.product_code}">
                                <p style="font-weight: 600; font-size: 15px">${property.bedroom}</p>
                            </div>` : '')}
                            ${!isTerrain && property.bathroom > 0 ? `<div class="characteristics text-center pl-2">
                                <img width="30px" height="30px" src="{{ asset('img/banio.png') }}" alt="Icono de baños de la propiedad ${property.product_code}" title="Icono de baños de la propiedad ${property.product_code}">
                                <p style="font-weight: 600; font-size: 15px">${property.bathroom}</p>
                            </div>` : ''}
                            ${!isTerrain && property.garage > 0 ? `<div class="characteristics text-center pl-2">
                                <img width="30px" height="30px" src="{{ asset('img/estacionamiento.png') }}" alt="Icono de estacionamientos de la propiedad ${property.product_code}" title="Icono de estacionamientos de la propiedad ${property.product_code}">
                                <p style="font-weight: 600; font-size: 15px">${property.garage}</p>
                            </div>` : ''}
                            ${areaInfo ? `<div class="characteristics text-center pl-2">
                                <img width="30px" height="30px" src="{{ asset('img/icono-de-area-de-construccion.png') }}" alt="Icono de area de construccion de la propiedad ${property.product_code}" title="Icono de area de construccion de la propiedad ${property.product_code}">
                                <p style="font-weight: 600; font-size: 15px">${areaInfo}</p>
                            </div>` : ''}
                            ${landArea ? `<div class="characteristics text-center pl-2">
                                <img width="30px" height="30px" src="{{ asset('img/area.png') }}" alt="Icono de area de terreno de la propiedad ${property.product_code}" title="Icono de area de terreno de la propiedad ${property.product_code}">
                                <p style="font-weight: 600; font-size: 15px">${landArea}</p>
                            </div>` : ''}
                            ${isTerrain && frontArea ? `<div class="characteristics text-center pl-2">
                                <img width="30px" height="30px" src="{{ asset('img/area.png') }}" alt="">
                                <p style="font-weight: 600; font-size: 15px">Frente: ${frontArea}</p>
                            </div>` : ''}
                            ${isTerrain && fundArea ? `<div class="characteristics text-center pl-2">
                                <img width="30px" height="30px" src="{{ asset('img/area.png') }}" alt="">
                                <p style="font-weight: 600; font-size: 15px">Fondo: ${fundArea}</p>
                            </div>` : ''}
                        </div>
                        <p class="card-text" style="font-weight: 500; font-size: 23px; font-family: 'Sharp Grotesk', sans-serif;">${property_price}</p>
                    </div>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-center gap-3 mt-3">
                            <div class="w-100 d-flex justify-content-center">
                                <a href="tel:${phoneNumber}" onclick="gtag_report_conversion('tel:${phoneNumber}')" class="btn btn-outline-primary rounded-pill w-75 d-flex justify-content-center align-items-center">
                                    <i class="fas fa-phone-alt me-2 mr-1"></i>Llamar
                                </a>
                            </div>
                            <div class="w-100 d-flex justify-content-center">
                                <a onclick="gtag_report_conversion_whatsapp('https://wa.me/${phoneNumberWhatsapp}?text=${whatsappMessage}')" href="https://wa.me/${phoneNumberWhatsapp}?text=${whatsappMessage}" class="btn btn-outline-success rounded-pill w-75 d-flex justify-content-center align-items-center">
                                    <i class="fab fa-whatsapp me-2 mr-1"></i> WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </article>`;
        }

        function clearSearch(isModal) {
            const searchTermId = isModal ? 'searchTermModal' : 'searchTerm';
            const bedroomsId = isModal ? 'bedroomsModal' : 'bedrooms';
            const bathroomsId = isModal ? 'bathroomsModal' : 'bathrooms';
            const garageId = isModal ? 'garageModal' : 'garage';
            const minPriceId = isModal ? 'minPriceModal' : 'minPrice';
            const maxPriceId = isModal ? 'maxPriceModal' : 'maxPrice';
            const minConstructionAreaId = isModal ? 'constructionAreaMinModal' : 'constructionAreaMin';
            const maxConstructionAreaId = isModal ? 'constructionAreaMaxModal' : 'constructionAreaMax';
            const minLandAreaId = isModal ? 'landAreaMinModal' : 'landAreaMin';
            const maxLandAreaId = isModal ? 'landAreaMaxModal' : 'landAreaMax';
            const cityId = isModal ? 'cityModal' : 'city';
            const stateId = isModal ? 'stateModal' : 'state';
            const sectorId = isModal ? 'sectorModal' : 'sector';
            const propertyTypeId = isModal ? 'propertyTypeModal' : 'propertyType';
            const propertyStatusId = isModal ? 'propertyStatusModal' : 'propertyStatus';

            document.getElementById(searchTermId).value = '';
            document.getElementById(bathroomsId).value = '';
            document.getElementById(garageId).value = '';
            document.getElementById(minPriceId).value = '';
            document.getElementById(maxPriceId).value = '';
            document.getElementById(minConstructionAreaId).value = '';
            document.getElementById(maxConstructionAreaId).value = '';
            document.getElementById(minLandAreaId).value = '';
            document.getElementById(maxLandAreaId).value = '';
            document.getElementById(cityId).value = '';
            document.getElementById(stateId).value = '';
            document.getElementById(propertyTypeId).selectedIndex = 0;
            document.getElementById(propertyStatusId).selectedIndex = 0;

            // Limpiar zona cardinal
            document.getElementById('cardinalZone').value = '';
            document.getElementById('zonaModal').value = '';
            selectedZoneValue = '';
            highlightZoneOnMap('');

            if (isModal) {
                typeIdsArrayModal = [];
            } else {
                typeIdsArray = [];
            }

            searchProperties(1, isModal);
        }

        document.getElementById('searchFormDesktop').addEventListener('submit', function(event) {
            event.preventDefault();
            searchProperties(1, false);
        });

        document.getElementById('searchFormModal').addEventListener('submit', function(event) {
            event.preventDefault();
            searchProperties(1, true);
        });

        function generateDynamicContentForPropertyCode(propertyCode) {
            let content = `
                <section class="mt-4">
                    <h2>¿Qué puedes encontrar sobre la propiedad ${propertyCode}?</h2>
                    <h3>La propiedad con código ${propertyCode} forma parte de nuestro exclusivo catálogo de inmuebles en Grupo Housing. Nuestro equipo de profesionales está listo para brindarte toda la información detallada que necesitas sobre esta propiedad, incluyendo características, ubicación, precio y condiciones especiales.</h3>
                </section>
                <section class="mt-4">
                    <h2>¿Cómo obtener más información de la propiedad ${propertyCode}?</h2>
                    <h3>Para conocer todos los detalles de la propiedad ${propertyCode}, puedes contactarnos directamente por teléfono, WhatsApp o visitando nuestras redes sociales. En Grupo Housing nos especializamos en brindar un servicio personalizado, donde te acompañamos desde la consulta inicial hasta la concreción de tu compra o alquiler.</h3>
                </section>
                <section class="mt-4">
                    <h2>¿Por qué elegir Grupo Housing para la propiedad ${propertyCode}?</h2>
                    <h3>Al consultar sobre la propiedad ${propertyCode} con Grupo Housing, te garantizamos transparencia total en la información, asesoramiento profesional y acompañamiento integral en todo el proceso. Contamos con años de experiencia en el mercado inmobiliario ecuatoriano y un equipo comprometido con encontrar la solución habitacional perfecta para ti.</h3>
                </section>
            `;
            let containerDynamicContent = document.getElementById('dynamic_content');
            if (containerDynamicContent) {
                containerDynamicContent.innerHTML = content;
            }
        }

        function generateDynamicDescriptionForPropertyCode(propertyCode, exists = true) {
            let descriptionText = '';
            if (exists) {
                descriptionText = `En Grupo Housing, te brindamos acceso directo a la información de la <b>propiedad ${propertyCode}</b>. Nuestro equipo especializado está preparado para proporcionarte todos los detalles sobre esta <b>propiedad código ${propertyCode}</b>, incluyendo características únicas, ubicación estratégica y condiciones especiales. <strong>Contacta ahora</strong> para una asesoría personalizada sobre la <b>propiedad ${propertyCode}</b> y descubre por qué es la opción ideal para ti.`;
            } else {
                descriptionText = `No hemos encontrado la propiedad con el código <b>${propertyCode}</b>. Por favor verifica el código o contáctanos para ayudarte a encontrar la opción ideal para ti.`;
            }
            const paragraphContainer = document.getElementById('dynamic-description-paragraph');
            if (paragraphContainer) {
                paragraphContainer.innerHTML = descriptionText;
            }
        }

        function clearSearchModal() {
            const modal = document.getElementById('filtersModal');
            if (!modal) return;
            const textInputs = modal.querySelectorAll('input[type="text"], input[type="number"]');
            textInputs.forEach(input => {
                if (input.type === 'number') {
                    input.value = 0;
                } else {
                    input.value = '';
                }
            });
            const checkboxes = modal.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = false);
            const selects = modal.querySelectorAll('select');
            selects.forEach(select => select.selectedIndex = 0);
            const searchInput = modal.querySelector('#searchTermModal');
            if (searchInput) searchInput.value = '';

            // Limpiar zona
            selectedZoneValue = '';
            document.getElementById('cardinalZone').value = '';
            highlightZoneOnMap('');

            searchProperties();
        }

        function capitalizeFirstLetter(str) {
            if (!str || typeof str !== 'string') return str;
            return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        }

        function buildLocationString(searchTerm, city, state) {
            let locationParts = [];
            if (searchTerm && searchTerm.trim() !== '') {
                locationParts.push(capitalizeFirstLetter(searchTerm.trim()));
            }
            if (city && city.trim() !== '' && city.toLowerCase() !== searchTerm?.toLowerCase()) {
                locationParts.push(capitalizeFirstLetter(city.trim()));
            }
            if (state && state.trim() !== '' && 
                state.toLowerCase() !== city?.toLowerCase() && 
                state.toLowerCase() !== searchTerm?.toLowerCase()) {
                locationParts.push(capitalizeFirstLetter(state.trim()));
            }
            return locationParts.length > 0 ? locationParts.join(', ') : 'Ecuador';
        }

        function buildLocationSlugForURL(searchTerm, city, state) {
            if (searchTerm && searchTerm.trim() !== '') {
                return searchTerm.toLowerCase().replace(/\s+/g, '-');
            }
            if (city && city.trim() !== '') {
                return city.toLowerCase().replace(/\s+/g, '-');
            }
            if (state && state.trim() !== '') {
                return state.toLowerCase().replace(/\s+/g, '-');
            }
            return '';
        }

        function generateDynamicContent(property_type, operation, searchTerm = '', city = '', state = '') {
            let content = '';
            let qaPairs = [];
            
            if (property_type && operation) {
                let propertyTypeDisplay = property_type.replace(/[-_]/g, ' ');
                let operationDisplay = (operation === 'venta' || operation === 'renta') ? operation : 'general';
                let locationDisplay = buildLocationString(searchTerm, city, state);
                
                let keywordV1 = `${propertyTypeDisplay} en ${operationDisplay} en ${locationDisplay}`;
                let keywordV2 = `${operationDisplay} de ${propertyTypeDisplay} en ${locationDisplay}`;
                
                qaPairs.push({
                    question: `¿Por qué ${operationDisplay === 'venta' ? 'comprar' : operationDisplay === 'renta' ? 'alquilar' : 'buscar'} ${keywordV1}?`,
                    answer: `Encontrar ${keywordV1} ofrece diversas ventajas. Se presenta como una opción inmobiliaria atractiva debido a su notable crecimiento turístico, lo que impulsa una economía local en expansión y ofrece oportunidades de inversión con potencial de valorización.`
                });
                
                qaPairs.push({
                    question: `¿Dónde puedo ${operationDisplay === 'venta' ? 'comprar' : operationDisplay === 'renta' ? 'alquilar' : 'encontrar'} ${keywordV1}?`,
                    answer: `Puedes encontrar una amplia variedad de ${keywordV1} en nuestra inmobiliaria. En Grupo Housing, comprendemos la importancia de esta decisión y nos comprometemos a brindarte un servicio integral y personalizado. Nuestro equipo de profesionales te acompañará en cada paso.`
                });
                
                qaPairs.push({
                    question: `¿Cómo puedo ${operationDisplay === 'venta' ? 'comprar' : operationDisplay === 'renta' ? 'alquilar' : 'buscar'} ${keywordV1}?`,
                    answer: `Para ${operationDisplay === 'venta' ? 'comprar' : operationDisplay === 'renta' ? 'alquilar' : 'buscar'} ${keywordV1}, puedes definir tu presupuesto y necesidades, explorar opciones en línea mediante nuestro sitio web o contáctarnos directamente en Grupo Housing por teléfono, WhatsApp o redes sociales. Te brindaremos asesoramiento profesional, gestionaremos trámites y te guiaremos en todo el proceso.`
                });
                
                qaPairs.push({
                    question: `Beneficios de la ${keywordV2}`,
                    answer: `La ${keywordV2} representa una excelente oportunidad de inversión en el mercado inmobiliario. Gracias al desarrollo constante de ${locationDisplay}, la demanda se mantiene en crecimiento, lo que favorece la valorización de las propiedades y garantiza una decisión inteligente a mediano y largo plazo.`
                });
            }
            
            qaPairs.forEach(qa => {
                content += `
                    <section class="mt-4">
                        <h2>${qa.question}</h2>
                        <h3>${qa.answer}</h3>
                    </section>
                `;
            });
            
            let containerDynamicContent = document.getElementById('dynamic_content');
            if (containerDynamicContent) {
                containerDynamicContent.innerHTML = content;
            }
        }

        function generateDynamicDescriptionParagraph(property_type, operation, searchTerm = '', city = '', state = '') {
            let descriptionText = '';
            const formattedPropertyType = property_type ? property_type.replace(/[-_]/g, ' ').toLowerCase() : '';
            const formattedOperation = operation ? (operation === 'venta' ? 'venta' : 'renta') : '';
            const fullLocation = buildLocationString(searchTerm, city, state);
            const formattedLocation = fullLocation !== 'Ecuador' ? fullLocation : '';
            let propertyTypeText = formattedPropertyType || 'propiedades';
            let operationText = formattedOperation ? `de ${formattedOperation}` : '';
            let locationText = formattedLocation ? `en ${formattedLocation}` : 'en Ecuador';
            
            if (formattedPropertyType && formattedOperation && formattedLocation) {
                descriptionText = `En Grupo Housing, te ofrecemos una selecta variedad de <b>${propertyTypeText} ${operationText} en ${formattedLocation}</b>. Desde acogedoras viviendas hasta exclusivas <b>${propertyTypeText} ${operationText} en ${formattedLocation} nuevas</b> y atractivas de <strong>oportunidad</strong>, nuestro catálogo está diseñado para satisfacer tus necesidades en ${formattedLocation}.`;
            }
            else if (formattedPropertyType && formattedOperation) {
                descriptionText = `Explora nuestra amplia oferta de <b>${propertyTypeText} ${operationText}</b> en diversas ubicaciones ${locationText}. En Grupo Housing, encontrarás desde <b>${propertyTypeText} ${operationText} baratas</b> hasta opciones de lujo, todas con el respaldo y asesoramiento que mereces.`;
            }
            else if (formattedLocation) {
                descriptionText = `Descubre las mejores <b>propiedades de venta en ${formattedLocation}</b> con Grupo Housing. Si buscas <b>casas de venta en ${formattedLocation} de oportunidad</b> o cualquier otro tipo de inmueble, somos tu mejor opción para encontrar tu nuevo hogar o inversión en ${formattedLocation}.`;
            }
            else {
                descriptionText = `Bienvenido al catálogo de Grupo Housing, donde encontrarás una extensa selección de <b>casas de venta en Cuenca</b>. Explora nuestras <b>propiedades de venta en Cuenca</b>, incluyendo opciones <b>nuevas</b> y de <b>oportunidad</b> en Cuenca, Azuay, Ecuador.`;
            }
            
            const paragraphContainer = document.getElementById('dynamic-description-paragraph');
            if (paragraphContainer) {
                paragraphContainer.innerHTML = descriptionText;
            }
        }

        function updateDynamicTitle(total, searchParams, isModal, isPropertyCode = false) {
            const searchTerm = document.getElementById(isModal ? 'searchTermModal' : 'searchTerm');
            let metaDescripcion = document.querySelector('meta[name="description"]');
            let keywords = document.querySelector('meta[name="keywords"]');
            let description_banner = document.getElementById('description_banner');

            if (isPropertyCode) {
                const propertyCode = searchTerm.value.trim();
                let contentMetaDescription = "";
                let contentBannerDescription = "";
                let titleComponents = "";
                
                if (total < 1) {
                    contentMetaDescription = `No encontramos la propiedad con código ${propertyCode}. Contáctanos para más información sobre propiedades disponibles.`;
                    contentBannerDescription = `No se encontró la propiedad ${propertyCode}. Te mostramos otras opciones disponibles.`;
                    titleComponents = `Propiedad ${propertyCode} - No encontrada`;
                } else if (total === 1) {
                    contentMetaDescription = `Propiedad ${propertyCode} encontrada. ¡Solicita ahora una visita y descubre todos los detalles! Clic aquí para más información.`;
                    contentBannerDescription = `Propiedad ${propertyCode} encontrada.`;
                    titleComponents = `Propiedad ${propertyCode} - Grupo Housing`;
                } else {
                    contentMetaDescription = `Encontramos ${total} propiedades relacionadas con ${propertyCode}. ¡Descubre todas las opciones disponibles!`;
                    contentBannerDescription = `Se encontraron ${total} propiedades relacionadas con ${propertyCode}.`;
                    titleComponents = `${total} propiedades encontradas para ${propertyCode}`;
                }

                if (metaDescripcion) {
                    metaDescripcion.setAttribute('content', contentMetaDescription);
                    keywords.setAttribute('content', `propiedad ${propertyCode}, código ${propertyCode}, inmueble ${propertyCode}`);
                    description_banner.innerHTML = contentBannerDescription;
                }

                document.title = titleComponents;
                document.querySelector('h1').innerHTML = total === 1 
                    ? `<span style="font-weight: 500">Propiedad</span><span style="font-weight: 100"> ${propertyCode}</span>`
                    : `<span style="font-weight: 500">${total}</span><span style="font-weight: 100"> resultado${total !== 1 ? 's' : ''} para ${propertyCode}</span>`;
                
                return;
            }

            const typeElement = document.getElementById(isModal ? 'propertyTypeModal' : 'propertyType');
            const selectedTypeIndex = typeElement.selectedIndex;
            const typeName = typeElement.options[selectedTypeIndex].text;
            const state = searchParams.get('state');
            const city = searchParams.get('city');
            const sector = searchParams.get('sector');

            let titleSuffix = `propiedades`;
            if (selectedTypeIndex !== 0 && typeName.toLowerCase() !== "tipo de propiedad") {
                titleSuffix = `${typeName.toLowerCase()}`;
            }

            if (searchParams.get('normalized_status')) {
                titleSuffix += ` en ${searchParams.get('normalized_status')}`;
            } else {
                titleSuffix += ` en general`;
            }

            const fullLocation = buildLocationString(searchTerm.value, city, state);
            if(fullLocation !== 'Ecuador'){
                titleSuffix += ` en ${fullLocation}`;
            } else {
                let locationDetails = [];
                if (sector) locationDetails.push(capitalizeFirstLetter(sector));
                if (locationDetails.length) {
                    titleSuffix += ` en ${locationDetails.join(", ")}`;
                }
            }

            if (metaDescripcion) {
                let contentMetaDescription = "";
                let contentBannerDescription = "";

                if(total < 1){
                    contentMetaDescription = 'Encuentre la casa de sus sueños, donde los sueños se hacen realidad 😉 Contamos con una gran variedad de propiedades disponibles ¡Contáctenos!';
                    contentBannerDescription = 'Descubre todas las propiedades en venta y renta que Grupo Housing tiene para ti';
                } else{
                    contentMetaDescription = `Encontramos ${total} ${strTitle(titleSuffix)} disponibles. ¡Solicita ahora una visita y descubre tu opción ideal! Clic aquí para más información`;
                    contentBannerDescription = `Hemos encontrado ${total} ${replaceFirstEnWithDe(titleSuffix)} disponibles.`;
                }
                
                metaDescripcion.setAttribute('content', contentMetaDescription);
                keywords.setAttribute('content', titleSuffix);
                description_banner.innerHTML = contentBannerDescription;
            }

            let titleComponents = `${total} ${titleSuffix} en Ecuador`;
            document.title = `${titleComponents}`;
            document.querySelector('h1').innerHTML =
                `<span style="font-weight: 500">${total}</span><span style="font-weight: 100"> ${titleSuffix}</span>`;        
        }


                window.searchProperties = function(page = 1, isModal = false, forcePropertyCode = '') {
            page = parseInt(page);
 
            // Si viene un código forzado (desde inicialización), usarlo
            // Si no, leer el campo searchTerm y verificar si es un código
            const rawCode = forcePropertyCode.trim();
            const searchTermValue = rawCode || (document.getElementById(isModal ? 'searchTermModal' : 'searchTerm')?.value || '');
            const isPropertyCode = rawCode !== ''
                ? /^[0-9]{3,10}$/.test(rawCode)
                : /^[0-9]{3,10}$/.test(searchTermValue.trim());
 
            var currentTypeIds = isModal ? typeIdsArrayModal : typeIdsArray;
            var selectElement = isModal ? document.getElementById('propertyTypeModal') : document.getElementById('propertyType');
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var typeName = selectedOption.text;
            var typeValue = selectedOption.value;
 
            var statusElement = isModal ? document.getElementById('propertyStatusModal') : document.getElementById('propertyStatus');
            var statusValue = statusElement.value;
            var statusText = statusElement.options[statusElement.selectedIndex].text;
 
            const cityValue  = document.getElementById(isModal ? 'cityModal'  : 'city')?.value  || '';
            const stateValue = document.getElementById(isModal ? 'stateModal' : 'state')?.value || '';
 
            const cardinalZoneValue = isModal
                ? (document.getElementById('zonaModal')?.value    || '')
                : (document.getElementById('cardinalZone')?.value || '');
 
            if (!typeValue) {
                typeName = 'propiedades';
            } else {
                typeName = typeName.toLowerCase().replace(/\s+/g, '-');
            }
 
            const featuresMap = {
                gymModal:     { field: 'listingcharacteristic',        ids: [99] },
                wifiModal:    { field: 'listinglistservices',           ids: [33,5] },
                poolModal:    { field: 'listinggeneralcharacteristics', ids: [5] },
                cisternModal: { field: 'listingcharacteristic',        ids: [42] },
                terraceModal: { field: 'listinggeneralcharacteristics', ids: [6] },
                gardenModal:  { field: 'listinggeneralcharacteristics', ids: [] }
            };
 
            let listingcharacteristic         = [];
            let listinggeneralcharacteristics  = [];
            let listinglistservices            = [];
 
            Object.keys(featuresMap).forEach(key => {
                const checkbox = document.getElementById(key);
                if (checkbox && checkbox.checked) {
                    const field = featuresMap[key].field;
                    const ids   = featuresMap[key].ids;
                    if (field === 'listingcharacteristic')         listingcharacteristic.push(...ids);
                    if (field === 'listinggeneralcharacteristics') listinggeneralcharacteristics.push(...ids);
                    if (field === 'listinglistservices')           listinglistservices.push(...ids);
                }
            });
 
            // Cuando es búsqueda por código, pasar solo el código y limpiar el resto
            const searchParams = new URLSearchParams({
                searchTerm:    isPropertyCode ? searchTermValue.trim() : searchTermValue,
                bedrooms:      isPropertyCode ? '' : (document.getElementById(isModal ? 'bedroomsModal'           : 'bedrooms')?.value           || ''),
                bathrooms:     isPropertyCode ? '' : (document.getElementById(isModal ? 'bathroomsModal'          : 'bathrooms')?.value          || ''),
                garage:        isPropertyCode ? '' : (document.getElementById(isModal ? 'garageModal'             : 'garage')?.value             || ''),
                min_price:     isPropertyCode ? '' : (document.getElementById(isModal ? 'minPriceModal'           : 'minPrice')?.value           || ''),
                max_price:     isPropertyCode ? '' : (document.getElementById(isModal ? 'maxPriceModal'           : 'maxPrice')?.value           || ''),
                city:          isPropertyCode ? '' : cityValue,
                state:         isPropertyCode ? '' : stateValue,
                sector:        isPropertyCode ? '' : (document.getElementById(isModal ? 'sectorModal'             : 'sector')?.value             || ''),
                construction_area_min: isPropertyCode ? '' : (document.getElementById(isModal ? 'constructionAreaMinModal' : 'constructionAreaMin')?.value || ''),
                construction_area_max: isPropertyCode ? '' : (document.getElementById(isModal ? 'constructionAreaMaxModal' : 'constructionAreaMax')?.value || ''),
                land_area_min:         isPropertyCode ? '' : (document.getElementById(isModal ? 'landAreaMinModal'         : 'landAreaMin')?.value         || ''),
                land_area_max:         isPropertyCode ? '' : (document.getElementById(isModal ? 'landAreaMaxModal'         : 'landAreaMax')?.value         || ''),
                page:              page,
                normalized_status: isPropertyCode ? '' : (document.getElementById(isModal ? 'propertyStatusModal' : 'propertyStatus')?.value || ''),
                is_new:            isPropertyCode ? '' : (document.getElementById('nuevaModal')?.checked ? 1 : ''),
                listyears_min:     isPropertyCode ? '' : (document.getElementById('listyearsmin')?.value || ''),
                listyears_max:     isPropertyCode ? '' : (document.getElementById('listyearsmax')?.value || ''),
                listingcharacteristic:         isPropertyCode ? '' : listingcharacteristic.join(','),
                listinggeneralcharacteristics: isPropertyCode ? '' : listinggeneralcharacteristics.join(','),
                listinglistservices:           isPropertyCode ? '' : listinglistservices.join(','),
                cardinal_zone:     isPropertyCode ? '' : cardinalZoneValue
            });
 
            // URL limpia
            let urlSlug;
            if (isPropertyCode) {
                urlSlug = `/${searchTermValue.trim()}`;
            } else {
                urlSlug = `/${typeName}`;
                if (statusValue) urlSlug += `-en-${statusValue}`;
 
                const locationSlugForURL = buildLocationSlugForURL(searchTermValue, cityValue, stateValue);
                if (locationSlugForURL) urlSlug += `-en-${locationSlugForURL}`;
                if (searchParams.get('min_price')) urlSlug += `-desde-${searchParams.get('min_price')}`;
                if (searchParams.get('max_price')) urlSlug += `-hasta-${searchParams.get('max_price')}`;
            }
 
            // Título
            if (isPropertyCode) {
                document.title = `Propiedad ${searchTermValue.trim()} - Grupo Housing`;
            } else {
                const fullLocation = buildLocationString(searchTermValue, cityValue, stateValue);
                let titleComponents = [typeName.charAt(0).toUpperCase() + typeName.slice(1)];
                if (fullLocation !== 'Ecuador') titleComponents.push(fullLocation);
                document.title = `${titleComponents.join(' en ')} - ${statusText}`;
            }
 
            let queryString = searchParams.toString();
            // Solo adjuntar type_ids cuando NO es búsqueda por código
            if (!isPropertyCode && Array.isArray(currentTypeIds) && currentTypeIds.length > 0) {
                currentTypeIds.forEach(id => {
                    queryString += `&type_ids[]=${encodeURIComponent(id)}`;
                });
            }
 
            let canonical = document.querySelector("link[rel='canonical']");
            window.history.pushState({ path: urlSlug }, '', urlSlug);
            if (canonical) canonical.href = urlSlug;
 
            if (isPropertyCode) {
                generateDynamicContentForPropertyCode(searchTermValue.trim());
                generateDynamicDescriptionForPropertyCode(searchTermValue.trim());
            } else {
                generateDynamicContent(typeName, statusValue, searchTermValue, cityValue, stateValue);
                generateDynamicDescriptionParagraph(typeName, statusValue, searchTermValue, cityValue, stateValue);
            }
 
            axios.get('/api/propertys/list?' + queryString)
                .then(function(response) {
                    const properties = response.data.properties;
                    let html = '';
                    if (properties.length > 0) {
                        properties.forEach((property, index) => {
                            let imageUrl = getImageUrl(property);
                            html += useCardView ? buildCardPropertyHTML(property, index) :
                                buildHorizontalPropertyHTML(property, index);
                        });
                        updateDynamicTitle(response.data.pagination.total, searchParams, isModal, isPropertyCode);
                        if (isPropertyCode) {
                            generateDynamicDescriptionForPropertyCode(searchTermValue.trim(), true);
                        }
                    } else {
                        html = '<section class="row"><p class="text-center fw-bold">No hemos encontrado propiedades</p></section>';
                        updateDynamicTitle(response.data.pagination.total, searchParams, isModal, isPropertyCode);
                        if (isPropertyCode) {
                            generateDynamicDescriptionForPropertyCode(searchTermValue.trim(), false);
                        }
                    }
                    document.getElementById('propertiesList').innerHTML = html;
                    updatePagination(response.data.pagination, isModal);
                })
                .catch(function(error) {
                    console.error('Error en la búsqueda:', error.response ? error.response.data : 'Error desconocido');
                    document.getElementById('propertiesList').innerHTML =
                        '<section class="row"><p class="text-center fw-bold">Error al cargar propiedades.</p></section>';
                });
        };
    </script>
@endsection