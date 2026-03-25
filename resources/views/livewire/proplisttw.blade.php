<div class="bg-white pb-10">
    @if(count($properties)>0)
    @if($view == 'grid')
    <div class="grid sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mx-4">
        @foreach ($properties as $propertie)
            @php
                $firstImg = array_filter(explode("|", $propertie->images)) ;
                $dirImg = $firstImg[0]??'';

                //call to customers in X days
                $createdDay = Illuminate\Support\Carbon::parse($propertie->contact_at)->addDays(31);
                $now = Illuminate\Support\Carbon::now();

                if($now > $createdDay){
                    $callAt = 0;
                } else {
                    $callAt = $createdDay->diffInDays($now);
                }
                
            @endphp
            <div class="rounded overflow-hidden shadow-lg w-full relative mt-4 mb-2 hover-trigger pb-2">
                {{-- web.detail  --}}
                {{-- {{route('admin.listings.edit',$propertie->id)}} --}}
                @if(Auth::user()->role == "user" || Auth::user()->role == "ASESOR")
                    <a href="@if(($url_current == "admin.myproperties" || Route::current()->getName() == "admin.myproperties") && $propertie->property_by == "Housing") {{ route('admin.housing.property.edit', $propertie) }} @elseif(($url_current == "admin.myproperties" || Route::current()->getName() == "admin.myproperties") && $propertie->property_by == "Promotora") {{ route('admin.promotora.property.edit', $propertie) }} @elseif($url_current == "admin.myproperties" || Route::current()->getName() == "admin.myproperties" && $propertie->property_by == "" || $propertie->property_by == "Casa Credito") {{ route('admin.listings.edit', $propertie->id) }} @elseif(Route::currentRouteName() == "admin.properties") {{route('admin.show.listing', $propertie->id)}} @endif" target="_blank">
                @endif
                
                <div class="relative">
                    @if($dirImg != null || $dirImg != "")
                        @php
                            $imageVerification = asset('uploads/listing/thumb/600/'.$dirImg);
                        @endphp
                        <a target="_blank" href="@if($url_current == "admin.myproperties" || Route::current()->getName() == "admin.myproperties" && $propertie->property_by == 'Housing') {{ route('admin.housing.property.edit', $propertie) }} @elseif($url_current == "admin.myproperties" || Route::current()->getName() == "admin.myproperties" && $propertie->property_by == "Casa Credito") {{ route('home.tw.edit', $propertie) }} @elseif($url_current == "admin.myproperties" || Route::current()->getName() == "admin.myproperties" && $propertie->property_by == 'Promotora') {{ route('admin.promotora.property.edit', $propertie) }} @else {{ route('admin.show.listing', $propertie) }} @endif"><img class="w-full" src="@if(file_exists(public_path().'/uploads/listing/thumb/600/'.$dirImg)) {{ url('/uploads/listing/thumb/600/', $dirImg) }} @else {{url('uploads/listing/600', $dirImg)}} @endif" alt="{{ $propertie->listing_title}}"></a>
                        {{-- @if(@getimagesize($imageVerification)) {{ url('/uploads/listing/thumb/600/', $dirImg) }} @else {{url('uploads/listing/600', $dirImg)}} @endif --}}
                    @else
                        <a target="_blank" href="@if($url_current == "admin.myproperties" || Route::current()->getName() =="admin.myproperties" && $propertie->property_by == 'Housing') {{ route('admin.housing.property.edit', $propertie) }} @elseif($url_current == "admin.myproperties" || Route::current()->getName() =="admin.myproperties" && $propertie->property_by == 'Promotora') {{ route('admin.promotora.property.edit', $propertie) }} @elseif($url_current == "admin.myproperties" || Route::current()->getName() =="admin.myproperties" && $propertie->property_by == '' || $propertie->property_by == "Casa Credito") {{route('admin.listings.edit', $propertie->id) }} @else {{ route('admin.show.listing', $propertie) }} @endif"><img class="w-full" src="{{ asset('img/sin-imagenes.jpg') }}" alt="Sunset in the mountains"></a>
                        {{-- <div class="absolute top-0 left-0 w-full h-full" style="display: flex; justify-content: center; align-items: center">
                            <p style="color: #ffffff" class="flex">Sin imágenes</p>
                        </div> --}}
                    
                    @endif
                    @if(Auth::user()->role == "administrator")
                        <div class="absolute bottom-0 right-0">
                            @if($callAt == 0)
                                <p class="bg-red-500 text-white px-2 pt-1 pb-2 cursor-pointer" onclick="setIdModalContactar('{{ $propertie->id }}', '{{$propertie->product_code}}', '{{ $propertie->owner_name}}', '{{ $propertie->phone_number}}', '{{ $propertie->listing_title}}')"></i> Contactar ahora</p>
                            @else
                                <p class="bg-yellow-500 text-white px-2 pt-1 pb-2">Contactar en {{ $callAt }} dias</p>
                            @endif
                            <div id="popup{{$propertie->id}}" class="bg-gray-100 w-auto lg:w-80 border hidden popup" style="z-index: 100 !important">
                                <div>
                                    <div class="flex justify-end" onclick="closeModalContact('{{$propertie->id}}')">
                                        <span class="text-white cursor-pointer bg-red-700 px-3 rounded">X</span>
                                    </div>
                                    <div class="p-4 md:p-5">
                                            <div>
                                                <div>
                                                    <input type="hidden" name="product_id" id="product_code_modal_contact">
                                                    <label for="comment">Comentario</label> <br>
                                                    <input type="text" name="comment" id="commentContactDay{{$propertie->id}}" class="bg-gray-200 px-2 py-2 w-full border-none mt-2 mb-2">
                                                </div>
                                                <div>
                                                    <label for="date">Fecha de contacto</label>
                                                    <input type="date" name="date" id="dateContactDay{{$propertie->id}}" class="bg-gray-200 px-2 py-2 w-full border-none mt-2">
                                                </div>
                                                <div class="mt-5 text-center">
                                                    <button class="bg-green-500 text-white px-5 py-2 rounded" onclick="saveContactDay('{{$propertie->id}}')">GUARDAR</button>
                                                </div>
                                            </div>
                                        {{-- </form> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="absolute left-0 top-0">
                    @if($propertie->status == 1)
                        <div class="text-xs font-semibold" style="margin-top: 5px; margin-left:5px; border-radius: 10px; width: 10px; height: 10px; background-color: #01842a; border-radius: 25px">
                            {{-- <img width="35px" src="{{ asset('img/on.png') }}" alt="ON"> --}}
                        </div>
                    @else
                        <div class="text-xs font-semibold" style="margin-top: 5px; margin-left:5px; border-radius: 10px; width: 10px; height: 10px; background-color: #b11213; border-radius: 25px">
                            {{-- <img width="35px" src="{{ asset('img/off.png') }}" alt="OFF"> --}}
                        </div>
                    @endif
                </div>

                <div class="absolute right-0 top-0">
                    @if($propertie->property_by == "Housing")
                        <div style="background-color: #1C2444" class="px-2 py-1">
                            <img width="70px" src="{{ asset('img/logo-footer.png') }}" alt="">
                        </div>
                    @elseif($propertie->property_by == "Promotora")
                        <div style="background-color: #670C07" class="px-2 py-1">
                            <img width="90px" src="{{ asset('img/logo-promotora.png') }}" alt="">
                        </div>
                    @else
                        <div>
                            <img class="bg-white px-2 py-2" width="110px" src="{{ asset('img/logo-azul-grupo-housing.png') }}" alt="">
                        </div>
                    @endif
                </div>

                {{-- @php
                    if($propertie->delete_at != null){
                        $startTime = Illuminate\Support\Carbon::now();
                        $endTime = Illuminate\Support\Carbon::parse($propertie->delete_at);
                        $totalDuration = $endTime->diffInHours($startTime);
                    }
                @endphp
                @if($propertie->delete_at != null)
                    <div class="absolute right-0 top-0 bg-red-500 text-white px-2 rounded">
                        @if($totalDuration > 0)
                        <p>En {{ $totalDuration }} hrs. será eliminada</p>
                        @else
                        <p>La propiedad va a ser eliminada</p>
                        @endif
                    </div>
                @endif --}}


                <div class="px-2 py-2">
                    <div class="text-xs text-gray-500">{{$propertie->created_at->format('d-M-y')}}</div>
                    <div class="font-bold text-sm">{{ Str::limit($propertie->listing_title, 30, '...')}}</div>
                    @php
                        $address = "";
                        if($propertie->address != null && $propertie->sector == null) $address = $propertie->address;
                        if($propertie->sector != null) $address = $propertie->sector;
                    @endphp
                    <p class="text-gray-700 text-base">
                        @if(Str::contains($address, ',')){{ Str::limit($propertie->address, 30, '...')}} @else {{Str::limit($propertie->state . ', ' . $propertie->city . ', ' . $address, 30, '...') }} @endif
                    </p>
                    @if($propertie->address != null)
                        <p class="text-gray-600 text-sm"> <span class="font-bold">Sector:</span> {{ $propertie->address }}</p>
                    @endif
                    @if($propertie->cardinal_zone)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                            @if($propertie->cardinal_zone == 'norte')  bg-blue-100 text-blue-700
                            @elseif($propertie->cardinal_zone == 'sur') bg-yellow-100 text-yellow-700
                            @elseif($propertie->cardinal_zone == 'este') bg-green-100 text-green-700
                            @elseif($propertie->cardinal_zone == 'oeste') bg-purple-100 text-purple-700
                            @else bg-gray-100 text-gray-600 @endif">
                            @if($propertie->cardinal_zone == 'norte') ↑ Norte
                            @elseif($propertie->cardinal_zone == 'sur') ↓ Sur
                            @elseif($propertie->cardinal_zone == 'este') → Este
                            @elseif($propertie->cardinal_zone == 'oeste') ← Oeste
                            @else ⊙ Centro @endif
                        </span>
                    @endif
                    <p>@if(Auth::id()==123)<span style="font-size: 10px">{{$propertie->slug}}</span> <br>@endif</p>
                </div>
                <div class="grid grid-cols-2 px-2 py-2 w-full">
                    <div>
                        <span class="inline-block bg-gray-200 rounded-full px-2 text-sm font-semibold text-gray-700">
                            {{ $propertie->listingtypestatus}}
                        </span>
                        <p class="mx-2 text-red-600 font-extrabold text-xl">
                            @if($propertie->customized_price != null)
                                {{ $propertie->customized_price}}
                            @else
                                ${{ number_format($propertie->property_price)}}
                            @endif
                        </p>
                    </div>
                    <div style="@if($propertie->listingtagstatus==2 && $propertie->listingtype != 26) margin-left: -20px @else margin-left: 0px @endif">
                        <div class="bottom-0 right-0 flex">
                            @if($propertie->available != null)
                            <div class="mr-1">
                                    @if($propertie->available == 2)
                                        <img title="NO DISPONIBLE" width="28px" src="{{asset('img/not-available.png')}}" alt="NOT AVAILABLE">
                                    @else
                                        <img title="DISPONIBLE" width="28px" src="{{asset('img/available.png')}}" alt="AVAILABLE">
                                    @endif
                                </div>
                            @endif
                            <div class="mr-1">
                                @if ($propertie->listing_type==2)
                                    <img width="28px" src="{{ asset('img/pagada.png') }}" alt="Pagada" title="PROPIEDAD PAGADA">
                                @elseif($propertie->listing_type==1)
                                    <img width="28px" src="{{ asset('img/free.png') }}" alt="Gratis" title="PROPIEDAD GRATIS">
                                @endif
                            </div>
                            <div class="mr-1">
                                @if ($propertie->listingtagstatus==2 && $propertie->listingtype != 26 && $propertie->property_by != "Housing")
                                    <img width="28px" src="{{ asset('img/worker.png') }}" alt="Constructora" title="CONSTRUCTORA">
                                @endif
                            </div>
                            <div style="width:80px; height: auto; background-color: #017cd3; color: #ffffff; padding: 3px 10px 3px 10px; border-radius: 10px">
                                <p class="text-xs font-semibold">
                                    COD: {{ $propertie->product_code }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                

                {{-- @if(Auth::user()->role == "administrator")
                <div class="absolute bg-white border border-grey-100 px-4 py-2 hover-target" style="top: 5px; right: 5px;">
                    <div class="flex">
                        <a target="_blank" href="{{ route('admin.show.listing', $propertie) }}" style="text-decoration: none">
                            <p class="text-green-500 text-xs font-bold">Ver propiedad</p>
                        </a>
                    </div>
                    <div class="flex">
                        <a target="_blank" href="{{ route('home.tw.edit', $propertie) }}" style="text-decoration: none">
                            <p class="text-yellow-500 text-xs font-bold">Editar propiedad</p>
                        </a>
                    </div>
                </div>
                @endif --}}
                @if(Auth::user()->role == "user" || Auth::user()->role == "ASESOR")
                    </a>
                @endif

                @if(Auth::user()->email == "developer2@casacredito.com")
                    <div class="flex ml-3">
                        <p>h-{{$propertie->bedroom}}</p>
                        <p>b-{{$propertie->bathroom}}</p>
                        <p>g-{{$propertie->garage}}</p>
                    </div>
                    <div class="mx-3">
                        <p>{{$propertie->slug}}</p>
                    </div>
                @endif

                @if(Auth::user()->role == 'administrator' || $propertie->user_id == Auth::user()->id)
                <div>
                    <div class="flex justify-center">
                        <a target="_blank" class="btn-edit ml-1 p-1 rounded" style="background-color: #c6f6d5" href="@if($propertie->property_by == 'Housing') {{ route('admin.housing.property.edit', $propertie) }} @elseif($propertie->property_by == 'Casa Credito' || $propertie->property_by == null || $propertie->property_by == ""){{ route('home.tw.edit', $propertie) }} @elseif($propertie->property_by == "Promotora") {{ route('admin.promotora.property.edit', $propertie) }} @endif" style="text-decoration: none">
                            <p class="text-black text-sm" style="font-weight: 500">Editar propiedad</p>
                        </a>
                    </div>
                </div>
                @endif
                <div class="flex float-right mr-2">
                    @if($propertie->status && (Auth::user()->email == "marketing@casacredito.com" || Auth::user()->email == "developer2@casacredito.com") && $propertie->slug != null)
                        <a target="_blank" href="{{ route('web.detail', $propertie->slug) }}">
                            <img width="20px" title="Visualizar en el sitio web" src="{{ asset('img/redireccionar.png') }}" alt="">
                        </a>
                    @endif
                    @if($propertie->plusvalia)
                        <div>
                            <img width="12" height="12" src="{{asset('img/plusvalia.png')}}" alt="">
                        </div>
                        @endif
                        <div class="ml-2">
                            <input type="checkbox" value="{{$propertie->listing_title.'|'.$propertie->slug}}" name="propertiestoshare[]" class="checktoshare" onclick="share()">
                        </div>
                    </div>
            </div>
        @endforeach
        <input type="hidden" id="pagActual" value="{{$pagActual}}">
        <input type="hidden" id="firstItem" value="{{$firstItem}}">
        <input type="hidden" id="totalProperties2" value="{{$totalProperties}}">

    </div>

    {{-- DIV PARA SIMILAR PROPERTIES --}}
    @if(count($similar_properties)>0)
    <hr class="mt-3">
    <h3 class="mx-4 text-md mt-3 font-semibold">Propiedades Similares a la Búsqueda</h3>
    <div class="grid sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mx-4">
        @foreach ($similar_properties as $s_propertie)
            @php
                $firstImg = array_filter(explode("|", $s_propertie->images)) ;
                $dirImg = $firstImg[0]??'';
            @endphp
            <div class="rounded overflow-hidden shadow-lg w-full relative mt-4 mb-2 hover-trigger pb-2">
                @if(Auth::user()->role == "user" || Auth::user()->role == "ASESOR")
                    <a href="@if($url_current == "admin.myproperties" || Route::current()->getName() == "admin.myproperties"){{ route('admin.listings.edit', $s_propertie->id) }} @else {{route('admin.show.listing', $s_propertie->id)}}@endif" target="_blank">
                @endif

                @if($dirImg != null || $dirImg != "")
                @php
                    $imageVerification = asset('uploads/listing/thumb/600/'.$dirImg);
                @endphp
                <a target="_blank" href="@if($url_current == "admin.myproperties" || Route::current()->getName() == "admin.myproperties"){{ route('admin.listings.edit', $s_propertie->id) }} @else {{ route('admin.show.listing', $s_propertie) }} @endif"><img class="w-full" src="@if(file_exists(public_path().'/uploads/listing/thumb/600/'.$dirImg)) {{ url('/uploads/listing/thumb/600/', $dirImg) }} @else {{url('uploads/listing/600', $dirImg)}} @endif" alt="{{ $propertie->listing_title}}"></a>
                @else
                    <a target="_blank" href="@if($url_current == "admin.myproperties" || Route::current()->getName() =="admin.myproperties"){{route('admin.listings.edit', $s_propertie->id) }} @else {{ route('admin.show.listing', $s_propertie) }} @endif"><img class="w-full" src="{{ asset('img/sin-imagenes.jpg') }}" alt="Sunset in the mountains"></a>
                @endif
                <div class="absolute left-0 top-0">
                    @if($s_propertie->status == 1)
                        <div class="text-xs font-semibold" style="margin-top: 5px; margin-left:5px; border-radius: 10px; width: 10px; height: 10px; background-color: #01842a; border-radius: 25px">
                            {{-- <img width="35px" src="{{ asset('img/on.png') }}" alt="ON"> --}}
                        </div>
                    @else
                        <div class="text-xs font-semibold" style="margin-top: 5px; margin-left:5px; border-radius: 10px; width: 10px; height: 10px; background-color: #b11213; border-radius: 25px">
                            {{-- <img width="35px" src="{{ asset('img/off.png') }}" alt="OFF"> --}}
                        </div>
                    @endif
                </div>

                <div class="px-2 py-2">
                    <div class="text-xs text-gray-500">{{$s_propertie->created_at->format('d-M-y')}}</div>
                    <div class="font-bold text-sm">{{ Str::limit($s_propertie->listing_title, 30, '...')}}</div>
                    <p class="text-gray-700 text-base">
                        @if(Str::contains($s_propertie->address, ',')){{ Str::limit($s_propertie->address, 30, '...')}} @else {{Str::limit($s_propertie->state . ', ' . $s_propertie->city . ', ' . $s_propertie->address, 30, '...') }} @endif
                    </p>
                    @if($propertie->cardinal_zone)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                            @if($propertie->cardinal_zone == 'norte')  bg-blue-100 text-blue-700
                            @elseif($propertie->cardinal_zone == 'sur') bg-yellow-100 text-yellow-700
                            @elseif($propertie->cardinal_zone == 'este') bg-green-100 text-green-700
                            @elseif($propertie->cardinal_zone == 'oeste') bg-purple-100 text-purple-700
                            @else bg-gray-100 text-gray-600 @endif">
                            @if($propertie->cardinal_zone == 'norte') ↑ Norte
                            @elseif($propertie->cardinal_zone == 'sur') ↓ Sur
                            @elseif($propertie->cardinal_zone == 'este') → Este
                            @elseif($propertie->cardinal_zone == 'oeste') ← Oeste
                            @else ⊙ Centro @endif
                        </span>
                    @endif
                    <p>@if(Auth::id()==123)<span style="font-size: 10px">{{$s_propertie->slug}}</span> <br>@endif</p>
                </div>
                <div class="grid grid-cols-2 px-2 py-2 w-full">
                    <div>
                        <span class="inline-block bg-gray-200 rounded-full px-2 text-sm font-semibold text-gray-700">{{ $s_propertie->listingtypestatus}}</span>
                        <p class="mx-2 text-red-600 font-extrabold text-xl">${{ number_format($s_propertie->property_price)}}</p class="mx-2 text-red-600 font-bold">
                    </div>
                    <div style="@if($s_propertie->listingtagstatus==2 && $s_propertie->listingtype != 26) margin-left: -20px @else margin-left: 0px @endif">
                        <div class="bottom-0 right-0 flex">
                            @if($s_propertie->available != null)
                            <div class="mr-1">
                                    @if($s_propertie->available == 2)
                                        <img title="NO DISPONIBLE" width="28px" src="{{asset('img/not-available.png')}}" alt="NOT AVAILABLE">
                                    @else
                                        <img title="DISPONIBLE" width="28px" src="{{asset('img/available.png')}}" alt="AVAILABLE">
                                    @endif
                                </div>
                            @endif
                            <div class="mr-1">
                                @if ($s_propertie->listing_type==2)
                                    <img width="28px" src="{{ asset('img/pagada.png') }}" alt="Pagada" title="PROPIEDAD PAGADA">
                                @elseif($s_propertie->listing_type==1)
                                    <img width="28px" src="{{ asset('img/free.png') }}" alt="Gratis" title="PROPIEDAD GRATIS">
                                @endif
                            </div>
                            <div class="mr-1">
                                @if ($s_propertie->listingtagstatus==2 && $s_propertie->listingtype != 26)
                                    <img width="28px" src="{{ asset('img/worker.png') }}" alt="Constructora" title="CONSTRUCTORA">
                                @endif
                            </div>
                            <div style="width:80px; height: auto; background-color: #017cd3; color: #ffffff; padding: 3px 10px 3px 10px; border-radius: 10px">
                                <p class="text-xs font-semibold">
                                    COD: {{ $s_propertie->product_code }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @if(Auth::user()->role == "user" || Auth::user()->role == "ASESOR")
                    </a>
                @endif


                @if(Auth::user()->email == "developer2@casacredito.com")
                    <div class="flex ml-3">
                        <p>h-{{$s_propertie->bedroom}}</p>
                        <p>b-{{$s_propertie->bathroom}}</p>
                        <p>g-{{$s_propertie->garage}}</p>
                    </div>
                @endif
                @if(Auth::user()->role == 'administrator' || $s_propertie->user_id == Auth::user()->id)
                    <div class="flex justify-center">
                        <a target="_blank" class="btn-edit ml-1 p-1 rounded" style="background-color: #c6f6d5" href="{{ route('home.tw.edit', $s_propertie) }}" style="text-decoration: none">
                            <p class="text-black text-sm" style="font-weight: 500">Editar propiedad</p>
                        </a>
                    </div>
                @endif
                <div class="flex float-right mr-2">
                    {{-- <button onclick="setLinkToShare('{{$propertie->slug}}')">
                        <img width="25px" src="{{asset('img/wpp_logo.png')}}" alt="">
                    </button> --}}
                    <div class="ml-2">
                        <input type="checkbox" value="{{$s_propertie->listing_title.'|'.$s_propertie->slug}}" name="propertiestoshare[]" class="checktoshare" onclick="share()">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- TERMINA DIV DE SIMILAR PROPERTIES --}}

    @elseif($view == 'list')

    <table class="w-full whitespace-nowrap bg-white">
        <thead>
            <tr class="sticky top-0">
                <th class="px-1 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    </th>
                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    </th>
                <th class="w-2 px-2 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    COD</th>
                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Status</th>
                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Precio</th>
                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Detalle</th>
                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Categoria</th>
                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    Tipo</th>
                <th class="px-2 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                    User</th>
            </tr>    
            {{-- <tr><th></th><th></th>
                <th class="px-2"> <input class="block w-24 py-2 border rounded-md pl-2" 
                    id="b_code" name="b_code" type="text" placeholder="Código..."></th>
                <td class="px-2">
                    <select class="block w-14 py-2 border rounded-md"
                     id="b_status" name="b_status"  class="w-20" style="color: gray">
                            <option value="" selected>Seleccione</option>
                            <option value="A">Activa</option>
                            <option value="D">Desactivada</option>
                    </select>
                </td>
                <th>
                    <select class="block w-14 py-2 border rounded-md"
                     id="b_price" name="b_price"  class="w-20" style="color: gray">
                            <option value="" selected>Seleccione</option>
                            <option value="ASC">Ascendente</option>
                            <option value="DESC">Descendente</option>
                    </select>
                </th>
                <th class="px-2" style="width: 40%"><input class="block w-full py-2 border rounded-md pl-2"
                    id="b_detalle" name="b_detalle" type="text" placeholder="Ingrese un sector..."></th>
                <td class="w-14">  
                    <select class="block w-full py-2 border rounded-md"
                    id="b_categoria" style="color: gray">
                            <option value="" selected>Seleccione</option>	
                            <option value="23">Casas </option>
                            <option value="24">Departamentos </option>
                            <option value="25">Casas Comerciales</option>
                            <option value="26">Terrenos</option>
                            <option value="29">Quintas</option>
                            <option value="30">Haciendas</option>
                            <option value="32">Locales Comerciales</option>
                            <option value="35">Oficinas</option>
                            <option value="36">Suites</option>
                    </select>
                </td>
                <td class="w-40 px-2">  
                    <select class="block w-full py-2 border rounded-md"
                    id="b_tipo" style="max-width:200px; color: gray">
                            <option value="" selected>Seleccione</option>
                            <option value="en-venta">Venta</option>
                            <option value="alquilar">Alquiler</option>
                    </select>
            </td>
            <td></td>
            </tr>   --}}
        </thead>
    <tbody>
        @foreach ($properties as $pro)    
    
            @php 
                $firstImg = array_filter(explode("|", $pro->images)) ;
                $dirImg = $firstImg[0]??'';
    
                $categoria='';
                    if($pro->listingtype==23) $categoria='Casas';
                    if($pro->listingtype==24) $categoria='Departamentos';
                    if($pro->listingtype==25) $categoria='Casas Comerciales';
                    if($pro->listingtype==26) $categoria='Terrenos';
                    if($pro->listingtype==29) $categoria='Quintas';
                    if($pro->listingtype==30) $categoria='Haciendas';
                    if($pro->listingtype==32) $categoria='Locales Comerciales';
                    if($pro->listingtype==35) $categoria='Oficinas';
                    if($pro->listingtype==36) $categoria='Suites';
    
            @endphp
        <tr class="hover:bg-gray-100 border-0 border-b border-gay-200 text-xs">
            <td style="width: 1px;background-color: @if($pro->status==1) royalblue @else red @endif"></td>            
            <td class="w-16"> <img class="w-16" src="https://casacredito.com/uploads/listing/300/{{$dirImg}}" alt=""> </td>
            <td class="text-center"><span class="font-semibold">{{$pro->product_code}}</span><br><span style="color:darkgray;font-size:11px">{{$pro->created_at->format('dMy')}}</span></td>            
            <td class="text-center w-12">
                @if ($pro->status==1) <span class="font-semibold text-blue-800">Activa</span>
                @else <span class="font-semibold text-gray-800">Desactivada</span>         @endif
            </td>
            <td class="px-2 w-12 text-center">{{number_format($pro->property_price)}} <br><span style="font-size: 12px;color:darkgray">{{number_format($pro->property_price_min)}}</span> </td>
            <td class="p-2"> 
                @if(Auth::id()==123)<span style="font-size: 10px">{{$pro->slug}}</span> <br>@endif
                <a href="{{route('admin.listings.edit',$pro->id)}}" target="_blank" class="font-semibold text-blue-800">{{$pro->listing_title}}</a><br> @if(Str::contains($pro->address, ',')) {{$pro->address}} @else {{$pro->state}}, {{$pro->city}}, {{$pro->address}} @endif</td>
            <td class="font-semibold w-14 flex justify-center pt-4">{{$categoria}}</td>
            <td class="w-40 text-center">{{$pro->listingtypestatus}}</td>
            <td>{{$pro->user->name??''}}</td>
            </tr>
            @endforeach
            <tr><td colspan="6">
            <input type="hidden" id="pagActual" value="{{$pagActual}}">
            <input type="hidden" id="firstItem" value="{{$firstItem}}">
            <input type="hidden" id="totalProperties2" value="{{$totalProperties}}">
            </td></tr>
    
        </tbody>
    </table>
    @endif
    @else
    <div class="flex justify-center p-5 font-semibold">
        <p>
            No hemos encontrado propiedades
        </p>
    </div>
    @endif
    {{-- <div id="default" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 bg-red-500">
                    <h3 class="text-xl font-semibold text-white dark:text-white">
                        Se requiere contactar al cliente
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="authentication-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4" action="{{ route('admin.set.contact.date') }}" method="POST">
                        @csrf
                        <div>
                            <p><span class="font-semibold">Propiedad:</span> <span id="span-id-modal-property"></span></p>
                            <p><span class="font-semibold">Cliente:</span> <span id="span-name-modal-property"></span></p>
                            <p><span class="font-semibold">Número de teléfono:</span> <span id="span-phone-modal-property"></span></p>
                            <p><span class="font-semibold">Titulo:</span> <span id="span-title-modal-property"></span></p>
                        </div>
                        <div>
                            <div>
                                <input type="hidden" name="product_id" id="product_code_modal_contact">
                                <label for="comment">Comentario</label> <br>
                                <input type="text" name="comment" class="bg-gray-200 px-2 py-2 w-full border-none mt-2 mb-2" required>
                            </div>
                            <div>
                                <label for="date">Fecha de contacto</label>
                                <input type="date" name="date" class="bg-gray-200 px-2 py-2 w-full border-none mt-2" required>
                            </div>
                            <div class="mt-5 text-center">
                                <button class="bg-green-500 text-white px-5 py-2 rounded">GUARDAR</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>  --}}
</div>


    
@push('scripts')
<script src="{{ asset('js/flowbite.min.js') }}" defer></script>
<script type="text/javascript">

window.addEventListener('cardinal-filter-changed', function(event) {
    @this.call('applyCardinalFilters', event.detail);
});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('totalProperties').innerText="{{$properties->total()}}";
    document.getElementById('unoalcien').innerText="{{$pagActual}}";
    
    Livewire.hook('element.updated', (el,comp) => {
        document.getElementById('totalProperties').innerText= @this.totalProperties;
        document.getElementById('unoalcien').innerText = @this.pagActual;
    });

    const pagActual = @this.pagActual;

});

const saveContactDay = (id) => {

    let idContactDay = document.getElementById('product_code_modal_contact');
    let commentContactDay = document.getElementById('commentContactDay'+id);
    let dateContactDay = document.getElementById('dateContactDay'+id);

    @this.set('idContactDay', idContactDay.value);
    @this.set('commentContactDay', commentContactDay.value);
    @this.set('dateContactDay', dateContactDay.value);
}

const setIdModalContactar = (id, propertie_code, owner_name, owner_phone, listing_title) => {
    
    let popup = document.getElementById("popup"+id);

    popup.classList.contains('hidden') ? popup.classList.remove('hidden') : popup.classList.add('hidden');


    let product_code_modal_contact = document.getElementById('product_code_modal_contact');
    product_code_modal_contact.value = propertie_code;
    
}

const closeModalContact = (id) => {

    let popup = document.getElementById('popup'+id);

    popup.classList.contains('hidden') ? popup.classList.remove('hidden') : popup.classList.add('hidden');

}

function clear_filters(){
    let b_code      = document.getElementById('b_code').value = "";
    let b_status    = document.getElementById('b_status').value = "";
    let b_detalle   = document.getElementById('b_detalle').value = "";
    let b_categoria = document.getElementById('b_categoria').value = "";
    let b_tipo      = document.getElementById('b_tipo').value = "";
    // let b_view      = document.getElementById('view').value = "";
    let b_current_url = document.getElementById('b_current_url').value = "";

    let b_state     = document.getElementById('b_state').value = "";
    let b_city      = document.getElementById('b_city').value = "";

    let b_maxprice = document.getElementById('maxprice').value = "";
    let b_minprice = document.getElementById('minprice').value = "";

    let b_order_asc = document.getElementById('asc');
    let b_order_desc = document.getElementById('desc');

    let b_asesor = document.getElementById('b_asesor').value = "";

    document.getElementById('b_owner').value = "";

    let b_fromdate = document.getElementById('fromdate').value = "";
    let b_untildate = document.getElementById('untildate').value = "";
    filter_properties(); toggleModal();
}

function filter_properties(){
    let b_code      = document.getElementById('b_code').value;
    let b_status    = document.getElementById('b_status').value;
    //let b_detalle   = document.getElementById('b_detalle').value;
    let b_categoria = document.getElementById('b_categoria').value;
    let b_tipo      = document.getElementById('b_tipo').value;
    //let b_price     = document.getElementById('b_price').value;
    let b_view      = document.getElementById('view').value;
    //let b_available = document.getElementById('b_available').value; //variable para buscar por disponibilidad
    let b_current_url = document.getElementById('b_current_url').value; //saber la ruta actual

    let b_owner = document.getElementById('b_owner') ? document.getElementById('b_owner').value : '';


    //variables nuevas para buscar por pais, provincia y ciudad
    //let b_country   = document.getElementById('b_country').value;
    let b_state     = document.getElementById('b_state').value;
    let b_city      = document.getElementById('b_city').value;
    let b_sector    = document.getElementById('b_sector').value;
    let b_zona      = document.getElementById('b_zona').value;

    //variables nuevas para buscar por precio y ordenar asc o desc
    let b_maxprice = document.getElementById('maxprice').value;
    let b_minprice = document.getElementById('minprice').value;

    let b_order_asc = document.getElementById('asc');
    let b_order_desc = document.getElementById('desc');

    //filtro por venta o renta
    let b_transaccion = document.getElementById('b_transaccion').value;

    let b_tagstatus = document.getElementById('b_tagstatus').value;

    let b_cardinal_zone = document.getElementById('b_cardinal_zone');
    if (b_cardinal_zone) b_cardinal_zone = b_cardinal_zone.value;

    //filtrar por asesor
    // let b_asesor = document.getElementById('b_asesor').value;

    //filter date
    // let b_fromdate = document.getElementById('fromdate').value;
    // let b_untildate = document.getElementById('untildate').value;

    let b_credit_vip = document.getElementById('b_credit_vip');
    if(b_credit_vip) b_credit_vip = b_credit_vip.value;

    //if(b_untildate < b_fromdate) alert('Formato no valido');

    let order_aux;

    // let b_plusvalia = document.getElementById('b_plusvalia');

    // if(b_plusvalia.checked) @this.set('plusvalia', 1);
    // else @this.set('plusvalia', '');

    let b_bedrooms = "";

    let ele = document.getElementsByName('bedrooms');
    for (let i = 0; i < ele.length; i++) {
        if(ele[i].checked) b_bedrooms = ele[i].value;
    }

    let b_bathrooms = "";
    let elementsBath = document.getElementsByName('bathrooms');
    for (let i = 0; i < elementsBath.length; i++) {
        if(elementsBath[i].checked) b_bathrooms = elementsBath[i].value;
    }

    if(b_order_asc.checked){
        @this.set('price', b_order_asc.value);
    } else if(b_order_desc.checked){
        @this.set('price', b_order_desc.value);
    }

    @this.set('code', b_code);  
    @this.set('status', b_status);  
    //@this.set('detalle', b_detalle);  
    @this.set('categoria', b_categoria);  
    @this.set('tipo', b_tipo);
    //@this.set('price', b_price);
    @this.set('pressButtom', 1);

    @this.set('view', b_view); //para renderizar de nuevo y cambie de vista
    //@this.set('available', b_available); //buscar por disponibilidad
    @this.set('current_url', b_current_url); //mandar la actual url -> si es myproperties

    @this.set('state', b_state);
    @this.set('city', b_city);
    @this.set('sector', b_sector);
    @this.set('zona', b_zona);

    @this.set('owner_search', b_owner);

    @this.set('fromprice', b_minprice);
    @this.set('uptoprice', b_maxprice);

    @this.set('transaccion', b_transaccion);
    @this.set('tagstatus', b_tagstatus);

    @this.set('credit_vip', b_credit_vip);

    @this.set('bedrooms', b_bedrooms);
    @this.set('bathrooms', b_bathrooms);
    @this.set('cardinal_zone', b_cardinal_zone);

}

    
const nextpage = () => {
    let pagActual    = document.getElementById('pagActual').value;
    let firstItem    = document.getElementById('firstItem').value;
    let totalProperties    = document.getElementById('totalProperties2').value;
    console.log('Actual: '+pagActual);        console.log('Total de Paginas: '+ (Math.ceil(totalProperties/50)) );
    if( Math.ceil(totalProperties/50)>pagActual ){
        @this.nextPage()
    }
}
const prevpage = () => {
    let pagActual    = document.getElementById('pagActual').value;
    if(pagActual>1){
        @this.previousPage()
    }else{        
        console.log(pagActual);
    }
}

    function setLinkToShare(slug){
        let link = "https://api.whatsapp.com/send?text=";
        let message = "https://casacredito.com/propiedad/"+slug;
        message += "%0AReciba un cordial saludo de Casa Crédito 👋🏻🏠 Le hacemos llegar la propiedad en la que se encuentra interesado.%0A"
        message += "%0A_*Casa Crédito, Haciendo sus sueños realidad*_%0A"
        window.open(link+message, '_blank');
    }

    let values = [];

    if(values.length>0) document.getElementById('btnCompartir').style.display = "block";
    else if(values.length == 0) document.getElementById('btnCompartir').style.display = "none";
    
    function share(){
        let checkboxes = document.querySelectorAll(".checktoshare");
        let valuesaux = [];
        for (let i = 0; i < checkboxes.length; i++) {
            if(checkboxes[i].checked){
                valuesaux.push(checkboxes[i].value);
            }
        }
        values = valuesaux;
        console.log(values);
        if(values.length>0) document.getElementById('btnCompartir').style.display = "block";
        else if(values.length == 0) document.getElementById('btnCompartir').style.display = "none";
    }

    function sharetowpp(){
        let link = "https://api.whatsapp.com/send?text=";
        let message = "Reciba un cordial saludo de Grupo Housing 👋🏻🏠 Le hacemos llegar la propiedad en la que se encuentra interesado.%0A"
        values.forEach(listing => {
            let index = listing.indexOf("|");
            let linklisting = listing.substring(0, index);
            let title = listing.substring(index+1);
            message += "%0A"+linklisting+"✅";
            message += "%0Ahttps://grupohousing.com/propiedad/"+title+"%0A";
        });

        message += "%0A_*Grupo Housing, Haciendo sus sueños realidad*_"
        window.open(link+message, '_blank');
    }
</script>

@endpush
