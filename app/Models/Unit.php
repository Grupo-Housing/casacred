<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'listing_id',
        'listing_type_id',
        'name',
        'unit_number',
        'floor',
        'area_m2',
        'bedrooms',
        'bathrooms',
        'price',
        'min_price',
        'rent_price',
        'status',
        'description',
    ];

    /**
     * Relación: Una unidad pertenece a un listing (proyecto)
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
