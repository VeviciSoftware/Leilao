<?php

namespace App\Repositories;

use App\Http\Requests\UserRequest;

interface IUserRepository
{
    public function add(UserRequest $request);
}