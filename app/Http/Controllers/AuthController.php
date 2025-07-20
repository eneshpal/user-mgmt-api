<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    

    public function login(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Attempt to log the user in with email and password
        if (!Auth::attempt($validated)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the authenticated user and generate the token
        $user = Auth::user();
        $token = $user->createToken('AccessToken')->accessToken;

        // Load role relationship to access role name
        $user->load('role');
        //dd($token);
        
        // Respond with token, user ID, and success message
        return response()->json([
            'status' => true,
            'user_id' => $user->id,
            'token' => $token,
            'role_name' => $user->role ? $user->role->name : null,
            'message' => 'Login successfully',
        ]);
    }

    public function logout(Request $request)
    {
        //echo "Logout called";die();

        $user = Auth::user();
        //dd($user);
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'message' => 'Logged out successfully',
        ]);

    }


    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
