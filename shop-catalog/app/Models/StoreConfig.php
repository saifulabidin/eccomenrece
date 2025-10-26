<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreConfig extends Model
{
    protected $fillable = [
        'store_name', 
        'whatsapp_number', 
        'address', 
        'description', 
        'hero_images', 
        'logo',
        'email',
        'phone',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'favicon',
        'pwa_icon_192',
        'pwa_icon_512',
    ];

    protected $casts = [
        'hero_images' => 'array',
    ];
}
