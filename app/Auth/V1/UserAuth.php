<?php

namespace App\Auth\V1;

use Illuminate\Support\Str;
use App\Models\User;
use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;
use App\Permissions\V1\Abilities;
use App\Traits\V1\ApiResponses;
use Illuminate\Support\Facades\Hash;

final class UserAuth
{
    use ApiResponses;

    private $authenticatedUser = null;

    private function createTokens($user)
    {
        $token = $user->createToken('API token for ' . $user->email, Abilities::getAbilities($user), now()->addDay())->plainTextToken;
        $refreshToken = Str::random(60);

        $user->tokens()->create([
            'name' => 'API Refresh Token',
            'token' => hash('sha256', $refreshToken),
            'refresh_token_expires_at' => now()->addDay(),
        ]);

        return [$token, $refreshToken];
    }

    private function getTokenFromRequest(Request $request, string $tokenType = 'access')
    {
        $token = '';

        if ($tokenType === 'access') {
            $token = explode('|', $request->access_token, 2)[1] ?? $request->access_token;
        }

        if ($tokenType === 'refresh') {
            $token = $request->refresh_token;
        }

        return hash('sha256', $token);
    }

    private function getUserBasedOffAccessTokenAndRefreshToken(Request $request)
    {
        $request->validated($request->only(['access_token', 'refresh_token']));

        if (!$request->filled('access_token') || !$request->filled('refresh_token')) {
            return $this->error('No token exists.', 400);
        }

        $user = PersonalAccessToken::where('token', $this->getTokenFromRequest($request))->first();

        if (!$user) {
            return $this->error('Invalid request', 400);
        }

        $user = $user->tokenable;

        return $user;
    }

    public function setAuthenticatedUser($authenticatedUser)
    {
        $this->authenticatedUser = $authenticatedUser;
    }

    public function getAuthenticatedUser()
    {
        return $this->authenticatedUser;
    }

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $createdTokens = $this->createTokens($user);

        $this->setAuthenticatedUser($user);

        return $this->ok(
            'User registered successfully',
            [
                'access_token' => $createdTokens[0],
                'refresh_token' => $createdTokens[1],
            ],
            201
        );
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        $createdTokens = $this->createTokens($user);

        $this->setAuthenticatedUser($user);

        return $this->ok(
            'Authenticated',
            [
                'access_token' => $createdTokens[0],
                'refresh_token' => $createdTokens[1],
            ],
            200
        );
    }

    public function logout(Request $request)
    {
        $user = $this->getUserBasedOffAccessTokenAndRefreshToken($request);

        foreach ($user->tokens as $token) {
            if ($this->getTokenFromRequest($request) === $token->token) {
                $token->delete();
            }

            if ($this->getTokenFromRequest($request, 'refresh') === $token->token) {
                $token->delete();
            }
        }

        $this->setAuthenticatedUser(null);

        return $this->ok('User logged out Successfully.');
    }

    public function refreshToken(Request $request)
    {
        $user = $this->getUserBasedOffAccessTokenAndRefreshToken($request);

        $refreshToken = $user->tokens->where('token', $this->getTokenFromRequest($request, 'refresh'))->first();

        if (!$refreshToken) {
            return $this->error('Invalid refresh token', 400);
        }

        if ($refreshToken->refresh_token_expires_at < now()) {
            return $this->error('Refresh token expired', 400);
        }

        $user->tokens->where('token', $this->getTokenFromRequest($request))->first()->delete();
        $refreshToken->delete();

        $createdTokens = $this->createTokens($user);

        $this->setAuthenticatedUser($user);

        return $this->ok(
            'New tokens have been generated.',
            [
                'access_token' => $createdTokens[0],
                'refresh_token' => $createdTokens[1],
            ],
            201
        );
    }
}