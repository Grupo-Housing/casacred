<?php

namespace App\Http\Controllers;

use App\Enums\NotificationType;
use App\Models\Comment;
use App\Models\Sector;
use App\Models\Listing;
use App\Models\Notification;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Services\CardinalZoneService;

class ListingController extends Controller
{

    public function index()
    {
        // $ismobile = $this->isMobile();
        return view('admin.listing.index');
    }

    public function create()
    {

        $currentRouteName = Route::currentRouteName();

        $lastcode = Listing::where('product_code', '!=', '')->orderBy('product_code', 'desc')->first();
        //$lastcode = Listing::latest()->first();

        strlen($lastcode->product_code) > 4 ? $lastcode->product_code = substr($lastcode->product_code, 2) : null;

        //dd($lastcode->product_code);

        $benefits = DB::table('listing_benefits')->get();
        $services = DB::table('listing_services')->get();

        //new data to db
        $environments = DB::table('listing_environments')->get();
        $general_characteristics = DB::table('listing_general_characteristics')->get();

        $types = DB::table('listing_types')->get();
        $details = DB::table('listing_characteristics')->orderBy('charac_titile', 'ASC')->get();
        $categories = DB::table('listing_status')->get();
        $tags = DB::table('listing_tags')->get();

        $states = DB::table('info_states')->where('country_id', 63)->orderBy('name')->get();
        $optAttrib = [];
        foreach ($states as $state) {
            $optAttrib[$state->name] = ['data-id' => $state->id];
        }

        $optAttribSector = [];

        return view('admin.listing.add-tw', compact('benefits', 'services', 'types', 'categories', 'tags', 'details', 'states', 'optAttrib', 'lastcode', 'environments', 'general_characteristics', 'optAttribSector', 'currentRouteName'));
    }

    public function storing_property(Request $request)
    {

        $propertie = null;

        if (!empty($request->dbID)) {
            // Actualización
            $propertie = Listing::find($request->dbID);

            if (!$propertie) {
                return response()->json(['error' => 'Registro no encontrado'], 404);
            }

            $propertie->fill($request->all());
        } else {
            // Creación
            $propertie = new Listing();
            $propertie->fill($request->all());
        }

        $propertie->save();

        return response()->json([
            'databaseID' => $propertie->id,
            'message' => 'Registro guardado exitosamente'
        ], 200);
    }

    public function store(Request $request)
    {

        $listing = Listing::where('product_code', $request->product_code)->first();

        if (isset($request->listing_title)) {
            $slug = Str::of($request->listing_title)->trim()->slug()->limit(70, '') . '-' . rand(10000, 99999);
            $request->merge(['slug' => $slug]);
        }
        $request->merge(['user_id' => Auth::id()]);

        if (is_array($request->checkBene)) $request->merge(['listingcharacteristic' => implode(",", $request->checkBene)]);
        else $request->merge(['listingcharacteristic' => '']);

        if (is_array($request->checkgc)) $request->merge(['listinggeneralcharacteristics' => implode(",", $request->checkgc)]);
        else $request->merge(['listinggeneralcharacteristics' => '']);

        if (is_array($request->checkEnvir)) $request->merge(['listingenvironments' => implode(",", $request->checkEnvir)]);
        else $request->merge(['listingenvironments' => '']);

        if (is_array($request->checkServ)) $request->merge(['listinglistservices' => implode(",", $request->checkServ)]);
        else $request->merge(['listinglistservices' => '']);

        if (is_array($request->updatedImages)) $request->merge(['images' => implode("|", $request->updatedImages)]);
        else $request->merge(['images' => '']);

        $result = [];
        $ii = 0;
        foreach ($request->all() as $key => $value) {
            if ("detail" == substr($key, 0, 6)) $result[] = $value;
        }
        $bathrooms = 0;
        $bedrooms = 0;
        $garage = 0;

        //codigo antiguo
        // if (count($result) > 0) {
        //     $request->merge(['heading_details' => json_encode($result)]);
        //     //return count($result);
        //     foreach ($result as $r) {
        //         for ($i = 0; $i < count($r); $i++) {
        //             if ($r[$i] == 109) $bedrooms = $bedrooms + $r[$i + 1];
        //             if ($r[$i] == 110) $bedrooms = $bedrooms + $r[$i + 2];
        //             if ($r[$i] == 111) $bedrooms = $bedrooms + $r[$i + 3];
        //             if ($r[$i] == 49 || $r[$i] == 86 || $r[$i] == 41 || $r[$i] == 115 || $r[$i] == 62 || $r[$i] == 113) $bedrooms = $bedrooms + $r[$i + 1];
        //             if ($r[$i] == 48 || $r[$i] == 76 || $r[$i] == 81 || $r[$i] == 49 || $r = [$i] == 113) $bathrooms = $bathrooms + $r[$i + 1];
        //             if ($r[$i] == 43) $garage = $garage + $r[$i + 1];
        //         }
        //     }
        // } else {
        //     $request->merge(['heading_details' => '']);
        // }


        // Codigo nuevo con validacion
        if (count($result) > 0) {
            $request->merge(['heading_details' => json_encode($result)]);

            foreach ($result as $r) {

                if (!is_array($r)) {
                    continue;
                }

                for ($i = 0; $i < count($r); $i++) {
                    if ($r[$i] == 109) $bedrooms = $bedrooms + $r[$i + 1];
                    if ($r[$i] == 110) $bedrooms = $bedrooms + $r[$i + 2];
                    if ($r[$i] == 111) $bedrooms = $bedrooms + $r[$i + 3];
                    if ($r[$i] == 49 || $r[$i] == 86 || $r[$i] == 41 || $r[$i] == 115 || $r[$i] == 62 || $r[$i] == 113) $bedrooms = $bedrooms + $r[$i + 1];

                    if ($r[$i] == 48 || $r[$i] == 76 || $r[$i] == 81 || $r[$i] == 49 || $r[$i] == 113) $bathrooms = $bathrooms + $r[$i + 1];

                    if ($r[$i] == 43) $garage = $garage + $r[$i + 1];
                }
            }
        } else {
            $request->merge(['heading_details' => '']);
        }

        $uploads = [];

        if ($request->hasFile('galleryImages')) {
            foreach ($request->file('galleryImages') as $image) {
                if ($image->isValid()) {
                    $validate = $image->getClientOriginalExtension();
                    if (in_array($validate, ['jpeg', 'jpg', 'png', 'JPG', 'PNG'])) {
                        $img = Image::make($image);
                        $img2 = Image::make($image);
                        $img3 = Image::make($image);
                        $mime = $img->mime();
                        if ($mime == 'image/jpeg') $ext = '.jpg';
                        elseif ($mime == 'image/png') $ext = '.png';
                        else $ext = '';
                        if (strlen($ext) > 0) {
                            $folder = 'uploads/listing/';
                            $nameFile = "IMG_$listing->id-" . uniqid() . $ext;
                            $img->save($folder . $nameFile);
                            $uploads[] = $nameFile;

                            $folder2 = 'uploads/listing/600/';
                            $img2->fit(600, 400, function ($constraint) {
                                $constraint->upsize();
                                $constraint->aspectRatio();
                            });
                            $img2->save($folder2 . $nameFile, 40);

                            $folder3 = 'uploads/listing/300/';
                            $img3->fit(300, 200, function ($constraint) {
                                $constraint->upsize();
                                $constraint->aspectRatio();
                            });
                            $img3->save($folder3 . $nameFile, 40);

                            //agregando marca de agua
                            $folder_1 = 'uploads/listing/thumb/';
                            $watermark = Image::make(public_path('img/MARCADEAGUA.png'));
                            $imageWidth = $img->width();

                            $watermarkSize = round(10 * $imageWidth / 40);
                            $watermark->resize($watermarkSize, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });

                            $watermark->opacity(15);

                            $img->insert($watermark, 'center', 0, 0);
                            $img->save($folder_1 . $nameFile);

                            $folder_2 = 'uploads/listing/thumb/600/';
                            $img2->fit(600, 400, function ($constraint) {
                                $constraint->upsize();
                                $constraint->aspectRatio();
                            });
                            $imageWidth2 = $img2->width();
                            $watermarkSize = round(10 * $imageWidth2 / 40);
                            $watermark->resize($watermarkSize, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });

                            $img2->insert($watermark, 'center', 0, 0);
                            $img2->save($folder_2 . $nameFile, 40);

                            $folder_3 = 'uploads/listing/thumb/300/';
                            $img3->fit(300, 200, function ($constraint) {
                                $constraint->upsize();
                                $constraint->aspectRatio();
                            });
                            $imageWidth3 = $img3->width();
                            $watermarkSize = round(10 * $imageWidth3 / 40);
                            $watermark->resize($watermarkSize, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });

                            $img3->insert($watermark, 'center', 0, 0);
                            $img3->save($folder_3 . $nameFile, 40);
                        }
                    } elseif ($validate == "mp4") {
                        $namefile = $listing->slug . "." . $image->getClientOriginalExtension();
                        $filepath = public_path() . '/uploads/video/';
                        $image->move($filepath, $namefile);
                        $listing->video = $namefile;
                    }
                }
            }
        }

        if (count($uploads) > 0) {
            $save_uploads = implode("|", $uploads);
            if (strlen($request->images) > 2) {
                $listing->update(['images'  =>  $request->images . '|' . $save_uploads]);
            } else {
                $listing->update(['images'  => $save_uploads]);
            }
        }

        if (!$listing) {
            $listing = Listing::create($request->all());
        } else {
            //set numero de habitaciones, baños y garage para los filtros
            $listing->listing_title = $request->listing_title;
            if (!isset($listing->slug)) $listing->slug = $request->slug;

            $listing->bedroom = $bedrooms;
            $listing->bathroom = $bathrooms;
            $listing->garage = $garage;

            $listing->num_factura = $request->num_factura;

            $listing->meta_description = $request->meta_description;
            $listing->keywords = $request->keywords;
            $listing->Front = $request->Front;
            $listing->Fund = $request->Fund;
            $listing->land_area = $request->land_area;
            $listing->construction_area = $request->construction_area;
            $listing->property_price = $request->property_price;
            $listing->property_price_min = $request->property_price_min;

            $listing->aval = $request->aval;

            //new values
            $listing->owner_address = $request->owner_address;
            $listing->cadastral_key = $request->cadastral_key;
            $listing->aliquot = $request->aliquot;
            $listing->observations_type_property = $request->observations_type_property;
            $listing->listinggeneralcharacteristics = $request->listinggeneralcharacteristics;
            $listing->listingenvironments = $request->listingenvironments;
            if ($request->cavity_error == "on") $listing->cavity_error = 1;
            else $listing->cavity_error = 0;
            if ($request->vip == "on") $listing->vip = 1;
            else $listing->vip = 0;
            if ($request->mortgaged == "on") $listing->mortgaged = 1;
            else $listing->mortgaged = 0;

            $listing->listing_description = $request->listing_description;
            $listing->listing_type = $request->listing_type;
            $listing->address = $request->address;

            //save sector
            $listing->sector = $request->sector;

            $listing->state = $request->state;
            $listing->city = $request->city;
            $listing->listingtype = $request->listingtype;

            if ($request->listingtype == 26) $listing->planing_license = $request->planing_license;

            if ($request->mortgaged == "on") {
                $listing->entity_mortgaged = $request->entity_mortgaged;
                $listing->mount_mortgaged = $request->mount_mortgaged;
                $listing->warranty = $request->warranty;
            } else {
                $listing->entity_mortgaged = null;
                $listing->mount_mortgaged = null;
                $listing->warranty = null;
            }

            $listing->listingcharacteristic = $request->listingcharacteristic;
            $listing->listinglistservices = $request->listinglistservices;
            $listing->listingtypestatus = $request->listingtypestatus;
            $listing->listingtagstatus = $request->listingtagstatus;

            $listing->listyears = $request->listyears;

            $listing->lat = $request->lat;
            $listing->lng = $request->lng;
            $listing->cardinal_zone = (new CardinalZoneService())->calculate(
                (float) $request->lat,
                (float) $request->lng,
                $request->city
            );

            $listing->available = $request->available;

            $listing->user_id = $request->user_id;

            $listing->heading_details = $request->heading_details;
            $listing->owner_name = $request->owner_name;
            $listing->owner_email = $request->owner_email;

            $listing->identification = $request->identification;
            $listing->phone_number = $request->phone_number;

            $listing->status = $request->status;

            if ($request->niv_constr <= 0) $listing->niv_constr = null;
            else $listing->niv_constr = $request->niv_constr;
            if ($request->num_pisos <= 0) $listing->num_pisos = null;
            else $listing->num_pisos = $request->num_pisos;
            if ($request->pisos_constr <= 0) $listing->pisos_constr = null;
            else $listing->pisos_constr = $request->pisos_constr;

            $listing->land_appraisal = $request->land_appraisal;
            $listing->construction_appraisal = $request->construction_appraisal;

            $listing->customized_price = $request->customized_price;

            if (!$listing->locked && ($listing->owner_name != null || $request->owner_name != null) && ($listing->identification != null || $request->identification != null) && ($listing->phone_number != null || $request->phone_number != null) && ($listing->owner_email != null || $request->owner_email != null) && ($listing->owner_address != null || $request->owner_address != null) && ($listing->property_price != null || $request->property_price != null) && ($listing->property_price_min != null || $request->property_price_min)) $listing->locked = true;

            if ($request->currentUrl == "admin.housing.property.create") $listing->property_by = 'Housing';
            if ($request->currentUrl == "admin.promotora.property.create") $listing->property_by = 'Promotora';
            else $listing->property_by = 'Casa Credito';

            $listing->contact_at = $listing->created_at;

            $listing->is_dual_operation = $request->is_dual_operation;

            $manualZone = $request->input('cardinal_zone');

            if (!empty($manualZone)) {
                $listing->cardinal_zone = $manualZone;
            } else {
                $computed = (new CardinalZoneService())->calculate(
                    (float) $request->lat,
                    (float) $request->lng,
                    $request->city ?? $listing->city
                );
                if ($computed !== null) {
                    $listing->cardinal_zone = $computed;
                }
            }

            $listing->save();

            if ($this->iscomplete($listing)) {
                $listing->isvalid = 1;
                $listing->save();
            }
        }

        return response()->json([
            'success' => true,
            'fragment' => $request->fragment,
            'productcode' => $listing->product_code
        ]);
    }

    public function edit(Listing $listing)
    {

        $currentRouteName = Route::currentRouteName();

        $fields_to_edit = $this->getMissingFields($listing);

        $isvalid = $this->iscomplete($listing);

        $benefits = DB::table('listing_benefits')->get();
        $services = DB::table('listing_services')->get();

        //new data to db
        $environments = DB::table('listing_environments')->get();
        $general_characteristics = DB::table('listing_general_characteristics')->get();

        $types = DB::table('listing_types')->get();
        $details = DB::table('listing_characteristics')->orderBy('charac_titile', 'ASC')->get();
        $categories = DB::table('listing_status')->get();
        $tags = DB::table('listing_tags')->get();
        $states = DB::table('info_states')->where('country_id', 63)->orderBy('name')->get();
        $optAttrib = [];
        $getCities = 0;
        foreach ($states as $state) {
            if ($listing->state == $state->name) {
                $getCities = $state->id;
            }
            $optAttrib[$state->name] = ['data-id' => $state->id];
        }

        $cities = DB::table('info_cities')->where('state_id', $getCities)->get();

        $units = Unit::where('listing_id', $listing->id)->get();

        $optAttribSector = [];
        $cityID = 0;
        foreach ($cities as $city) {
            if ($listing->city == $city->name) {
                $cityID = $city->id;
            }
            $optAttribSector[$city->name] = ['data-id' => $cityID];
        }

        $sectores = Sector::where('city_id', $cityID)->get();

        return view('admin.listing.add-tw', compact(
            'listing',
            'benefits',
            'services',
            'types',
            'categories',
            'tags',
            'details',
            'states',
            'optAttrib',
            'cities',
            'environments',
            'general_characteristics',
            'isvalid',
            'sectores',
            'optAttribSector',
            'currentRouteName',
            'units',
            'fields_to_edit'
        ));
    }

    public function update(Request $request, Listing $listing)
    {

        $fields = [
            'listing_type' => 'plan',
            'status' => 'estado',
            'owner_name' => 'nombre de propietario',
            'identification' => 'identificación',
            'phone_number' => 'telefono',
            'owner_email' => 'email',
            'available' => 'disponibilidad',
            'listing_title' => 'titulo de propiedad',
            'meta_description' => 'metadescripcion',
            'property_price' => 'precio',
            'property_price_min' => 'precio minimo',
            'customized_price' => 'precio personalizado',
            'construction_area' => 'area de construccion',
            'land_area' => 'area de terreno',
            'Front' => 'frente',
            'Fund' => 'fondo',
            'listyears' => 'años de construccion',
            'aval' => 'avaluo de la propiedad',
            'state' => 'provincia',
            'city' => 'ciudad',
            'address' => 'direccion',
            'lat' => 'latitud',
            'lng' => 'longitud',
            'listingtype' => 'categoria',
            'listingtypestatus' => 'tipo',
            'listingtagstatus' => 'etiquetas',
            'listing_description' => 'descripcion'
        ];

        $changes = [];

        foreach ($fields as $field => $label) {
            if (isset($request->$field) && $request->$field != $listing->$field) {
                $changes[] = $label; // Solo guarda el nombre del campo
            }
        }

        if (!empty($changes)) {
            DB::table('updated_listing')->insert([
                'listing_id' => $listing->id,
                'property_code' => $listing->product_code,
                'value_change' => json_encode($changes),
                'user_id' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        //Funcion para crear 
        if ($listing->available == 1 && $listing->status == 0 && $request->status == 1) {

            $uploadUser = User::select('name')->where('id', $listing->user_id)->first();

            $notificationHTML = view('notifications.index', compact('listing', 'uploadUser'))->render();

            $users = User::select('id', 'user_second_id', 'name')
                ->where('status', 1)
                ->whereNotNull('user_second_id')
                ->where(function ($query) {
                    $query->where('role', 'administrator')
                        ->orWhere('role', 'ASESOR');
                })
                ->get();

            $user_ids = $users->pluck('user_second_id')->toArray();

            $notificationData = [
                'type' => NotificationType::INFORMATION,
                'title' => 'Nueva propiedad activada',
                'content' => $notificationHTML,
                'user_ids' => $user_ids,
                'viewed_by' => [],
                'is_read' => false
            ];

            $notificationData['type'] = NotificationType::INFORMATION;

            $notification = Notification::create($notificationData);
        }

        if (isset($request->listing_title) && !isset($listing->slug)) {
            $slug = Str::of($request->listing_title)->trim()->slug()->limit(70, '') . '-' . rand(10000, 99999);
            $request->merge(['slug' => $slug]);
        }

        if (Auth::user()->role != "administrator") $this->authorize('update', $listing);
        if (is_array($request->checkBene)) $request->merge(['listingcharacteristic' => implode(",", $request->checkBene)]);
        else $request->merge(['listingcharacteristic' => '']);

        if (is_array($request->checkgc)) $request->merge(['listinggeneralcharacteristics' => implode(",", $request->checkgc)]);
        else $request->merge(['listinggeneralcharacteristics' => '']);

        if (is_array($request->checkEnvir)) $request->merge(['listingenvironments' => implode(",", $request->checkEnvir)]);
        else $request->merge(['listingenvironments' => '']);

        if (is_array($request->checkServ)) $request->merge(['listinglistservices' => implode(",", $request->checkServ)]);
        else $request->merge(['listinglistservices' => '']);

        if (is_array($request->updatedImages)) $request->merge(['images' => implode("|", $request->updatedImages)]);
        else $request->merge(['images' => '']);

        if (!$request->updatedVideo && $listing->video != null) $listing->video = null;

        $result = [];
        $ii = 0;
        foreach ($request->all() as $key => $value) {
            if ("detail" == substr($key, 0, 6)) $result[] = $value;
        }
        $bedrooms = 0;
        $bathrooms = 0;
        $garage = 0;
        if (count($result) > 0) {
            $request->merge(['heading_details' => json_encode($result)]);
            foreach ($result as $r) {
                for ($i = 0; $i < count($r); $i++) {
                    if ($r[$i] == 109) $bedrooms = $bedrooms + $r[$i + 1] * 1;
                    if ($r[$i] == 110) $bedrooms = $bedrooms + $r[$i + 1] * 2;
                    if ($r[$i] == 111) $bedrooms = $bedrooms + $r[$i + 1] * 3;
                    if ($r[$i] == 49 || $r[$i] == 86 || $r[$i] == 41 || $r[$i] == 115 || $r[$i] == 62 || $r[$i] == 113) $bedrooms = $bedrooms + $r[$i + 1];
                    if ($r[$i] == 48 || $r[$i] == 76 || $r[$i] == 81 || $r[$i] == 49 || $r[$i] == 113) $bathrooms = $bathrooms + $r[$i + 1];
                    if ($r[$i] == 43) $garage = $garage + $r[$i + 1];
                }
            }
        } else {
            $request->merge(['heading_details' => '']);
        }

        if (isset($request->property_price) && $request->property_price != $listing->property_price && $listing->property_price > 0) {
            $comment = Comment::create([
                'listing_id' => $listing->id,
                'user_id' => Auth::user()->id,
                'property_code' => $listing->product_code,
                'type' => 'price',
                'comment' => $request->comment,
                'property_price_prev' => $listing->property_price,
                'property_price' => $request->property_price,
                'property_price_min_prev' => $listing->property_price,
                'property_price_min' => $request->property_price_min,
            ]);
        }

        if (Auth::user()->role == "administrator" && $listing->status == null && $request->status == 1) {
            $comment = Comment::create([
                'listing_id' => $listing->id,
                'user_id' => Auth::user()->id,
                'property_code' => $listing->product_code,
                'type' => 'status',
                'comment' => 'Se publico la propiedad en la página web',
                'value' => 1
            ]);
        }

        $listing->fill($request->all());

        //set variables bedroom bathroom y garage
        $listing->bedroom = $bedrooms;
        $listing->bathroom = $bathrooms;
        $listing->garage = $garage;

        $listing->customized_price = $request->customized_price;

        if ($request->niv_constr <= 0) $listing->niv_constr = null;
        else $listing->niv_constr = $request->niv_constr;
        if ($request->num_pisos <= 0) $listing->num_pisos = null;
        else $listing->num_pisos = $request->num_pisos;
        if ($request->pisos_constr <= 0) $listing->pisos_constr = null;
        else $listing->pisos_constr = $request->pisos_constr;

        //new values
        $listing->owner_address = $request->owner_address;
        $listing->cadastral_key = $request->cadastral_key;
        $listing->aliquot = $request->aliquot;
        $listing->observations_type_property = $request->observations_type_property;
        $listing->listinggeneralcharacteristics = $request->listinggeneralcharacteristics;
        $listing->listingenvironments = $request->listingenvironments;
        if ($request->cavity_error == "on") $listing->cavity_error = 1;
        else $listing->cavity_error = 0;
        if ($request->vip == "on") $listing->vip = 1;
        else $listing->vip = 0;
        if ($request->morgaged == "on") $listing->mortgaged = 1;
        else $listing->mortgaged = 0;

        //$listing->mount_mortgage = $request->mount_mortgage;

        if ($request->listingtype == 26) $listing->planing_license = $request->planing_license;
        if ($request->mortgaged == "on") $listing->mortgaged = 1;
        else $listing->mortgaged = 0;
        if ($request->mortgaged == "on") {
            $listing->entity_mortgaged = $request->entity_mortgaged;
            $listing->mount_mortgaged = $request->mount_mortgaged;
            $listing->warranty = $request->warranty;
        } else {
            $listing->entity_mortgaged = null;
            $listing->mount_mortgaged = null;
            $listing->warranty = null;
        }

        if (!isset($listing->slug)) $listing->slug = $request->slug;

        //set if listing credit vip
        //$listing->credit_vip = $request->credit_vip;

        $listing->is_dual_operation = $request->is_dual_operation;

        if (!$listing->locked && ($listing->owner_name != null || $request->owner_name != null) && ($listing->identification != null || $request->identification != null) && ($listing->phone_number != null || $request->phone_number != null) && ($listing->owner_email != null || $request->owner_email != null) && ($listing->owner_address != null || $request->owner_address != null) && ($listing->property_price != null || $request->property_price != null) && ($listing->property_price_min != null || $request->property_price_min != null)) $listing->locked = true;

        $uploads = [];

        if ($request->hasFile('galleryImages')) {
            foreach ($request->file('galleryImages') as $image) {
                if ($image->isValid()) {
                    $validate = $image->getClientOriginalExtension();
                    if (in_array($validate, ['jpeg', 'jpg', 'png', 'heic', 'heif', 'webp'])) {
                        $img = Image::make($image);
                        $img2 = Image::make($image);
                        $img3 = Image::make($image);
                        $mime = $img->mime();
                        if ($mime == 'image/jpeg') $ext = '.jpg';
                        elseif ($mime == 'image/png') $ext = '.png';
                        elseif ($mime == 'image/heic') $ext = '.heic';
                        elseif ($mime == 'image/heif') $ext = '.heif';
                        elseif ($mime == 'image/webp') $ext = '.webp';
                        else $ext = '';
                        if (strlen($ext) > 0) {
                            $folder = 'uploads/listing/';
                            $nameFile = "IMG_$listing->id-" . uniqid() . $ext;
                            $img->save($folder . $nameFile);
                            $uploads[] = $nameFile;

                            $folder2 = 'uploads/listing/600/';
                            $img2->fit(600, 400, function ($constraint) {
                                $constraint->upsize();
                                $constraint->aspectRatio();
                            });
                            $img2->save($folder2 . $nameFile, 40);

                            $folder3 = 'uploads/listing/300/';
                            $img3->fit(300, 200, function ($constraint) {
                                $constraint->upsize();
                                $constraint->aspectRatio();
                            });
                            $img3->save($folder3 . $nameFile, 40);

                            //agregando marca de agua
                            $folder_1 = 'uploads/listing/thumb/';
                            //$namefile = "THUMB_IMG_$listing->id-".uniqid().$ext;
                            $watermark = Image::make(public_path('img/MARCADEAGUA.png'));
                            $imageWidth = $img->width();

                            $watermarkSize = round(10 * $imageWidth / 40);
                            $watermark->resize($watermarkSize, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });

                            $watermark->opacity(15);

                            $img->insert($watermark, 'center', 0, 0);
                            $img->save($folder_1 . $nameFile);

                            $folder_2 = 'uploads/listing/thumb/600/';
                            $img2->fit(600, 400, function ($constraint) {
                                $constraint->upsize();
                                $constraint->aspectRatio();
                            });
                            $imageWidth2 = $img2->width();
                            $watermarkSize = round(10 * $imageWidth2 / 40);
                            $watermark->resize($watermarkSize, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });

                            $img2->insert($watermark, 'center', 0, 0);
                            $img2->save($folder_2 . $nameFile, 40);

                            $folder_3 = 'uploads/listing/thumb/300/';
                            $img3->fit(300, 200, function ($constraint) {
                                $constraint->upsize();
                                $constraint->aspectRatio();
                            });
                            $imageWidth3 = $img3->width();
                            $watermarkSize = round(10 * $imageWidth3 / 40);
                            $watermark->resize($watermarkSize, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });

                            $img3->insert($watermark, 'center', 0, 0);
                            $img3->save($folder_3 . $nameFile, 40);
                        }
                    } elseif ($validate == "mp4" || $validate == "MOV") {
                        $namefile = $listing->slug . "." . $image->getClientOriginalExtension();
                        $filepath = public_path() . '/uploads/video/';
                        $image->move($filepath, $namefile);
                        $listing->video = $namefile;
                    }
                }
            }
        }

        if (count($uploads) > 0) {
            $save_uploads = implode("|", $uploads);
            if (strlen($request->images) > 2) {
                $listing->update(['images'  =>  $request->images . '|' . $save_uploads]);
            } else {
                $listing->update(['images'  => $save_uploads]);
            }
        }

        $listing->cardinal_zone = (new CardinalZoneService())->calculate(
            (float) $request->lat,
            (float) $request->lng,
            $request->city ?? $listing->city
        );

        $manualZone = $request->input('cardinal_zone');

        if (!empty($manualZone)) {
            $listing->cardinal_zone = $manualZone;
        } else {
            $computed = (new CardinalZoneService())->calculate(
                (float) $request->lat,
                (float) $request->lng,
                $request->city ?? $listing->city
            );
            if ($computed !== null) {
                $listing->cardinal_zone = $computed;
            }
        }

        $listing->save();

        if ($this->iscomplete($listing)) {
            $listing->isvalid = 1;
            $listing->save();
        }

        // return response()->json([
        //     'listing' => $listing
        // ]);

        $messages = ['status' => 'Propiedad Actualizada'];
        //$messages = array_merge($messages, ['alert'=>'Hola Mundo']);

        if (isset($request->edit)) {
            if ($listing->property_by == "Housing") {
                return redirect()->route('admin.housing.property.edit', compact('listing'))->with($messages);
            } else {
                return redirect()->route('admin.listings.edit', compact('listing'))->with($messages);
            }
        } else {
            return response()->json([
                'success' => true,
                'fragment' => $request->fragment
            ]);
        }
    }

    public function show(Listing $listing)
    {
        $benefits = DB::table('listing_benefits')->get();
        $services = DB::table('listing_services')->get();
        $types = DB::table('listing_types')->get();
        $details = DB::table('listing_characteristics')->get();
        $categories = DB::table('listing_status')->get();
        $tags = DB::table('listing_tags')->get();
        $states = DB::table('info_states')->where('country_id', 63)->orderBy('name')->get();
        $optAttrib = [];
        $getCities = 0;
        foreach ($states as $state) {
            if ($listing->state == $state->name) {
                $getCities = $state->id;
            }
            $optAttrib[$state->name] = ['data-id' => $state->id];
        }
        $cities = DB::table('info_cities')->where('state_id', $getCities)->get();
        return view('admin.listing.show', compact(
            'listing',
            'benefits',
            'services',
            'types',
            'categories',
            'tags',
            'details',
            'states',
            'optAttrib',
            'cities'
        ));
    }

    public function create_code(Request $request)
    {

        $validateCode = Listing::where('product_code', $request->product_code)->first();

        if ($validateCode) {
            return response()->json([
                'msg' => "Editando la propiedad"
            ]);
        }

        $listing = Listing::create([
            'product_code' => $request->product_code
        ]);

        return response()->json([
            'msg' => "Se creo el codigo $listing->product_code"
        ]);
    }

    public function seo(Listing $listing)
    {
        $data['listings'] = Listing::selectRaw('count(*) as total,state,address')->where('status', 1)->where('state', 'Azuay')->orderBy('total', 'desc')->groupBy('address')->get();
        return view('admin.seo.index', $data);
    }

    public function reslug(Listing $listing)
    {
        if (Auth::id() == 123) {
            $slug = Str::of($listing->listing_title)->trim()->slug()->limit(70, '') . '-' . rand(10000, 99999);
            $listing->update(['slug' => $slug]);
        }
    }

    public function show_listing($id)
    {

        $radius = 2000;

        $propertie = Listing::where('id', $id)->first();

        $similarProperties = [];
        //$nearbyproperties = []; 
        //$nearbyproperties_aux = [];

        $latitude = $propertie->lat;
        $longitude = $propertie->lng;

        if ($propertie) {

            $priceDifference = ($propertie->listingtypestatus === 'en-venta') ? 20000 : 100;

            $minPrice = $propertie->property_price - $priceDifference;
            $maxPrice = $propertie->property_price + $priceDifference;

            $similarProperties = Listing::selectRaw("*, (6371000 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance", [$latitude, $longitude, $latitude])
                ->where('listingtype', 'LIKE', "%$propertie->listingtype%")
                ->where('listingtypestatus', 'LIKE', "%$propertie->listingtypestatus%")
                ->where('available', 1)
                //->where('status', 1)
                ->where("product_code", "!=", $propertie->product_code)
                ->whereBetween('property_price', [$minPrice, $maxPrice])
                ->having("distance", "<=", $radius)
                //->take(10)
                ->get();
        }

        $comments = DB::table('comments')->where('type', '!=', 'price')->where('listing_id', $id)->orderBy('created_at', 'desc')->get();
        $benefits = DB::table('listing_benefits')->get();
        $services = DB::table('listing_services')->get();
        $details = DB::table('listing_characteristics')->get();
        $general_characteristics = DB::table('listing_general_characteristics')->get();
        $environments = DB::table('listing_environments')->get();
        $units = Unit::where('listing_id', $id)->orderByRaw('CAST(unit_number AS UNSIGNED) asc')->get();
        return view('admin.listing.show-tw', compact('propertie', 'benefits', 'services', 'details', 'comments', 'similarProperties', 'general_characteristics', 'environments', 'units'));
    }

    public function unlocked($id)
    {
        $listing = Listing::where('id', $id)->first();
        $listing->locked = false;
        $listing->save();
        return redirect()->route('admin.listings.edit', $listing)->with('message', 'Propiedad ' . $listing->product_code . ' desbloqueada');
    }

    public function iscomplete(Listing $listing)
    {
        $isvalid = true;

        // Corregir lógica de address
        $address = $listing->address ?? $listing->sector;

        if (
            $listing->listing_type === null ||
            $listing->owner_name === null ||
            $listing->identification === null ||
            $listing->phone_number === null ||
            $listing->owner_email === null ||
            $listing->owner_address === null ||
            $listing->listing_title === null ||
            $listing->listing_description === null ||
            $listing->state === null ||
            $listing->city === null ||
            empty($address) ||
            $listing->land_area === null ||
            $listing->Front === null ||
            $listing->Fund === null ||
            $listing->property_price === null ||
            $listing->property_price_min === null ||
            $listing->lat === null ||
            $listing->lng === null ||
            $listing->listinglistservices === "" ||
            $listing->listinggeneralcharacteristics === "" ||
            $listing->listingcharacteristic === "" ||
            $listing->images === ""
        ) {
            $isvalid = false;
        }

        // Validar construction_area solo si NO es terreno
        if ($listing->listingtype != 26) {
            if ($listing->listingenvironments === "" || $listing->construction_area === null || $listing->listyears === null) {
                $isvalid = false;
            }
        }

        // Validar heading_details
        $aux_heading_details = json_decode($listing->heading_details);

        if (
            is_array($aux_heading_details) &&
            isset($aux_heading_details[0]) &&
            is_array($aux_heading_details[0]) &&
            (empty($aux_heading_details[0][0]) || count($aux_heading_details[0]) <= 1)
        ) {
            $isvalid = false;
        }

        // Validaciones solo si es en venta
        if ($listing->listingtypestatus === 'en-venta') {
            if ($listing->aval === null) {
                $isvalid = false;
            }

            if ($listing->cadastral_key === null) {
                $isvalid = false;
            }

            if (
                $listing->mortgaged &&
                (
                    $listing->entity_mortgaged === null ||
                    $listing->mount_mortgaged === null ||
                    $listing->warranty === null
                )
            ) {
                $isvalid = false;
            }
        }

        return $isvalid;
    }

    public function getMissingFields(Listing $listing): array
    {
        $missing = [];

        $address = $listing->address ?? $listing->sector;

        // Campos básicos
        if (empty($listing->listing_type)) $missing[] = 'Tipo de propiedad';
        if (empty($listing->owner_name)) $missing[] = 'Nombre del propietario';
        if (empty($listing->identification)) $missing[] = 'Identificación del propietario';
        if (empty($listing->phone_number)) $missing[] = 'Teléfono del propietario';
        if (empty($listing->owner_email)) $missing[] = 'Correo electrónico del propietario';
        if (empty($listing->owner_address)) $missing[] = 'Dirección del propietario';
        if (empty($listing->listing_title)) $missing[] = 'Título de la propiedad';
        if (empty($listing->listing_description)) $missing[] = 'Descripción de la propiedad';
        if (empty($listing->state)) $missing[] = 'Provincia';
        if (empty($listing->city)) $missing[] = 'Ciudad';
        if (empty($address)) $missing[] = 'Dirección o sector';
        if (empty($listing->land_area)) $missing[] = 'Área de terreno';
        if (empty($listing->Front)) $missing[] = 'Frente del terreno';
        if (empty($listing->Fund)) $missing[] = 'Fondo del terreno';
        if (empty($listing->property_price)) $missing[] = 'Precio de la propiedad';
        if (empty($listing->property_price_min)) $missing[] = 'Precio mínimo';
        if (empty($listing->lat) || empty($listing->lng)) $missing[] = 'Ubicación en el mapa';
        if (empty($listing->listinglistservices)) $missing[] = 'Servicios';
        if (empty($listing->listinggeneralcharacteristics)) $missing[] = 'Características generales';
        if (empty($listing->listingcharacteristic)) $missing[] = 'Características internas';
        if (empty($listing->images)) $missing[] = 'Imágenes';

        // Validar solo si NO es terreno
        if ($listing->listingtype != 26) {
            if (empty($listing->listingenvironments)) $missing[] = 'Ambientes';
            if (empty($listing->construction_area)) $missing[] = 'Área de construcción';
            if (empty($listing->listyears)) $missing[] = 'Años de construcción';
        }

        // Heading details
        $aux_heading_details = json_decode($listing->heading_details);
        if (
            is_array($aux_heading_details) &&
            isset($aux_heading_details[0]) &&
            is_array($aux_heading_details[0]) &&
            (empty($aux_heading_details[0][0]) || count($aux_heading_details[0]) <= 1)
        ) {
            $missing[] = 'Información destacada';
        }

        // Solo si es venta
        if ($listing->listingtypestatus === 'en-venta') {
            if (empty($listing->aval)) $missing[] = 'Avaluo municipal';
            if (empty($listing->cadastral_key)) $missing[] = 'Clave catastral';

            if ($listing->mortgaged) {
                if (empty($listing->entity_mortgaged)) $missing[] = 'Entidad hipotecaria';
                if (empty($listing->mount_mortgaged)) $missing[] = 'Monto de hipoteca';
                if (empty($listing->warranty)) $missing[] = 'Tipo de garantía';
            }
        }

        return $missing;
    }

    public function isMobile()
    {
        $mobile = false;
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $ismobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|zh-cn|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
            if ($ismobile) $mobile = true;
            else  $mobile = false;
        } else {
            $ismobile = false;
        }
        return $mobile;
    }

    public function postedfacebook($listing_id)
    {
        $listing = Listing::where('id', $listing_id)->first();
        $listing->posted_on_facebook = !$listing->posted_on_facebook;
        $listing->date_posted_facebook != null ? null : $listing->date_posted_facebook = date(now());
        $listing->save();

        return redirect()->back();
    }

    public function updateContactDate(Request $request)
    {
        $product_code = $request->input('product_code');
        $comment      = $request->input('comentario');
        $response     = $request->input('respuesta_contacto');

        $propertie       = Listing::where('product_code', $product_code)->first();
        $currentDateTime = now();

        if ($response !== 'NO CONTESTA') {
            $propertie->contact_at = $currentDateTime;
        } else {
            $propertie->no_answer_at = $currentDateTime;
        }
        $propertie->save();

        Comment::create([
            'listing_id'    => $propertie->id,
            'user_id'       => Auth::user()->id,
            'property_code' => $propertie->product_code,
            'type'          => 'Contact',
            'comment'       => $comment
        ]);

        // ✅ Marcar como done buscando por listing_id y user_id
        // No dependemos del queue_id del frontend
        \App\Models\ContactQueue::where('listing_id', $propertie->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->update([
                'status'       => 'done',
                'completed_at' => $currentDateTime,
            ]);

        return response()->json(['success' => true]);
    }

    public function validateListingsCompleteness()
    {
        // Obtener todas las propiedades disponibles
        $listings = Listing::where('available', 1)->get();

        foreach ($listings as $listing) {
            // Verificar si la propiedad está completa
            if ($this->iscomplete($listing)) {
                $listing->isvalid = 1;
            } else {
                $listing->isvalid = 0;
            }
            $listing->save();
        }

        return response()->json(['message' => 'Validación completada correctamente.']);
    }

    public function searchCadastralKey($clave)
    {
        try {
            $url = "https://enlinea.cuenca.gob.ec/geoservicios/rest/predios/{$clave}?tipo=A";

            $response = Http::withoutVerifying()->get($url);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo obtener la información del predio'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la consulta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function mapPins()
    {
        $pins = Listing::select(
            'id',
            'listing_title',
            'product_code',
            'lat',
            'lng',
            'cardinal_zone',
            'property_price',
            'property_by',
            'listingtype',
            'listingtypestatus',  // <-- necesarios para filtrar en el mapa
            'city',
            'slug'
        )
            ->where('available', 1)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('lat', '!=', '')
            ->where('lng', '!=', '')
            ->get();

        return response()->json($pins);
    }
}
