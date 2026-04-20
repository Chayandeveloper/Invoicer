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
}
