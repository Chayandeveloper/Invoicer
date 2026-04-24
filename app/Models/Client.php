<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasActivityLogs;

class Client extends Model
{
    // use HasActivityLogs;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function getTotalInvoicedAttribute()
    {
        return $this->invoices()->sum('total');
    }

    public function getPendingBalanceAttribute()
    {
        // Simple calculation: Total - Paid
        return $this->invoices()->get()->sum->remaining_balance;
    }
}
