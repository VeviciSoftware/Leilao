<?php
namespace App\Repositories;

use App\Http\Requests\UserRequest;
use App\Models\User;

class EloquentUserRepository implements IUserRepository
{
    public function add(UserRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return $user;
    }
}