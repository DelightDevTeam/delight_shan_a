<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\HomeResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\UserResource;
use App\Models\Admin\BannerText;
use App\Models\Admin\UserLog;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    private const PLAYER_ROLE = 5;

    public function login(LoginRequest $request): JsonResponse
    {

        $credentials = $request->only('user_name', 'password');

        if (! Auth::attempt($credentials)) {
            return $this->error('', 'Credentials do not match!', 401);
        }
        $user = Auth::user();

        UserLog::create([
            'ip_address' => $request->ip(),
            'user_id' => $user->id,
            'user_agent' => $request->userAgent(),
        ]);

        return $this->success(new UserResource($user), 'User login successfully.');
    }

    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function getUser(): JsonResponse
    {
        $user = Auth::user();

        return $this->success(new PlayerResource($user), 'User Success');
    }

    public function home(): JsonResponse
    {
        $user = Auth::user();
        $bannerText = BannerText::latest()->first();

        $result = ['user' => $user, 'bannerText' => $bannerText];
        return $this->success(new HomeResource($result), 'User Success');
    }
}
