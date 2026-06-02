<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $table = 'vehicles';

    protected $fillable = [
        'owner_id',
        'vehicle_model_price_id',
        'registration_number',
        'insurance_validity',
        'permit_validity',
    ];

    protected $casts = [
        'insurance_validity' => 'date',
        'permit_validity'    => 'date',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function vehicleModelPrice(): BelongsTo
    {
        return $this->belongsTo(VehicleModelPrice::class);
    }
}
