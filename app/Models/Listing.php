<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'listing_title',
        'product_code',
        'slug',
        'images',
        'video',
        'bedroom',
        'bathroom',
        'garage',
        'meta_description',
        'keywords',
        'Front',
        'Fund',
        'land_area',
        'construction_area',
        'property_price_min',
        'property_price',
        'customized_price',
        'listing_description',
        'listing_type',
        'num_factura',
        'address',
        'state',
        'city',
        'listingtype',
        'sector',
        'listingcharacteristic',
        'listinglistservices',
        'listingtypestatus',
        'listingtagstatus',
        'listinggeneralcharacteristics',
        'listingenvironments',
        'listyears',
        'lat',
        'lng',
        'cardinal_zone',
        'available',
        'status',
        'user_id',
        'heading_details',
        'owner_name',
        'owner_email',
        'owner_address',
        'identification',
        'phone_number',
        'aval',
        'locked',
        'vip',
        'planing_license',
        'mortgaged',
        'entity_mortgaged',
        'mount_mortgaged',
        'cadastral_key',
        'aliquot',
        'observations_type_property',
        'cavity_error',
        'warranty',
        'niv_constr',
        'num_pisos',
        'pisos_constr',
        'land_appraisal',
        'construction_appraisal',
        'delete_at',
        'posted_on_facebook',
        'date_posted_facebook',
        'contact_at',
        'no_answer_at',
        'is_dual_operation'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFilterByState($query, $state)
    {
        if ($state) {
            return $query->where('state', 'LIKE', $state);
        }
    }

    public function scopeFilterByCity($query, $city)
    {
        if ($city) {
            return $query->where('city', 'LIKE', "%$city%");
        }
    }

    public function scopeFilterByListingTitle($query, $ubication)
    {
        if ($ubication) {
            return $query->where('listing_title', 'LIKE', "%$ubication%");
        }
    }

    public function scopeFilterByCardinalZone($query, $cardinalZone)
    {
        if ($cardinalZone) {
            return $query->where('cardinal_zone', $cardinalZone);
        }
    }

    /**
     * Accessor para mostrar el label con ícono.
     */
    public function getCardinalZoneLabelAttribute(): ?string
    {
        if (!$this->cardinal_zone) return null;

        $labels = [
            'norte'  => '⬆ Norte',
            'sur'    => '⬇ Sur',
            'este'   => '➡ Este',
            'oeste'  => '⬅ Oeste',
            'centro' => '⊙ Centro',
        ];

        return $labels[$this->cardinal_zone] ?? $this->cardinal_zone;
    }

    public function resolveImageUrl(string $image, string $size = 'full'): string
    {
        $s3Url = 'https://grupohousing.s3.amazonaws.com/listings/' . $image;

        // Cachear el resultado por 24 horas para no hacer peticiones repetidas
        $existsInS3 = Cache::remember('s3_exists_' . $image, 86400, function () use ($s3Url) {
            try {
                $headers = get_headers($s3Url, 1);
                return $headers && strpos($headers[0], '200') !== false;
            } catch (\Exception $e) {
                return false;
            }
        });

        if ($existsInS3) {
            return $s3Url;
        }

        switch ($size) {
            case 'thumb':
                return url('uploads/listing/thumb/' . $image);
            case 'thumb_600':
                return url('uploads/listing/thumb/600/' . $image);
            default:
                return url('uploads/listing/' . $image);
        }
    }
}
