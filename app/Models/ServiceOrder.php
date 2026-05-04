<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    protected $fillable = [
        'machine_id', 'technician_id', 'created_by',
        'type', 'status', 'started_at', 'completed_at',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }
}