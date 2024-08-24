<?php

namespace App\Http\Controllers\Api\Live22;

use Illuminate\Http\Request;
use App\Services\GameService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SlotWebhookRequest;

class GetBalanceController extends Controller
{
    
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function getBalance(Request $request)
    {
        $user = Auth::user();

        // Log if the user is authenticated and has a wallet
        if ($user && $user->wallet) {
            Log::info('User Wallet Balance:', ['balance' => $user->wallet->balance]);
        } else {
            Log::warning('No wallet associated with the user');
        }

        // Retrieve the PlayerId from the request
        //$playerId = $request->input('PlayerId');
        $playerId = $user->user_name;


        if (!$playerId) {
            return response()->json(['error' => 'PlayerId is missing from the request.'], 400);
        }

        // Ensure the user is authenticated and retrieve the current access token
        $token = $user->currentAccessToken()->token;

        if (!$token) {
            return response()->json(['error' => 'Authentication token is missing or invalid.'], 401);
        }

        

        // Pass the token and PlayerId to the GameService's getBalance method
        $response = $this->gameService->getBalance($token, $playerId);

        $balance = $user->wallet->balance;

        // Check if the API request was successful
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $responseData = $response->getData(true);

            // Assuming the API response contains the 'Balance' and you want to replace it with your site's wallet balance
            if (isset($responseData['Balance'])) {
                $responseData['Balance'] = $balance;
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
