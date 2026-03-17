@extends('layouts.dashtw')

@section('firstscript')
    <title> @if(isset($listing->id)) Editar: {{$listing->product_code}} {{$listing->listing_title}} @else Crear @endif Propiedad</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <!-- Load Esri Leaflet from CDN -->
    <script src="https://unpkg.com/esri-leaflet@3.0.10/dist/esri-leaflet.js"></script>
    <!-- Load Esri Leaflet Geocoder from CDN -->
    <link rel="stylesheet" href="https://unpkg.com/esri-leaflet-geocoder@3.1.4/dist/esri-leaflet-geocoder.css" crossorigin="" />
    <script src="https://unpkg.com/esri-leaflet-geocoder@3.1.4/dist/esri-leaflet-geocoder.js" crossorigin=""></script>
<style>
    body{
        scroll-behavior: smooth !important;
    }
    .modal, .modalSuccess {
      transition: opacity 0.25s ease;
    }
    body.modal-active {
      overflow-x: hidden;
      overflow-y: visible !important;
    }

        .loader {
        width: 60px;
        }

        .loader-wheel {
            animation: spin 1s infinite linear;
            border: 2px solid rgba(30, 30, 30, 0.5);
            border-left: 4px solid #fff;
            border-radius: 50%;
            height: 50px;
            margin-bottom: 10px;
            width: 50px;
        }

        .loader-text {
        color: #000;
        font-family: arial, sans-serif;
        }

        .loader-text:after {
        content: 'Loading';
        animation: load 2s linear infinite;
        }

        @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
        }

        @keyframes load {
        0% {
            content: 'Loading';
        }
        33% {
            content: 'Loading.';
        }
        67% {
            content: 'Loading..';
        }
        100% {
            content: 'Loading...';
        }
        }
        #map{
            width: 100% !important;
            height: 500px !important;
            border-radius: 10px !important;
        }
        .leaflet-grab {
            cursor: auto;
        }
</style>

@endsection

@section('content')

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
    
    <div class="loader absolute" style="top: 50%; left: 50%; z-index: 300; display: none">
        <div class="loader-wheel"></div>
        <div class="loader-text"></div>
    </div>

    @if (!empty($fields_to_edit))
        <div class="fixed top-20 right-10 z-50 bg-red-500 text-white rounded-lg shadow-lg p-4 w-72">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Campos incompletos</h3>
            </div>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($fields_to_edit as $field)
                    <li>{{ $field }}</li>
                @endforeach
            </ul>
        </div>
    @endif

 <section class="max-w-4xl p-6 mx-auto bg-white rounded-md shadow-md mt-10">
    
    @if(session('message'))
        <div class="alert-del bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded relative" role="alert">
            {{session('message')}}
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif

    @php
        //to change color to checkboxes
        $bgcheckbox = 'text-red-600';
        if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit"){
            $bgcheckbox = 'text-blue-900';
        }
    @endphp
    
    @if(isset($listing->id))
    <div>
        <div class="flex justify-between">
            <div class="flex">
                <h2 class="text-lg font-semibold @if($currentRouteName == "admin.housing.property.edit") text-blue-900 @else text-red-700 @endif">EDITAR PROPIEDAD <span style="color:darkgray"> Creado: {{$listing->created_at->format('d M y')}} ({{$listing->user->name??'User'}})</span></h2>
                @if(Auth::user()->role == "administrator" && $listing->locked)
                    <form action="{{route('admin.listings.unlocked', $listing->id)}}" method="POST" class="flex justify-center items-center">
                        @csrf
                        <button type="submit" class="bg-gray-300 pl-1 pr-1 rounded font-semibold ml-2">Desbloquear</button>
                    </form>
                @endif
            </div>
            <div class="flex">
                @if(Auth::user()->id == 934 || Auth::user()->id == 147 || Auth::user()->id == 942 || Auth::user()->id == 949 || Auth::user()->id == 936 || Auth::user()->id == 925 || Auth::user()->id == 922 || Auth::user()->id == 175)
                    <div>
                        <button onclick="abrirModal()" class="text-white text-center bg-red-600 rounded px-2 mr-2">
                            <span class="text-sm text-white font-semibold">Actualizar fecha de contacto</span>
                        </button>
                    </div>
                @endif
                <form action="{{ route('home.tw.setoutstanding') }}" method="POST">
                    @csrf
                    <input type="hidden" id="outstanding" name="outstanding" value="{{$listing->id}}">
                    <button type="submit" style="outline: none">
                        <i onclick="setoutstanding()" class="fas fa-star @if($listing->outstanding) text-yellow-400 @else text-gray-400 @endif"></i>
                    </button>
                </form>
            </div>
        </div>
        @if(count($units) > 0)
            <div class="mt-3">
                <button onclick="abrirModalUnits()" class="text-white text-center bg-red-600 rounded px-2 mr-2">
                    <span class="text-white font-semibold">Ver Unidades</span>
                </button>
            </div>
        @endif
    </div>

    {!! Form::model($listing, ['route' => ['admin.listings.update',$listing->id],'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'formsave']) !!}
    @else
    <h2 class="text-lg font-semibold @if($currentRouteName == "admin.housing.property.create") text-blue-800 @else text-red-700 @endif">NUEVA PROPIEDAD EN @if($currentRouteName == "admin.housing.property.create") HOUSING RENT @elseif(Route::currentRouteName() == "admin.promotora.property.create") CASA PROMOTORA @else GRUPO HOUSING @endif</h2>
    {!! Form::open(['route' => 'admin.listings.store','enctype' => 'multipart/form-data', 'id' => 'formsave']) !!}

    <input type="hidden" id="dbID" name="dbID" value="">
    @endif

    {!! Form::hidden('currentUrl', $currentRouteName) !!}

    @if(Auth::user()->role != "administrator")
        @if(isset($listing->status) && $listing->status == "0")
            <div class="bg-green-300 rounded mt-2 mb-2 p-2"><i class="fa-solid fa-circle-info"></i> El Administrador activará la propiedad una vez los campos hayan sido completados.</div>
        @elseif(isset($listing->status) && $listing->status == "1")
            <div class="bg-green-300 rounded mt-2 mb-2 p-2"><i class="fa-solid fa-circle-info"></i> Si necesita desactivar la propiedad, por favor comunicarse con el Administrador indicando la razón.</div>
        @elseif(!isset($listing))
            <div class="bg-green-300 rounded mt-2 mb-2 p-2"><i class="fa-solid fa-circle-info"></i> Completar todos los campos para que el Administrador active la nueva propiedad.</div>
        @endif
    @endif

    <hr class="my-4">
    @if(Route::current()->getName() == "admin.listings.create" || Route::current()->getName() == "home.tw.create" || Route::current()->getName() == "admin.housing.property.create")
    <div class="grid grid-cols-3 gap-x-2 items-center justify-center">
        <div>
            <div>
                <span class="font-semibold">Paso 1</span>
                <div id="paso1" class="bg-red-500" style="width: auto; height: 15px;"></div>
            </div>
        </div>
        <div>
            <div>
                <span class="font-semibold">Paso 2</span>
                <div id="paso2" class="bg-gray-500" style="width: auto; height: 15px;"></div>
            </div>
        </div>
        <div>
            <div>
                <span class="font-semibold">Paso 3</span>
                <div id="paso3" class="bg-gray-500" style="width: auto; height: 15px;"></div>
            </div>
        </div>
    </div>                
    @endif
    
    @if(Route::current()->getName() == "admin.listings.edit" || Route::current()->getName() == "home.tw.edit" || Route::current()->getName() == "admin.housing.property.edit" || Route::current()->getName() == "admin.promotora.property.edit")
        <div class="flex gap-6 justify-center">
            <label id="btnfragment1" style="cursor: pointer" onclick="changefragment('first')" class="@if($currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif($currentRouteName == "admin.promotora.property.edit") bg-red-800 @else bg-red-600 @endif text-white px-5 font-semibold">Información del Propietario</label>
            <label id="btnfragment2" style="cursor: pointer" onclick="changefragment('second')" class="font-semibold">Información del Inmueble</label>
            {{-- <label id="btnfragment3" style="cursor: pointer" onclick="changefragment('third')" class="font-semibold">Informacion adicional</label> --}}
            <label id="btnfragment3" style="cursor: pointer" onclick="changefragment('third')" class="font-semibold">Multimedia</label>
        </div>
    @endif

    <hr class="mt-4">



        @php  
            // class
            $inputs = 'block w-full px-4 py-2 mt-2 text-gray-700 bg-white text-sm border border-gray-300 rounded-0 focus:border-red-500 focus:outline-none focus:ring';

            if(isset($listing->id)) $default = null; else $default = '0';
            if(Auth::user()->role == 'administrator') $readonly = ['class' => 'form-select form-select-sm','readonly'=>'readonly'];
            else $classStatus = ['class' => 'form-select form-select-sm'];
            if(isset($lastcode->product_code)){ $codelast=(int)filter_var($lastcode->product_code, FILTER_SANITIZE_NUMBER_INT); $newcode = $codelast+1; }else $newcode = null;
        @endphp

        <div id="first" class="fragment1">
            {!! Form::hidden('fragment', 'first') !!}
            @if (Route::current()->getName() == "admin.listings.edit" || Route::current()->getName() == "home.tw.edit")
                {!! Form::hidden('edit', 1) !!}
            @endif
            <div class="border px-5 py-4 shadow-sm hover:shadow-md">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                    <div>          
                        {!! Form::label('product_code', 'Codigo',['class' => 'font-semibold']) !!}
                        {!! Form::text('product_code', $newcode, ['class' => $inputs.' font-bold', 'readonly']) !!}
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div>
                        {!! Form::label('available', 'Disponibilidad', ['class' => 'font-semibold']) !!}
                        {!! Form::select('available', ['1' => 'DISPONIBLE', '2' => 'NO DISPONIBLE'], null, ['class' => $inputs, 'onchange' => 'requiredFalse(this.value);', 'required']) !!}
                    </div>
                        <div>       
                            {!! Form::label('listing_type', 'Plan',['class' => 'font-semibold']) !!}
                            {!! Form::select('listing_type',['' => 'Seleccione', '2'=>'PAGO','1'=>'GRATIS'], null, ['class' => $inputs]) !!}
                        </div>
            
                    @if(Auth::user()->role == "administrator")
                        @if(isset($isvalid) && $isvalid || isset($listing) && $listing->status == 1)
                            <div>
                                {!! Form::label('status', 'Status',['class' => 'font-semibold']) !!}
                                {!! Form::select('status',['0'=>'DESACTIVADO','1'=>'ACTIVO'], null, ['class' => $inputs]) !!}
                            </div>
                        @else
                            <div class="text-xs mt-8">
                                <p class="font-semibold">Faltan campos por ser completados para habilitar la opción de Activar</p>
                            </div>
                        @endif
                    @else
                        @isset($listing)
                            <div>
                                {!! Form::label('status', 'Estado', ['class' => 'font-semibold']) !!}
                                    @if($listing->status == "0" || $listing->status == "") 
                                        <div class="flex items-center mt-3 text-gray-500">
                                            <div class="bg-red-500 mr-2 rounded-md" style="width: 10px; height: 10px"></div> 
                                            DESACTIVADA
                                        </div>  
                                    @elseif($listing->status == "1") 
                                        <div class="flex items-center mt-3 text-gray-500">
                                            <div class="bg-green-500 mr-2 rounded-md" style="width: 10px; height: 10px"></div> 
                                            ACTIVADA
                                        </div> 
                                    @endif
                            </div>
                        @else
                            <div>
                                {!! Form::label('status', 'Estado', ['class' => 'font-semibold']) !!}
                                <div class="flex items-center mt-3 text-gray-500">
                                    <div class="bg-red-500 mr-2 rounded-md" style="width: 10px; height: 10px"></div> 
                                    DESACTIVADA
                                </div>
                            </div>
                        @endisset
                    @endif
                </div>
                <div id="numfactura" class="grid grid-cols-1 gap-4 mt-3 sm:gap-6 @if(isset($listing) && $listing->listing_type == 2) block  @else hidden @endif">
                    <div>
                        {!! Form::label('num_factura', '# Número de factura', ['class' => 'font-semibold']) !!}
                        {!! Form::text('num_factura', null, ['class' => $inputs]) !!}
                        <div>
                            <p class="font-semibold text-gray-500" style="font-size: 14px"><i class="fas fa-info-circle"></i> La propiedad no podrá ser activada si no cuenta con el número de factura</p>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="grid grid-cols-1 gap-6 mt-8 sm:grid-cols-3 border px-5 py-4 relative hover:shadow-md">
                <div>      
                    {!! Form::label('owner_name', 'Nombre de Propietario', ['class' => 'font-semibold']) !!}
                    @if(isset($listing) && $listing->locked)
                        {!! Form::text('owner_name', null, ['class' =>  $inputs, 'placeholder' => 'JUAN PEREZ', 'readonly']) !!}
                    @else
                        {!! Form::text('owner_name', null, ['class' =>  $inputs, 'placeholder' => 'JUAN PEREZ']) !!}
                    @endif
                </div>
                <div>
                    {!! Form::label('identification', 'Cédula de Identidad', ['class' => 'font-semibold']) !!}
                    @if(isset($listing) && $listing->locked)
                    {!! Form::text('identification', null, ['class' => $inputs, 'readonly']) !!}
                    @elseif(Auth::user()->email == "developer2@casacredito.com")
                    {!! Form::text('identification', null, ['class' => $inputs, 'placeholder' => '0147859032', 'minlength' => 10, 'maxlength' => 10, 'pattern' => '[0-9]+']) !!}
                    @else
                    {!! Form::text('identification', null, ['class' => $inputs, 'placeholder' => '0147859032', 'minlength' => 10, 'maxlength' => 10, 'pattern' => '[0-9]+']) !!}
                    @endif
                </div>
                <div>
                    {!! Form::label('phone_number', 'Teléfono/Celular', ['class' => 'font-semibold']) !!}
                    @if(isset($listing) && $listing->locked)
                    {!! Form::text('phone_number', null, ['class' => $inputs, 'readonly']) !!}
                    @elseif(Auth::user()->email == "developer2@casacredito.com")
                    {!! Form::text('phone_number', null, ['class' => $inputs, 'placeholder' => '098-467-1223']) !!}
                    @else
                    {!! Form::text('phone_number', null, ['class' => $inputs, 'placeholder' => '098-467-1223']) !!}
                    @endif
                </div>
                <div>
                    {!! Form::label('owner_email', 'Email del Propietario', ['class' => 'font-semibold']) !!}
                    @if(isset($listing) && $listing->locked)
                    {!! Form::email('owner_email', null, ['class' => $inputs, 'placeholder' => 'juanperez@gmail.com', 'readonly']) !!}
                    @elseif(Auth::user()->email == "developer2@casacredito.com")
                    {!! Form::email('owner_email', null, ['class' => $inputs, 'placeholder' => 'juanperez@gmail.com']) !!}
                    @else
                    {!! Form::email('owner_email', null, ['class' => $inputs, 'placeholder' => 'juanperez@gmail.com']) !!}
                    @endif
                </div>
                <div>
                    {!! Form::label('owner_address', 'Dirección del propietario', ['class' => 'font-semibold']) !!}
                    @if(isset($listing) && $listing->locked)
                    {!! Form::text('owner_address', null, ['class' => $inputs, 'placeholder' => 'Calle primaria y secundaria', 'readonly']) !!}
                    @else
                    {!! Form::text('owner_address', null, ['class' => $inputs, 'placeholder' => 'Calle primaria y secundaria']) !!}
                    @endif
                </div>
                <p class="@if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-600 @endif text-white w-64 font-semibold absolute text-center ml-3" style="margin-top: -13px; letter-spacing: 1px">DATOS DEL PROPIETARIO</p>
            </div>
        </div>

        <div id="second" style="display: none" class="fragment2">

            <!--div datos del inmueble-->
            <div class="relative border px-5 py-4 mt-10 hover:shadow-md">
                <div class="gap-4 mt-4 sm:gap-6">
                        {!! Form::label('listing_title', 'Titulo de Propiedad', ['class' => 'font-semibold']) !!}
                        <br>
                        {{-- {!! Form::text('listing_title', null, ['class' => $inputs, 'pattern' => isset($currentRouteName) && ($currentRouteName != "admin.housing.property.create" || $currentRouteName != "admin.housing.property.edit") ? '.{50,60}' : '.{0,150}', 'onkeyup' => 'countCharsTitle(this);', 'placeholder' => 'Ej: Casa en Venta en Cuenca - Sector...']) !!} --}}
                        {!! Form::text('listing_title', null, ['class' => $inputs, 'onkeyup' => 'countCharsTitle(this);setPreviewOnGoogle();', 'placeholder' => 'Ej: Casa en Venta en Cuenca - Sector...']) !!}
                    {{-- @if($currentRouteName != "admin.housing.property.create" && $currentRouteName != "admin.housing.property.edit") --}}
                        <div id="div_info_character" style="background-color: @if(isset($listing) &&  Str::length($listing->listing_title) >= 50 && Str::length($listing->listing_title) <=60) #9AE6B4 @else #FEB2B2 @endif" class="flex p-1 mt-2">
                            <label style="font-weight: 400">
                                Actual <b id="label_count_title"></b> caracteres. (Mínimo 50 - Máximo 60 caracteres)
                            </label>
                        </div>
                    {{-- @endif --}}
                    @if(isset($listing->id) && Auth::id()==123)
                        <a href="{{route('admin.reslug',$listing->id)}}" target="_blank">{{$listing->slug}}</a>            
                    @endif
                </div>
                <div class="gap-4 mt-4 sm:gap-6">
                    {!! Form::label('meta_description', 'Meta Descripcion en Google', ['class' => 'font-semibold']) !!}
                    <br>
                    <p class="bg-gray-200 px-2 py-1 text-sm">Esta descripcion no será visible para el cliente. Solamente se visualizará para Google y su buscador</p>
                    {!! Form::textarea('meta_description', 
                    isset($listing->meta_description) && $listing->meta_description!=null ? $listing->meta_description : '',
                    ['class' => $inputs,'rows' => '3', 'placeholder' => '', 'maxlength' => '150', 'onkeyup' => 'countCharacterMetaDescription();setPreviewOnGoogle()']) !!}
                    <div id="div_info_character_meta" style="background-color: @if(isset($listing) &&  Str::length($listing->meta_description) >= 150 && Str::length($listing->meta_description) <=140) #9AE6B4 @else #FEB2B2 @endif" class="flex p-1 mt-2">
                        <label style="font-weight: 400">
                            Actual <b id="label_count_metadescription"></b> caracteres. (Mínimo 140 - Máximo 150 caracteres)
                        </label>
                    </div>
                </div>
                <div class="gap-4 mt-4 sm:gap-6">
                    <span>Previsualización en Google</span>
                    <div class="bg-gray-100 p-4">
                        <h2 id="preview_title" class="text-blue-500" style="font-weight: 500">Casa de Venta en Cuenca - Sector Totoracocha</h2>
                        <p id="preview_link" class="text-xs text-green-600">https://grupohousing.com/propiedad/casa-de-venta-en-cuenca-sector-totoracocha</p>
                        <p id="preview_meta" class="text-sm text-gray-600">Este es un ejemplo de una meta description para el titulo de casas de venta en cuenca para probar como funciona</p>
                    </div>
                </div>
                <div class="gap-4 mt-4 sm:gap-6">
                    {!! Form::label('listing_description', 'Descripción de la propiedad', ['class' => 'font-semibold']) !!}
                    {!! Form::textarea('listing_description', 
                    isset($listing->listing_description) && $listing->listing_description!=null ? $listing->listing_description : '',
                    ['class' => $inputs,'rows' => '3', 'placeholder' => 'Ej: Casa de Venta en Sector ... en Cuenca. Consta de X metros de construcción, X habitaciones, X baños...']) !!}
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4 sm:gap-6">
                    <div>          
                        {!! Form::label('state', 'Provincia', ['class' => 'font-semibold']) !!}
                        {!! Form::select('state',[''=>'Selecione']+$states->pluck('name','name')->toArray(), null, ['id'=>'state','class' => $inputs ], $optAttrib ) !!}
                    </div>
                    <div>          
                        {!! Form::label('city', 'Ciudad', ['class' => 'font-semibold']) !!}
                        {!! Form::select('city', isset($cities) ? $cities->pluck('name','name')->toArray() : [''=>'Selecione'] , null, ['id'=>'city','class' => $inputs ], $optAttribSector) !!}
                    </div>
                    @if(isset($listing) && $listing->address != null && $listing->sector == null)
                        <div>
                            {!! Form::label('address', 'Sector (Ej: Ricaurte) ', ['class' => 'font-semibold']) !!}
                            {!! Form::text('address', null, ['class' => $inputs]) !!}
                        </div>
                    @else
                        <div>
                            {!! Form::label('sector', 'Parroquia', ['class' => 'font-semibold']) !!}
                            {!! Form::select('sector', isset($sectores) ? $sectores->pluck('name', 'name')->toArray() : [''=>'Seleccione'], null, ['id' => 'sector', 'class' => $inputs]) !!}
                        </div>
                    @endif
                </div>

                @if(!isset($listing) || (isset($listing) && $listing->address != null))
                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <div>
                            {!! Form::label('address', 'Sector', ['class' => 'font-semibold']) !!}
                            <br>
                            <span class="text-gray-500 text-sm font-semibold">Zona donde se encuentra la propiedad. Por ejemplo: Misicata</span>
                            {!! Form::text('address', null, ['class' => $inputs]) !!}
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-3 gap-4 mt-5">
                    <div> 
                        {!! Form::label('listingtype', 'Categoría', ['class' => 'font-semibold']) !!}
                        {!! Form::select('listingtype',$types->pluck('type_title','id'),    null,    ['class' => $inputs]) !!}
                    </div>
                    <div>     
                        {!! Form::label('listingtypestatus', 'Tipo', ['class' => 'font-semibold']) !!}
                        {!! Form::select('listingtypestatus',$categories->pluck('status_title','slug'),    null,    ['class' => $inputs, 'id' => 'listingtypestatus']) !!}
                    </div>
                    <div>  
                        {!! Form::label('listingtagstatus', 'Etiqueta', ['class' => 'font-semibold']) !!}
                        {!! Form::select('listingtagstatus',$tags->pluck('tags_title','id'),    null,    ['class' => $inputs]) !!}
                    </div>
                </div>

                <div id="container-btn-create-unit" class="grid grid-cols-1 mt-4" style="display: none">
                    <button type="button" id="create-unit-btn"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        Crear Unidad
                    </button>
                </div>

                <div class="grid grid-cols-1 mt-5">
                    <div>
                        {!! Form::label('is_dual_operation', '¿La propiedad también está a la renta?', ['class' => 'font-semibold', 'id' => 'dual-operation-label']) !!}
                        <div class="mt-2 space-x-4">
                            <label class="inline-flex items-center">
                                {!! Form::radio('is_dual_operation', 1, old('is_dual_operation', isset($property) ? $property->is_dual_operation : false) == 1) !!}
                                <span class="ml-2">Sí</span>
                            </label>
                
                            <label class="inline-flex items-center">
                                {!! Form::radio('is_dual_operation', 0, old('is_dual_operation', isset($property) ? $property->is_dual_operation : false) == 0) !!}
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="gap-4 mt-4 w-100">
                    <div id="create-unit-container" class="mt-8 p-6 bg-white rounded-lg shadow-md w-100" style="display: none">
                        <h4 class="font-semibold text-2xl mb-6 text-gray-800">Crear Unidad</h4>
                        <div class="mb-4">
                            <label for="unit_name" class="block text-gray-700 text-sm font-bold mb-2">Nombre de la Unidad</label>
                            <input type="text" id="unit_name" name="unit_name"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label for="unit_number" class="block text-gray-700 text-sm font-bold mb-2">Número</label>
                                <input type="text" id="unit_number" name="unit_number"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="unit_type" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Propiedad</label>
                                <select id="unit_type" name="unit_type"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccione un tipo</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->type_title }}</option>
                                    @endforeach
                                </select>
                            </div>
                
                            <div>
                                <label for="unit_floor" class="block text-gray-700 text-sm font-bold mb-2">Piso</label>
                                <input type="number" id="unit_floor" name="unit_floor"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                
                            <div>
                                <label for="unit_area" class="block text-gray-700 text-sm font-bold mb-2">Área m2</label>
                                <input type="number" id="unit_area" name="unit_area" step="0.01"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                
                            <div>
                                <label for="unit_rooms" class="block text-gray-700 text-sm font-bold mb-2">Habitaciones</label>
                                <input type="number" id="unit_rooms" name="unit_rooms"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                
                            <div>
                                <label for="unit_bathrooms" class="block text-gray-700 text-sm font-bold mb-2">Baños</label>
                                <input type="number" id="unit_bathrooms" name="unit_bathrooms"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                
                            <div>
                                <label for="unit_price" class="block text-gray-700 text-sm font-bold mb-2">Precio</label>
                                <input type="number" id="unit_price" name="unit_price" step="0.01"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                
                            <div>
                                <label for="unit_min_price" class="block text-gray-700 text-sm font-bold mb-2">Precio Mínimo</label>
                                <input type="number" id="unit_min_price" name="unit_min_price" step="0.01"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div class="mb-2">
                                <label class="text-sm font-medium text-gray-700">Precio de Renta</label>
                                <input type="number" step="0.01" id="unit_rent_price" placeholder="0.00"
                                    class="w-full border-b bg-transparent focus:outline-none">
                            </div>

                        </div>

                        <div class="grid grid-cols-1">
                            <div class="mb-4">
                                <label for="unit_description" class="block text-gray-700 text-sm font-bold mb-2">Descripción</label>
                                <textarea id="unit_description" name="unit_description"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    rows="4"></textarea>
                            </div>
                        </div>
                
                        <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                            <button type="button" id="cancel-unit-btn"
                                class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                                Cancelar
                            </button>
                            <button type="button" id="save-unit-btn"
                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>



                <div class="grid grid-cols-2 gap-4 mt-4 sm:gap-6 sm:grid-cols-4">
                    <div id="construction_area_container">          
                        {!! Form::label('construction_area', 'Construcción', ['class' => 'font-semibold']) !!}
                        {!! Form::text('construction_area', null, ['class' => $inputs]) !!}
                    </div>
                    <div>          
                        {!! Form::label('land_area', 'Superficie', ['class' => 'font-semibold']) !!}
                            {!! Form::text('land_area', null, ['class' => $inputs]) !!}
                    </div>
                    <div>          
                        {!! Form::label('Front', 'Frente', ['class' => 'font-semibold']) !!}
                        {!! Form::text('Front', null, ['class' => $inputs]) !!}
                    </div>
        
                    <div>          
                        {!! Form::label('Fund', 'Fondo', ['class' => 'font-semibold']) !!}
                        {!! Form::text('Fund', null, ['class' => $inputs]) !!}
                    </div>
                </div>
                <div class="grid grid-cols-1 bg-gray-200 mt-3 p-3 rounded text-sm">
                    <p>
                        En caso que el tipo de propiedad no cuente con metros de construcción, superficie, frente o fondo, completar el campo con un <b>0</b>
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4 sm:gap-6">
                    <div>
                        {!! Form::label('property_price', 'Precio Max', ['class' => 'font-semibold']) !!}
                        {{-- @if(isset($listing) && $listing->locked)
                            {!! Form::text('property_price', null, ['class' => $inputs, 'disabled']) !!}
                        @else --}}
                            {!! Form::text('property_price', null, ['class' => $inputs]) !!}
                        {{-- @endif --}}
                    </div>
                    <div>
                        {!! Form::label('property_price_min', 'Precio Min', ['class' => 'font-semibold']) !!}
                        {{-- @if(isset($listing) && $listing->locked)
                        {!! Form::text('property_price_min', null, ['class' => $inputs, 'disabled']) !!}
                        @else --}}
                        {!! Form::text('property_price_min', null, ['class' => $inputs]) !!}
                        {{-- @endif --}}
                    </div>
                </div>

                <div class="grid grid-cols-1 mt-3">
                    <button type="button"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                        onclick="showCustomizedPrice()"
                        >
                        Agregar precio personalizado
                    </button>
                </div>

                @isset($listing)
                <div class="grid grid-cols-1" id="container-customized-price" style="display: @if($listing->customized_price != null || $listing->customized_price > 0) block @else none @endif">
                    <input type="text" name="customized_price" id="customized_price" class="{{ $inputs}}" @if($listing->customized_price > 0 || $listing->customized_price != null) value="{{ $listing->customized_price }}" @endif placeholder="Agregar precio personalizado">
                </div>
                @endisset
                
                <div id="divcomment" class="grid grid-cols-1 mt-4" style="display: none">
                    {!! Form::label('comment', 'Comentario', ['class' => 'font-semibold']) !!}
                    {!! Form::textarea('comment', null, ['class' => $inputs, 'rows' => 2, 'placeholder' => 'Ingrese el motivo por el cual cambio el precio']) !!}
                </div>
                
                {{-- map --}}
                <div>
                    <p class="font-semibold mt-5">Ubicación de la propiedad</p>
                    <p class="text-gray-500 text-sm">Realice la búsqueda de las calles, haga click en el punto donde se encuentra la propiedad en el mapa y automaticamente cargará la latitud y longitud.</p>
                    <div id="map" class="mt-5" style="height: 400px !important">
                        
                    </div>
                </div>
                {{-- termina map --}}
    
                <div class="grid grid-cols-2 gap-4 mt-4 sm:gap-6">
                    <div>          
                        {!! Form::label('lat', 'Latitud', ['class' => 'font-semibold']) !!}
                        @if(Auth::user()->email == "developer2@casacredito.com")
                            {!! Form::text('lat', null, ['class' => $inputs]) !!}
                        @else
                            {!! Form::text('lat', null, ['class' => $inputs]) !!}
                        @endif
                    </div>
                    <div>          
                        {!! Form::label('lng', 'Longitud', ['class' => 'font-semibold']) !!}
                        @if(Auth::user()->email == "developer2@casacredito.com")
                            {!! Form::text('lng', null, ['class' => $inputs]) !!}
                        @else
                            {!! Form::text('lng', null, ['class' => $inputs]) !!}
                        @endif
                    </div>
                </div>

                {{-- Zona Cardinal - se calcula automáticamente pero puede editarse --}}
                <div class="grid grid-cols-1 gap-4 mt-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="font-semibold text-sm">
                                    Zona Cardinal
                                    <span class="text-gray-400 font-normal ml-1">(se asigna automáticamente al guardar)</span>
                                </label>
                                @if(isset($listing->cardinal_zone) && $listing->cardinal_zone)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                            @if($listing->cardinal_zone == 'norte')  bg-blue-100 text-blue-800
                                            @elseif($listing->cardinal_zone == 'sur') bg-yellow-100 text-yellow-800
                                            @elseif($listing->cardinal_zone == 'este') bg-green-100 text-green-800
                                            @elseif($listing->cardinal_zone == 'oeste') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $listing->cardinal_zone_label ?? ucfirst($listing->cardinal_zone) }}
                                        </span>
                                    </div>
                                @else
                                    <p class="text-gray-400 text-sm mt-1">Se calculará al guardar si tiene coordenadas</p>
                                @endif
                            </div>

                            {{-- Solo administrador puede sobreescribir manualmente --}}
                            @if(Auth::user()->role == 'administrator')
                                <div>
                                    {!! Form::select('cardinal_zone', 
                                        ['' => 'Auto-calcular', 'norte' => '⬆ Norte', 'sur' => '⬇ Sur', 
                                        'este' => '➡ Este', 'oeste' => '⬅ Oeste', 'centro' => '⊙ Centro'],
                                        isset($listing->cardinal_zone) ? $listing->cardinal_zone : null,
                                        ['class' => 'block px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring']
                                    ) !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

    
                <p class="@if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-600 @endif text-white w-64 font-semibold absolute text-center top-0" style="margin-top: -13px; letter-spacing: 1px">DATOS DEL INMUEBLE</p>
            </div>
            <!--termina div datos del inmueble-->
    
            <div class="relative border px-5 py-4 mt-10 hover:shadow-md">
                <div id="divlicenciaurbanistica" class="grid grid-cols-1 @if(isset($listing) && $listing->listingtype == 26) block @else hidden @endif mt-5">
                    <div class="flex content-center border p-4">
                        <div>
                            <i class="fas fa-map text-red-600"></i> {!! Form::label('planing_license', '¿El terreno posee licencia urbanística?', ['class' => 'font-semibold mr-4']) !!}
                        </div>
                        <div class="form-check form-check-inline">
                            {!! Form::radio('planing_license', '1', null, ['class' => 'form-check-input']) !!}
                            {!! Form::label('inlineRadio1', 'SI', ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-check-inline ml-5">
                            {!! Form::radio('planing_license', '0', null, ['class' => 'form-check-input']) !!}
                            {!! Form::label('inlineRadio2', 'NO', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4 sm:gap-6">
                    <div id="listyears_container">
                        {!! Form::label('listyears', 'Antiguedad', ['class' => 'font-semibold']) !!}
                        {!! Form::number('listyears',null, ['class' => $inputs]) !!}
                    </div>
                    <div>
                        {!! Form::label('aliquot', 'Condominio/Alicuota', ['class' => 'font-semibold']) !!}
                        {!! Form::text('aliquot', null, ['class' => $inputs]) !!}
                    </div>
                </div>
                <div class="grid grid-cols-1 mt-4">
                    {!! Form::label('observations_type_property', '¿La propiedad tiene alguna observación?', ['class' => 'font-semibold']) !!}
                    {!! Form::textarea('observations_type_property', null, ['class' => $inputs, 'rows' => 4]) !!}
                </div>
                <p class="@if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-600 @endif text-white w-64 font-semibold absolute text-center top-0" style="margin-top: -13px; letter-spacing: 1px">OBSERVACIONES</p>
            </div>
    
            <div class="border relative px-5 py-4 mt-10 hover:shadow-md">
                <div id="rowsTitles">
                    @if(isset($listing) && is_array(json_decode($listing->heading_details)))
                    @php $ii=-1; @endphp
                        @foreach(json_decode($listing->heading_details) as $dets)
                            @php $ii++; @endphp 
                            <div class="gap-4 mt-4 sm:gap-6">
                                <label class="font-semibold">Titulo</label>
                                <div class="flex flex-row mt-2">
                                    <input id="details" class="w-full h-10 px-4 py-2 text-gray-700 bg-white text-sm border border-gray-300 rounded-l" name="details{{$ii}}[]" type="text" value="{{$dets[0]}}"/>
                                </div>            
                            @php unset($dets[0]); $printControl=0; @endphp
                            <div class="font-semibold ml-4 mt-4">Detalles</div>
                                @foreach($dets as $det)
                                    @if($printControl==0)
                                    @php $printControl=1; @endphp                
                                        <div class="flex flex-row mt-2 ml-4">
                                            {!! Form::select('details'.$ii.'[]',$details->pluck('charac_titile','id'), $det   ,    ['class' => 'w-44 h-10 px-4 py-2 text-gray-700 bg-white text-sm border border-gray-300 rounded-l']) !!}
                                    @else                
                                    @php $printControl=0; @endphp
                                            <input id="subdetails" class="w-24 h-10 px-4 py-2 text-gray-700 bg-white text-sm border border-gray-300" type="number" name="details{{$ii}}[]" pattern="[0-9]+" onkeydown="return false" value="{{!is_numeric($det)?1:$det}}"/>
                                            <button class="w-12 h-10 py-2 @if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-700 @endif text-white rounded-r text-sm" type="button" onclick="delrowDetail(this)">X</button>
                                        </div>
                                    @endif
                                @endforeach
                                <button type="button" class="px-4 py-2 ml-4 mt-4 text-xl leading-5 text-black bg-green-300 rounded" onclick="addRowDetail(this,{{$ii}})">Agregar Detalle</button>
                            </div>    
                        @endforeach
                        </div>
                    @else
                        <div class="gap-4 mt-4 sm:gap-6">
                            <label class="font-semibold">Titulo</label>
                            <div class="flex flex-row mt-2">
                                <input id="details"  class="w-full h-10 px-4 py-2 text-gray-700 bg-white text-sm border border-gray-300 rounded-l" name="details0[]" type="text"/>
                                <button class="w-12 h-10 py-2 @if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-700 @endif text-white rounded-r text-sm" type="button" onclick="delrowTitle(this)">X</button>
                            </div>
                            <div class="font-semibold ml-4 mt-4">Detalles</div>
                            <div class="flex flex-row mt-2 ml-4">
                                <select class="w-44 h-10 px-4 py-2 text-gray-700 bg-white text-sm border border-gray-300 rounded-l" name="details0[]">
                                    @foreach ($details as $detail)
                                        <option value="{{$detail->id}}">{{$detail->charac_titile}}</option>
                                    @endforeach
                                </select>
                                <input id="subdetails"  class="w-24 h-10 px-4 py-2 text-gray-700 bg-white text-sm border border-gray-300" type="number" name="details0[]" pattern="[0-9]+" onkeydown="return false" value="1"/>
                                <button class="w-12 h-10 py-2 @if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-700 @endif text-white rounded-r text-sm" type="button" onclick="delrowDetail(this)">X</button>
                            </div>
                            <button type="button" class="px-4 py-2 ml-4 mt-4 text-xl leading-5 text-black bg-green-300 rounded" onclick="addRowDetail(this,0)">Agregar Detalle</button>
                        </div>
                    </div>
                    @endif
                    <button type="button" class="px-4 py-2 mt-4 text-xl leading-5 text-black bg-blue-300 rounded" onclick="addRowTitles()">Agregar Titulo</button>
                    <p class="@if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-600 @endif text-white w-64 font-semibold absolute text-center top-0" style="margin-top: -13px; letter-spacing: 1px">DETALLES DEL INMUEBLE</p>
                </div>

            <div class="border relative px-5 py-4 mt-10 hover:shadow-md">
                <div class="gap-4 mt-4 sm:gap-6">
                    <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-3  @if(isset($listing) && $listing->listinglistservices == null) border-red-500 @else border-gray-300 @endif rounded-md px-4 py-2">
                        @foreach ($services as $serv)
                                <label class="inline-flex items-center mt-3">  
                                    {!! Form::checkbox("checkServ[]", $serv->id, 
                                    isset($listing->listinglistservices) && in_array($serv->id,explode(",", $listing->listinglistservices)) ? true : false,
                                    ['class' => 'form-checkbox h-5 w-5 '.$bgcheckbox, 'type'=>'checkbox', 'id'=>"checkServ$serv->id"]) !!}
                                    <span class="ml-2 text-gray-700">{{$serv->charac_titile}}</span>
                                </label>
                        @endforeach
                    </div>    
                </div>
                <p class="@if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-600 @endif text-white w-64 font-semibold absolute text-center top-0" style="margin-top: -13px; letter-spacing: 1px">SERVICIOS</p>
            </div>
    
            <div class="border relative px-5 py-4 mt-10 hover:shadow-md">
                <div class="gap-4 mt-4 sm:gap-6">
                    <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-3 border-gray-300 rounded-md px-4 py-2">
                        @foreach ($general_characteristics as $general_characteristic)
                                <label class="inline-flex items-center mt-3">  
                                    {!! Form::checkbox("checkgc[]", $general_characteristic->id, isset($listing->listinggeneralcharacteristics) && in_array($general_characteristic->id,explode(",", $listing->listinggeneralcharacteristics)) ? true : false,['class' => 'form-checkbox h-5 w-5 checkbox-characteristic '.$bgcheckbox, 'type'=>'checkbox', 'id'=>"checkgc$general_characteristic->id"]) !!}
                                    <span class="ml-2 text-gray-700">{{$general_characteristic->title}}</span>
                                    @switch($general_characteristic->id)
                                        @case(7) @php $newinput = "niv_constr"; @endphp @break
                                        @case(8) @php $newinput = "num_pisos"; @endphp @break
                                        @case(15) @php $newinput = "pisos_constr"; @endphp @break
                                        @default @break
                                    @endswitch
                                    @if($general_characteristic->id == 7 || $general_characteristic->id == 8 || $general_characteristic->id == 15)
                                        {!! Form::number($newinput, null, ['class' => 'w-12 h-7 ml-3 pl-2 py-2 mt-2 text-gray-700 bg-white text-sm border border-gray-300 rounded-0 focus:border-red-500 focus:outline-none focus:ring hidden']) !!}
                                    @endif
                                </label>
                        @endforeach
                    </div>    
                </div>
                <p class="@if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-600 @endif text-white w-64 font-semibold absolute text-center top-0" style="margin-top: -13px; letter-spacing: 1px">CARACTERISTICAS GENERALES</p>
            </div>
    
            <div id="environments_container" class="border relative px-5 py-4 mt-10 hover:shadow-md">
                <div class="gap-4 mt-4 sm:gap-6">
                    <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-3 border-gray-300 rounded-md px-4 py-2">
                        @foreach ($environments as $envir)
                                <label class="inline-flex items-center mt-3">  
                                    {!! Form::checkbox("checkEnvir[]", $envir->id, isset($listing->listingenvironments) && in_array($envir->id,explode(",", $listing->listingenvironments)) ? true : false,['class' => 'form-checkbox h-5 w-5 '.$bgcheckbox, 'type'=>'checkbox', 'id'=>"checkEnvir$envir->id"]) !!}
                                    <span class="ml-2 text-gray-700">{{$envir->title}}</span>
                                </label>
                        @endforeach
                    </div>    
                </div>
                <p class="@if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-600 @endif text-white w-64 font-semibold absolute text-center top-0" style="margin-top: -13px; letter-spacing: 1px">AMBIENTES</p>
            </div>
    
            <div class="gap-4 mt-4 sm:gap-6 border relative px-5 py-4 mt-10 hover:shadow-md">
                {{-- <h4 class="font-semibold">Beneficios</h4>    --}}
                <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-3 px-4 py-2">
                    @foreach ($benefits as $bene)            
                            <label class="inline-flex items-center mt-3">  
                                {!! Form::checkbox("checkBene[]", $bene->id, 
                                isset($listing->listingcharacteristic) && in_array($bene->id,explode(",", $listing->listingcharacteristic)) ? true : false,
                                ['class' => 'form-checkbox h-5 w-5 '.$bgcheckbox, 'type'=>'checkbox', 'id'=>"checkBene$bene->id"]) !!}
                                <span class="ml-2 text-gray-700">{{$bene->charac_titile}}</span>
                            </label>
                    @endforeach
                </div>    
                <p class="@if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-600 @endif text-white w-64 font-semibold absolute text-center top-0 shadow-lg" style="margin-top: -13px; letter-spacing: 1px">BENEFICIOS</p>
            </div>

            @if($currentRouteName != "admin.housing.property.create" && $currentRouteName != "admin.housing.property.edit")
                <div id="div_additional_data" class="gap-4 mt-4 sm:gap-6 border relative px-5 py-4 hover:shadow-md">
                    <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-3 px-4 py-2">
                        <div>
                            <div>
                                <label class="inline-flex items-center mt-3">  
                                    <span class="ml-2 text-gray-700 mr-2">Error de Cavida</span>
                                    {!! Form::checkbox("cavity_error", null, 
                                    isset($listing->cavity_error) && $listing->cavity_error ? true : false,
                                    ['class' => 'form-checkbox h-5 w-5 '.$bgcheckbox, 'type'=>'checkbox', 'id'=>"check_cavity_error"]) !!}
                                </label>
                            </div>
                            <div>
                                <label class="inline-flex items-center mt-3">  
                                    <span class="ml-2 text-gray-700 mr-2">Aplica Crédito VIP</span>
                                    {!! Form::checkbox("vip", null, 
                                    isset($listing->vip) && $listing->vip ? true : false,
                                    ['class' => 'form-checkbox h-5 w-5 '.$bgcheckbox, 'type'=>'checkbox']) !!}
                                </label>
                            </div>
                        </div>
                        <div>
                            <div>
                                <label class="inline-flex items-center mt-3">  
                                    <span class="ml-2 text-gray-700 mr-2">¿Está Hipotecada?</span>
                                    {!! Form::checkbox("mortgaged", null, 
                                    isset($listing->mortgaged) && $listing->mortgaged ? true : false,
                                    ['class' => 'form-checkbox h-5 w-5 '.$bgcheckbox, 'type'=>'checkbox', 'id'=>"check_mortgage"]) !!}
                                </label>
                            </div>
                            <div>
                                {!! Form::text('mount_mortgaged', null, ['class' => $inputs, 'placeholder' => 'Monto $', 'id' => 'mount_mortgaged']) !!}
                            </div>
                        </div>
                        <div>
                            <div>
                                {!! Form::text('entity_mortgaged', null, ['class' => $inputs, 'placeholder' => 'Banco', 'id' => 'entity_mortgaged']) !!}
                            </div>
                            <div>
                                {!! Form::text('warranty', null, ['class' => $inputs, 'placeholder' => 'Garante', 'id' => 'warranty']) !!}
                            </div>
                        </div>
                    </div>   
                        
                    <div>
                        @if($currentRouteName != "admin.housing.property.create" && $currentRouteName != "admin.housing.property.edit")

                            <div>
                                <div class="grid grid-cols-1 mt-4">
                                    <div class="flex items-end gap-2 w-full">
                                        <div class="w-3/4">
                                            {!! Form::label('cadastral_key', 'Clave Catastral', ['class' => 'font-semibold']) !!}
                                            <p class="text-sm"><strong>Ingrese la clave catastral</strong> y realice la búsqueda para completar los datos automáticamente. </p>
                                            {!! Form::text('cadastral_key', null, ['class' => $inputs]) !!}
                                        </div>
                                        <div class="w-1/4">
                                            <button id="btn-search-cadastral-key" class="bg-green-800 text-white p-2 rounded w-full">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="mt-3">
                                    {!! Form::label('land_appraisal', 'Avaluo del terreno', ['class' => 'font-semibold']) !!}
                                    {!! Form::number('land_appraisal', null, ['class' => $inputs, 'placeholder' => 'Ej: 61388.77']) !!}
                                </div>
                                <div class="mt-3">
                                    {!! Form::label('construction_appraisal', 'Avaluo de construcción', ['class' => 'font-semibold']) !!}
                                    {!! Form::number('construction_appraisal', null, ['class' => $inputs, 'placeholder' => 'Ej: 61388.77']) !!}
                                </div>
                                <div class="mt-3">
                                    {!! Form::label('aval', 'Avaluo de la propiedad', ['class' => 'font-semibold']) !!}
                                    {!! Form::number('aval', null, ['class' => $inputs, 'placeholder' => 'Ej: 100000']) !!}
                                </div>
                            </div>
                            <div class="bg-gray-100 mt-3 p-2 rounded-lg">
                                <i class="fa-solid fa-circle-info"></i> Para obtener el avaluo de la propiedad, puede ingresar al <strong><a target="_blank" class="text-blue-500" href="https://enlinea.cuenca.gob.ec/#/informe-predial">siguiente enlace</a></strong>  y consultar por la <strong>clave catastral</strong>.
                            </div>

                        @endif

                    </div> 
                    <p class="@if($currentRouteName == "admin.housing.property.create" || $currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-600 @endif text-white w-64 font-semibold absolute text-center top-0 shadow-lg" style="margin-top: -13px; letter-spacing: 1px">DATOS ADICIONALES</p>
                </div>
            @endif
        </div>

        <div id="third" style="display: none" class="fragment3">
            <div class="gap-4 mt-4 sm:gap-6">
                <label class="font-semibold">Galeria de Imagenes</label>
                <div>
                    <input type="file" class="px-4 py-2 border border-gray-300 rounded-md" name="galleryImages[]" id="galleryImages" accept=".jpg, .jpeg, .png, .mp4" multiple>
                </div>      
                <ul id="gridImages" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 px-4 py-2 border @if(isset($listing) && $listing->images == null) border-red-500 @else border-gray-300 @endif rounded-md">
                    @isset($listing)
                        @php $ii=0; @endphp
                        @foreach(array_filter(explode("|", $listing->images)) as $img)
                            @php $ii++; $imageVerification = asset('uploads/listing/thumb/300/'.$img); @endphp
                            <li class="relative"  id="imageUpload{{$ii}}"> 
                                <button type="button" onclick="delImageUpload({{$ii}})" class="absolute right-0 px-2 rounded @if($listing->property_by == "Housing") bg-blue-800 @elseif($listing->property_by == "Promotora") bg-red-700 @else bg-red-800 @endif text-white font-bold">X</button>
                                <img class="rounded" src="@if(@getimagesize($imageVerification)){{url('uploads/listing/thumb/300', $img)}} @else {{url('uploads/listing/300',$img)}} @endif">
                                <input type="hidden" value="{{$img}}" name="updatedImages[]">
                            </li>
                        @endforeach
                    @endisset
                </ul>
                @if(isset($listing) && $listing->video)
                    <div id="gridVideo" class="border border-gray-300 rounded-md mt-4 relative">
                        <p class="absolute bg-red-800 text-white px-3 rounded-full" style="top:-10px; left: 15px">Videos</p>
                        <ul class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-3 px-4 py-2">
                            <li class="relative"> 
                                <button type="button" style="z-index: 3" onclick="delVideoUpload('gridVideo')" class="absolute right-0 px-2 rounded bg-red-800 text-white font-bold">X</button>
                                <video class="rounded" src="{{asset('uploads/video/'.$listing->video)}}" autoplay muted loop controls></video>
                                <input type="hidden" value="{{$listing->video}}" name="updatedVideo[]">
                            </li>
                        </ul>
                    </div>
                @endif
            </div>          
        </div>
        

        <hr class="mt-4">
        <div class="flex justify-center mt-6">
                @if(Route::current()->getName() == "admin.listings.create" || Route::current()->getName() == "home.tw.create" ||  Route::current()->getName() == "admin.housing.property.create" || Route::currentRouteName() == "admin.promotora.property.create")
                <button id="btnSave" type="submit" class="px-6 py-2 text-xl leading-5 text-white transition-colors duration-200 transform @if($currentRouteName == "admin.housing.property.create") bg-blue-900 hover:bg-blue-700 @elseif(Route::currentRouteName() == "admin.promotora.property.create") bg-red-800 hover:bg-red-700 focus:bg-red-700 @else bg-red-700 hover:bg-red-600 focus:bg-red-600 @endif rounded focus:outline-none">GUARDAR</button>
                @endif
                @if(Route::current()->getName() == "admin.listings.edit" || Route::current()->getName() == "home.tw.edit" || Route::current()->getName() == "admin.housing.property.edit" || Route::currentRouteName() == "admin.promotora.property.edit")
                <button onclick="saveandclose(event)" class="px-6 py-2 ml-3 text-xl leading-5 text-white @if($currentRouteName == "admin.housing.property.edit") bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-green-500 @endif transition-colors duration-200 transform rounded  focus:outline-none">GUARDAR Y SALIR</button>
                @endif
        </div>
    </form>

    @isset($listing)
    <div id="modalStatus" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
        <div class="absolute w-full h-full bg-gray-900 opacity-50"></div>
        
        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50">
    
          <div class="modal-content py-4 text-left px-6">
            <div class="flex justify-between items-center pb-3">
              <p class="text-md font-bold" id="txttitlemodal">¿Cuál es la razón por la que se desactiva la propiedad?</p>
              <div class="modal-close cursor-pointer z-50">
                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                  <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                </svg>
              </div>
            </div>
                <input type="text" class="border p-5 rounded-md w-full" name="comment_desact" id="comment_desact"/>
                <div class="flex justify-center pt-2 mt-2">
                  <button onclick="setcomment({{$listing->id}}, this)" class="px-4 bg-green-300 p-3 rounded-lg text-black-500 hover:bg-green-100 hover:text-black-300 mr-2">Guardar</button>
                </div>
            
          </div>
        </div>
      </div>
      @endisset

      {{-- modal success --}}
      <div class="modalSuccess opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
        <div class="absolute w-full h-full bg-gray-900 opacity-50"></div>
        
        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50">
    
          <div class="modal-content py-4 text-left px-6">
            <div class="flex justify-between items-center pb-3">
              <p class="text-2xl font-bold">El comentario se ha enviado</p>
            </div>
                <p>Ahora puede guardar los cambios realizados para que la propiedad se actualice</p>
                <div class="flex justify-center pt-2 mt-2">
                  <button class="modal-close px-4 bg-green-300 p-3 rounded-lg text-black-500 hover:bg-green-100 hover:text-black-300 mr-2">Entendido</button>
                </div>
            
          </div>
        </div>
      </div>

</section>

<x-modal-update-contact-date></x-modal-update-contact-date>

@isset($units)
    @if(count($units) > 0)
        <x-modal-units :units="$units" :types="$types" :listing="$listing"></x-modal-units>
    @endif
@endisset

</main>
@endsection


@section('endscript')
    <script src="{{asset('js/sortable.min.js')}}"></script>
    <script>
        const searchCadastralKeyBaseUrl = "{{ route('search.cadastral.key') }}";
    </script>
    <script defer src="{{ asset('js/listings/search-cadastral-key.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentRoute = @json(Route::current()->getName());
    </script>
    <script src="{{ asset('js/listings/validate.js?v=9') }}" defer></script>
    <script>let bandera = false;</script>
    @if(Route::current()->getName() == "admin.listings.create" || Route::current()->getName() == "admin.housing.property.create" || Route::currentRouteName() == "admin.promotora.property.create")
        <script>
            const save = async(event) => {
                if(bandera){
                    event.preventDefault();
                } else {
                    let dataform = new FormData(document.getElementById('formsave'));
                    console.log(dataform);
                    const response = await fetch("{{route('admin.listings.store')}}",
                    { body: dataform, method: 'POST', headers: {"X-CSRF-Token": "{!!csrf_token()!!}"}});
                    let mensaje = await response.json();
                    console.log(mensaje);
                    document.querySelector('.loader').style.display = "none";
                    if(mensaje.success && mensaje.fragment == "first"){ 
                        document.getElementById('first').style.display="none";
                        document.getElementById('second').style.display="block";
                        setfragmentvalue('second'); 
                        let map = L.map('map').setView([-2.9003262789921513, -79.00517388859988], 13);

                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);

                        let inpLat = document.getElementById('lat');
                        let inpLng = document.getElementById('lng');
                        
                        let marker = null;
                        const apiKey = "AAPK6cd0390360a34c47abb6992f612c3a4eHDSN5oz15wvKsDnnOXQAT1xiCNYDtP4B8XRcytqys3UphqELHcSD_tlTbsijCbGz";
                        map.on('click', (event)=> {

                            if(marker !== null){
                                map.removeLayer(marker);
                            }

                            marker = L.marker([event.latlng.lat , event.latlng.lng]).addTo(map);

                            inpLat.value = event.latlng.lat;
                            inpLng.value = event.latlng.lng;
                            
                        });

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

                        const results = L.layerGroup().addTo(map);

                        searchControl.on("results", function (data) {
                            results.clearLayers();
                            for (let i = data.results.length - 1; i >= 0; i--) {
                            results.addLayer(L.marker(data.results[i].latlng));
                            }
                        });
                        if(document.getElementById('paso1')){document.getElementById('paso1').classList.remove('bg-red-500');if("{{ $currentRouteName == 'admin.housing.property.create'}}") {document.getElementById('paso1').classList.add('bg-blue-900');} else {document.getElementById('paso1').classList.add('bg-green-500');}; if(document.getElementById('paso2')){document.getElementById('paso2').classList.remove('bg-gray-500'); document.getElementById('paso2').classList.add('bg-red-500')}}
                    }
                    if(mensaje.success && mensaje.fragment == "second"){ 
                        document.getElementById('second').style.display="none";
                        document.getElementById('third').style.display="block";
                        setfragmentvalue('third');
                        //cambiando value de input product_code con el nuevo codigo con letras
                        //document.getElementById('product_code').value = mensaje.productcode;
                        if(document.getElementById('paso2')){document.getElementById('paso2').classList.remove('bg-red-500');if("{{ $currentRouteName == 'admin.housing.property.create'}}") {document.getElementById('paso2').classList.add('bg-blue-900');} else {document.getElementById('paso2').classList.add('bg-green-500');}; if(document.getElementById('paso3')){document.getElementById('paso3').classList.remove('bg-gray-500'); document.getElementById('paso3').classList.add('bg-red-500')}}}
                    //if(mensaje.success && mensaje.fragment == "third"){ document.getElementById('third').style.display="none";document.getElementById('fourth').style.display="block";setfragmentvalue('fourth'); if(document.getElementById('paso3')){document.getElementById('paso3').classList.remove('bg-red-500');if("{{ $currentRouteName == 'admin.housing.property.create'}}") {document.getElementById('paso3').classList.add('bg-blue-900');} else {document.getElementById('paso3').classList.add('bg-green-500');}; if(document.getElementById('paso4')){document.getElementById('paso4').classList.remove('bg-gray-500'); document.getElementById('paso4').classList.add('bg-red-500')}}}
                    if(mensaje.success && mensaje.fragment == "third" && !ischangestatus && !ischangeplan && !ischangeavailable){window.location.replace("{{route('admin.properties')}}"); if(document.getElementById('paso4')){document.getElementById('paso4').classList.remove('bg-red-500');document.getElementById('paso4').classList.add('bg-green-500');}}
                }
            }
        </script>
    @elseif(Route::current()->getName() == "admin.listings.edit" || Route::current()->getName() == "home.tw.edit")
        <script>
            const save = async(event) => {
            if(bandera){
                event.preventDefault();
            } else {
                let dataform = new FormData(document.getElementById('formsave'));
                const response = await fetch("{{route('admin.listings.update', $listing->id)}}",
                { body: dataform, method: 'POST', headers: {"X-CSRF-Token": "{!!csrf_token()!!}"}});
                let mensaje = await response.json();
                document.querySelector('.loader').style.display = "none";
                console.log('saving...');
                if(mensaje.success && mensaje.fragment == "first"){ document.getElementById('first').style.display="none";document.getElementById('second').style.display="block";setfragmentvalue('second');changeclass('second');}
                if(mensaje.success && mensaje.fragment == "second"){ document.getElementById('second').style.display="none";document.getElementById('third').style.display="block";setfragmentvalue('third');changeclass('third');}
                //if(mensaje.success && mensaje.fragment == "third"){ document.getElementById('third').style.display="none";document.getElementById('fourth').style.display="block";setfragmentvalue('fourth');changeclass('fourth');}
                if(mensaje.success && mensaje.fragment == "third" && !ischangestatus && !ischangeplan && !ischangeavailable){window.location.replace("{{route('admin.properties')}}");}
            }
            }
        </script>
    @endif

    <script>

        let form = document.getElementById('formsave');

        // obteniendo el valor del title para mandar a la funcion countChar
        var div_info_character = document.getElementById('div_info_character');
        var div_info_character_desc = document.getElementById('div_info_character_desc');
        var input_listing_title = document.querySelector("input[name='listing_title']");
        var input_meta_description = document.querySelector("input[name='meta_description']"); 
        var label_count_title = document.getElementById('label_count_title');
        var label_count_desc = document.getElementById('label_count_desc');
        
        let selstatus = document.querySelector("select[name='status']");
        if(selstatus) selstatus = selstatus.value; 
        let selavailable = document.querySelector("select[name='available']");
        if(selavailable) selavailable = selavailable.value;

        let selectlistingtype = document.querySelector("select[name='listingtype']");
        selectlistingtype.addEventListener('change', function(){
            let divlicenciaurbanistica = document.getElementById('divlicenciaurbanistica');
            if(selectlistingtype.value == 26){
                document.getElementById("planing_license").required = true;
                divlicenciaurbanistica.classList.remove('hidden');
                divlicenciaurbanistica.classList.add('block');
            } else {
                document.getElementById("planing_license").required = false;
                divlicenciaurbanistica.classList.remove('block');
                divlicenciaurbanistica.classList.add('hidden');
            }   
        });

        let ischangestatus = false;
        let ischangeplan = false;
        let ischangeavailable = false;

        const showmodalsifchange = () => {
            document.querySelector('.loader').style.display = "none";
            let valueStatus = document.querySelector("select[name='status']");
            let valuePlan = document.querySelector("select[name='listing_type']").value;
            let valueAvailable = document.querySelector("select[name='available']").value;
            if(valueStatus) {
                valueStatus = valueStatus.value;
                if(selstatus == 1 && valueStatus == 0){
                    ischangestatus = true;
                    event.preventDefault();
                    bandera = true;
                    toggleModal("status");
                } else if(valuePlan == 1 && selstatus == 0 && valueStatus == 1){
                    ischangeplan = true;
                    event.preventDefault();
                    bandera = true;
                    toggleModal('plan');
                } else if(valueAvailable == 2 && selavailable == 1) {
                    ischangeavailable = true;
                    event.preventDefault();
                    bandera = true;
                    toggleModal('available');
                }
            }
        }

        let btnSave = document.getElementById('btnSave');
        if(btnSave){
            btnSave.addEventListener("click", function(event){
                event.preventDefault();
                if(!validate()) return;
                document.querySelector('.loader').style.display = "block";
                removeelement();
                save(event);
            });
        }

        const savelisting = async() => {
            let dataform = new FormData(document.getElementById('formsave'));
            const response = await fetch("{{route('admin.listings.store')}}",
            { body: dataform, method: 'POST', headers: {"X-CSRF-Token": "{!!csrf_token()!!}"}});
            let mensaje = await response.text();
            console.log('saving...');
        }

        function setcomment(listing_id, button){
            let comment = document.getElementById("comment_desact");
            let form = document.getElementById('formsave');
            let value = ""; let type = "";
            if(ischangestatus){value = document.querySelector("select[name='status']").value;type="status";}
            if(ischangeplan){value = document.querySelector("select[name='listing_type']").value;type="plan";}
            if(ischangeavailable){value = document.querySelector("select[name='available']").value;type="available"}
            if(comment.value.length > 4){

                let icon = document.createElement('i');
                icon.classList.add('fa', 'fa-spinner', 'fa-spin', 'ml-2');
                button.classList.add('buttonload');
                button.appendChild(icon);
                
                if(ischangeavailable && value == 2 && selstatus == 1){
                    alert('La propiedad pasará a estar DESACTIVADA debido a que ya NO ESTA DISPONIBLE');
                    document.querySelector("select[name='status']").value = 0;
                }

                $.ajax({
                    url: "{{route('home.tw.setcomment')}}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "listing_id": listing_id,
                        "type": type,
                        "value": value,
                        "comment" : comment.value
                    },
                    dataType: "json",
                    success: function(response){
                    if(response){
                        icon.classList.remove('fa', 'fa-spinner', 'fa-spin');
                        //toggleModal('modalStatus');
                        form.submit();
                        //toggleModalSuccess();
                    } else {
                        alert('Algo salio mal guardando el comentario, por favor recargue la pagina');
                    }
                },
                    error: function(error){
                        console.log('Hubo un error con el servidor, por favor recargue la pagina');
                    }
                });
            } else {
                alert('Por favor complete el campo');
            }
        }

        window.addEventListener('load', (event) => {

            createProductCode();

            setPreviewOnGoogle();

            countCharacterMetaDescription();

            var range =  document.getElementById('listyears').value;
            //rangeSlide(range);
            //document.getElementById('charcount').innerHTML = document.getElementById('metadescription').value.length;
            if(label_count_title){
                if(input_listing_title.value.length > 0) label_count_title.innerHTML = input_listing_title.value.length;
                else label_count_title.innerHTML = "0";
            }

            let checkbox_characteristics = document.querySelectorAll(".checkbox-characteristic");
    
            checkbox_characteristics.forEach(element => {
                if(element.checked && element.value == 7) document.querySelector("input[name='niv_constr']").classList.remove('hidden');document.querySelector("input[name='niv_constr']").classList.add('block');
                if(element.checked && element.value == 8) document.querySelector("input[name='num_pisos']").classList.remove('hidden');document.querySelector("input[name='num_pisos']").classList.add('block')
                if(element.checked && element.value == 15) document.querySelector("input[name='pisos_constr']").classList.remove('hidden');document.querySelector("input[name='pisos_constr']").classList.add('block')
            });
        });

        function countCharsTitle(object){
            if(label_count_title){
                if(object.value.length >= 50 && object.value.length <=60){
                    div_info_character.style.backgroundColor = "#9AE6B4";
                } else {
                    div_info_character.style.backgroundColor = "#FEB2B2";
                }
                label_count_title.innerHTML = object.value.length;
            }
        }

        function countCharsDesc(object){
            if(object.value.length >= 130 && object.value.length <= 160){
                div_info_character_desc.style.backgroundColor = "#9AE6B4";
            } else {
                div_info_character_desc.style.backgroundColor = "#FEB2B2";
            }
            label_count_desc.innerHTML = object.value.length;
        }

        let input_price = document.querySelector("[name='property_price']");
        let price = input_price.value;

        function validateCommentChangePrice(){
            let divcomment = document.getElementById('divcomment');
            let newprice = input_price.value;
            if(price != "" && price != newprice){
                divcomment.style.display = "block";
                let comment = document.getElementById('comment').value;
                if(comment == null || comment == ""){
                    document.querySelector("[name='comment']").required = true;
                    alert('Por favor, indique la razón por la que cambia el precio');
                    divcomment.scrollIntoView({ 
                        behavior: 'smooth' 
                    });
                    return true;
                } else {
                    return false;
                }
            } else {
                divcomment.style.display = "none";
                document.querySelector("[name='comment']").required = false;
                return false;
            }
        }

        //esta funcion crea un codigo cuando se carga la pagina para que no se crucen
        async function createProductCode(){
            
            const productCode = document.getElementById('product_code').value;

            try {
                const response = await fetch("{{ route('admin.listings.create_code') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ product_code: productCode })
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log(data);
                } else {
                    console.error('Error en la solicitud:', response.status);
                }
            } catch (error) {
                console.error('Error al procesar la solicitud:', error);
            }
        }

        const gridImages = document.getElementById('gridImages');
        new Sortable(gridImages, {
            swapThreshold: 1,
            animation: 150
        });
        const selState = document.getElementById('state');
        const selCities= document.getElementById('city');
        const selSector = document.getElementById('sector');
        
        selState.addEventListener("change", async function() {
            selCities.options.length = 0;
            let id = selState.options[selState.selectedIndex].dataset.id;
            const response = await fetch("{{url('getcities')}}/"+id );
            const cities = await response.json(); 

            let firstopt = document.createElement('option');
            firstopt.appendChild( document.createTextNode('Seleccione') );
            firstopt.value = "";
            selCities.appendChild(firstopt);

            cities.forEach(city => {
                var opt = document.createElement('option');
                opt.appendChild( document.createTextNode(city.name) );
                opt.value = city.name;
                opt.setAttribute('data-id', city.id);
                selCities.appendChild(opt);
            });
        });

        if(selSector){
            selCities.addEventListener("change", async function(){
                selSector.options.length = 0;
                let id = selCities.options[selCities.selectedIndex].dataset.id;
                const response = await fetch("{{ url('getsector') }}/"+id);
                const sectores = await response.json();

                let firstopt = document.createElement('option');
                firstopt.appendChild( document.createTextNode('Seleccione') );
                firstopt.value = '';
                selSector.appendChild(firstopt);

                sectores.forEach(sector => {
                    let opt = document.createElement('option');
                    opt.appendChild( document.createTextNode(sector.name) );
                    opt.value = sector.name;
                    selSector.appendChild(opt);
                });
            });
        }

        const selOptionsDetails = @json($details);
        
    const addRowTitles = () => {
        let idUniq  = new Date().valueOf();
        let InsertOptions ='';
        selOptionsDetails.forEach(opts =>{        InsertOptions +=`<option value="${opts.id}">${opts.charac_titile}</option>`;    });
        let rowTemplate =`
            <div class="gap-4 mt-4 sm:gap-6">
                <label class="font-semibold">Titulo</label>
                <div class="flex flex-row mt-2">
                    <input id="details" class="w-full h-10 px-4 py-2 text-gray-700 bg-white text-sm border border-gray-300 rounded-l" type="text" name="details${idUniq}[]" required/>
                    <button class="w-12 h-10 py-2 @if($currentRouteName == 'admin.housing.property.create' || $currentRouteName == 'admin.housing.property.edit') bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-700 @endif text-white rounded-r text-sm" type="button" onclick="delrowTitle(this)">X</button>
                </div>
                <div class="font-semibold ml-4 mt-4">Detalles</div>
                <div class="flex flex-row mt-2 ml-4">
                    <select class="w-44 h-10 px-4 py-2 text-gray-700 bg-white text-sm border border-gray-300 rounded-l" name="details${idUniq}[]">${InsertOptions}</select>
                    <input id="subdetails" class="w-24 h-10 px-4 py-2 text-gray-700 bg-white text-sm border border-gray-300" type="number"  name="details${idUniq}[]" pattern="[0-9]+" onkeydown="return false" value="1"/>
                    <button class="w-12 h-10 py-2 @if($currentRouteName == 'admin.housing.property.create' || $currentRouteName == 'admin.housing.property.edit') bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-700 @endif text-white rounded-r text-sm" type="button" onclick="delrowDetail(this)">X</button>
                </div>
                <button type="button" class="px-4 py-2 ml-4 mt-4 text-xl leading-5 text-black bg-green-300 rounded" onclick="addRowDetail(this,${idUniq})">Agregar Detalle</button>
            </div>`
        document.getElementById('rowsTitles').insertAdjacentHTML('beforeend',rowTemplate);
    }

    const addRowDetail = (row,id=0) =>{
        let InsertOptions ='';
        selOptionsDetails.forEach(opts =>{ InsertOptions +=`<option value="${opts.id}">${opts.charac_titile}</option>`;    });
        let rowTemplate =   `<div class="flex flex-row mt-2 ml-4">
                                <select class="w-44 h-10 px-4 py-2 text-gray-700 bg-white text-sm border border-gray-300 rounded-l" name="details${id}[]">${InsertOptions}</select>
                                <input id="subdetails"  class="w-24 h-10 px-4 py-2 text-gray-700 bg-white text-sm border border-gray-300" type="number" name="details${id}[]" pattern="[0-9]+" onkeydown="return false" value="1"/>
                                <button class="w-12 h-10 py-2 @if($currentRouteName == 'admin.housing.property.create' || $currentRouteName == 'admin.housing.property.edit') bg-blue-900 @elseif(Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit") bg-red-800 @else bg-red-700 @endif text-white rounded-r text-sm" type="button" onclick="delrowDetail(this)">X</button>
                            </div>`;
        row.insertAdjacentHTML('beforebegin',rowTemplate);
    }
    

    const delrowDetail = (row) =>{
        row.parentElement.remove();
    }

    const delrowTitle = (row) =>{
        row.parentElement.parentElement.remove();
    }
    const delImageUpload = (idDiv) =>{
        document.getElementById('imageUpload'+idDiv).remove();
    }

    const delVideoUpload = (idDiv) => {
        document.getElementById(idDiv).remove();
    }

    const openHelpDescription = () => {
        const div_help_desc = document.getElementById('div_help_desc');
        if(div_help_desc.style.display == "none") div_help_desc.style.display = "block";
        else if(div_help_desc.style.display == "block") div_help_desc.style.display = "none";
    }

    var alert_del = document.querySelectorAll('.alert-del');
    alert_del.forEach((x) =>
    x.addEventListener('click', function () {
      x.classList.add('hidden');
    })
  );

    function getTotalAppraisal() {
        let inp_land_appraisal = document.getElementById('land_appraisal');
        let inp_construction_appraisal = document.getElementById('construction_appraisal');
        let inp_aval = document.getElementById('aval');

        // Función para calcular y actualizar el aval
        const actualizarAval = () => {
            const landValue = parseFloat(inp_land_appraisal.value) || 0;
            const constructionValue = parseFloat(inp_construction_appraisal.value) || 0;
            const total = landValue + constructionValue;
            inp_aval.value = total.toFixed(2);
        };

        // Adjunta los event listeners a los inputs
        if (inp_land_appraisal && inp_construction_appraisal && inp_aval) {
            inp_land_appraisal.addEventListener('keyup', actualizarAval);
            inp_construction_appraisal.addEventListener('keyup', actualizarAval);

            // También puedes llamar a actualizarAval() inicialmente si quieres un valor inicial en 'aval'
            // actualizarAval();
        }
    }

// Asegúrate de llamar a getTotalAppraisal() después de que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', getTotalAppraisal);

document.addEventListener('DOMContentLoaded', function(){
    const select = document.getElementById('listingtypestatus');
    const label = document.getElementById('dual-operation-label');

    if (!select || !label) return; // protección extra por si acaso

    function updateLabel() {
        const selected = select.value;

        if (selected === 'en-venta') {
            label.textContent = '¿La propiedad también está a la renta?';
        } else if (selected === 'alquilar') {
            label.textContent = '¿La propiedad también está a la venta?';
        } else {
            label.textContent = '¿La propiedad también tiene otro tipo de operación?';
        }
    }

    updateLabel();
    select.addEventListener('change', updateLabel);

    const selectListingType = document.querySelector('[name="listingtype"]');
    const btnContainer = document.getElementById('container-btn-create-unit');
    const unitContainer = document.getElementById('create-unit-container');
    const cancelBtn = document.getElementById('cancel-unit-btn');
    const saveBtn = document.getElementById('save-unit-btn');
    const createBtn = document.getElementById('create-unit-btn');

    selectListingType.addEventListener('change', showBtnCreateUnit);

    function showBtnCreateUnit(){
        if (parseInt(this.value) === 41) {
            btnContainer.style.display = 'block';
        } else {
            btnContainer.style.display = 'none';
        }
    }

    function showContainerCreateUnit(){
        if(unitContainer.style.display === "none"){
            unitContainer.style.display = "block";
        } else {
            unitContainer.style.display = "none";
        }
    }

    cancelBtn.addEventListener('click', function () {
        unitContainer.style.display = 'none';
    });

    saveBtn.addEventListener('click', function () {
        createUnit();
    });

    createBtn.addEventListener('click', function () {
        showContainerCreateUnit();
    });

    function createUnit() {

        let product_code = document.getElementById('product_code').value;

        const data = {
            listing_id: product_code, // O el ID real de tu proyecto
            name: document.getElementById('unit_name').value,
            unit_number: document.getElementById('unit_number').value,
            floor: document.getElementById('unit_floor').value,
            area_m2: document.getElementById('unit_area').value,
            bedrooms: document.getElementById('unit_rooms').value,
            unit_type: document.getElementById('unit_type').value,
            bathrooms: document.getElementById('unit_bathrooms').value,
            price: document.getElementById('unit_price').value,
            min_price: document.getElementById('unit_min_price').value,
            rent_price: document.getElementById('unit_rent_price').value,
            description: document.getElementById('unit_description').value,
        };

        fetch("{{ route('units.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(error => {
                    console.log(error);
                    throw new Error('Error al guardar la unidad: ' + JSON.stringify(error));
                });
            }
            return response.json();
        })
        .then(data => {
            alert('✅ Unidad creada correctamente');
            document.getElementById('unit_name').value = "";
            document.getElementById('unit_number').value = "";
            document.getElementById('unit_type').value = "";
            document.getElementById('unit_floor').value = "";
            document.getElementById('unit_area').value = "";
            document.getElementById('unit_rooms').value = "";
            document.getElementById('unit_bathrooms').value = "";
            document.getElementById('unit_price').value = "";
            document.getElementById('unit_min_price').value = "";
            document.getElementById('unit_rent_price').value = "";
            document.getElementById('unit_description').value = "";
        })
        .catch(error => {
            console.error(error);
            alert('❌ Ocurrió un error al crear la unidad');
        });
    }

})

function showCustomizedPrice(){

    let containerCustomizedPrice = document.getElementById('container-customized-price');

    if(containerCustomizedPrice.style.display == "none"){
        containerCustomizedPrice.style.display = 'block';
        document.getElementById('customized_price').required = true;
    } else {
        containerCustomizedPrice.style.display = 'none';
        document.getElementById('customized_price').required = false;
    }

}


    function requiredFalse(available_value){
        if(available_value == 2){
            let inputs = document.getElementsByTagName('input');
            for (let i = 0; i < inputs.length; i++) {
                const element = inputs[i];
                element.required = false;
                    if(element.pattern != null){
                        element.pattern = ".{0,500}";
                    }
                }
            }
        }

    
    let closemodal = document.querySelectorAll('.modal-close')
    for (let i = 0; i < closemodal.length; i++) {
      closemodal[i].addEventListener('click', toggleModalStatus)
    }
    
    
    function toggleModalStatus (selectchange) {
        
        let txttitlemodal = document.getElementById('txttitlemodal');
        switch (selectchange) {
            case "status": txttitlemodal.innerHTML = "Por favor, indique la razón por la cual se desactiva la propiedad"; break;
            case "plan"  : txttitlemodal.innerHTML = "Por favor, indique la razón por la cual se activa la propiedad gratis"; break;
            case "available": txttitlemodal.innerHTML = "Por favor, ingrese la razón por la cual la propiedad ya no está disponible"; break;
            default:break;
        }
        console.log('entra aqui');
        const body = document.querySelector('body')
        const modal = document.querySelector('.modal')
        modal.classList.toggle('opacity-0')
        modal.classList.toggle('pointer-events-none')
        body.classList.toggle('modal-active')
        //saveandclose();
    }

   

    function toggleModalSuccess(){
        const body = document.querySelector('body')
        const modal = document.querySelector('.modalSuccess')
        modal.classList.toggle('opacity-0')
        modal.classList.toggle('pointer-events-none')
        body.classList.toggle('modal-active')
    }


    let sellistingtype = document.querySelector("select[name='listing_type']");
    let divnumfactura = document.getElementById('numfactura');

    if(sellistingtype){
        sellistingtype.addEventListener('change', () => {
            if(sellistingtype.value == 2){
                divnumfactura.classList.remove('hidden');
                divnumfactura.classList.add('block');
                //document.querySelector("input[name='num_factura']").required = true;
            } else if(sellistingtype.value == 1 || sellistingtype.value == ""){
                divnumfactura.classList.remove('block');
                divnumfactura.classList.add('hidden');
                //document.querySelector("input[name='num_factura']").required = false;
            }
        });
    }

    let first_fragment = document.getElementById('first');
    let second_fragment = document.getElementById('second');
    let third_fragment = document.getElementById('third');
    //let fourth_fragment = document.getElementById('fourth');

    let btnfragment1 = document.getElementById('btnfragment1');
    let btnfragment2 = document.getElementById('btnfragment2');
    let btnfragment3 = document.getElementById('btnfragment3');
    //let btnfragment4 = document.getElementById('btnfragment4');

    const changefragment = (id) => {
        switch (id) {
            case 'first': first_fragment.style.display='block';second_fragment.style.display='none';third_fragment.style.display='none';setfragmentvalue('first');changeclass('first');break;
            case 'second': first_fragment.style.display='none';second_fragment.style.display='block';third_fragment.style.display='none';setfragmentvalue('second');changeclass('second'); 
            let map = L.map('map').setView([-2.9003262789921513, -79.00517388859988], 13);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            let inpLat = document.getElementById('lat');
            let inpLng = document.getElementById('lng');

            if((inpLat.value != null || inpLat.value > 0) && (inpLng.value != null || inpLng.value > 0)){
                L.marker([inpLat.value, inpLng.value]).addTo(map)
                    .bindPopup(`<a target='blank' href='https://api.whatsapp.com/send?text=https://maps.google.com/?q=${inpLat.value},${inpLng.value}'>Compartir Ubicación</a>`)
                    .openPopup();
            }

            let marker = null;
            const apiKey = "AAPK6cd0390360a34c47abb6992f612c3a4eHDSN5oz15wvKsDnnOXQAT1xiCNYDtP4B8XRcytqys3UphqELHcSD_tlTbsijCbGz";
            map.on('click', (event)=> {

                if(marker !== null){
                    map.removeLayer(marker);
                }

                marker = L.marker([event.latlng.lat , event.latlng.lng]).addTo(map);

                inpLat.value = event.latlng.lat;
                inpLng.value = event.latlng.lng;
                
            });

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

            const results = L.layerGroup().addTo(map);

            searchControl.on("results", function (data) {
                results.clearLayers();
                for (let i = data.results.length - 1; i >= 0; i--) {
                results.addLayer(L.marker(data.results[i].latlng));
                }
            });

            break;
            case 'third': first_fragment.style.display='none';second_fragment.style.display='none';third_fragment.style.display='block';setfragmentvalue('third');changeclass('third');break;
            default: break;
        }
    }

    const setfragmentvalue = (value) => {
        document.querySelector("input[name='fragment']").value = value;
    }

    const changeclass = (fragment) => {
        if("{{ $currentRouteName == 'admin.housing.property.edit'}}"){
            switch (fragment) {
                case 'first': btnfragment1.classList.add('bg-blue-900', 'text-white', 'px-5');btnfragment2.classList.remove('bg-blue-900', 'text-white', 'px-5');btnfragment3.classList.remove('bg-blue-900', 'text-white', 'px-5');break;
                case 'second': btnfragment1.classList.remove('bg-blue-900', 'text-white', 'px-5');btnfragment2.classList.add('bg-blue-900', 'text-white', 'px-5');btnfragment3.classList.remove('bg-blue-900', 'text-white', 'px-5');break;
                case 'third': btnfragment1.classList.remove('bg-blue-900', 'text-white', 'px-5');btnfragment2.classList.remove('bg-blue-900', 'text-white', 'px-5');btnfragment3.classList.add('bg-blue-900', 'text-white', 'px-5');break;
                case 'fourth': btnfragment1.classList.remove('bg-blue-900', 'text-white', 'px-5');btnfragment2.classList.remove('bg-blue-900', 'text-white', 'px-5');btnfragment3.classList.remove('bg-blue-900', 'text-white', 'px-5');break;
                default:break;
            }
        } else if("{{ $currentRouteName == 'admin.promotora.property.edit'}}"){
            switch(fragment){
                case 'first': btnfragment1.classList.add('bg-red-800', 'text-white', 'px-5');btnfragment2.classList.remove('bg-red-800', 'text-white', 'px-5');btnfragment3.classList.remove('bg-red-800', 'text-white', 'px-5');break;
                case 'second': btnfragment1.classList.remove('bg-red-800', 'text-white', 'px-5');btnfragment2.classList.add('bg-red-800', 'text-white', 'px-5');btnfragment3.classList.remove('bg-red-800', 'text-white', 'px-5');break;
                case 'third': btnfragment1.classList.remove('bg-red-800', 'text-white', 'px-5');btnfragment2.classList.remove('bg-red-800', 'text-white', 'px-5');btnfragment3.classList.add('bg-red-800', 'text-white', 'px-5');break;
                case 'fourth': btnfragment1.classList.remove('bg-red-800', 'text-white', 'px-5');btnfragment2.classList.remove('bg-red-800', 'text-white', 'px-5');btnfragment3.classList.remove('bg-red-800', 'text-white', 'px-5');break;
                default:break;
            }
        } else {
            switch (fragment) {
                case 'first': btnfragment1.classList.add('bg-red-600', 'text-white', 'px-5');btnfragment2.classList.remove('bg-red-600', 'text-white', 'px-5');btnfragment3.classList.remove('bg-red-600', 'text-white', 'px-5');break;
                case 'second': btnfragment1.classList.remove('bg-red-600', 'text-white', 'px-5');btnfragment2.classList.add('bg-red-600', 'text-white', 'px-5');btnfragment3.classList.remove('bg-red-600', 'text-white', 'px-5');break;
                case 'third': btnfragment1.classList.remove('bg-red-600', 'text-white', 'px-5');btnfragment2.classList.remove('bg-red-600', 'text-white', 'px-5');btnfragment3.classList.add('bg-red-600', 'text-white', 'px-5');break;
                case 'fourth': btnfragment1.classList.remove('bg-red-600', 'text-white', 'px-5');btnfragment2.classList.remove('bg-red-600', 'text-white', 'px-5');btnfragment3.classList.remove('bg-red-600', 'text-white', 'px-5');break;
                default:break;
            }
        }
    } 

    const saveandclose = (event) => {
        if(!validate() || validateCommentChangePrice()){
            event.preventDefault();
            return;
        }
        let input_hidden_edit = document.querySelector("input[name='edit']");
        let form = document.getElementById('formsave');
        if(!input_hidden_edit){
            let input = document.createElement('input');
            input.type = "hidden"; input.name = "edit"; input.value = 1;
            form.appendChild(input);
        } else {
            form.appendChild(input_hidden_edit);
        }
        let valueStatus = document.querySelector("select[name='status']");
        let valuePlan = document.querySelector("select[name='listing_type']").value;
        let valueAvailable = document.querySelector("select[name='available']").value;
        if(valueStatus) {
            valueStatus = valueStatus.value;
            if(selstatus == 1 && valueStatus == 0){
                ischangestatus = true;
                event.preventDefault();
                toggleModalStatus("status");
            } else if(valuePlan == 1 && selstatus == 0 && valueStatus == 1){
                ischangeplan = true;
                event.preventDefault();
                toggleModalStatus('plan');
            } else if(valueAvailable == 2 && selavailable == 1) {
                ischangeavailable = true;
                event.preventDefault();
                toggleModalStatus('available');
            }
        } else {
            form.submit();
        }
    }

    const removeelement = () => {
        let input = document.querySelector("input[name='edit']");
        if(input) input.remove();
    }

    let checkbox_characteristics = document.querySelectorAll(".checkbox-characteristic");
    
    checkbox_characteristics.forEach(element => {
        if(element.value == 7){
            element.addEventListener('change', () => {
                if(element.checked && element.value == 7){document.querySelector("input[name='niv_constr']").classList.remove('hidden');document.querySelector("input[name='niv_constr']").classList.add('block');} else {document.querySelector("input[name='niv_constr']").classList.remove('block');document.querySelector("input[name='niv_constr']").classList.add('hidden');document.querySelector("input[name='niv_constr']").value = 0;}
            });
        }
        if(element.value == 8){
            element.addEventListener('change', () => {
                if(element.checked && element.value == 8){document.querySelector("input[name='num_pisos']").classList.remove('hidden');document.querySelector("input[name='num_pisos']").classList.add('block')} else {document.querySelector("input[name='num_pisos']").classList.remove('block');document.querySelector("input[name='num_pisos']").classList.add('hidden'); document.querySelector("input[name='num_pisos']").value = 0;}
            });
        }
        if(element.value == 15){
            element.addEventListener('change', () => {
                if(element.checked && element.value == 15){document.querySelector("input[name='pisos_constr']").classList.remove('hidden');document.querySelector("input[name='pisos_constr']").classList.add('block')} else {document.querySelector("input[name='pisos_constr']").classList.remove('block');document.querySelector("input[name='pisos_constr']").classList.add('hidden');document.querySelector("input[name='pisos_constr']").value = 0;}
            });
        }
    });

    const countCharacterMetaDescription = () => {
        let txtMetaDescription = document.getElementById('meta_description');
        let label_count_metadescription = document.getElementById('label_count_metadescription');
        label_count_metadescription.innerHTML = txtMetaDescription.value.length;

        let div_info_character_meta = document.getElementById('div_info_character_meta');
        if(txtMetaDescription.value.length <= 150 && txtMetaDescription.value.length >= 140){
            div_info_character_meta.style.backgroundColor = "#9AE6B4";
        } else {
            div_info_character_meta.style.backgroundColor = "#FEB2B2";
        }
    }

    const setPreviewOnGoogle = () => {
        let preview_title = document.getElementById('preview_title');
        let preview_link = document.getElementById('preview_link');
        let preview_meta = document.getElementById('preview_meta');

        let listing_title = document.getElementById('listing_title');
        let meta_description = document.getElementById('meta_description');
        const url = "https://grupohousing.com/propiedad/";
        
        listing_title ? preview_link.innerHTML = url+slugify(listing_title.value) : preview_link.innerHTML = url+slugify('Ejemplo de Titulo de Buscador en Google');
        listing_title ? preview_title.innerHTML = listing_title.value : preview_title.innerHTML = 'Ejemplo de Titulo en Buscador de Google';
        meta_description ? preview_meta.innerHTML = meta_description.value : preview_meta.innerHTML = 'Ejemplo de Meta Description en Buscador de Google';

    }

    const slugify = str =>
        str
            .toString()
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');

    const validateMinPrice = () => {

        let property_price = document.getElementById('property_price');
        let listingtypestatus = document.getElementById('listingtypestatus');

        if(property_price.value != "" && property_price.value < 400 && listingtypestatus.value == "alquilar"){
            let steptow = document.getElementById('second');
            let inputs = steptow.querySelectorAll('input, select, textarea, button');
            inputs.forEach(element => {
                element.disabled = true;
            })
            Swal.fire({
                title: 'Advertencia',
                text: 'El sistema no permite propiedades de alquiler menores a $400. Todos los campos se bloquearon para no subir la propiedad',
                icon: 'warning',
                iconColor: 'red',
                confirmButtonText: 'Salir',
                confirmButtonColor: 'red',
                allowOutsideClick: false // Evitar que se cierre al hacer clic fuera
            });
        }
    }

    </script>
@endsection