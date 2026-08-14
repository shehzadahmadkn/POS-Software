<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReceive extends Model
{
    protected $guarded = [];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function from()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }
}
