<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreConfig extends Model
{
    protected $fillable = ['store_name', 'whatsapp_number', 'address', 'description', 'hero_images', 'logo'];

    protected $casts = [
        'hero_images' => 'array',
    ];
}
