<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'bill_id',
        'bill_number',
        'bill_date',
        'customer',
        'from_place',
        'to_place',
        'dep_date',
        'dep_time',
        'arr_date',
        'arr_time',
        'vehicle_reg_no',
        'amount',
        'car',
        'driver_id',
        'zoho_invoice_id',
        'synced_at',
    ];

    
}
