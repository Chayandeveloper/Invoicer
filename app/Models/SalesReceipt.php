<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReceipt extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
