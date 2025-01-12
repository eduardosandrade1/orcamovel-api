<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPartsVehicleImage extends Model
{
    protected function path(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => asset('storage/' . ltrim($value, '/')),
        );
    }

    public function typeParts(): BelongsTo
    {
        return $this->belongsTo(OrderPartsVehicle::class);
    }
}
