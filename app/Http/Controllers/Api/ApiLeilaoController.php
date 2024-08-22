<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeilaoRequest;
use App\Repositories\ILeilaoRepository;
use App\Services\Leilao\Encerrador;
use App\Events\LeilaoGanhadorEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'mensagem' => 'Leilão deletado com sucesso',
            204
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

    public function encerraLeiloes(Request $request)
    {
        try {
            if ($request->has('id')) {
                // Finaliza um leilão específico
                $leilao = Leilao::find($request->id);

                if (!$leilao) {
                    return response()->json(['mensagem' => 'Leilão não encontrado'], 404);
                }

                $encerrador = new Encerrador($leilao);
                $encerrador->encerra();

                return response()->json(['mensagem' => 'Leilão finalizado com sucesso.'], 200);
            } else {
                // Finaliza todos os leilões expirados
                Encerrador::encerraLeiloesExpirados();
                return response()->json(['mensagem' => 'Leilões expirados finalizados com sucesso.'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['mensagem' => $e->getMessage()], 400);
        }
    }

    //  Quando um leilão assumir o estado FINALIZADO, o ganhador do leilão (se houver) deve
    //  receber um e-mail parabenizando-o pelo arremate.
    public function finalizaLeilao($id)
    {
        try {
            Log::info('Iniciando finalização do leilão', ['leilao_id' => $id]);
    
            $leilao = Leilao::find($id);
    
            if (!$leilao) {
                Log::warning('Leilão não encontrado', ['leilao_id' => $id]);
                return response()->json(['mensagem' => 'Leilão não encontrado'], 404);
            }
    
            $lanceVencedor = $leilao->lances()->orderBy('valor', 'desc')->first();
    
            if ($lanceVencedor) {
                Log::info('Lance vencedor encontrado', ['user_id' => $lanceVencedor->usuario_id]);
                $leilao->leilao_ganhador = $lanceVencedor->usuario_id;
            } else {
                Log::info('Nenhum lance vencedor encontrado', ['leilao_id' => $id]);
            }
    
            // Verifica se o leilão está com o status ABERTO ou EXPIRADO. Somente leilões com esse status podem ser FINALIZADO.
            if (!$leilao->isAberto() && !$leilao->isExpirado()) {
                Log::warning('Tentativa de finalizar leilão com status inválido', ['leilao_id' => $id, 'status' => $leilao->status]);
                return response()->json(['mensagem' => 'Apenas leilões com status ABERTO e EXPIRADO podem ser FINALIZADOS. Status do leilão: ' . $leilao->status], 400);
            }
    
            // Alterar o status do leilão para FINALIZADO
            $leilao->status = 'FINALIZADO';
            $leilao->save();
    
            // Dispara o evento de envio de e-mail para o ganhador do leilão
            if ($lanceVencedor && $lanceVencedor->participante) {
                event(new LeilaoGanhadorEvent($leilao));
                Log::info('Evento de e-mail disparado para o ganhador do leilão', ['user_id' => $lanceVencedor->usuario_id]);
            }
    
            if ($lanceVencedor && $lanceVencedor->participante) {
                return response()->json(['mensagem' => 'Parabéns ' . $lanceVencedor->participante->name . ', você ganhou o leilão. Confira sua caixa de e-mail.'], 200);
            }
    
            return response()->json(['mensagem' => 'Leilão encerrado com sucesso'], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao finalizar leilão', ['error' => $e->getMessage(), 'leilao_id' => $id]);
            return response()->json(['mensagem' => 'Ocorreu um erro ao finalizar o leilão. Por favor, tente novamente mais tarde.'], 500);
        }
    }




}
