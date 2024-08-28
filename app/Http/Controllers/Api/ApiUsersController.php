<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\IUserRepository;
use Illuminate\Http\Request;

class ApiUsersController extends Controller
{
    public function __construct(private IUserRepository $repository)
    {

    }

    
    public function index() {

        if (User::all()->isEmpty()) {
            return response()->json(['message' => 'Nenhum usuário encontrado'], 404);
        }

        return User::all();
    }

    public function store(UserRequest $request) {
        $user = $this->repository->add($request);
        return response()->json(['mensagem' => 'Usuário criado com sucesso!', 'user' => $user], 201);
    }

    public function show($id) {
        try {
            $user = User::findOrFail($id);
            return response()->json(['mensagem' => 'Usuário encontrado!', 'user' => $user], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['mensagem' => 'Usuário não encontrado!'], 404);
        }
    }

    public function update(UserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->all();
    
            // Verifica se o campo 'password' está presente e não está vazio
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                // Remove o campo 'password' para não sobrescrever com um valor vazio
                unset($data['password']);
            }
    
            $user->update($data);
            return response()->json(['mensagem' => 'Usuário atualizado com sucesso!', 'user' => $user], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['mensagem' => 'Usuário não encontrado!'], 404);
        }
    }

    public function destroy($id) {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['mensagem' => 'Usuário deletado com sucesso!'], 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['mensagem' => 'Usuário não encontrado!'], 404);
        }
    }

}
