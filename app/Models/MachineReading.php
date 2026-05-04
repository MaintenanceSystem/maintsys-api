<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineReading extends Model
{
    protected $fillable = [
        'machine_id', 'sensor_key',
        'value', 'unit', 'read_at',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}