<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthAdminLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthAdminController extends Controller
{
    public function login(AuthAdminLoginRequest $request)
    {
        $user = User::where('email', $request->email)
            ->first();

        if(! $user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'You entered incorrect data to enter the site'
            ], 401);
        }

        return response()->json(['token' => $user->createToken(Str::random(10))->plainTextToken], 200);
    }

    public function getAdmin(Request $request)
    {
        return $request->user();
    }
}
