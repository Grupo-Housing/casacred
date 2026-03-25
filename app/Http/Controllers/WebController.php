<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Parish;
use App\Models\Post;
use App\Models\SeoPage;
use App\Models\Service;
use App\Models\User;
use App\Models\Sector;
use App\Models\Unit;
use App\Traits\SendEmailTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class WebController extends Controller
{
    use SendEmailTrait;

    public function index(Request $request, ?string $slug = null)
    {

        $operacion = session('operacion');
        $type = session('tipo');
        $text = session('text');

        if ($operacion == null || $type == null || $text == null) {

            $slug = request()->segment(2);

            $segments = explode('-', $slug);
            if (count($segments) > 1) {
                if ($segments[0] != "propiedades") {
                    if ($segments[1] == "comerciales") {
                        $typeSlug = $segments[0] . " " . $segments[1];
                        $operacionSlug = $segments[3];
                        if (count($segments) > 4) $textSlug = $segments[5];
                        else $textSlug = null;
                    } else {
                        $typeSlug = $segments[0];
                        if ($segments[2] == "renta" || $segments[2] == "alquiler") {
                            $operacionSlug = "alquilar";
                        } else {
                            $operacionSlug = Str::slug($segments[1] . " " . $segments[2]);
                        }
                        if (count($segments) > 3) $textSlug = $segments[4];
                        else $textSlug = null;
                    }
                    $type = DB::table('listing_types')->select('id')->where('type_title', 'LIKE', '%' . $typeSlug . '%')->first();
                    $type = $type->id;
                    if ($operacionSlug == "alquiler" || $operacionSlug == "renta") $operacion = "alquilar";
                    else $operacion = $operacionSlug;

                    $text = $textSlug;
                }
            }
        } else {
            $operacion = session('operacion');
            $type = session('tipo');
            $text = session('text');
        }


        return view('indexweb', compact('operacion', 'type', 'text'));

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $ismobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|zh-cn|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
        } else {
            $ismobile = false;
        }

        $keywords = "";
        $keywordscas = "casas venta, venta casas, venta casa, casa venta, casa en venta, en venta casas, casas a venta, casas en venta, casas de venta";
        $keywordsdep = "departamentos venta, venta departamentos, departamento de venta, en venta departamentos, departamento de venta, departamento venta, venta departamento, departamento alquiler, departamentos alquiler, departamentos arriendo, arriendo departamentos, arriendo de departamentos, departamentos en venta";
        $keywordster = "terrenos venta, terrenos de venta, venta terrenos, venta de terrenos, terreno venta, venta terreno, terreno en venta, terrenos en venta";

        $current_url = request()->segment(1);

        $types = DB::table('listing_types')->get();
        $states = DB::table('info_states')->where('country_id', 63)->orderBy('name')->get();
        $categories = DB::table('listing_status')->get();

        if (request()->segment(2)) {

            //return request()->segment(2);
            switch ($current_url) {
                case 'casas-de-venta-en-ecuador':
                    $keywords = $keywordscas . ", venta casas ecuador, casas de venta ecuador, casas de venta en ecuador, casas en venta ecuador, casas en venta en ecuador";
                    break;
                case 'departamentos-de-venta-en-ecuador':
                    $keywords = $keywordsdep . ", departamentos venta ecuador, departamentos de venta ecuador, departamentos de venta en ecuador, departamentos en venta ecuador, departamentos en venta en ecuador";
                    break;
                case 'terrenos-de-venta-en-ecuador':
                    $keywords = $keywordster . ", terrenos venta ecuador, terrenos de venta ecuador, terrenos de venta en ecuador, terrenos en venta ecuador, terrenos en venta en ecuador";
                    break;
                case 'casas-de-venta-en-cuenca':
                    $keywords = $keywordscas . ", casas venta cuenca, venta casas cuenca, casas en cuenca de venta, casas de venta cuenca, casas venta en cuenca, cuenca casas en venta, casa cuenca venta, casas de venta cuenca ecuador, casas de venta en cuenca ecuador, venta casa cuenca, venta casa en cuenca, venta de casa cuenca, venta de casas cuenca";
                    break;
                case 'departamentos-de-venta-en-cuenca':
                    $keywords = $keywordsdep . ", departamentos venta cuenca, departamentos de venta en cuenca, departamentos de venta cuenca ecuador, venta departamentos cuenca, venta de departamentos en cuenca ecuador, arriendo cuenca, cuenca arriendos, departamentos alquiler cuenca, departamentos arriendo cuenca, departamentos de arriendo en cuenca, departamentos en renta cuenca, cuenca departamentos de arriendo";
                    break;
                case 'terrenos-de-venta-en-cuenca':
                    $keywords = $keywordster . ", terrenos venta cuenca, terrenos de venta cuenca, venta de terrenos cuenca, venta de terrenos en cuenca, venta de terrenos en cuenca ecuador, terrenos de venta cuenca ecuador";
                    break;
                case 'casas-de-venta-en-quito':
                    $keywords = $keywordscas . ", casas venta quito, casa venta quito, venta casas quito, quito casas de venta, quito casas en venta, quito venta casas, casas quito venta, casa de venta en quito, venta de casa quito, casa en venta quito, venta casa quito, venta de casas quito norte, venta de casas quito sur, venta de casas en quito, venta de casas en quito de oportunidad, casa de venta quito sur, casas de venta en quito, casas de venta en quito al norte, casas de venta quito sur, casas venta sur de quito, casas venta norte de quito, casas de venta en quito sur, casas de venta quito norte";
                    break;
                case 'departamentos-de-venta-en-quito':
                    $keywords = $keywordsdep . ", departamentos venta quito, departamento venta quito, venta departamentos quito, departamento quito venta, venta departamentos en quito, departamentos quito venta, venta de departamentos quito, departamentos de venta en quito, departamentos de venta quito, departamentos alquiler quito, departamento de arriendo quito sur, departamentos arriendo sur de quito, departamentos de arriendo quito sur, departamentos de arriendo quito norte, departamentos arriendo norte de quito, quito arriendo, arriendo departamentos quito, departamentos arriendo en quito, quito departamentos de arriendo";
                    break;
                case 'terrenos-de-venta-en-quito':
                    $keywords = $keywordster . ", venta de terrenos en quito, terrenos en venta en quito, terrenos de venta quito, venta de terrenos quito, terreno venta quito, venta terrenos quito, terrenos quito venta, terrenos venta quito, terrenos en venta quito, terrenos en quito de venta, venta de terreno quito, terrenos de venta en quito";
                    break;
                case 'casas-de-venta-en-guayaquil':
                    $keywords = $keywordscas . ", venta de casas guayaquil norte, guayaquil casas en venta, venta de casa guayaquil, venta de casas guayaquil, casa venta guayaquil, casa en venta guayaquil, casas de venta en guayaquil, venta de casas en guayaquil, casas en venta en guayaquil baratas, casas en venta guayaquil baratas, venta de casa en guayaquil, casas de venta guayaquil norte, venta casas guayaquil, casas guayaquil venta, casas guayaquil, casa guayaquil venta, casas de venta guayaquil, venta casa guayaquil, casas venta guayaquil, casa guayaquil";
                    break;
                case 'departamentos-de-venta-en-guayaquil':
                    $keywords = $keywordsdep . ", guayaquil departamentos en alquiler, venta departamentos en guayaquil, departamentos en venta en guayaquil, venta departamento guayaquil, alquiler departamentos guayaquil, alquilo departamento guayaquil, departamentos venta guayaquil, departamento guayaquil venta, departamento venta guayaquil, departamentos guayaquil venta, departamentos en guayaquil venta, venta departamentos guayaquil";
                    break;
                case 'terrenos-de-venta-en-guayaquil':
                    $keywords = $keywordster . ", guayaquil terrenos en venta, venta de terrenos guayaquil, terreno en venta en guayaquil, terreno guayaquil venta, terreno venta guayaquil, terrenos guayaquil venta, terrenos de venta guayaquil, venta terreno guayaquil, venta de terreno en guayaquil, terreno venta guayaquil, venta de terreno guayaquil, venta de terrenos en guayaquil, venta terrenos guayaquil, terrenos en venta en guayaquil";
                    break;
                default:
                    # code...
                    break;
            }

            //return "code: ". $code . " - slug: " . $slug . " - ubication: " . $ubication;

            // if(!is_numeric($code)) {
            //     $ubication = $slug;
            //     $slug = $code;
            //     if($ubication){
            //         $listings = Listing::filterByListingTitle($ubication)->paginate(20);
            //         //dd($listings);
            //         return view('indexweb',compact('states', 'keywords', 'listings', 'types', 'categories', 'ismobile'));
            //     }
            // } else {
            //     $listings = Listing::where('product_code', $code)->first();
            //     //dd($listings);
            //     //dd($listings);
            //     return view('indexweb',compact('states', 'keywords', 'listings', 'types', 'categories', 'ismobile'));
            // }

            if ($slug) {
                $seopage = SeoPage::where('slug', $slug)->first();
                if ($seopage) {
                    if ($seopage->category == 0) {
                        $listings = Listing::where('listingtype', $seopage->type)->where('status', 1)->where('available', 1)->where('listingtypestatus', 'LIKE', "%$seopage->typestatus%")->inRandomOrder()->limit(6)->get();
                    } else {
                        $listings = Listing::where('state', $seopage->state)->where('city', $seopage->city)->where('listingtype', $seopage->type)->where('status', 1)->where('listingtypestatus', 'LIKE', "%$seopage->typestatus%")->paginate(10);
                    }
                    $similarwords = $this->getSimilarWords($seopage->info_header);
                    $similarwordsfooter = $this->getSimilarWords($seopage->info_footer);
                    for ($i = 0; $i < count($similarwords); $i++) {
                        $wordbysearch = $similarwords[$i][0];
                        $synonim = $similarwords[$i][1][rand(0, count($similarwords[$i][1]) - 1)];
                        $seopage->info_header = str_replace("{" . $wordbysearch . "}", $synonim, $seopage->info_header);
                    }
                    for ($i = 0; $i < count($similarwordsfooter); $i++) {
                        $wordbysearch = $similarwordsfooter[$i][0];
                        $synonim = $similarwordsfooter[$i][1][rand(0, count($similarwordsfooter[$i][1]) - 1)];
                        $seopage->info_footer = str_replace("{" . $wordbysearch . "}", $synonim, $seopage->info_footer);
                    }
                    if (str_contains($seopage->info_header, "{ciudad}")) $seopage->info_header = str_replace("{ciudad}", $seopage->city, $seopage->info_header);
                    if (isset($seopage->info_footer)) if (str_contains($seopage->info_footer, "{ciudad}")) $seopage->info_footer = str_replace("{ciudad}", $seopage->city, $seopage->info_footer);
                    return view('webseopage', compact('seopage', 'listings', 'ismobile', 'types', 'similarwords', 'states'));
                } else {
                    $seopage = SeoPage::where('old_slug', $slug)->first();
                    if ($seopage) {
                        return redirect()->route('web.propiedades', $seopage->slug);
                        if ($seopage->category == 0) {
                            $listings = Listing::where('listingtype', $seopage->type)->where('status', 1)->where('available', 1)->where('listingtypestatus', 'LIKE', "%$seopage->typestatus%")->inRandomOrder()->limit(6)->get();
                        } else {
                            $listings = Listing::where('state', $seopage->state)->where('city', $seopage->city)->where('listingtype', $seopage->type)->where('status', 1)->where('listingtypestatus', 'LIKE', "%$seopage->typestatus%")->paginate(10);
                        }
                        $similarwords = $this->getSimilarWords($seopage->info_header);
                        $similarwordsfooter = $this->getSimilarWords($seopage->info_footer);
                        for ($i = 0; $i < count($similarwords); $i++) {
                            $wordbysearch = $similarwords[$i][0];
                            $synonim = $similarwords[$i][1][rand(0, count($similarwords[$i][1]) - 1)];
                            $seopage->info_header = str_replace("{" . $wordbysearch . "}", $synonim, $seopage->info_header);
                        }
                        for ($i = 0; $i < count($similarwordsfooter); $i++) {
                            $wordbysearch = $similarwordsfooter[$i][0];
                            $synonim = $similarwordsfooter[$i][1][rand(0, count($similarwordsfooter[$i][1]) - 1)];
                            $seopage->info_footer = str_replace("{" . $wordbysearch . "}", $synonim, $seopage->info_footer);
                        }
                        if (str_contains($seopage->info_header, "{ciudad}")) $seopage->info_header = str_replace("{ciudad}", $seopage->city, $seopage->info_header);
                        if (isset($seopage->info_footer)) if (str_contains($seopage->info_footer, "{ciudad}")) $seopage->info_footer = str_replace("{ciudad}", $seopage->city, $seopage->info_footer);
                        return view('webseopage', compact('seopage', 'listings', 'ismobile', 'types', 'similarwords', 'states'));
                    } else {
                        $city = "";
                        $state = "";
                        $segment2 = request()->segment(2);
                        if ($segment2) {
                            if (!is_numeric($segment2)) {
                                $segments = explode("-", $segment2);
                                $lastelement = end($segments);
                                $city = $lastelement;
                                // $state = $segments[count($segments)-1];
                                // $city = $segments[count($segments)-2];
                            }
                        }

                        // $city = DB::table('info_cities')->where('name', $city)->get();

                        // //return $city;

                        // $state = DB::table('info_states')->where('id', $city);

                        //$listings = Listing::filterByState($state)->filterByCity($city)->paginate(20);
                        $listings = Listing::where('city', 'LIKE', "%$city%")->paginate(20);
                        return view('indexweb', compact('states', 'keywords', 'listings', 'types', 'categories', 'ismobile'));
                    }
                }
            }

            $listings = Listing::filterByState($request->state)->filterByCity($request->city)->paginate(20);
            //$types = DB::table('listing_types')->get();
            if ($ismobile) return view('indexmobile', compact('states', 'keywords', 'types'));
            else          return view('indexweb', compact('states', 'keywords', 'listings', 'types', 'categories', 'ismobile'));
        } else {
            if ($ismobile) {
                return view('indexmobile', compact('states', 'keywords', 'types'));
            } else {
                $types = DB::table('listing_types')->get();
                $states = DB::table('info_states')->where('country_id', 63)->orderBy('name')->get();
                $listingsc = Listing::where('state', 'LIKE', 'Azuay')->where('city', 'LIKE', 'Cuenca')->where('available', 1)->where('status', 1)->inRandomOrder()->take(3)->get();
                $listingsq = Listing::where('state', 'LIKE', 'Pichincha')->where('city', 'LIKE', 'Quito')->where('available', 1)->where('status', 1)->inRandomOrder()->take(3)->get();
                $listingsg = Listing::where('state', 'LIKE', 'Guayas')->where('city', 'LIKE', 'Guayaquil')->where('available', 1)->where('status', 1)->inRandomOrder()->take(3)->get();
                return view('general', compact('listingsc', 'listingsq', 'listingsg', 'states', 'types', 'ismobile'));
            }
        }
    }

    protected $array_positions = [];
    public function getSimilarWords($cadena)
    {
        $count = substr_count($cadena, '{');
        for ($i = 0; $i < $count; $i++) {
            $start = strpos($cadena, '{');
            $position_aux = 0;
            if (!array_key_exists($start, $this->array_positions)) {
                $start += strlen('{');
                $size = strpos($cadena, '}', $start) - $start;
                $similarwords = substr($cadena, $start, $size);
                if (str_contains($similarwords, "|")) $this->array_positions[$i] = array($similarwords, explode("|", $similarwords)); //str_replace("|", ",", $similarwords)
                $position_aux = strpos($cadena, $similarwords);
            }
            $cadena_aux = substr($cadena, $position_aux);
            $this->getSimilarWords($cadena_aux);
        }
        return $this->array_positions;
    }

    public function home()
    {

        $properties = Listing::where('status', 1)->orderBy('id', 'desc')->take(12)->get();
        $states = DB::table('info_states')->where('country_id', 63)->orderBy('name')->get();
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $ismobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|zh-cn|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
        } else {
            $ismobile = false;
        }
        // if($ismobile) return view('indexmobile', compact('states'));
        // else 
        $listings_outstanding = Listing::where('status', 1)->where('available', 1)->where('outstanding', 1)->take(4)->inRandomOrder()->get();
        //return view('home3', compact('ismobile', 'listings_outstanding', 'properties'));
        return view('home4');
    }

    public function creditos()
    {
        return view('creditos');
    }

    public function propiedades()
    {
        $listings = Listing::where('status', 1)->orderBy('id', 'desc')->paginate(12);
        return view('propiedades', compact('listings'));
    }

    public function indextest()
    {
        return view('webtail/index');
    }

    public function mobile()
    {
        return view('indexmobile');
    }

    public function mobiledet(Listing $listing)
    {
        $benefits = DB::table('listing_benefits')->get();
        $services = DB::table('listing_services')->get();
        $types = DB::table('listing_types')->get();
        $details = DB::table('listing_characteristics')->get();
        return view('detailmobile', compact('listing', 'details', 'benefits', 'services', 'types'));
    }

    public function searchlistings($category, $type, ?string $city = null)
    {

        return "Las variables pasados por la url son las siguientes: " . $category . " " . $type . " " . $city;

        //$city = substr($city, 4);
        $types = DB::table('listing_types')->get();
        $states = DB::table('info_states')->where('country_id', 63)->orderBy('name')->get();
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $ismobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|zh-cn|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
        } else {
            $ismobile = false;
        }
        $listings = Listing::filterByCity($city)->paginate(20);
        $categories = DB::table('listing_status')->get();

        if ($ismobile) return view('indexmobile', compact('states', 'keywords', 'types'));
        else          return view('indexweb', compact('states', 'listings', 'types', 'categories'));
    }

    public function detail(Listing $listing)
    {

        $values = [
            5 => 2.05,
            6 => 1.78,
            7 => 1.58,
            8 => 1.44,
            9 => 1.33,
            10 => 1.24,
            11 => 1.17,
            12 => 1.11,
            13 => 1.06,
            14 => 1.02,
            15 => 0.98,
            16 => 0.96,
            17 => 0.92,
            18 => 0.9,
            19 => 0.88,
            20 => 0.86
        ];

        $mobile = false;
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $ismobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|zh-cn|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
            if ($ismobile) $mobile = true;
            else  $mobile = false;
        } else {
            $ismobile = false;
        }

        $benefits = DB::table('listing_benefits')->get();
        $services = DB::table('listing_services')->get();
        $types = DB::table('listing_types')->get();
        $details = DB::table('listing_characteristics')->get();
        $user = User::where('id', $listing->user_id)->first();
        $generalcharacteristics = DB::table('listing_general_characteristics')->get();
        $environments = DB::table('listing_environments')->get();

        $difference_prices = $listing->property_price - $listing->property_price_min;
        $media = $difference_prices / 2;
        // if($ismobile) return view('detailmobile',compact('listing','details','benefits','services','types'));
        // else

        $units = Unit::where('listing_id', $listing->id)->where('status', 'available')->orderByRaw('CAST(unit_number AS UNSIGNED) asc')->get();

        if ($listing->available == 1) return view('detailprop2', compact('listing', 'details', 'benefits', 'services', 'types', 'mobile', 'user', 'values', 'generalcharacteristics', 'environments', 'media', 'units'));
        else return redirect('/propiedades-en-general');
    }

    public function getstates($id)
    {
        $states = DB::table('info_states')->where('country_id', $id)->get();
        return response()->json($states);
    }

    public function getcities($id)
    {
        $cities = DB::table('info_cities')->where('state_id', $id)->get();
        return response()->json($cities);
    }

    public function getsector($city_id)
    {
        $sectores = Sector::where('city_id', $city_id)->get();
        return response()->json($sectores);
    }

    public function notariausa()
    {
        return view('services.notariausa');
    }

    public function serviciosall()
    {

        return view('services');

        // $services=Service::where('status',1)->where('parent',0)->get();
        // foreach($services as $serv){

        //     echo '<img src="'.url('uploads/services',$serv->image).'">';
        //     echo '<a href="'.url('servicios/'.$serv->slug).'">'.$serv->title.'</a><br>';

        // }

    }

    public function servicios(Service $service)
    {
        $ismobile = $this->isMobile();
        $services = Service::where('status', 1)->where('parent', $service->id)->get();
        return view('services.subs', compact('services', 'service', 'ismobile'));
    }

    public function servicio(Service $service)
    {
        $ismobile = $this->isMobile();
        $otros = Service::where('status', 1)->where('parent', $service->parent)->get();
        $types = DB::table('listing_types')->select('type_title')->get();
        $states = DB::table('info_states')->where('country_id', 63)->orderBy('name')->get();
        return view('services.detail', compact('service', 'otros', 'types', 'states', 'ismobile'));
    }

    public function sendlead(Request $request)
    {

        $message = "<br><strong>Nuevo Lead</strong>
                    <br> Nombre: " . strip_tags($request->fname) . " " . strip_tags($request->flastname) . "
                    <br> Telef: " .  strip_tags($request->tlf) . "
                    <br> Email: " .  strip_tags($request->email) . "
                    ";

        if ($request->interest == "Crédito") $message .= "<br> Monto del Crédito: $" . number_format(strip_tags($request->mount), 0, ',', '.');
        if ($request->interest == "Vender una propiedad") {
            $typelisting = DB::table('listing_types')->where('id', strip_tags($request->type))->first();
            $message .= "<br> Tipo de propiedad: " . $typelisting->type_title;
            $message .= "<br> Provincia: " . strip_tags($request->state);
            $message .= "<br> Ciudad: " . strip_tags($request->city);
        }

        //if($request->message == null || $request->message == "") $request->message = "Sin información";

        $message .= "<br> Interes: " . strip_tags($request->interest) . "
                     <br> Mensaje: " . strip_tags($request->message) . "
                     <br> Fuente: Website ";

        $header = '';
        $header .= 'From: <leads@casacredito.com>' . "\r\n";
        $header .= "Reply-To: " . 'info@casacredito.com' . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        mail('info@casacredito.com', 'Lead GrupoHousing: ' . strip_tags($request->fname), $message, $header);
        mail('sebas31051999@gmail.com', 'Lead GrupoHousing: ' . strip_tags($request->fname), $message, $header);
        //mvargas@casacredito.com,info@casacredito.com,ventas@casacredito.com

        return redirect()->route('thank');
    }

    public function sendcite(Request $request)
    {

        $message = "<br><strong>Nueva Cita de Lead</strong>
                    <br><b>Nombre:</b> " . strip_tags($request->fname) . " " . strip_tags($request->flastname) . "
                    <br><b>Telef:</b> " .  strip_tags($request->tlf) . "
                    <br><b>Email:</b> " .  strip_tags($request->email) . "
                    <br><b>Interes:</b> Propiedad " . strip_tags($request->interest) . "
                    <br><b>Fecha de la cita:</b> El " . strip_tags($request->date) . " a las " . strip_tags($request->hour) . " 
                    <br><b>Comentario:</b> " . strip_tags($request->message) . "
                    ";

        $header = '';
        $header .= 'From: <leads@casacredito.com>' . "\r\n";
        $header .= "Reply-To: " . 'info@casacredito.com' . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        if (is_numeric($request->tlf) && $request->interest != "General") {
            mail('mvargas@casacredito.com,info@casacredito.com', 'Lead GrupoHousing: ' . strip_tags($request->fname), $message, $header);
            mail('sebas31051999@gmail.com', 'Lead GrupoHousing: ' . strip_tags($request->fname), $message, $header);
        } else {
            mail('sebas31051999@gmail.com', 'BOT CASA CREDITO: ' . strip_tags($request->fname), $message, $header);
        }

        return redirect()->back()->with('citesend', true);
    }

    public function sendLeadContact(Request $request)
    {

        /*$response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => '6Le1UsshAAAAAInuqh1QQ_C3jCx6YQAn_tDBNnOO',
            'response' => $request->input('g-recaptcha-response')
        ])->object();

        if($response->success && $response->score >= 0.7){
            $this->sendemail($request);
        } else {
            return "La validación de Recaptcha ha fallado. Por favor inténtelo de nuevo...";
        }*/

        $ismobile = $this->isMobile();

        $request->session()->flash('emailsend', 'Se ha enviado el correo');

        switch ($request->interest) {
            case 'Vender una propiedad':
            case 'Poner en Alquiler':
            case 'Créditos Hipotecarios':
            case 'Créditos de Consumo':
            case 'Créditos de Construcción':
                return back();
                break;
            case 'Busca Alquiler':
                if ($ismobile) {
                    return redirect('/propiedades?searchtxt=' . $request->city . '&type=alquilar');
                } else {
                    return redirect('/propiedades?pstate=' . $request->state . '&city=' . $request->city . '&category=alquilar');
                }
                break;
            default:
                return back();
                break;
        }
    }

    public function sendLeadAval(Request $request)
    {

        $message = "<br><strong>Nuevo Lead</strong>
                    <br> Nombre: " . strip_tags($request->name_aval) . "
                    <br> Telef: " .  strip_tags($request->phone_aval) . "
                    <br> Email: " .  strip_tags($request->email_aval) . "
                    <br> Interes: " . strip_tags($request->interest_aval) . "
                    <br> Mensaje: " . strip_tags($request->message_aval) . "
                    <br> Tipo de Propiedad: " . strip_tags($request->type) . "
                    <br> Provincia: " . strip_tags($request->state) . "
                    <br> Ciudad: " . strip_tags($request->city) . "
                    <br> Fuente: Website ";

        $header = '';
        $header .= 'From: <leads@casacredito.com>' . "\r\n";
        $header .= "Reply-To: " . 'info@casacredito.com' . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        mail('info@casacredito.com', 'Lead GrupoHousing: ' . strip_tags($request->fname), $message, $header);
        mail('sebas31051999@gmail.com', 'Lead GrupoHousing: ' . strip_tags($request->fname), $message, $header);
    }

    public function politicas()
    {
        return view('politicas');
    }

    public function seo()
    {
        $typeSlugMap = [
            23 => 'casas',
            24 => 'departamentos',
            25 => 'casas-comerciales',
            26 => 'terrenos',
            29 => 'quintas',
            30 => 'haciendas',
            32 => 'locales-comerciales',
            35 => 'oficinas',
            36 => 'suites',
            37 => 'edificios',
            38 => 'coloniales',
            39 => 'hoteles',
            40 => 'fabricas',
            41 => 'en-proyecto',
            42 => 'parqueaderos',
            43 => 'bodegas',
            44 => 'naves-industriales',
            45 => 'hostales',
            46 => 'penthouses',
        ];

        $statusSlugMap = [
            'venta'     => 'venta',
            'renta'     => 'renta',
            'general'   => 'general',
            'proyectos' => 'proyectos',
        ];

        // Combinaciones reales que existen en la BD (solo activas y disponibles)
        $combinations = Listing::where('status', 1)
            ->where('available', 1)
            ->whereNotNull('listingtype')
            ->whereNotNull('listingtypestatus')
            ->select('listingtype', 'listingtypestatus', 'city')
            ->distinct()
            ->get();

        $categoryUrls = [];
        foreach ($combinations as $combo) {
            $typeId     = (int) $combo->listingtype;
            $typeSlug   = $typeSlugMap[$typeId] ?? null;
            $statusSlug = $statusSlugMap[$combo->listingtypestatus] ?? null;

            if (!$typeSlug || !$statusSlug) continue;

            $citySlug = $combo->city ? \Illuminate\Support\Str::slug($combo->city) : null;

            $url = $citySlug
                ? url("/{$typeSlug}-en-{$statusSlug}-en-{$citySlug}")
                : url("/{$typeSlug}-en-{$statusSlug}");

            $categoryUrls[] = [
                'url'       => $url,
                'type_id'   => $typeId,
                'type_slug' => $typeSlug,
                'status'    => $combo->listingtypestatus,
                'city'      => $combo->city,
                'label'     => ucfirst(str_replace('-', ' ', $typeSlug))
                    . ' en ' . ucfirst($statusSlug)
                    . ($combo->city ? ' en ' . $combo->city : ''),
            ];
        }

        // Ordenar por tipo luego ciudad
        usort($categoryUrls, function ($a, $b) {
            $t = strcmp($a['type_slug'], $b['type_slug']);
            return $t !== 0 ? $t : strcmp($a['city'] ?? '', $b['city'] ?? '');
        });

        // Propiedades individuales (solo para la seccion de slugs)
        $listings = Listing::where('status', 1)
            ->where('available', 1)
            ->orderBy('product_code')
            ->get(['id', 'product_code', 'listing_title', 'slug', 'address']);

        return view('seo', compact('listings', 'categoryUrls'));
    }


    public function listingscsv()
    {

        $listings  = Listing::where('status', '1')->where('available', '1')->latest()->limit(500)->get();

        if (count($listings) > 0) {

            $delimiter = ",";
            $filename = "listings_" . date('Y-m-d') . ".csv";
            $f = fopen('php://memory', 'w');
            $fields = array(
                'id',
                'mpn',
                'brand',
                'availability',
                'condition',
                'link',
                'image[0].url',
                'additional_image_link',
                'title',
                'description',
                'address.city',
                'address.region',
                'address.country',
                'address.postal_code',
                'listing_type',
                'num_baths',
                'num_beds',
                'num_units',
                'property_type',
                'price',
                'inventory',
                'year_built',
                'google_product_category'
            );
            fputcsv($f, $fields, $delimiter);

            foreach ($listings as $li) {

                $imgcover = "https://grupohousing.com/uploads/listing/";
                $imgpri = array_filter(explode("|", $li->images));

                if (isset($imgpri[0]))
                    $imgpri = $imgcover . $imgpri[0];
                else
                    $imgpri = $imgcover . $li->cover_image;

                $li->images = $imgcover . str_replace("|", ",$imgcover", $li->images);
                $condition = 'Nueva';
                if ($li->listingtagstatus == 6) $condition = 'Usada';

                $listingtypestatus = "";
                if ($li->listingtypestatus == "en-venta") {
                    $listingtypestatus = "for_sale";
                } else {
                    $listingtypestatus = "for_rent";
                }

                $description = strip_tags($li->listing_description);

                $lineData = array(
                    $li->id,
                    $li->product_code,
                    'Grupo Housing',
                    // 'in stock',
                    $listingtypestatus,
                    'new',
                    'https://grupohousing.com/propiedad/' . $li->slug,
                    $imgpri,
                    $li->images,
                    ucwords(strtolower($li->listing_title)),
                    ucwords(strtolower($description)),
                    'Cuenca',
                    'Azuay',
                    'Ecuador',
                    '010202',
                    'new_listing',
                    '1',
                    '1',
                    '1',
                    'house',
                    $li->property_price . ' USD',
                    '1',
                    '2020',
                    '536'
                );
                fputcsv($f, $lineData, $delimiter);
            }
            header('Access-Control-Allow-Origin: *');
            fseek($f, 0);
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            fpassthru($f);
        }
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

    public function sendemailinterested(Request $request)
    {
        //return $request['interestname'];
        $similar_properties = array();
        for ($i = 0; $i <= 10; $i++) {
            if ($request['similar' . $i]) {
                $propertie = Listing::where('product_code', $request['similar' . $i])->first();
                array_push($similar_properties, $propertie);
            }
        }

        $propertie = Listing::where('product_code', $request->propertie)->first();
        $firstimage = strtok($propertie->images, '|');
        $message = "<br><strong>Propiedad " . $request->propertie . " - Casa Crédito 🏠</strong>
            <div style='border: 0.5px solid #DC2626; font-size: 15px; padding:3%; border-radius: 25px; margin-top: 2%'>
            <p>
            Estimado/a <b>" . $request->interestname . "</b> reciba un cordial saludo de Casa Crédito. Le hacemos llegar el enlace de la propiedad en la que se encuentra interesado
            </p>
            <div style='margin-top:2%'>
            <div style='text-align:center'>
                <a href='https://casacredito.com/propiedad/$propertie->slug' target='_blank'>
                    <img style='width: 70%; height: 50%' src='https://casacredito.com/uploads/listing/thumb/$firstimage' alt='No se pudo cargar la imagen'>
                </a>
            </div>
            <p style='color: blue; margin-top: 2%; font-size: 12px'>https://casacredito.com/propiedad/$propertie->slug</p>
            <p style='font-size: 17px; font-weight: 500'>$propertie->listing_title</p>
            <p style='font-size: 16px'>$propertie->listing_description</p>
            <i>Casa Crédito, ¡Haciendo sus sueños realidad! <img style='width:30px;height:15px' src='https://casacredito.com/img/logo_actualizado2.png'/></i>
            </div>
            </div>
        ";

        if (count($similar_properties) > 0) {
            $message .= "<h3>Propiedades Similares</h3>";
            foreach ($similar_properties as $s) {
                $_firstimage = strtok($s->images, '|');
                $message .= "
                <div style='border: 0.5px solid #DC2626; font-size:13px;padding:2%;border-radius: 25px;margin-top:2%; display:flex'>
                    <div style='border-radius: 25px'>
                        <a href='https://casacredito.com/propiedad/$s->slug' target='_blank'>
                            <img style='width: 100%; height: 100%; border-radius:25px' src='https://casacredito.com/uploads/listing/300/$_firstimage' alt='No se pudo cargar la imagen'>
                        </a>
                    </div>
                    <div style='padding-left: 20px'>
                        <p style='color: blue; margin-top: 2%; font-size: 12px'>https://casacredito.com/propiedad/$s->slug</p>
                        <p style='font-size: 17px; font-weight: 500'>$s->listing_title</p>
                        <p style='font-size: 16px'>$s->meta_description</p>
                    </div>
                </div>
                ";
            }
        }

        $header = '';
        $header .= 'From: <propiedades@casacredito.com>' . "\r\n";
        $header .= "Reply-To: " . 'info@casacredito.com' . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $enviado = mail($request->interestemail, 'Propiedad ' . $request->propertie . ' - Casa Crédito', $message, $header);

        return redirect()->back()->with('status', $enviado);
    }

    public function getstatebycity($city)
    {
        if (is_numeric($city)) {
            $state = DB::table('info_states')->select('name')->where('id', $city)->first();
        } else {
            $city = DB::table('info_cities')->where('name', 'LIKE', "%$city%")->get();
            $id_state = 0;
            //dd($city_aux);
            if (count($city) > 0) {
                foreach ($city as $c) {
                    if (($c->state_id >= 1022 && $c->state_id <= 1043) || ($c->state_id == 3979 || $c->state_id == 3980)) $id_state = $c->state_id;
                }
            }
            $state = DB::table('info_states')->select('name')->where('id', $id_state)->first();
        }
        //$state = DB::select('select name from info_states where id = ? and country_id = 63', [$city->state_id]);
        return response()->json($state);
    }

    public function blog()
    {

        return view('admin.post.blog');

        // $posts = Post::where('status', 1)->orderBy('created_at', 'desc')->paginate(10);
        // return view('admin.post.indexweb', compact('posts'));
    }

    public function showpost($slug)
    {
        $post = Post::where('slug', $slug)->where('status', 1)->first();
        if ($post) {
            $related_post = Post::where('slug', '!=', $slug)->where('status', 1)->latest()->take(3)->get();
            return view('admin.post.show', compact('post', 'related_post'));
        } else {
            return redirect()->route('web.blog');
        }
    }

    public function getPropertiesByCategory($type)
    {
        $properties = Listing::where('listingtype', $type)->where('status', 1)->where('available', 1)->get();
        return response()->json($properties);
    }

    public function searchHome(Request $request)
    {

        // Olvidar las variables de sesión anteriores
        session()->forget(['operacion', 'tipo', 'text']);

        // Obtener valores del request y asignarlos a variables
        $operacion = $request->has('category') ? $request->category : null;
        $tipo = $request->has('ptype') ? $request->ptype : null;
        $text = $request->has('searchtxt') ? $request->searchtxt : null;

        // Guardar los nuevos valores en la sesión
        session(['operacion' => $operacion]);
        session(['tipo' => $tipo]);
        session(['text' => $text]);

        // Ajustar el valor de 'operacion' si es necesario
        if ($operacion === "alquilar") {
            $operacion = "en-renta";
        }

        // Generar el slug en base al tipo y operación
        if (!empty($tipo)) {
            $propertyType = DB::table('listing_types')->where('id', $tipo)->first();  // Obtener el tipo de propiedad
            $typeName = Str::lower($propertyType->type_title);
            $slug = Str::slug($typeName . " " . $operacion);
        } else {
            $slug = Str::slug('propiedades ' . $operacion);
        }

        // Redirigir a la ruta correspondiente
        if (!empty($text)) {
            return redirect()->route('web.propiedades');
        } else {
            return redirect()->route('web.propiedades', ['slug' => $slug]);
        }

        // session()->forget(['operacion', 'tipo', 'text']);

        // $request->category ? $operacion = $request->category : $operacion = null;
        // $request->ptype ? $tipo = $request->ptype : $tipo = null;
        // $request->searchtxt ? $text = $request->searchtxt : $text = null;

        // session(['operacion' => $operacion]);
        // session(['tipo' => $tipo]);
        // session(['text' => $text]);

        // $operacion == "alquilar" ? $operacion = "en-renta" : null;

        // if($tipo != null || $tipo != ""){
        //     $propertyType = DB::table('listing_types')->where('id', $tipo)->first();  //obtengo el tipo de propiedad
        //     $typeName = Str::lower($propertyType->type_title);
        //     $slug = Str::slug($typeName . " " . $operacion);
        // } else {
        //     $slug = Str::slug('propiedades ' . $operacion);
        // }


        // if($text != null || $text != ""){
        //     return redirect()->route('web.propiedades');
        // } else {
        //     return redirect()->route('web.propiedades', ['slug' => $slug]);
        // }
    }

    public function sendLeadHome(Request $request)
    {

        $message = "<br><strong>Nuevo Lead Grupo Housing</strong>
                    <br> Nombre: " . strip_tags($request->names) . "
                    <br> Telef: " .  strip_tags($request->phone) . "
                    <br> Mensaje: " . strip_tags($request->message) . "
                    <br> Fuente: Website ";

        $header = '';
        $header .= 'From: <leads@grupohousing.com>' . "\r\n";
        $header .= "Reply-To: " . 'info@grupohousing.com' . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        mail('info@casacredito.com', 'Lead Grupo Housing: ' . strip_tags($request->names), $message, $header);
        mail('sebas31051999@gmail.com', 'Lead Grupo Housing: ' . strip_tags($request->names), $message, $header);

        return view('thank');
    }

    public function sendLeadContactSection(Request $request)
    {

        // Construir mensaje en HTML
        $message = "
            <br><strong>Nuevo Lead Grupo Housing</strong>
            <br> Nombre: " . strip_tags($request->nombre) . " " . strip_tags($request->apellido) . "
            <br> Teléfono: " . strip_tags($request->telefono) . "
            <br> Email: " . strip_tags($request->email) . "
            <br> Asunto: " . strip_tags($request->asunto) . "
            <br> Mensaje: " . nl2br(strip_tags($request->mensaje)) . "
            <br> Fuente: Website
        ";

        // Encabezados del correo
        $header  = 'From: <leads@grupohousing.com>' . "\r\n";
        $header .= 'Reply-To: ' . strip_tags($request->email) . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type:text/html;charset=UTF-8\r\n";

        // Por defecto asumimos que es SPAM
        $isValid = false;

        //if (!app()->environment('local', 'testing')) {
        $verify = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        if ($verify->ok()) {
            $payload  = $verify->json();

            Log::info('reCAPTCHA payload', $payload);

            $minScore = (float) config('services.recaptcha.score', 0.5);

            // ✅ Validación más sencilla: solo success + score
            $isValid = ($payload['success'] ?? false)
                && (($payload['score'] ?? 0) >= $minScore);
        }
        //}

        // Enviar a correos distintos según validación
        if ($isValid) {
            // Lead real -> correos oficiales
            mail('info@casacredito.com', 'Lead Grupo Housing: ' . strip_tags($request->nombre) . " " . strip_tags($request->apellido), $message, $header);
            mail('sebas31051999@gmail.com', 'Lead Grupo Housing: ' . strip_tags($request->nombre) . " " . strip_tags($request->apellido), $message, $header);
        } else {
            // Lead sospechoso -> correo de pruebas
            mail('sebas31051999@gmail.com', 'Posible SPAM Grupo Housing: ' . strip_tags($request->nombre) . " " . strip_tags($request->apellido), $message, $header);
        }

        // Retornar vista de agradecimiento 
        return view('thank');
    }
}
