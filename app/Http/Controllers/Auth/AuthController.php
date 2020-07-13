<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function registerUser(Request $request)
    {
        $this->validate($request, [
            'first_name' => ['required', 'string', 'min:3'],
            'last_name' => ['required', 'string', 'min:3'],
            'phone' => ['required', 'string', 'min:9'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ]);

        return DB::transaction(function () use ($request) {
            $user = new User($request->all());
            $user->password = Hash::make($request->password);
            $user->api_token = $this->createToken();
            $user->save();


            return [
                'token' => $user->api_token,
                'user' => $user
            ];
        });
    }

    public function userLogin(Request $request)
    {
        $this->validate($request, [
           'email_or_username' => 'required|string|max:255',
           'password' => 'required|string'
        ]);
        $email_or_username = $request->input('email_or_username');
        $user = User::where(function ($builder) use ($email_or_username) {
            $builder->where('username', $email_or_username)->orWhere('email', $email_or_username);
        })->first();
        abort_unless(is_object($user), Response::HTTP_UNAUTHORIZED, "Unauthorized");
        $verify = Hash::check($request->password, $user->password);
        abort_unless($verify, Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        $user->api_token = $this->createToken($user->id);
        $user->save();

        return [
            'token' => $user->token,
            'user' => $user
        ];
    }

    public function userLogout(Request $request)
    {
        $user = $request->user();
        $user->api_token = null;
        $user->save();
        return;
    }

    private function createToken($user_id = null)
    {
        return sha1($user_id . bin2hex(random_bytes(16)));
    }
}

