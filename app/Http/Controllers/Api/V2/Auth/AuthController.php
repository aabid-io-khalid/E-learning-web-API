<?php

namespace App\Http\Controllers\Api\V2\Auth;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use OpenAPI\Annotations as OA;


/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Gestion de l'authentification des utilisateurs"
 * )
 */
class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    /**
     * Inscription d'un utilisateur
     *
     * @OA\Post(
     *     path="/api/v2/auth/register",
     *     summary="Inscription d'un utilisateur",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="password", type="string", example="password123"),
     *                 @OA\Property(property="role", type="string", example="student"),
     *                 @OA\Property(property="profile_image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Inscription réussie"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Rôle invalide"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function register(Request $request)
    {
        try {
                $data = $request->validate([
                    'name' => 'required|string',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|string|min:6',
                    'role' => 'required|string|in:student,mentor', 
                    'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
            
            $role = Role::where('name', $data['role'])->first();

            if (!$role) {
                return response()->json(['error' => 'Rôle invalide.'], 400);
            }

            $data['role_id'] = $role->id;

            if ($request->hasFile('profile_image')) {
                $data['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
            }
            
            $user = $this->authService->register($data);

            return response()->json([
                'user' => $user['user'],
                'token' => $user['token'],
            ], 201);

        } catch (ValidationException $e) {

            return response()->json(['error' => $e->errors()], 422);

        } catch (Exception $e) {

            return response()->json(['error' => 'An error occurred during registration.'], 500);
        }
    }


    /**
     * Connexion d'un utilisateur
     *
     * @OA\Post(
     *     path="/api/v2/auth/login",
     *     summary="Connexion d'un utilisateur",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Identifiants invalides"
     *     )
     * )
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
    
            $result = $this->authService->login($credentials);
    
            if ($result) {
                return response()->json($result);
            }
    
            return response()->json(['error' => 'Invalid credentials'], 401);

        } catch (ValidationException $e) {
            
            return response()->json(['error' => $e->errors()], 422);

        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred during login.'], 500);
        }
    }


     /**
     * Déconnexion d'un utilisateur
     *
     * @OA\Post(
     *     path="/api/v2/auth/logout",
     *     summary="Déconnexion d'un utilisateur",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Utilisateur non authentifié"
     *     )
     * )
     */
    public function logout()
    {
        try {
            $user = auth()->user();
    
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié', 'headers' => request()->headers->all()], 401);
            }

            $user->tokens()->delete();

            Auth::guard('web')->logout(); 
    
            return response()->json(['message' => 'Logged out successfully']);
            
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred during logout.'], 500);
        }
    }   
}

