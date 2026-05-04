<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $fillable = [
        'machine_id', 'service_order_id',
        'user_id', 'action', 'defect_type',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}