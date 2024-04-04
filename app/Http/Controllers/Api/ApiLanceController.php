<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanceRequest;
use App\Models\Lance;
use App\Repositories\ILanceRepository;
use Illuminate\Http\Request;


class ApiLanceController extends Controller
{

    public function __construct(private ILanceRepository $repository) {
        
    }

    public function index()
    {
        if (Lance::all()->isEmpty()) {
            return response()->json(['message' => 'Nenhum lance encontrado'], 404);
        }

        return Lance::all();
    }

    public function store(LanceRequest $request)
    {
        $lance = $this->repository->add($request);

        return response()->json($lance);
        
    }

    public function show($id)
    {
        try {
            $lance = Lance::findOrFail($id);
            return response()->json(['mensagem' => 'Lance encontrado!', 'lance' => $lance], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['mensagem' => 'Lance não encontrado!'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        
    }

    public function destroy($id)
    {
        //
    }   
}
