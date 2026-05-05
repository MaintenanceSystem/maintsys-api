<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(
            User::with('roles')->get()
        );
    }

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

    public function show(User $user)
    {
        return response()->json(
            $user->load('roles')
        );
    }

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

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Usuário removido']);
    }
}