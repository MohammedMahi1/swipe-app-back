<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            return response()->json([
                'user' => $user,
                'status' => 200
            ]);
        }
    }
    public function create(Request $request)
    {
        // Validate the request data
        $validation = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string'
        ]);
        if ($validation) {
            // Create the user
            $createUser = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $createUser->save();
            return response()->json(['message' => 'User created successfully'], 201);
        } 
        return response()->json(['message' => 'Validation failed'], 422);
       
    }
    public function login(Request $request)
    {
        // Validate the request data
        $user = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:12',
        ]);

        if (Auth::guard('web')->attempt($user)) {
            $device = $request->userAgent();
            $token = Auth::user()->createToken($device)->plainTextToken;
            return response()->json([
                "user" => Auth::user(),
                "token" => $token
            ]);
        } else {
            return response()->json(
                "Email or Password is incorrect",
                403
            );
        }
    }
    public function logout($token = null)
    {

        $user = Auth::guard('sanctum')->user();
        if (null == $token) {
            $user->currentAccessToken()->delete();
            return;
        }
        $personaleToken = PersonalAccessToken::findToken($token);
        if ($user->id == $personaleToken->tokenable_id && get_class($user) == $personaleToken->tokenable_type) {
            $personaleToken->delete();
            return response()->json([
                'status' => 200,
                'message' => 'logout successful',
            ]);
        }
    }
}
