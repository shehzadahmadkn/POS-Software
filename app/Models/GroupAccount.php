<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupAccount extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'customer_id', 'vendor_id'];

    public function customer()
    {
        return $this->belongsTo(Account::class, 'customer_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Account::class, 'vendor_id');
    }
}
