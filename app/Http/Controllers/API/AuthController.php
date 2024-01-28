<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\JWTAuth as JWTAuthJWTAuth;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
/**
 * @OA\Info(
 *      title="Certif",
 *      version="1.0.0",
 *      description="Api de l'appli PENC"
 * )
 */
     /**
     * Authentifier l'utilisateur et obtenir le jeton JWT
     *
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Authentifier l'utilisateur et obtenir le jeton JWT",
     *     description="Authentifier un utilisateur en fournissant son adresse e-mail et son mot de passe, et obtenir un jeton JWT en réponse",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="utilisateur@exemple.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Authentification réussie, jeton JWT reçu"),
     *     @OA\Response(response="401", description="Non autorisé, identifiants invalides")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);
        $credentials = $request->only('email', 'password');
        $token =    JWTAuth::attempt($credentials);
        
        if (!$token) {
            return response()->json([
                'message' => 'Veillez vérifier vos informations, Action non autorisé',
                'email.required' => 'L\'adresse e-mail est requise.',
                'email.email' => 'L\'adresse e-mail doit être au format valide.',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * Enregistrer un nouvel utilisateur
     *
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Enregistrer un nouvel utilisateur",
     *     description="Enregistrer un nouvel utilisateur en fournissant les informations requises",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"firstname", "lastname", "email", "password", "password_confirmation"},
     *             @OA\Property(property="firstname", type="string", example="John"),
     *             @OA\Property(property="lastname", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="nouveau@utilisateur.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Inscription réussie, utilisateur créé"),
     *     @OA\Response(response="422", description="Échec de la validation des données")
     * )
     */
    public function register(Request $request)
    {

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
        
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Inscription effectuer avec success👏🏽',
            'user' => $user
        ]);
    }

    /**
     * Déconnecter l'utilisateur actuellement authentifié
     *
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="Déconnecter l'utilisateur actuellement authentifié",
     *     description="Déconnecter l'utilisateur actuellement authentifié et révoquer le jeton JWT",
     *     @OA\Response(response="200", description="Déconnexion réussie")
     * )
     */
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Déconnection reuissite ',
        ]);
    }

     /**
     * Rafraîchir le jeton JWT actuel
     *
     * @OA\Post(
     *     path="/api/refresh",
     *     tags={"Auth"},
     *     summary="Rafraîchir le jeton JWT actuel",
     *     description="Rafraîchir le jeton JWT actuel en obtenant un nouveau jeton valide",
     *     @OA\Response(response="200", description="Rafraîchissement réussi, nouveau jeton JWT reçu"),
     *     @OA\Response(response="401", description="Non autorisé, jeton JWT invalide")
     * )
     */
    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
