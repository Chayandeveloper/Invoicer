<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasActivityLogs;

class Payment extends Model
{
    // use HasActivityLogs;
    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
