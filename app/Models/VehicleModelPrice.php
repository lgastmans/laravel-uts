<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleModelPrice extends Model
{
    protected $table = 'vehicle_model_prices';

    protected $fillable = [
        'model',
        'day_rent',
        'rate_per_km',
        'hourly_charge',
        'local_charge',
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
}
