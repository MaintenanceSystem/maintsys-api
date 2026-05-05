<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    public function index()
    {
        return response()->json(
            MaintenanceLog::with(['machine', 'serviceOrder', 'user'])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_id'       => 'required|exists:machines,id',
            'service_order_id' => 'nullable|exists:service_orders,id',
            'action'           => 'required|string',
            'defect_type'      => 'nullable|string',
        ]);

        $log = MaintenanceLog::create([
            ...$request->all(),
            'user_id' => auth()->id(),
        ]);

        return response()->json(
            $log->load(['machine', 'serviceOrder', 'user']), 201
        );
    }

    public function show(MaintenanceLog $maintenanceLog)
    {
        return response()->json(
            $maintenanceLog->load(['machine', 'serviceOrder', 'user'])
        );
    }

    public function update(Request $request, MaintenanceLog $maintenanceLog)
    {
        $request->validate([
            'action'      => 'string',
            'defect_type' => 'nullable|string',
        ]);

        $maintenanceLog->update($request->all());

        return response()->json($maintenanceLog);
    }

    public function destroy(MaintenanceLog $maintenanceLog)
    {
        $maintenanceLog->delete();
        return response()->json(['message' => 'Log removido']);
    }
}