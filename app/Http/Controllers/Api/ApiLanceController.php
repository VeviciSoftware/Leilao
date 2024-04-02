<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanceRequest;
use App\Models\Lance;
use App\Repositories\ILanceRepository;
use App\Models\Leilao;
use Illuminate\Http\Request;
use App\Models\User;

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

        //Retorna resposta com status 201
        return response()->json($lance, 201);
        
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }   
}
