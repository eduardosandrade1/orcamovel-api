<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderPartsVehicle extends Model
{
    protected $with = [
        'images',
        'avarias',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(OrderPartsVehicleImage::class);
    }

    public function avarias(): HasMany
    {
        return $this->hasMany(OrderPartsVehicleAvarias::class);
    }
}
