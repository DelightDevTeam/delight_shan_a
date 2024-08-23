<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GameService
{
    public function gameLogin(string $playerId, string $gameCode, string $playerIp, bool $launchDemo = false)
    {
        // Retrieve values from the config/game.php file
        $operatorId = config('game.api.operator_code'); 
        $secretKey = config('game.api.secret_key');
        $apiUrl = config('game.api.url') . 'GameLogin'; // Append the endpoint to the base URL
        $currency = config('game.api.currency');
        $requestDateTime = now()->setTimezone('UTC')->format('Y-m-d H:i:s');

        // Generate the signature using MD5 hashing
        $signature = md5('GameLogin' . $requestDateTime . $operatorId . $secretKey . $playerId);

        // Prepare the data to be sent in the request
        $data = [
            'OperatorId' => $operatorId,
            'RequestDateTime' => $requestDateTime,
            'Signature' => $signature,
            'PlayerId' => $playerId,
            'Ip' => $playerIp,
            'GameCode' => $gameCode,
            'Currency' => $currency,
            'LaunchDemo' => $launchDemo,
        ];

        try {
            // Log request data for debugging
            Log::info('Sending GameLogin request to API', $data);

            // Send the request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($apiUrl, $data);

            // Log response data for debugging
            Log::info('Received GameLogin response from API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                return $response->json(); // Return the JSON response from the API
            }

            return response()->json([
                'error' => 'API request failed',
                'details' => $response->body(),
            ], $response->status());
        } catch (\Throwable $e) {
            // Handle unexpected exceptions and log the error
            Log::error('An error occurred during GameLogin API request', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }
}
