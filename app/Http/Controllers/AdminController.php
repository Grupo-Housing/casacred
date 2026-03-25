<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Listing;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\HistoryListings;
use App\Models\Sector;

class AdminController extends Controller
{
    public function index()
    {

        // $listings = Listing::where('delete_at', '<', Carbon::now())->get();

        // if(count($listings)>0){
        //     foreach($listings as $listing){
        //         HistoryListings::create($listing);
        //         $listing->delete();
        //     }
        // }

        $now = Carbon::now();

        // $properties_to_delete = Listing::select('created_at')->where('user_id', Auth::user()->id)->where('created_at', '<', Carbon::now()->subDays(1))->get();

        // return $properties_to_delete;

        $properties_today = Listing::whereDate('created_at', '=', Carbon::today()->toDateString())->get();

        $totalcasas = Listing::where('listingtype', 23)->where('available', 1)->count();
        $totaldepartamentos = Listing::where('listingtype', 24)->where('available', 1)->count();
        $totalcasascomer = Listing::where('listingtype', 25)->where('available', 1)->count();
        $totalterrenos = Listing::where('listingtype', 26)->where('available', 1)->count();
        $totalquintas = Listing::where('listingtype', 29)->where('available', 1)->count();
        $totalhaciendas = Listing::where('listingtype', 30)->where('available', 1)->count();
        $totallocalcomer = Listing::where('listingtype', 32)->where('available', 1)->count();
        $totaloficinas = Listing::where('listingtype', 35)->where('available', 1)->count();
        $totalsuites = Listing::where('listingtype', 36)->where('available', 1)->count();

        $totalactivatedproperties = Listing::where('status', 1)->count();
        $totalavailableproperties = Listing::where('available', 1)->count();

        $properties = [];
        $properties_aux = [];
        $properties = Listing::select('product_code', 'lat', 'lng', 'listing_title', 'id', 'address', 'images', 'created_at', 'listingtype')
            ->where('city', 'LIKE', "%Cuenca%")
            // ->where('listingtype', 'LIKE', "%%")
            ->where('available', 1)
            ->where(DB::raw('LENGTH(lat)'), '>', 5)
            ->where(DB::raw('LENGTH(lng)'), '>', 5)
            //->latest()->take(20)->get();
            ->orderBy('product_code', 'desc')
            ->get();
        foreach ($properties as $p) {
            if (Str::startsWith($p->lat, '-') && Str::startsWith($p->lng, '-') && Str::contains($p->lat, '.') && Str::contains($p->lng, '.')) {
                array_push($properties_aux, $p);
            }
        }

        $properties_dropped = DB::select("select * from comments where type LIKE '%price%' AND property_price < property_price_prev order by created_at desc  LIMIT 5");

        $properties_at_week = Listing::where('user_id', Auth::user()->id)->whereBetween('created_at', [$now->startOfWeek()->format('Y-m-d'), $now->endOfWeek()->format('Y-m-d')])->get();

        $updated_listing = DB::table('updated_listing')->where("created_at", "LIKE", "%" . substr(date(now()), 0, 10) . "%")->where('user_id', Auth::user()->id)->get();

        return view('admin.index', compact('totalactivatedproperties', 'totalavailableproperties', 'properties_aux', 'totalcasas', 'totaldepartamentos', 'totalcasascomer', 'totalterrenos', 'totalquintas', 'totalhaciendas', 'totallocalcomer', 'totaloficinas', 'totalsuites', 'properties_at_week', 'properties_today', 'properties_dropped', 'updated_listing', 'now'));
    }
    public function test()
    {
        return view('admin.test');
    }
    public function getallimg(Listing $listing)
    {
        if ($listing) {
            $listing = Listing::find($listing->id);
            return view('admin.z-getallimg', compact('listing'));
        }
    }
    public function words()
    {
        $words = Word::orderBy('id', 'desc')->get();
        return view('admin.words.index', compact('words'));
    }

    public function propertieschangeprice(Request $request)
    {
        if ($request->property_code) {
            $properties_change_price = Comment::where('property_code', $request->property_code)->paginate(10);
        } else {
            $properties_change_price = Comment::where('type', 'LIKE', "%price%")->orWhere('property_price', '<', 'property_price_prev')->orderBy('created_at', 'DESC')->paginate(10);
        }
        return view('admin.comments.allprice', compact('properties_change_price'));
    }

    public function setoutstanding(Request $request)
    {
        $listing = Listing::where('id', $request->outstanding)->first();
        if ($listing->outstanding) $listing->outstanding = false;
        else $listing->outstanding = true;
        $listing->save();
        return redirect()->route('home.tw.edit', $listing);
    }

    public function setContactDate(Request $request)
    {

        $now = Carbon::now();

        $listing = Listing::where('product_code', $request->product_id)->first();
        $listing->contact_at = $now;
        $listing->save();

        Comment::create([
            'listing_id' => $listing->id,
            'user_id' => Auth::user()->id,
            'property_code' => $listing->product_code,
            'type' => 'Contact',
            'comment' => $request->comment
        ]);


        return redirect()->route('admin.properties');
    }

    public function getzones($zona)
    {
        $zonas = Sector::where('name', 'LIKE', '%' . $zona . '%')->get();

        return response()->json($zonas);
    }
}
