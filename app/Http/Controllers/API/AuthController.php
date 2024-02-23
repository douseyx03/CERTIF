<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Notifications\UserBlocked;
use App\Notifications\UserDeleted;
use App\Notifications\UserUnblocked;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\JWTAuth as JWTAuthJWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {$request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string|min:6',
    ]);
    
    $user = User::where('email', $request->input('email'))->first();
    
    if (!$user || !Hash::check($request->input('password'), $user->password)) {
        return response()->json([
            'message' => 'Veillez vérifier vos informations, Action non autorisé',
            'email.required' => 'L\'adresse e-mail est requise.',
            'email.email' => 'L\'adresse e-mail doit être au format valide.',
        ], 401);
    }
    
    $token = JWTAuth::fromUser($user);
    
    return response()->json([
        'user' => $user,
        'authorization' => [
            'token' => $token,
            'type' => 'bearer',
        ]
    ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
        
        if ($request->filled(['firstname', 'lastname', 'email', 'password'])) {
            return DB::transaction(function () use ($request) {
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
            });
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Déconnection reuissite ',
        ]);
    }

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

    public function profile()
    {
        
        return response()->json([
         'message' => 'Les infos de votre profile sont :',
         'user' => Auth::user()->toArray(),
        ], 200);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        
            $user = Auth::user();
        
            $validatedData = $request->validate([
                'firstname' => 'required|string',
                'lastname' => 'string|nullable',
                'image' => 'image||max:2048|nullable',
                'email' => 'email',
                'password' => 'string|min:6|nullable|confirmed',
            ]);
            $filename = '';
            if($request->file('image')){
                        $file= $request->file('image');
                        $filename= date('YmdHi').$file->getClientOriginalName();
                        $file-> move(public_path('pictures/UserImage'), $filename);
                        $user['image']= $filename;
                    }
            if ($request->filled('password') && $request->input('password') !== $request->input('password_confirmation')) {
                return response()->json([
                    'message' => 'Le mot de passe et la confirmation ne correspondent pas.',
                ], 422);
            }

        
            try {
                $user->firstname = $request->input('firstname');
                $user->lastname = $request->input('lastname');
                $user->email = $request->input('email');
                $user->password = Hash::make($request->input('password'));

                $user->image = 'pictures/UserImage/' . $filename; // Set the image URL
                $user->save();
                return response()->json([
                    'message' => 'Modification de vos infos effectuée avec succès👏🏽',
                    'user' => Auth::user(),
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Une erreur s\'est produite lors de la modification de vos informations. Veuillez reessayer.',
                    'error' => $e->getMessage(),
                ], 500);
            }
    }

    public function getAllUsers(): JsonResponse
    {
        $users = User::all();
        return response()->json($users);
    }

    public function blockUser($id): JsonResponse
    {
        try {
            $user = User::find($id);
            if (!$user) {
                throw new \Exception('User not found');
            }
        
            $user->is_blocked = true;
            $user->save();
            Log::info('User ' . $user->id . ' has been blocked.'); // logging statement
            $user->notify(new UserBlocked);
            Log::info('User ' . $user->id . ' has been notified about being blocked.'); // logging statement
            return response()->json(['message' => 'Utilisateur bloque avec succes'], 200);
        } catch (\Exception $e) {
            Log::error('Error blocking user: ' . $e->getMessage());
            return response()->json(['message' => 'Error blocking user', 'error' => $e->getMessage()], 500);
        }
    }

    public function unBlockUser($id): JsonResponse
    {
        $user = User::find($id);
        $user->is_blocked = false;
        $user->save();
        $user->notify(new UserUnblocked);

        return response()->json(['message' => 'Utilisateur debloque avec succes']);
    }

    public function deleteUser($id): JsonResponse
    {
        $user = User::find($id);
        $user->delete();
        $user->notify(new UserDeleted);

        return response()->json(['message' => 'Utilisateur supprimé avec succes']);
    }

    public function getUsersInfos(): JsonResponse
        {
            $users = User::all(['id', 'firstname', 'lastname', 'image']);
            return response()->json($users);
        }

    public function listOfBlockedUsers(): JsonResponse 
    {
        $users = User::where('is_blocked', true)->get(['id', 'firstname', 'lastname', 'image','email']);
        return response()->json($users);
    }
    
}
