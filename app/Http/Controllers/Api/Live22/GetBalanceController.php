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

        // Convert the JsonResponse to an array
        $responseArray = $response->getData(true);

        // Assuming the API response contains the balance, but you want to replace it with your site's wallet balance
        if (isset($responseArray['Balance'])) {
            $responseArray['Balance'] = Auth::user()->wallet->balance;
        }

        // Return the modified response
        return response()->json($responseArray);
    }

}
