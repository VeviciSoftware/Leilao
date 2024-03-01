<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Leilao;

class ApiLeilaoController extends Controller
{

    public function healthCheck()
    {
        return response()->json([
            'status' => 'ok',
            'leilao' => 'Leilão API',
            'versao' => '1.0.0'
        ]);
    }
    
    public function index()
    {

    }

    public function create(Request $request)
    {
        return response()->json([
            'mensagem' => 'Leilão criado com sucesso'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
        ]);

        return Leilao::create($request->all());
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'mensagem' => 'Leilão atualizado com sucesso'
        ]);
    }

    public function destroy($id)
    {
        $leilao = Leilao::findOrFail($id);
        $leilao->delete();
    
        return response()->json([
            'mensagem' => 'Leilão deletado com sucesso'
        ]);
    }
}
