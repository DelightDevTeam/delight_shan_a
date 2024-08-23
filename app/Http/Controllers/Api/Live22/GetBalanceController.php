<?php

namespace App\Http\Controllers\Api\Live22;

use Illuminate\Http\Request;
use App\Services\GameService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GetBalanceController extends Controller
{
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

     public function getBalance(Request $request)
    {
        // Ensure the user is authenticated and retrieve the current access token
        $token = Auth::user()->currentAccessToken()->token;

        if (!$token) {
            return response()->json(['error' => 'Authentication token is missing or invalid.'], 401);
        }

        // Pass the token to the GameService's getBalance method
        $response = $this->gameService->getBalance($token);

        return response()->json($response);
    }
}
