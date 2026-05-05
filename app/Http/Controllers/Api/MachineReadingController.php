<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MachineReading;
use Illuminate\Http\Request;

class MachineReadingController extends Controller
{
    public function index()
    {
        return response()->json(
            MachineReading::with('machine')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'sensor_key' => 'required|string',
            'value'      => 'required|numeric',
            'unit'       => 'nullable|string',
            'read_at'    => 'required|date',
        ]);

        $reading = MachineReading::create($request->all());

        return response()->json(
            $reading->load('machine'), 201
        );
    }

    public function show(MachineReading $machineReading)
    {
        return response()->json(
            $machineReading->load('machine')
        );
    }

    public function update(Request $request, MachineReading $machineReading)
    {
        $request->validate([
            'sensor_key' => 'string',
            'value'      => 'numeric',
            'unit'       => 'nullable|string',
            'read_at'    => 'date',
        ]);

        $machineReading->update($request->all());

        return response()->json($machineReading);
    }

    public function destroy(MachineReading $machineReading)
    {
        $machineReading->delete();
        return response()->json(['message' => 'Leitura removida']);
    }
}