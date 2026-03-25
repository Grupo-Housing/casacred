<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Listing;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Proplisttw extends Component
{
    use WithPagination;

    public $code, $status, $detalle, $categoria, $tipo, $price,
        $pressButtom, $totalProperties = 0, $pagActual, $firstItem,
        $view = 'grid', $available,
        $current_url,
        //$country, 
        $cardinal_zone,
        $state, $city, $sector, $zona,
        $fromprice, $uptoprice,
        $asesor,
        $fromdate, $untildate,
        $credit_vip,
        $bedrooms,
        $bathrooms,
        $transaccion,
        $tagstatus,
        $owner_search;

    //variables para guardar el dia que se contactan
    public $idContactDay, $commentContactDay, $dateContactDay;

    public $show;

    protected $listeners = [
        'applyCardinalFilters' => 'applyCardinalFilters',
    ];

    public function applyCardinalFilters($filters)
    {
        $this->cardinal_zone = $filters['cardinal_zone'] ?? '';
        $this->categoria     = $filters['categoria']     ?? $this->categoria;
        $this->transaccion   = $filters['transaccion']   ?? $this->transaccion;
        $this->resetPage();
    }

    public function storeContactDay()
    {

        $now = Carbon::now();
        $listing = Listing::where('product_code', 'LIKE',  '%' . $this->idContactDay . '%')->first();
        $listing->contact_at = $now;
        $listing->save();

        Comment::create([
            'listing_id' => $listing->id,
            'user_id' => Auth::user()->id,
            'property_code' => $listing->product_code,
            'type' => 'Contact',
            'comment' => $this->commentContactDay
        ]);
    }

    public function render()
    {

        if ($this->idContactDay && $this->commentContactDay && $this->dateContactDay) $this->storeContactDay();

        if ($this->view == 'grid') {
            $viewaux = $this->view;
        } elseif ($this->view == 'list') {
            $viewaux = $this->view;
        }

        if ($this->pressButtom > 0) {
            $this->pressButtom = 0;
            $this->resetPage();
        }

        if ($this->price) {
            if ($this->price == 'ASC') $properties_filter = Listing::orderBy('property_price', 'ASC');
            else $properties_filter = Listing::orderBy('property_price', 'DESC');
        } else {
            $properties_filter = Listing::orderBy('id', 'DESC');
        }

        //mostrar las propiedades de un asesor
        if ($this->current_url == "admin.myproperties" || Route::current()->getName() == "admin.myproperties") {
            //if(Auth::user()->role != 'administrator') $properties_filter->where('user_id', Auth::id());
            $properties_filter->where('user_id', Auth::id());
        }
        if (Str::contains(URL::previous(), 'admin/my-properties') && $this->pagActual > 0) $properties_filter->where('user_id', Auth::id());

        //mostrar las propiedades vendidas
        if (Route::current()->getName() == "admin.soldout") $properties_filter->where('available', 2);
        if (Str::contains(URL::previous(), 'admin/sold-out') && $this->pagActual > 0) $properties_filter->where('available', 2);
        //if($this->pagActual > 0) dd($this->current_url);//$properties_filter->where('available', 2);

        // else $properties_filter->where('available', 1);
        if (Route::current()->getName() == "admin.properties") $properties_filter->where('available', 1);
        if (Str::contains(URL::previous(), 'admin/properties') && $this->pagActual > 0) $properties_filter->where('available', 1);

        $url_current = $this->current_url;

        if (strlen($this->detalle) > 2) {
            //$properties_filter->where('address','LIKE',"%$this->detalle%")->orWhere('listing_title', 'LIKE', "%$this->detalle%");
            $properties_filter->where('address', 'LIKE', "%$this->detalle%")->orWhere('sector', 'LIKE', "%$this->detalle%");
            if ($properties_filter->count() < 1) {
                $properties_filter->where('listing_title', 'LIKE', "%$this->detalle%");
            }
        }

        if ($this->code) {
            $properties_filter->where('product_code', 'LIKE', "%$this->code%");
        }

        if ($this->cardinal_zone) {
            $properties_filter->where('cardinal_zone', $this->cardinal_zone);
        }

        if ($this->owner_search) {
            $search = $this->owner_search;
            $properties_filter->where(function ($query) use ($search) {
                $query->where('owner_name',    'LIKE', '%' . $search . '%')
                    ->orWhere('owner_email', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone_number', 'LIKE', '%' . $search . '%');
            });
        }

        if ($this->status == 'A')                                  $properties_filter->where('status', 1); //agregarle || $this->variable == null para muestre por defecto las activas y disponibles
        if ($this->status == 'D')                                  $properties_filter->where('status', 0);
        if ($this->categoria)                                    $properties_filter->where('listingtype', $this->categoria);
        if ($this->tipo) {
            if ($this->tipo == "Housing") {
                $properties_filter->where('listingtypestatus', 'alquilar');
            } else {
                $properties_filter->where('property_by', $this->tipo);
            }
        }

        if ($this->state) {
            $properties_filter->where('state', $this->state);
        }

        if ($this->city) {
            $properties_filter->where('city', $this->city);
        }

        if ($this->sector) {
            $sector = $this->sector;

            $properties_filter->where(function ($query) use ($sector) {
                $query->where('sector', 'LIKE', '%' . $sector . '%')
                    ->orWhere('address', 'LIKE', '%' . $sector . '%');
            });
        }

        if ($this->zona) {
            $zona = $this->zona;

            $properties_filter->where(function ($query) use ($zona) {
                $query->where('state', 'LIKE', '%' . $zona . '%')
                    ->orWhere('city', 'LIKE', '%' . $zona . '%')
                    ->orWhere('sector', 'LIKE', '%' . $zona . '%')
                    ->orWhere('address', 'LIKE', '%' . $zona . '%');
            });
        }

        //buscando por asesor
        if ($this->asesor)               $properties_filter->where('user_id', $this->asesor);

        //buscando por fecha
        if ($this->fromdate || $this->untildate)         $properties_filter->whereBetween('created_at', [$this->fromdate, $this->untildate]);

        if ($this->credit_vip)           $properties_filter->where('vip', $this->credit_vip);

        if ($this->bedrooms)     $properties_filter->where('bedroom', $this->bedrooms);

        if ($this->bathrooms)    $properties_filter->where('bathroom', $this->bathrooms);

        if ($this->transaccion)          $properties_filter->where('listingtypestatus', 'LIKE', "%" . $this->transaccion . "%");

        if ($this->tagstatus)            $properties_filter->where('listingtagstatus', 'LIKE', "%" . $this->tagstatus . "%");

        //buscando por precio strlen($this->fromprice)>1   strlen($this->uptoprice)>1 
        if ($this->fromprice && filter_var($this->fromprice, FILTER_SANITIZE_NUMBER_INT) > 1) {
            $fromprice_ = filter_var($this->fromprice, FILTER_SANITIZE_NUMBER_INT);
            $properties_filter->where('property_price', '>', $fromprice_);
        }

        if ($this->uptoprice && filter_var($this->uptoprice, FILTER_SANITIZE_NUMBER_INT) > 1) {
            $uptoprice_ = filter_var($this->uptoprice, FILTER_SANITIZE_NUMBER_INT);
            $properties_filter->where('property_price', '<', $uptoprice_);
        }


        $properties = $properties_filter->paginate(50);
        $this->pagActual = $properties->currentPage();
        $this->firstItem = $properties->firstItem();
        $this->totalProperties = $properties->total();

        $similarProperties = Listing::where('available', 1);

        $similar_properties = [];
        if ($this->code) {
            $propertie_to_similar = Listing::where('product_code', 'LIKE', "%$this->code%")->first();
            if ($propertie_to_similar) {

                $radius = 2000;

                $priceDifference = ($propertie_to_similar->listingtypestatus === 'en-venta') ? 20000 : 100;

                $minPrice = $propertie_to_similar->property_price - $priceDifference;
                $maxPrice = $propertie_to_similar->property_price + $priceDifference;

                $similarProperties->selectRaw("*, (6371000 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance", [$propertie_to_similar->lat, $propertie_to_similar->lng, $propertie_to_similar->lat])
                    ->where('listingtype', 'LIKE', "%$propertie_to_similar->listingtype%")
                    ->where('listingtypestatus', 'LIKE', "%$propertie_to_similar->listingtypestatus%")
                    ->where('available', 1)
                    //->where('status', 1)
                    ->where("product_code", "!=", $propertie_to_similar->product_code)
                    ->whereBetween('property_price', [$minPrice, $maxPrice])
                    ->having("distance", "<=", $radius);
                //->take(10)

                // $address = "";
                // if($propertie_to_similar->address != null) $address = $propertie_to_similar->address;
                // if($propertie_to_similar->sector != null) $address = $propertie_to_similar->sector;
                // if(str_contains($address, ",")){
                //     $separate_address = explode(",", $address);
                //     $address = end($separate_address);
                //     $address = str_replace(" ", "", $address);
                // }
                // $similarProperties->where('address', 'LIKE', "%$address%");
                // $similarProperties->where('state', $propertie_to_similar->state);
                // $similarProperties->where('city', $propertie_to_similar->city);
                // $similarProperties->where('listingtype', $propertie_to_similar->listingtype);
                $similar_properties = $similarProperties->get();
            }
        }

        $types = DB::table('listing_types')->get();
        $categories = DB::table('listing_status')->get();

        return view('livewire.proplisttw', compact('properties', 'types', 'categories', 'viewaux', 'url_current', 'similar_properties'));
    }
}
