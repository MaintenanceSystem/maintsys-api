<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StatusAlert;
use Illuminate\Http\Request;

class StatusAlertController extends Controller
{
    public function index()
    {
        return response()->json(
            StatusAlert::with('machine')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_id'      => 'required|exists:machines,id',
            'previous_status' => 'required|string',
            'new_status'      => 'required|string',
            'message'         => 'nullable|string',
            'triggered_at'    => 'required|date',
        ]);

        $alert = StatusAlert::create($request->all());

        return response()->json(
            $alert->load('machine'), 201
        );
    }

    public function show(StatusAlert $statusAlert)
    {
        return response()->json(
            $statusAlert->load('machine')
        );
    }

    public function update(Request $request, StatusAlert $statusAlert)
    {
        $request->validate([
            'message' => 'nullable|string',
            'is_read' => 'boolean',
        ]);

        $statusAlert->update($request->all());

        return response()->json($statusAlert);
    }

    public function destroy(StatusAlert $statusAlert)
    {
        $statusAlert->delete();
        return response()->json(['message' => 'Alerta removido']);
    }
}