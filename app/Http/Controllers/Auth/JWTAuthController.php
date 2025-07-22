<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTAuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->apiResponse('Registered successfully', 201, [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'user'         => $user,
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (! $token = JWTAuth::attempt($credentials)) {
            return $this->apiResponse('Invalid credentials', 401);
        }

        return $this->apiResponse('Login successful', 200, [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'user'         => auth()->user()
        ]);
    }

    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->apiResponse('Logged out successfully', 200);
        } catch (JWTException $e) {
            return $this->apiResponse('Failed to logout', 500);
        }
    }

    public function me(): JsonResponse
    {
        return $this->apiResponse('Current user fetched', 200, [
            'user' => auth()->user()
        ]);
    }

    public function refresh(): JsonResponse
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return $this->apiResponse('Token refreshed successfully', 200, [
                'access_token' => $newToken,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60,
            ]);
        } catch (JWTException $e) {
            return $this->apiResponse('Token refresh failed', 500);
        }
    }
}