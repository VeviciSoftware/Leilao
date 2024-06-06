<?php 

namespace App\Repositories;

use App\Http\Requests\LeilaoRequest;

interface ILeilaoRepository {
    public function add(LeilaoRequest $request);
    public function getLeilaoById(int $id);
}