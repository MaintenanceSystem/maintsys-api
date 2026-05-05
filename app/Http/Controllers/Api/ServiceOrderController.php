<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class ServiceOrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/service-orders",
     *     summary="Lista todas as ordens de serviço",
     *     tags={"Ordens de Serviço"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ordens de serviço",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="machine_id", type="integer", example=1),
     *                 @OA\Property(property="technician_id", type="integer", example=2),
     *                 @OA\Property(property="created_by", type="integer", example=1),
     *                 @OA\Property(property="type", type="string", enum={"preventive","corrective"}, example="preventive"),
     *                 @OA\Property(property="status", type="string", enum={"open","in_progress","completed","cancelled"}, example="open"),
     *                 @OA\Property(property="started_at", type="string", format="date-time", example="2024-01-10T08:00:00Z"),
     *                 @OA\Property(property="completed_at", type="string", format="date-time", example=null),
     *                 @OA\Property(property="machine", type="object"),
     *                 @OA\Property(property="technician", type="object"),
     *                 @OA\Property(property="creator", type="object")
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
            ServiceOrder::with(['machine', 'technician', 'creator'])->get()
        );
    }

    /**
     * @OA\Post(
     *     path="/service-orders",
     *     summary="Cria uma nova ordem de serviço",
     *     tags={"Ordens de Serviço"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"machine_id","type"},
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="technician_id", type="integer", example=2),
     *             @OA\Property(property="type", type="string", enum={"preventive","corrective"}, example="corrective"),
     *             @OA\Property(property="status", type="string", enum={"open","in_progress","completed","cancelled"}, example="open"),
     *             @OA\Property(property="started_at", type="string", format="date-time", example="2024-01-10T08:00:00Z"),
     *             @OA\Property(property="completed_at", type="string", format="date-time", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ordem de serviço criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="type", type="string", example="corrective"),
     *             @OA\Property(property="status", type="string", example="open"),
     *             @OA\Property(property="created_by", type="integer", example=1),
     *             @OA\Property(property="machine", type="object"),
     *             @OA\Property(property="technician", type="object"),
     *             @OA\Property(property="creator", type="object")
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

    /**
     * @OA\Get(
     *     path="/service-orders/{id}",
     *     summary="Retorna uma ordem de serviço específica",
     *     tags={"Ordens de Serviço"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da ordem de serviço",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados da ordem de serviço",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="type", type="string", example="preventive"),
     *             @OA\Property(property="status", type="string", example="in_progress"),
     *             @OA\Property(property="machine", type="object"),
     *             @OA\Property(property="technician", type="object"),
     *             @OA\Property(property="creator", type="object"),
     *             @OA\Property(property="logs", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Ordem de serviço não encontrada")
     * )
     */
    public function show(ServiceOrder $serviceOrder)
    {
        return response()->json(
            $serviceOrder->load(['machine', 'technician', 'creator', 'logs'])
        );
    }

    /**
     * @OA\Put(
     *     path="/service-orders/{id}",
     *     summary="Atualiza uma ordem de serviço",
     *     tags={"Ordens de Serviço"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da ordem de serviço",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="machine_id", type="integer", example=1),
     *             @OA\Property(property="technician_id", type="integer", example=3),
     *             @OA\Property(property="type", type="string", enum={"preventive","corrective"}, example="preventive"),
     *             @OA\Property(property="status", type="string", enum={"open","in_progress","completed","cancelled"}, example="completed"),
     *             @OA\Property(property="started_at", type="string", format="date-time", example="2024-01-10T08:00:00Z"),
     *             @OA\Property(property="completed_at", type="string", format="date-time", example="2024-01-10T17:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ordem de serviço atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="completed"),
     *             @OA\Property(property="machine", type="object"),
     *             @OA\Property(property="technician", type="object"),
     *             @OA\Property(property="creator", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Ordem de serviço não encontrada"),
     *     @OA\Response(response=422, description="Dados inválidos")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/service-orders/{id}",
     *     summary="Remove uma ordem de serviço",
     *     tags={"Ordens de Serviço"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da ordem de serviço",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ordem de serviço removida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ordem de serviço removida")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Ordem de serviço não encontrada")
     * )
     */
    public function destroy(ServiceOrder $serviceOrder)
    {
        $serviceOrder->delete();

        return response()->json(['message' => 'Ordem de serviço removida']);
    }
}
