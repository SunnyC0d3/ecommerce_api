<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Requests\Api\V1\LoginUserRequest;
use App\Requests\Api\V1\LogoutUserRequest;
use App\Requests\Api\V1\RegisterUserRequest;
use App\Requests\Api\V1\RefreshTokenRequest;
use App\Models\User;
use App\Permissions\Abilities;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponses;

    /**
     * Register
     * 
     * Registers a new user and returns the user's API token.
     * 
     * @unauthenticated
     * @group Authentication
     * @response 201 {
     *   "data": {
     *       "token": "{YOUR_AUTH_KEY}"
     *   },
     *   "message": "User registered successfully",
     *   "status": 201
     * }
     */
    public function register(RegisterUserRequest $request)
    {
        $request->validated($request->only(['name', 'email', 'password', 'password_confirmation']));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $token = $user->createToken('API token for ' . $user->email, Abilities::getAbilities($user), now()->addDay())->plainTextToken;
        $refreshToken = Str::random(60);

        $user->tokens()->create([
            'name' => 'API Refresh Token',
            'token' => hash('sha256', $refreshToken),
            'refresh_token_expires_at' => now()->addDays(30),
        ]);

        return $this->ok(
            'User registered successfully',
            [
                'token' => $token,
                'refresh_token' => $refreshToken,
            ],
            201
        );
    }

    /**
     * Login
     * 
     * Authenticates the user and returns the user's API token.
     * 
     * @unauthenticated
     * @group Authentication
     * @response 200 {
     * "data": {
     *       "token": "{YOUR_AUTH_KEY}",
     *       "refresh_token": "{YOUR_AUTH_KEY}"
     * },
     *      "message": "Authenticated",
     *      "status": 200
     * }
     * @response 401 {
     *      "message": "Invalid credentials",
     *      "status": 200
     * }
     */
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->only(['email', 'passowrd']));

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        $token = $user->createToken('API token for ' . $user->email, Abilities::getAbilities($user), now()->addDay())->plainTextToken;
        $refreshToken = Str::random(60);

        $user->tokens()->create([
            'name' => 'API Refresh Token',
            'token' => hash('sha256', $refreshToken),
            'refresh_token_expires_at' => now()->addDays(30),
        ]);

        return $this->ok(
            'Authenticated',
            [
                'token' => $token,
                'refresh_token' => $refreshToken,
            ],
            200
        );
    }

    /**
     * Logout
     * 
     * Signs out the user and destroy's the API token.
     * 
     * @group Authentication
     * @response 200 {}
     */
    public function logout(LogoutUserRequest $request)
    {
        $request->validated($request->only(['refresh_token']));

        if (!$request->user()->currentAccessToken() || !$request->filled('refresh_token')) {
            return $this->error('No token exists.', 400);
        }

        $request->user()->currentAccessToken()->delete();

        $refreshTokens = $request->user()->tokens;

        foreach ($refreshTokens as $refreshToken) {
            if (hash('sha256', $request->refresh_token) === $refreshToken->token) {
                $refreshToken->delete();
            }
        }

        return $this->ok('User logged out Successfully.');
    }

    /**
     * refreshToken
     * 
     * Generates new tokens for the user.
     * 
     * @group Authentication
     * "data": {
     *       "token": "{YOUR_AUTH_KEY}",
     *       "refresh_token": "{YOUR_AUTH_KEY}"
     * },
     *      "message": "New tokens have been generated.",
     *      "status": 201
     * }
     * @response 400 {
     *      "message": "Refresh token expired",
     *      "status": 400
     * }
     * @response 400 {
     *      "message": "Invalid refresh token",
     *      "status": 400
     * }
     */
    public function refreshToken(RefreshTokenRequest $request)
    {
        $request->validated($request->only(['refresh_token']));

        $token = $request->user()->tokens()->where('token', hash('sha256', $request->refresh_token))->first();

        if (!$token) {
            return $this->error('Invalid refresh token', 400);
        }

        if ($token->refresh_token_expires_at < now()) {
            return $this->error('Refresh token expired', 400);
        }

        $user = User::firstWhere('email', $request->user()->email);

        $newAccessToken = $request->user()->createToken('API token for ' . $user->email, Abilities::getAbilities($user), now()->addDay())->plainTextToken;
        $newRefreshToken = Str::random(60);

        $token->update([
            'name' => 'API Refresh Token',
            'token' => hash('sha256', $newRefreshToken),
            'refresh_token_expires_at' => now()->addDays(30),
        ]);

        return $this->ok(
            'New tokens have been generated.',
            [
                'token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
            ],
            201
        );
    }
}
