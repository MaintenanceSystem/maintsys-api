<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MachineReading;
use Illuminate\Http\Request;

class MachineReadingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/machine-readings",
     *     summary="Lista todas as leituras de sensores",
     *     tags={"Leituras de Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de leituras de sensores",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="machine_id", type="integer", example=1),
     *                 @OA\Property(property="sensor_key", type="string", example="temperatura"),
     *                 @OA\Property(property="value", type="number", format="float", example=75.5),
     *                 @OA\Property(property="unit", type="string", example="°C"),
     *                 @OA\Property(property="read_at", type="string", format="date-time", example="2024-01-10T10:30:00Z"),
     *                 @OA\Property(property="machine", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index()
    {
        return response()->json(
            MachineReading::with('machine')->get()
        );
    }

    /**
     * @OA\Post(
     *     path="/machine-readings",
     *     summary="Registra uma nova leitura de sensor",
     *     tags={"Leituras de Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"machine_id","sensor_key","value","read_at"},
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="sensor_key", type="string", example="pressao"),
     *             @OA\Property(property="value", type="number", format="float", example=3.2),
     *             @OA\Property(property="unit", type="string", example="bar"),
     *             @OA\Property(property="read_at", type="string", format="date-time", example="2024-01-10T10:30:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Leitura registrada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="sensor_key", type="string", example="pressao"),
     *             @OA\Property(property="value", type="number", example=3.2),
     *             @OA\Property(property="unit", type="string", example="bar"),
     *             @OA\Property(property="read_at", type="string", example="2024-01-10T10:30:00Z"),
     *             @OA\Property(property="machine", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=422, description="Dados inválidos")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/machine-readings/{id}",
     *     summary="Retorna uma leitura de sensor específica",
     *     tags={"Leituras de Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da leitura",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados da leitura",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="sensor_key", type="string", example="temperatura"),
     *             @OA\Property(property="value", type="number", example=75.5),
     *             @OA\Property(property="unit", type="string", example="°C"),
     *             @OA\Property(property="read_at", type="string", example="2024-01-10T10:30:00Z"),
     *             @OA\Property(property="machine", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Leitura não encontrada")
     * )
     */
    public function show(MachineReading $machineReading)
    {
        return response()->json(
            $machineReading->load('machine')
        );
    }

    /**
     * @OA\Put(
     *     path="/machine-readings/{id}",
     *     summary="Atualiza uma leitura de sensor",
     *     tags={"Leituras de Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da leitura",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="sensor_key", type="string", example="temperatura"),
     *             @OA\Property(property="value", type="number", format="float", example=80.0),
     *             @OA\Property(property="unit", type="string", example="°C"),
     *             @OA\Property(property="read_at", type="string", format="date-time", example="2024-01-10T11:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Leitura atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="sensor_key", type="string", example="temperatura"),
     *             @OA\Property(property="value", type="number", example=80.0),
     *             @OA\Property(property="unit", type="string", example="°C")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Leitura não encontrada"),
     *     @OA\Response(response=422, description="Dados inválidos")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/machine-readings/{id}",
     *     summary="Remove uma leitura de sensor",
     *     tags={"Leituras de Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da leitura",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Leitura removida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Leitura removida")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Leitura não encontrada")
     * )
     */
    public function destroy(MachineReading $machineReading)
    {
        $machineReading->delete();
        return response()->json(['message' => 'Leitura removida']);
    }
}
