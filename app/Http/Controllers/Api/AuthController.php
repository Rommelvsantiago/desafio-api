<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param \App\Http\Requests\StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(StoreUserRequest $request)
    {
        try {
            // Criação de um novo usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Criptografa a senha
            ]);

            // Autentica o usuário recém-criado e gera um token JWT
            $token = Auth::guard('api')->login($user);

            // Retorna uma resposta com o usuário e o token
            return response()->json([
                'message' => 'User Created Successfully',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ], 201);

        } catch (\Exception $ex) {
            // Captura exceções e retorna uma mensagem de erro
            return response()->json([
                'error_message' => $ex->getMessage(),
                'status' => $ex->getCode()
            ], 400);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $responseToken = $this->respondWithToken($token)->original;

        $response = [
            'user' => auth('api')->user(),
            'access_token' => $responseToken["access_token"],
            'token_type' => $responseToken["token_type"],
            'expires_in' => $responseToken["expires_in"]
        ];

        return response()->json($response, 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $token = $request->headers->all()['authorization'][0];
        $responseToken = $this->respondWithToken($token)->original;
        $token = str_replace('Bearer ', '', $responseToken["access_token"]);
        $user = auth('api')->user();

        if (!$user)
            return response()->json(['error' => 'Unauthorized'], 401);

        $response = [
            'user' => $user,
            'access_token' => $token,
            'token_type' => $responseToken["token_type"],
            'expires_in' => $responseToken["expires_in"]
        ];

        return response()->json($response, 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60 // retorna o tempo em segundos
        ]);
    }
}
