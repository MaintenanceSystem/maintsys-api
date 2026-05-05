<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StatusAlert;
use Illuminate\Http\Request;

class StatusAlertController extends Controller
{
    /**
     * @OA\Get(
     *     path="/status-alerts",
     *     summary="Lista todos os alertas de status",
     *     tags={"Alertas de Status"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de alertas de status",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="machine_id", type="integer", example=1),
     *                 @OA\Property(property="previous_status", type="string", example="active"),
     *                 @OA\Property(property="new_status", type="string", example="maintenance"),
     *                 @OA\Property(property="message", type="string", example="Máquina entrou em manutenção"),
     *                 @OA\Property(property="triggered_at", type="string", format="date-time", example="2024-01-10T09:00:00Z"),
     *                 @OA\Property(property="is_read", type="boolean", example=false),
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
            StatusAlert::with('machine')->get()
        );
    }

    /**
     * @OA\Post(
     *     path="/status-alerts",
     *     summary="Cria um novo alerta de status",
     *     tags={"Alertas de Status"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"machine_id","previous_status","new_status","triggered_at"},
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="previous_status", type="string", example="active"),
     *             @OA\Property(property="new_status", type="string", example="inactive"),
     *             @OA\Property(property="message", type="string", example="Máquina desligada por falta de energia"),
     *             @OA\Property(property="triggered_at", type="string", format="date-time", example="2024-01-10T09:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Alerta criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="previous_status", type="string", example="active"),
     *             @OA\Property(property="new_status", type="string", example="inactive"),
     *             @OA\Property(property="is_read", type="boolean", example=false),
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

    /**
     * @OA\Get(
     *     path="/status-alerts/{id}",
     *     summary="Retorna um alerta de status específico",
     *     tags={"Alertas de Status"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do alerta",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do alerta",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="previous_status", type="string", example="active"),
     *             @OA\Property(property="new_status", type="string", example="maintenance"),
     *             @OA\Property(property="message", type="string", example="Máquina entrou em manutenção"),
     *             @OA\Property(property="triggered_at", type="string", example="2024-01-10T09:00:00Z"),
     *             @OA\Property(property="is_read", type="boolean", example=false),
     *             @OA\Property(property="machine", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Alerta não encontrado")
     * )
     */
    public function show(StatusAlert $statusAlert)
    {
        return response()->json(
            $statusAlert->load('machine')
        );
    }

    /**
     * @OA\Put(
     *     path="/status-alerts/{id}",
     *     summary="Atualiza um alerta de status",
     *     tags={"Alertas de Status"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do alerta",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Alerta revisado pela equipe"),
     *             @OA\Property(property="is_read", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Alerta atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="Alerta revisado pela equipe"),
     *             @OA\Property(property="is_read", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Alerta não encontrado"),
     *     @OA\Response(response=422, description="Dados inválidos")
     * )
     */
    public function update(Request $request, StatusAlert $statusAlert)
    {
        $request->validate([
            'message' => 'nullable|string',
            'is_read' => 'boolean',
        ]);

        $statusAlert->update($request->all());

        return response()->json($statusAlert);
    }

    /**
     * @OA\Delete(
     *     path="/status-alerts/{id}",
     *     summary="Remove um alerta de status",
     *     tags={"Alertas de Status"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do alerta",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Alerta removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Alerta removido")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Alerta não encontrado")
     * )
     */
    public function destroy(StatusAlert $statusAlert)
    {
        $statusAlert->delete();
        return response()->json(['message' => 'Alerta removido']);
    }
}
