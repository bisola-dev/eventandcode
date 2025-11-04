<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'qr_code', 'event_id', 'checked_in', 'checked_in_at'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
