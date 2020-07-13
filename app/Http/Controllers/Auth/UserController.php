<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Users;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        $user = $request->user();
        return ($user);
    }

    public function updateUser(Request $request)
    {
        $user = $request->user();
        $this->validate($request, [
            'first_name' => ['required', 'string', 'min:3'],
            'last_name' => ['required', 'string', 'min:3'],
            'username' => ['required', 'string', 'max:255', "unique:users,username,{$user->id}"],
            'phone' => ['required', 'string', 'min:9'],
            'email' => ['required', 'string', 'email', 'max:255', "unique:users,email,{$user->id}"]
        ]);
        $user->fill($request->all());
        $user->save();
        return;
    }

    public function updateUserPassword(Request $request)
    {
        $this->validate($request, [
            'password' => ['required', 'string', 'min:6'],
        ]);
        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return;
    }
}
