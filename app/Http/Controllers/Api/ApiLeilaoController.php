<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeilaoRequest;
use Illuminate\Http\Request;
use App\Models\Leilao;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

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

    public function store(LeilaoRequest $request)
    {
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
