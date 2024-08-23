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

        // Check if the API request was successful
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $responseData = $response->getData(true);

            // Assuming the API response contains the 'Balance' and you want to replace it with your site's wallet balance
            if (isset($responseData['Balance'])) {
                $responseData['Balance'] = Auth::user()->wallet->balance;
            }

            // Build the final response to match the expected structure
            $finalResponse = [
                'Status' => 200,
                'Description' => 'Success',
                'ResponseDateTime' => now()->setTimezone('UTC')->format('Y-m-d H:i:s'),
                'Balance' => $responseData['Balance'] ?? null,
            ];

            return response()->json($finalResponse);
        }

        // Handle the case where the API request fails
        return response()->json([
            'error' => 'API request failed',
            'details' => $response->getData(true),
        ], 500);
    }

}
