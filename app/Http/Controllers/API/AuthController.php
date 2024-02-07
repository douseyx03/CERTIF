<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\JWTAuth as JWTAuthJWTAuth;

// Define the AuthController class, which extends the base controller class
class AuthController extends Controller
{
    // Constructor method to apply the 'auth:api' middleware to all methods except 'login' and 'register'
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    // Method to handle user login
    public function login(Request $request)
    {
        // Validate the request input
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);
        // Get the credentials from the request
        $credentials = $request->only('email', 'password');
        // Attempt to generate a JWT token
        $token = JWTAuth::attempt($credentials);
        
        // If no token is generated, return an error response
        if (!$token) {
            return response()->json([
                'message' => 'Veillez vérifier vos informations, Action non autorisé',
                'email.required' => 'L\'adresse e-mail est requise.',
                'email.email' => 'L\'adresse e-mail doit être au format valide.',
            ], 401);
        }
        
        // If a token is generated, get the authenticated user and return a success response with user and token
        $user = Auth::user();
        return response()->json([
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    // Method to handle user registration
    public function register(Request $request)
    {
        // Validate the request input
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
        
        // Create a new user with the provided information and return a success response
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Inscription effectuer avec success👏🏽',
            'user' => $user
        ],201);
    }

    // Method to handle user logout
    public function logout()
    {
        // Log the user out and return a success response
        Auth::logout();
        return response()->json([
            'message' => 'Déconnection reuissite ',
        ]);
    }

    // Method to refresh the user token
    public function refresh()
    {
        // Return the authenticated user and a new token in a success response
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}