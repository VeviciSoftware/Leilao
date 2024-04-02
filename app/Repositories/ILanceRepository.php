<?php

namespace App\Repositories;

use App\Http\Requests\LanceRequest;
use App\Models\Lance;

interface ILanceRepository
{
    public function add(LanceRequest $request);
}