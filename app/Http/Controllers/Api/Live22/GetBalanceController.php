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

        // Assuming the API response contains the balance, but you want to replace it with your site's wallet balance
        if ($response && isset($response['Balance'])) {
            $response['Balance'] = Auth::user()->wallet->balance;
        }

        return response()->json($response);
    }

}
