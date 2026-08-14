<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

    public function scopeCustomers($query)
    {
        return $query->where('type', 'customer');
    }

    public function scopeVendors($query)
    {
        return $query->where('type', 'vendor');
    }

    public function scopeBusiness($query)
    {
        return $query->where('type', 'business');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'customer_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'vendor_id');
    }
}
