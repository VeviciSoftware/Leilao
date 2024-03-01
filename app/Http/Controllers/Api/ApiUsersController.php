<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ApiUsersController extends Controller
{
    public function index() {

        if (User::all()->isEmpty()) {
            return response()->json(['message' => 'Nenhum usuário encontrado'], 404);
        }

        return User::all();
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        return User::create($request->all());
    }

    public function show($id) {
        return User::findOrFail($id);
    }

}
