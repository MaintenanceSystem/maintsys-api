<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class ServiceOrderController extends Controller
{
    public function index()
    {
        return response()->json(
            ServiceOrder::with(['machine', 'technician', 'creator'])->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_id'     => 'required|exists:machines,id',
            'technician_id'  => 'nullable|exists:users,id',
            'type'           => 'required|in:preventive,corrective',
            'status'         => 'in:open,in_progress,completed,cancelled',
            'started_at'     => 'nullable|date',
            'completed_at'   => 'nullable|date',
        ]);

        $order = ServiceOrder::create([
            ...$request->all(),
            'created_by' => auth()->id(),
        ]);

        return response()->json(
            $order->load(['machine', 'technician', 'creator']), 201
        );
    }

    public function show(ServiceOrder $serviceOrder)
    {
        return response()->json(
            $serviceOrder->load(['machine', 'technician', 'creator', 'logs'])
        );
    }

    public function update(Request $request, ServiceOrder $serviceOrder)
    {
        $request->validate([
            'machine_id'    => 'exists:machines,id',
            'technician_id' => 'nullable|exists:users,id',
            'type'          => 'in:preventive,corrective',
            'status'        => 'in:open,in_progress,completed,cancelled',
            'started_at'    => 'nullable|date',
            'completed_at'  => 'nullable|date',
        ]);

        $serviceOrder->update($request->all());

        return response()->json(
            $serviceOrder->load(['machine', 'technician', 'creator'])
        );
    }

    public function destroy(ServiceOrder $serviceOrder)
    {
        $serviceOrder->delete();

        return response()->json(['message' => 'Ordem de serviço removida']);
    }
}