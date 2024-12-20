<?php

namespace App\Auth\V1;

use Illuminate\Support\Str;
use App\Models\User;
use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;
use App\Permissions\V1\Abilities;
use App\Traits\V1\ApiResponses;
use App\Traits\V1\TokenHelper;
use App\Policies\V1\UserPolicy;
use Illuminate\Support\Facades\Hash;
use \Exception;

final class UserAuth
{
    use ApiResponses, TokenHelper;

    private $authenticatedUser = null;
    private $userPolicy;
    private $accessToken;
    private $refreshToken;

    public function __construct()
    {
        $this->userPolicy = new UserPolicy();
    }

    private function createTokens($user)
    {
        $token = $user->createToken('API token for ' . $user->email, Abilities::getAbilities($user), now()->addDay())->plainTextToken;
        $refreshToken = Str::random(60);

        $user->tokens()->create([
            'name' => 'API Refresh Token',
            'token' => hash('sha256', $refreshToken),
            'refresh_token_expires_at' => now()->addDay(),
        ]);

        $this->accessToken = $token;
        $this->refreshToken = $refreshToken;

        return [$token, $refreshToken];
    }

    private function getUserBasedOffAccessTokenAndRefreshToken(Request $request)
    {
        if (!$request->filled('access_token') || !$request->filled('refresh_token')) {
            return $this->error('No token exists.', 400);
        }

        $user = PersonalAccessToken::where('token', $this->getTokenFromRequest($request))->first();

        if (!$user) {
            throw new Exception('Invalid request', 400);
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
            200
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
            throw new Exception('Invalid refresh token', 400);
        }

        if ($refreshToken->refresh_token_expires_at < now()) {
            throw new Exception('Refresh token expired', 400);
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
            200
        );
    }

    public function canCreate()
    {
        if ($this->authenticatedUser !== null) {
            return $this->userPolicy->create($this->authenticatedUser, $this->accessToken);
        }

        return false;
    }

    public function canReplace()
    {
        if ($this->authenticatedUser !== null) {
            return $this->userPolicy->replace($this->authenticatedUser, $this->accessToken);
        }

        return false;
    }

    public function canUpdate()
    {
        if ($this->authenticatedUser !== null) {
            return $this->userPolicy->update($this->authenticatedUser, $this->accessToken);
        }

        return false;
    }

    public function canDelete()
    {
        if ($this->authenticatedUser !== null) {
            return $this->userPolicy->delete($this->authenticatedUser, $this->accessToken);
        }

        return false;
    }

    public function only()
    {
        if ($this->authenticatedUser !== null) {
            return $this->userPolicy->only($this->authenticatedUser, $this->accessToken);
        }

        return false;
    }
}
