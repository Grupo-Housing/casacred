<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{asset('favicon-grupo-housing.png')}}" type="image/x-icon" />
    <link rel="stylesheet" href="{{asset('css/app.css?'.uniqid())}}">

    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    @yield('firstscript')
</head>
<body>

@php
    $propertiesAt30Days = App\Models\Listing::select('id', 'listing_title', 'product_code', 'images')->whereDate('created_at', '=', Illuminate\Support\Carbon::now()->subDay(30))->get();        
@endphp


<!-- component -->
<div>
    <script src="{{asset('js/alpine.min.js')}}" defer></script>
    
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">

        <div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false" class="fixed z-20 inset-0 bg-black opacity-50 transition-opacity lg:hidden"></div>
    
        <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'" class="fixed z-30 inset-y-0 left-0 w-52 transition duration-300 transform @if(Route::currentRouteName() != "admin.housing.property.create") bg-gray-500 @endif overflow-y-auto lg:translate-x-0 lg:static lg:inset-0" style="background-color: @if(Route::currentRouteName() == "admin.housing.property.create" || Route::currentRouteName() == "admin.housing.property.edit" || str_contains(Auth::user()->email, 'housingrentgroup')) #1C2444 @elseif(Route::currentRouteName()=='admin.promotora.property.create' || Route::currentRouteName() == "admin.promotora.property.edit") #670C07 @endif">
            <div class="flex items-center justify-center @if(Request::is('admin/show-listing*')) mt-3 @else mt-4 @endif">
                <div class="flex items-center">
                    @if(Route::currentRouteName() == "admin.housing.property.create" || Route::currentRouteName() == "admin.housing.property.edit" || str_contains(Auth::user()->email, 'housingrentgroup'))
                        <img class="h-14" src="{{asset('img/logo-footer.png')}}">
                    @elseif (Route::currentRouteName() == "admin.promotora.property.create" || Route::currentRouteName() == "admin.promotora.property.edit")
                        <img class="h-12" src="{{ asset('img/logo-promotora.png') }}" alt="">
                    @else
                    <img class="h-12" src="{{asset('img/logo-azul-grupo-housing.png')}}">
                    @endif
                </div>
            </div>
    
            <nav class="mt-4">                
    
                @php $actual_link = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; @endphp
                @if(strpos($actual_link, 'crm8')) 
                <a href="{{route('home.tw')}}" class="flex items-center px-4 text-sm bg-yellow-500 font-bold">
                    <span class="mx-3 py-4">Localhost</span>
                </a> 
                @endif

                <a style="text-decoration: none !important" href="{{route('admin.index')}}" class="flex items-center @if(Request::is('admin/show-listing*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('admin')) border-l-4 border-white bg-gray-900 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                    <span class="@if(Request::is('admin/show-listing*')) py-3  @else py-4 @endif  mx-3">Dashboard</span>
                </a>               
    
                <button  aria-controls="dropdown-example" data-collapse-toggle="dropdown-example" style="text-decoration: none !important" href="{{route('admin.listings.create')}}" class="flex items-center @if(Request::is('admin/show-listing*')) px-1 @else px-4 @endif text-sm text-white focus:outline-none @if(Request::is('admin/listings/create')) border-l-4 border-white bg-red-800 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white w-full">
                    <span class="mx-3 @if(Request::is('admin/show-listing*')) py-3 @else py-4 @endif">Crear Propiedad</span>
                    
                </button>
                <ul id="dropdown-example" class="hidden py-2 space-y-2 border w-52">
                    <li class="flex items-center">
                        <a href="{{ route('admin.listings.create') }}"
                            class="flex items-center w-full py-3 text-sm font-semibold text-white transition duration-75 px-2 group hover:bg-gray-600"><img width="50px" src="{{ asset('img/logo-azul-grupo-housing.png') }}" alt="" class="pr-3"> Grupo Housing</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.promotora.property.create') }}"
                            class="flex items-center w-52 py-3 text-sm font-semibold text-white transition duration-75 px-2 group hover:bg-gray-600"><img width="50px" src="{{ asset('img/logo-promotora.png') }}" alt="" class="pr-3"> Promotora</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.housing.property.create') }}"
                            class="flex items-center w-52 py-3 text-sm font-semibold text-white transition duration-75 px-2 group hover:bg-gray-600"><img width="50px" src="{{ asset('img/logo-housing-rent.png') }}" alt="" class="pr-3"> Housing Rent</a>
                    </li>
                </ul>
    
                <a style="text-decoration: none !important" href="{{route('admin.properties')}}" class="flex items-center @if(Request::is('admin/show-listing*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('admin/properties*') || (Request::is('admin/show-listing*') && Str::contains(URL::previous(), 'admin/properties'))) border-l-4 border-white bg-gray-900 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                    <span class="mx-3 @if(Request::is('admin/show-listing*')) py-3 @else py-4 @endif">Propiedades</span>
                </a>
                <a style="text-decoration: none !important" href="{{route('admin.history')}}" class="flex items-center @if(Request::is('admin/show-listing*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('admin/history*') || (Request::is('admin/history*') && Str::contains(URL::previous(), 'admin/properties'))) border-l-4 border-white bg-gray-800 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                    <span class="mx-3 @if(Request::is('admin/show-listing*')) py-3 @else py-4 @endif">Historial</span>
                </a>
            {{-- @if(Auth::user()->role!="administrator") --}}
                <a style="text-decoration: none !important" href="{{ route('admin.myproperties') }}" class="flex items-center @if(Request::is('admin/show-listing*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('admin/my-properties*') || (Request::is('admin/listings/*/edit') && Str::contains(URL::previous(), 'admin/my-properties'))) border-l-4 border-white bg-gray-800 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                    <span class="@if(Request::is('admin/show-listing*')) py-3 @else py-4 mx-3 @endif">Mis Propiedades</span>
                </a> 
            {{-- @endif          --}}
                <a style="text-decoration: none !important" href="{{route('admin.reports')}}" class="flex items-center @if(Request::is('admin/reports*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('admin/reports*') || (Request::is('admin/reports*') && Str::contains(URL::previous(), 'admin/reports'))) border-l-4 border-white bg-gray-900 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                    <span class="@if(Request::is('admin/show-listing*')) py-3 @else py-4 mx-3 @endif">Reportes</span>
                </a>
                <a style="text-decoration: none" href="{{ Route('updated.properties') }}" class="flex items-center px-4 text-sm text-white hover:bg-gray-700 hover:bg-opacity-25 hover:text-white @if(Request::is('admin/updated-properties*') || (Request::is('admin/updated-properties*') && Str::contains(URL::previous(), 'admin/updated-properties'))) border-l-4 border-white bg-gray-900 @endif">
                    <span class="@if(Request::is('admin/show-listing*')) py-3 @else py-4 mx-3 @endif font-semibold">Actualización de propiedades</span>
                </a>

                @if(Auth::user()->id == 900 || Auth::user()->id == 901 || Auth::user()->id == 147)
                    <button 
                        onclick="document.getElementById('contact-queue-panel').style.display='block'; document.getElementById('contact-queue-panel').__x.$data.panelVisible = true"
                        class="flex items-center w-full px-4 text-sm text-white hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                        <span class="mx-3 py-4">📞 Cola de Contacto</span>
                    </button>
                @endif

            @if (Auth::id()==123 || Auth::id()==147 || Auth::id()==15 || Auth::user()->id == 148 || Auth::user()->id == 929 || Auth::user()->id == 193 || Auth::user()->id == 934)

                <a style="text-decoration: none" href="{{ route('admin.notifications') }}" class="flex items-center @if(Request::is('admin/show-listing*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('admin/notification*')) border-l-4 border-white bg-gray-800 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                    <span class="mx-3 @if(Request::is('admin/show-listing*')) py-3 @else py-4 @endif">
                        Notificaciones
                    </span>
                    <div id="divnotification" style="display: none" class="rounded-md bg-red-500 px-1">0</div>
                </a>
            
                <a style="text-decoration: none" href="{{route('admin.services.index')}}" class="flex items-center @if(Request::is('admin/show-listing*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('admin/service*')) border-l-4 border-white bg-gray-800 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                    <span class="mx-3 @if(Request::is('admin/show-listing*')) py-3 @else py-4 @endif">Servicios</span>
                </a>        
    
                <a style="text-decoration: none" href="{{route('users.index')}}" class="flex items-center @if(Request::is('admin/show-listing*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('admin/users*')) border-l-4 border-white bg-gray-800 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                    <span class="mx-3 @if(Request::is('admin/show-listing*')) py-3 @else py-4 @endif">Usuarios</span>
                </a>
                
                <a style="text-decoration: none !important" href="{{route('admin.soldout')}}" class="flex items-center @if(Request::is('admin/show-listing*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('admin/sold-out*') || (Request::is('admin/show-listing*') && Str::contains(URL::previous(), 'admin/sold-out'))) border-l-4 border-white bg-gray-800 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                    <span class="mx-3 @if(Request::is('admin/show-listing*')) py-3 @else py-4 @endif">Archivadas</span>
                </a>
                
            @endif
            @if(Auth::user()->id == 148 || Auth::user()->email == "developer2@casacredito.com" || Auth::user()->id == 929)
            <a style="text-decoration: none" href="{{route('admin.seo.index')}}" class="flex items-center @if(Request::is('admin/seo*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('admin/seo*')) border-l-4 border-white bg-gray-800 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                <span class="@if(Request::is('admin/show-listing/*')) py-3 @else py-4 mx-3 @endif">SEO</span>
            </a>
            <a style="text-decoration: none" href="{{route('admin.post.index')}}" class="flex items-center @if(Request::is('admin/post*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('admin/post*')) border-l-4 border-white bg-gray-800 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                <span class="@if(Request::is('admin/show-listing/*')) py-3 @else py-4 mx-3 @endif">Blog</span>
            </a>
            @endif
                @if (Auth::id()==123)
                <a style="text-decoration: none !important" href="{{route('profile.show')}}" class="flex items-center @if(Request::is('admin/show-listing*')) px-2 @else px-4 @endif text-sm text-white @if(Request::is('user/profile')) border-l-4 border-white bg-red-800 @endif hover:bg-gray-700 hover:bg-opacity-25 hover:text-white">
                    <span class="mx-3 @if(Request::is('admin/show-listing*')) py-3 @else py-4 @endif">Perfil</span>
                </a>
                @endif
    
                
            </nav>
        </div>
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex justify-between items-center py-1 px-6 @if(str_contains(Auth::user()->email, 'casacredito')) bg-gray-300 @endif border-b-4 border-gray-800" @if(str_contains(Auth::user()->email, "housingrentgroup")) style="background-color: #1C2444" @endif>
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>
    
                <div class="flex items-center">
                    <div x-data="{ notificationOpen: false }" class="relative hidden">
                        <button @click="notificationOpen = ! notificationOpen"
                            class="flex mx-4 text-gray-600 focus:outline-none">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15 17H20L18.5951 15.5951C18.2141 15.2141 18 14.6973 18 14.1585V11C18 8.38757 16.3304 6.16509 14 5.34142V5C14 3.89543 13.1046 3 12 3C10.8954 3 10 3.89543 10 5V5.34142C7.66962 6.16509 6 8.38757 6 11V14.1585C6 14.6973 5.78595 15.2141 5.40493 15.5951L4 17H9M15 17V18C15 19.6569 13.6569 21 12 21C10.3431 21 9 19.6569 9 18V17M15 17H9"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </svg>
                        </button>
    
                        <div x-show="notificationOpen" @click="notificationOpen = false"
                            class="fixed inset-0 h-full w-full z-10" style="display: none;"></div>
    
                        <div x-show="notificationOpen"
                            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-10"
                            style="width: 20rem; display: none;">
                            <a href="#"
                                class="flex items-center px-4 py-3 text-gray-600 hover:text-white hover:bg-indigo-600 -mx-2">
                                <img class="h-8 w-8 rounded-full object-cover mx-1"
                                    src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=334&amp;q=80"
                                    alt="avatar">
                                <p class="text-sm mx-2">
                                    <span class="font-bold" href="#">Sara Salah</span> replied on the <span
                                        class="font-bold text-indigo-400" href="#">Upload Image</span> artical . 2m
                                </p>
                            </a>
                            <a href="#"
                                class="flex items-center px-4 py-3 text-gray-600 hover:text-white hover:bg-indigo-600 -mx-2">
                                <img class="h-8 w-8 rounded-full object-cover mx-1"
                                    src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=634&amp;q=80"
                                    alt="avatar">
                                <p class="text-sm mx-2">
                                    <span class="font-bold" href="#">Slick Net</span> start following you . 45m
                                </p>
                            </a>
                            <a href="#"
                                class="flex items-center px-4 py-3 text-gray-600 hover:text-white hover:bg-indigo-600 -mx-2">
                                <img class="h-8 w-8 rounded-full object-cover mx-1"
                                    src="https://images.unsplash.com/photo-1450297350677-623de575f31c?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=334&amp;q=80"
                                    alt="avatar">
                                <p class="text-sm mx-2">
                                    <span class="font-bold" href="#">Jane Doe</span> Like Your reply on <span
                                        class="font-bold text-indigo-400" href="#">Test with TDD</span> artical . 1h
                                </p>
                            </a>
                            <a href="#"
                                class="flex items-center px-4 py-3 text-gray-600 hover:text-white hover:bg-indigo-600 -mx-2">
                                <img class="h-8 w-8 rounded-full object-cover mx-1"
                                    src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=398&amp;q=80"
                                    alt="avatar">
                                <p class="text-sm mx-2">
                                    <span class="font-bold" href="#">Abigail Bennett</span> start following you . 3h
                                </p>
                            </a>
                        </div>
                    </div>
    
                    <div x-data="{ dropdownOpen: false }" class="relative flex">

                        <div class="flex items-center px-2 cursor-pointer" onclick="openOrCloseNotifications()">
                            <i class="fa-solid fa-bell  @if(count($propertiesAt30Days)>0) text-red-600 @endif"></i>
                            <span class="bg-white absolute rounded-full text-xs px-1 top-0 ml-1">1</span>
                        </div>

                        <div id="notifications" class="absolute mt-8 bg-white w-80 h-64 px-3 rounded py-3 z-10 overflow-y-auto hidden" style="margin-left: -290px !important">
                            <p class="text-sm font-semibold">Propiedades creadas hace 30 días</p>
                            <hr class="mt-1">
                            @foreach ($propertiesAt30Days as $propertie)
                                <a target="_blank" href="{{ route('admin.show.listing', $propertie->id) }}">
                                    <div class="flex gap-6 border my-2 p-3">
                                        <div>
                                            <span class="text-xs">{{ $propertie->product_code }}</span>
                                            <p class="text-xs">{{ $propertie->listing_title }}</p>
                                        </div>
                                        <div class="flex items-center">
                                            <img style="" src="{{ asset('uploads/listing/300/'. explode('|', $propertie->images)[0]) }}" alt="">
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>


                        <button @click="dropdownOpen = ! dropdownOpen"
                            class="relative block h-8 overflow-hidden focus:outline-none @if(str_contains(Auth::user()->email, "housingrentgroup")) text-white @endif">
                           {{Auth::user()->name}}
                        </button>
    
                        <div x-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 h-full w-full z-10"
                            style="display: none;"></div>
    
                        <div x-show="dropdownOpen"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-10"
                            style="display: none;">
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-800 hover:text-white"
                                    href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                    {{ __('Cerrar Sesión') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form></a>
                        </div>
                    </div>
                </div>
            </header>
            
                    @yield('content')

        </div>
    </div>

    <x-contact-queue-carousel />
</div>
@yield('endscript')
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/flowbite@1.3.4/dist/flowbite.js"></script>
<script src="{{ asset('js/openNotifications.js') }}" defer></script>
<script>
    window.addEventListener('load', function(){
        myFunction();
    });

    function myFunction(){
        $.ajax({
            url: "{{route('admin.count.notifications')}}",
            type: "GET",
            success: function(data){
                // alert(data);
                let divnotification = document.getElementById('divnotification');
                if(divnotification){
                    if(data > 0){
                        divnotification.innerHTML = parseInt(data);
                        divnotification.style.display = "block";
                    } else {
                        divnotification.style.display = "none";
                        divnotification.innerHTML = "0";
                    }
                }
            },
            error: function(error){
                //alert(error);
                alert('Algo salio mal, por favor recargue la página');
            }
        });
    }

    setInterval(() => {
        myFunction();
    }, 300000);
    
</script>
</body>
</html>