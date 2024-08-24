<?php

namespace App\Services;

use App\Enums\StatusCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GameService
{
    // public function gameLogin(string $playerId, string $gameCode, string $playerIp, bool $launchDemo = false)
    // {
    //     // Retrieve values from the config/game.php file
    //     $operatorId = config('game.api.operator_code'); 
    //     $secretKey = config('game.api.secret_key');
    //     $apiUrl = config('game.api.url') . 'GameLogin'; // Append the endpoint to the base URL
    //     $currency = config('game.api.currency');
    //     $requestDateTime = now()->setTimezone('UTC')->format('Y-m-d H:i:s');

    //     // Generate the signature using MD5 hashing
    //     $signature = md5('GameLogin' . $requestDateTime . $operatorId . $secretKey . $playerId);
    //     $user = Auth::user();

    //     // Prepare the data to be sent in the request
    //     $data = [
    //         'OperatorId' => $operatorId,
    //         'RequestDateTime' => $requestDateTime,
    //         'Signature' => $signature,
    //         'PlayerId' => $playerId,
    //         'Ip' => $playerIp,
    //         'GameCode' => $gameCode,
    //         'Currency' => $currency,
    //         'DisplayName' => $user->name,
    //         'PlayerBalance' => $user->wallet->balance,
    //         'LaunchDemo' => $launchDemo,
    //     ];

    //     try {
    //         // Log request data for debugging
    //         Log::info('Sending GameLogin request to API', $data);

    //         // Send the request
    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'Accept' => 'application/json',
    //         ])->post($apiUrl, $data);

    //         // Log response data for debugging
    //         Log::info('Received GameLogin response from API', [
    //             'status' => $response->status(),
    //             'body' => $response->body(),
    //         ]);

    //         if ($response->successful()) {
    //             return $response->json(); // Return the JSON response from the API
    //         }

    //         return response()->json([
    //             'error' => 'API request failed',
    //             'details' => $response->body(),
    //         ], $response->status());
    //     } catch (\Throwable $e) {
    //         // Handle unexpected exceptions and log the error
    //         Log::error('An error occurred during GameLogin API request', [
    //             'exception' => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'error' => 'An unexpected error occurred',
    //             'exception' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    public function gameLogin(string $playerId, string $gameCode, string $playerIp, bool $launchDemo = false)
    {
        $operatorId = config('game.api.operator_code'); 
        $secretKey = config('game.api.secret_key');
        $apiUrl = config('game.api.url') . 'GameLogin';
        $currency = config('game.api.currency');
        $requestDateTime = now()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $signature = md5('GameLogin' . $requestDateTime . $operatorId . $secretKey . $playerId);
        $user = Auth::user();

        $data = [
            'OperatorId' => $operatorId,
            'RequestDateTime' => $requestDateTime,
            'Signature' => $signature,
            'PlayerId' => $playerId,
            'Ip' => $playerIp,
            'GameCode' => $gameCode,
            'Currency' => $currency,
            'DisplayName' => $user->name,
            'PlayerBalance' => $user->wallet->balance,
            'LaunchDemo' => $launchDemo,
        ];

        try {
            Log::info('Sending GameLogin request to API', $data);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($apiUrl, $data);

            Log::info('Received GameLogin response from API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                 $apiResponse = $response->json(); // Retrieve the JSON response from the API
                // Add DisplayName and PlayerBalance to the response
                $apiResponse['DisplayName'] = $user->name;
                $apiResponse['PlayerBalance'] = $user->wallet->balance;
                return response()->json($response->json(), StatusCode::OK->value);
            }

            return response()->json([
                'error' => 'API request failed',
                'details' => $response->body(),
            ], $response->status());

        } catch (\Throwable $e) {
            Log::error('An error occurred during GameLogin API request', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred',
                'exception' => $e->getMessage(),
            ], StatusCode::InternalServerError->value);
        }
    }


    // get balance 
    

    public function getBalance(string $authToken, $playerId)
    {
        $operatorId = config('game.api.operator_code');
        $secretKey = config('game.api.secret_key');
        $apiUrl = config('game.api.url') . 'GetBalance';
        $currency = config('game.api.currency');
        $requestDateTime = now()->setTimezone('UTC')->format('Y-m-d H:i:s');

        $signature = md5('GetBalance' . $requestDateTime . $operatorId . $secretKey . $playerId);

        $data = [
            'OperatorId' => $operatorId,
            'RequestDateTime' => $requestDateTime,
            'Signature' => $signature,
            'PlayerId' => $playerId,
            'Currency' => $currency,
            'AuthToken' => $authToken,
        ];

        try {
            Log::info('Sending GetBalance request to API', $data);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($apiUrl, $data);

            Log::info('Received GetBalance response from API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->status() == 200) {
                return $response->json(); 
            }

            return response()->json([
                'error' => 'API request failed',
                'details' => $response->body(),
            ], $response->status());
        } catch (\Throwable $e) {
            Log::error('An error occurred during GetBalance API request', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

}
