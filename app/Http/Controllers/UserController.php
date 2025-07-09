<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        } else {
            return response()->json(['message' => 'Validation failed'], 422);
        }
    }
    public function login(Request $request)
    {
        // Validate the request data
        $user = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:12',
        ]);

        if (Auth::guard('web')->attempt($user)) {
            $user = User::where('email', $request->email)->first();
            if ($user && Hash::check($request->password, $user->password)) {
                $device = $request->userAgent();
                $token = $user->createToken($device)->plainTextToken;
                return Response([
                    "status" => 200,
                    'token' => $token
                ]);
            }
        }
        return Response([
            'status' => 400,
            'message' => 'Your data is incorect'
        ]);
    }
    // public function logout($token = null)
    // {

    //     $user = Auth::guard('sanctum')->user();
    //     if (null == $token) {
    //         $user->currentAccessToken()->delete();
    //         return;
    //     }
    //     $personaleToken = PersonalAccessToken::findToken($token);
    //     if ($user->id == $personaleToken->tokenable_id && get_class($user) == $personaleToken->tokenable_type) {
    //         $personaleToken->delete();
    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'logout successful',
    //         ]);
    //     }
    // }
}
