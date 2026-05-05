<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/maintenance-logs",
     *     summary="Lista todos os logs de manutenção",
     *     tags={"Logs de Manutenção"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de logs de manutenção",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="machine_id", type="integer", example=1),
     *                 @OA\Property(property="service_order_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=2),
     *                 @OA\Property(property="action", type="string", example="Troca de óleo"),
     *                 @OA\Property(property="defect_type", type="string", example="Desgaste"),
     *                 @OA\Property(property="machine", type="object"),
     *                 @OA\Property(property="serviceOrder", type="object"),
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=403, description="Sem permissão")
     * )
     */
    public function index()
    {
        return response()->json(
            MaintenanceLog::with(['machine', 'serviceOrder', 'user'])->get()
        );
    }

    /**
     * @OA\Post(
     *     path="/maintenance-logs",
     *     summary="Registra um novo log de manutenção",
     *     tags={"Logs de Manutenção"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"machine_id","action"},
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="service_order_id", type="integer", example=1),
     *             @OA\Property(property="action", type="string", example="Substituição de correia"),
     *             @OA\Property(property="defect_type", type="string", example="Quebra mecânica")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Log de manutenção criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="action", type="string", example="Substituição de correia"),
     *             @OA\Property(property="user_id", type="integer", example=2),
     *             @OA\Property(property="machine", type="object"),
     *             @OA\Property(property="serviceOrder", type="object"),
     *             @OA\Property(property="user", type="object")
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

    /**
     * @OA\Get(
     *     path="/maintenance-logs/{id}",
     *     summary="Retorna um log de manutenção específico",
     *     tags={"Logs de Manutenção"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do log de manutenção",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do log de manutenção",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="action", type="string", example="Troca de óleo"),
     *             @OA\Property(property="defect_type", type="string", example="Desgaste"),
     *             @OA\Property(property="machine", type="object"),
     *             @OA\Property(property="serviceOrder", type="object"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Log não encontrado")
     * )
     */
    public function show(MaintenanceLog $maintenanceLog)
    {
        return response()->json(
            $maintenanceLog->load(['machine', 'serviceOrder', 'user'])
        );
    }

    /**
     * @OA\Put(
     *     path="/maintenance-logs/{id}",
     *     summary="Atualiza um log de manutenção",
     *     tags={"Logs de Manutenção"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do log de manutenção",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="action", type="string", example="Lubrificação revisada"),
     *             @OA\Property(property="defect_type", type="string", example="Folga")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Log de manutenção atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="action", type="string", example="Lubrificação revisada"),
     *             @OA\Property(property="defect_type", type="string", example="Folga")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Log não encontrado"),
     *     @OA\Response(response=422, description="Dados inválidos")
     * )
     */
    public function update(Request $request, MaintenanceLog $maintenanceLog)
    {
        $request->validate([
            'action'      => 'string',
            'defect_type' => 'nullable|string',
        ]);

        $maintenanceLog->update($request->all());

        return response()->json($maintenanceLog);
    }

    /**
     * @OA\Delete(
     *     path="/maintenance-logs/{id}",
     *     summary="Remove um log de manutenção",
     *     tags={"Logs de Manutenção"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do log de manutenção",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Log removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Log removido")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Log não encontrado")
     * )
     */
    public function destroy(MaintenanceLog $maintenanceLog)
    {
        $maintenanceLog->delete();
        return response()->json(['message' => 'Log removido']);
    }
}
