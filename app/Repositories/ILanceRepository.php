<?php

namespace App\Repositories;

use App\Http\Requests\LanceRequest;

interface ILanceRepository
{
    public function add(LanceRequest $request);
}