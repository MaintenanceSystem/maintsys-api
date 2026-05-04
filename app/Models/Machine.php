<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'serial_number', 'name', 'model',
        'location', 'status', 'installed_at', 'last_reading_at',
    ];

    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function readings()
    {
        return $this->hasMany(MachineReading::class);
    }

    public function alerts()
    {
        return $this->hasMany(StatusAlert::class);
    }
}