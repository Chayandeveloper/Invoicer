<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasActivityLogs;

class Invoice extends Model
{
    // use HasActivityLogs;
    protected $guarded = [];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_profile');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
