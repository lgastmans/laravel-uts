<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'gst_number',
        'address',
        'zoho_contact_id',
    ];

    // A customer has many bills
    public function bills()
    {
        /**
         * the Bill model was created first, and was not planning to create a customer model 
         * and therefore the foreign_key and local_key are different and need to be specified
         */
        return $this->hasMany(Bill::class, 'zoho_customer_id', 'zoho_contact_id');
    }

}
