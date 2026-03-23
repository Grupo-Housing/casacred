<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $primaryKey = 'comment_id';

    protected $fillable = [
        'comment_id',
        'listing_id',
        'user_id',
        'property_code',
        'type',
        'value', //nuevas variables para que los comentarios sean generales y no solo para notificaciones  
        'comment',
        'property_price_prev',
        'property_price',
        'property_price_min_prev',
        'property_price_min',
        'viewed',
        'created_at',
        'updated_at'
    ];

    public function listing()
    {
        return $this->belongsTo(\App\Models\Listing::class, 'listing_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
