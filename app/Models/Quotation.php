<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasActivityLogs;

class Quotation extends Model
{
    // use HasActivityLogs;
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
