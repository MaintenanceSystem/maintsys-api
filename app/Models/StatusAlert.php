<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusAlert extends Model
{
    protected $fillable = [
        'machine_id', 'previous_status', 'new_status',
        'message', 'is_read', 'triggered_at',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}