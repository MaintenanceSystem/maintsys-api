<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index()
    {
        return response()->json(Machine::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string|unique:machines',
            'name'          => 'required|string',
            'model'         => 'nullable|string',
            'location'      => 'nullable|string',
            'status'        => 'in:active,inactive,maintenance',
            'installed_at'  => 'nullable|date',
        ]);

        $machine = Machine::create($request->all());

        return response()->json($machine, 201);
    }

    public function show(Machine $machine)
    {
        return response()->json($machine);
    }

    public function update(Request $request, Machine $machine)
    {
        $request->validate([
            'serial_number' => 'string|unique:machines,serial_number,' . $machine->id,
            'name'          => 'string',
            'model'         => 'nullable|string',
            'location'      => 'nullable|string',
            'status'        => 'in:active,inactive,maintenance',
            'installed_at'  => 'nullable|date',
        ]);

        $machine->update($request->all());

        return response()->json($machine);
    }

    public function destroy(Machine $machine)
    {
        $machine->delete();

        return response()->json(['message' => 'Máquina removida']);
    }
}