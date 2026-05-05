<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Lista todos os usuários",
     *     tags={"Usuários"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários com seus papéis",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@exemplo.com"),
     *                 @OA\Property(
     *                     property="roles",
     *                     type="array",
     *                     @OA\Items(@OA\Property(property="name", type="string", example="admin"))
     *                 )
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
            User::with('roles')->get()
        );
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Cria um novo usuário",
     *     tags={"Usuários"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","role"},
     *             @OA\Property(property="name", type="string", example="Maria Santos"),
     *             @OA\Property(property="email", type="string", format="email", example="maria@exemplo.com"),
     *             @OA\Property(property="password", type="string", minLength=6, example="secret123"),
     *             @OA\Property(property="role", type="string", enum={"admin","gerente","tecnico","operador"}, example="tecnico")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=2),
     *             @OA\Property(property="name", type="string", example="Maria Santos"),
     *             @OA\Property(property="email", type="string", example="maria@exemplo.com"),
     *             @OA\Property(
     *                 property="roles",
     *                 type="array",
     *                 @OA\Items(@OA\Property(property="name", type="string", example="tecnico"))
     *             )
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
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,gerente,tecnico,operador',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return response()->json(
            $user->load('roles'), 201
        );
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Retorna um usuário específico",
     *     tags={"Usuários"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", example="joao@exemplo.com"),
     *             @OA\Property(
     *                 property="roles",
     *                 type="array",
     *                 @OA\Items(@OA\Property(property="name", type="string", example="admin"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=403, description="Sem permissão"),
     *     @OA\Response(response=404, description="Usuário não encontrado")
     * )
     */
    public function show(User $user)
    {
        return response()->json(
            $user->load('roles')
        );
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Atualiza um usuário",
     *     tags={"Usuários"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="João Atualizado"),
     *             @OA\Property(property="email", type="string", format="email", example="joao.novo@exemplo.com"),
     *             @OA\Property(property="password", type="string", minLength=6, example="novasenha123"),
     *             @OA\Property(property="role", type="string", enum={"admin","gerente","tecnico","operador"}, example="gerente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="João Atualizado"),
     *             @OA\Property(property="email", type="string", example="joao.novo@exemplo.com"),
     *             @OA\Property(
     *                 property="roles",
     *                 type="array",
     *                 @OA\Items(@OA\Property(property="name", type="string", example="gerente"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=403, description="Sem permissão"),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(response=422, description="Dados inválidos")
     * )
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'string',
            'email'    => 'email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role'     => 'in:admin,gerente,tecnico,operador',
        ]);

        $user->update([
            'name'     => $request->name ?? $user->name,
            'email'    => $request->email ?? $user->email,
            'password' => $request->password
                            ? Hash::make($request->password)
                            : $user->password,
        ]);

        if ($request->role) {
            $user->syncRoles($request->role);
        }

        return response()->json($user->load('roles'));
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Remove um usuário",
     *     tags={"Usuários"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário removido")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=403, description="Sem permissão"),
     *     @OA\Response(response=404, description="Usuário não encontrado")
     * )
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Usuário removido']);
    }
}
