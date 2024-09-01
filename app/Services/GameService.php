<?php

namespace App\Services;

use App\Enums\StatusCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GameService
{
    public function gameLogin(int $product_id, int $game_type_id, string $gameCode, bool $launchDemo = false)
    {
        $operatorId = config('game.api.operator_code');
        $secretKey = config('game.api.secret_key');
        $apiUrl = config('game.api.url').'GameLogin';
        $currency = config('game.api.currency');
        $requestDateTime = now()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $player = Auth::user();

        $signature = md5('GameLogin'.$requestDateTime.$operatorId.$secretKey.$player->user_name);

        $data = [
            'OperatorId' => $operatorId,
            'RequestDateTime' => $requestDateTime,
            'Signature' => $signature,
            'PlayerId' => $player->user_name,
            'Ip' => request()->ip(),
            'GameCode' => $gameCode,
            'Currency' => $currency,
            'DisplayName' => $player->name,
            'PlayerBalance' => $player->wallet->balance,
            'LaunchDemo' => $launchDemo,
        ];

        try {

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($apiUrl, $data);

            if ($response->successful()) {
                $apiResponse = $response->json();
                return [
                    'url' => $apiResponse['Url'],
                    'ticket' => $apiResponse['Ticket'],
                ];
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

    public function getBalance(string $authToken, $playerId)
    {
        $operatorId = config('game.api.operator_code');
        $secretKey = config('game.api.secret_key');
        $apiUrl = config('game.api.url').'GetBalance';
        $currency = config('game.api.currency');
        $requestDateTime = now()->setTimezone('UTC')->format('Y-m-d H:i:s');

        // Generate the signature using MD5 hashing
        $signature = md5('GetBalance'.$requestDateTime.$operatorId.$secretKey.$playerId);

        $data = [
            'OperatorId' => $operatorId,
            'RequestDateTime' => $requestDateTime,
            'Signature' => $signature,
            'PlayerId' => $playerId,
            'Currency' => $currency,
            'AuthToken' => $authToken,
        ];

        Log::info('API URL:', ['url' => $apiUrl]);
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
