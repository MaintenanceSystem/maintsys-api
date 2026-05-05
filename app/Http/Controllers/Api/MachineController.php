<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    /**
     * @OA\Get(
     *     path="/machines",
     *     summary="Lista todas as máquinas",
     *     tags={"Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de máquinas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="serial_number", type="string", example="SN-001"),
     *                 @OA\Property(property="name", type="string", example="Torno CNC"),
     *                 @OA\Property(property="model", type="string", example="CNC-500"),
     *                 @OA\Property(property="location", type="string", example="Galpão A"),
     *                 @OA\Property(property="status", type="string", enum={"active","inactive","maintenance"}, example="active"),
     *                 @OA\Property(property="installed_at", type="string", format="date", example="2023-01-15")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=403, description="Sem permissão")
     * )
     */
    public function index()
    {
        return response()->json(Machine::all());
    }

    /**
     * @OA\Post(
     *     path="/machines",
     *     summary="Cadastra uma nova máquina",
     *     tags={"Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"serial_number","name"},
     *             @OA\Property(property="serial_number", type="string", example="SN-002"),
     *             @OA\Property(property="name", type="string", example="Fresadora"),
     *             @OA\Property(property="model", type="string", example="FR-300"),
     *             @OA\Property(property="location", type="string", example="Galpão B"),
     *             @OA\Property(property="status", type="string", enum={"active","inactive","maintenance"}, example="active"),
     *             @OA\Property(property="installed_at", type="string", format="date", example="2024-03-10")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Máquina criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=2),
     *             @OA\Property(property="serial_number", type="string", example="SN-002"),
     *             @OA\Property(property="name", type="string", example="Fresadora"),
     *             @OA\Property(property="model", type="string", example="FR-300"),
     *             @OA\Property(property="location", type="string", example="Galpão B"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="installed_at", type="string", example="2024-03-10")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=403, description="Sem permissão"),
     *     @OA\Response(response=422, description="Dados inválidos")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/machines/{id}",
     *     summary="Retorna uma máquina específica",
     *     tags={"Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da máquina",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados da máquina",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="serial_number", type="string", example="SN-001"),
     *             @OA\Property(property="name", type="string", example="Torno CNC"),
     *             @OA\Property(property="model", type="string", example="CNC-500"),
     *             @OA\Property(property="location", type="string", example="Galpão A"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="installed_at", type="string", example="2023-01-15")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=403, description="Sem permissão"),
     *     @OA\Response(response=404, description="Máquina não encontrada")
     * )
     */
    public function show(Machine $machine)
    {
        return response()->json($machine);
    }

    /**
     * @OA\Put(
     *     path="/machines/{id}",
     *     summary="Atualiza uma máquina",
     *     tags={"Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da máquina",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="serial_number", type="string", example="SN-001-A"),
     *             @OA\Property(property="name", type="string", example="Torno CNC Atualizado"),
     *             @OA\Property(property="model", type="string", example="CNC-600"),
     *             @OA\Property(property="location", type="string", example="Galpão C"),
     *             @OA\Property(property="status", type="string", enum={"active","inactive","maintenance"}, example="maintenance"),
     *             @OA\Property(property="installed_at", type="string", format="date", example="2023-01-15")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Máquina atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="serial_number", type="string", example="SN-001-A"),
     *             @OA\Property(property="name", type="string", example="Torno CNC Atualizado"),
     *             @OA\Property(property="status", type="string", example="maintenance")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=403, description="Sem permissão"),
     *     @OA\Response(response=404, description="Máquina não encontrada"),
     *     @OA\Response(response=422, description="Dados inválidos")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/machines/{id}",
     *     summary="Remove uma máquina",
     *     tags={"Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da máquina",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Máquina removida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Máquina removida")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=403, description="Sem permissão"),
     *     @OA\Response(response=404, description="Máquina não encontrada")
     * )
     */
    public function destroy(Machine $machine)
    {
        $machine->delete();

        return response()->json(['message' => 'Máquina removida']);
    }
}
