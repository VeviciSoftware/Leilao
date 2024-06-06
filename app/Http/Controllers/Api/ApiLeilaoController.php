<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeilaoRequest;
use App\Repositories\ILeilaoRepository;
use App\Services\Leilao\Encerrador;
use Illuminate\Http\Request;
use App\Models\Leilao;

class ApiLeilaoController extends Controller
{

    public function __construct(private ILeilaoRepository $repository)
    {
        
    }

    public function healthCheck()
    {
        return response()->json([
            'status' => 'ok',
            'leilao' => 'Leilão API',
            'versao' => '1.0.0'
        ]);
    }
    
    public function index(Request $request)
    {
        $status = $request->query('status');
    
        if ($status) {
            $leiloes = Leilao::where('status', strtoupper($status))->get();
            if ($leiloes->isEmpty()) {
                return response()->json(['message' => 'Nenhum leilão encontrado com o status ' . $status], 404);
            }
            return $leiloes;
        }
    
        $leiloes = Leilao::all();
        if ($leiloes->isEmpty()) {
            return response()->json(['message' => 'Nenhum leilão encontrado'], 200);
        }
    
        return $leiloes;
    }

    public function store(LeilaoRequest $request)
    {
        $leilao = $this->repository->add($request);

        return response()->json($leilao, 201);
    }

    public function update(Request $request, $id)
    {
        $leilao = Leilao::findOrFail($id);
        $leilao->update($request->all());

        return response()->json([
            'mensagem' => 'Leilão atualizado com sucesso',
            'leilao' => $leilao
        ]);
    }

    public function destroy($id)
    {
        $leilao = Leilao::find($id);
        $leilao->delete();
    
        return response()->json([
            'mensagem' => 'Leilão deletado com sucesso', 204
        ]);
    }

    public function show($id)
    {
        try {
            $leilao = Leilao::findOrFail($id);
            return response()->json(['mensagem' => 'Leilão encontrado!', 'leilao' => $leilao], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['mensagem' => 'Leilão não encontrado!'], 404);
        }
    }

    public function showLeilaoELances($id)
    {
        $leilaoObj = Leilao::findOrFail($id);
    
        $leilao = [
            'id' => $leilaoObj->id,
            'nome' => $leilaoObj->nome,
            'descricao' => $leilaoObj->descricao,
            'valor_inicial' => $leilaoObj->valor_inicial,
            'data_inicio' => $leilaoObj->data_inicio,
            'data_termino' => $leilaoObj->data_termino,
            'status' => $leilaoObj->status,
        ];
    
        $lances = $leilaoObj->lances()->with('participante')->get()->map(function ($lance) {
            return [
                'valor' => $lance->valor,
                'created_at' => $lance->created_at,
                'participante' => [
                    'id' => $lance->participante->id,
                    'email' => $lance->participante->email,
                    'name' => $lance->participante->name,
                ],
            ];
        });
    
        return response()->json(['leilao' => $leilao, 'lances' => $lances], 200);
    }

    public function encerrarLeilao(Request $request, $id)
    {
        $leilao = $this->repository->getLeilaoById($id);
    
        $encerrador = new Encerrador($leilao);
        $encerrador->encerra();
    
        return response()->json(['mensagem' => 'Leilão finalizado com sucesso'], 200);
    }

    
}
