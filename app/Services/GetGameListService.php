<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetGameListService
{
    protected $operatorId;
    protected $secretKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->operatorId = config('game.api.operator_id');
        $this->secretKey = config('game.api.secret_key');
        $this->apiUrl = config('game.api.url') . '/GetGameList';
    }

    public function fetchGames()
    {
        $requestDateTime = now()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $signature = $this->generateSignature($requestDateTime);

        $response = Http::post($this->apiUrl, [
            'OperatorId' => $this->operatorId,
            'RequestDateTime' => $requestDateTime,
            'Signature' => $signature
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::error('Failed to fetch game list', ['response' => $response->body()]);
            throw new \Exception("Error fetching games: " . $response->body());
        }
    }

    protected function generateSignature($requestDateTime)
    {
        return md5('GetGameList' . $requestDateTime . $this->operatorId . $this->secretKey);
    }
}
