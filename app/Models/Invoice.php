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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount') + $this->payments()->sum('credit_applied');
    }

    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->total - $this->paid_amount);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
