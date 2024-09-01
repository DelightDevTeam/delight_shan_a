<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DemoGameListService
{
    /**
     * Retrieve the game list for demo play.
     *
     * @param string $lang Optional language code.
     * @return array
     */
    public function getDemoGameList(string $lang = 'en-us')
    {
        $baseUrl = config('game.api.url'); // Ensure this config path is correctly defined in your 'config/game.php'
        $operatorId = config('game.api.operator_code'); // Operator ID from config

        $url = "{$baseUrl}/demo/GameList?opId={$operatorId}&lang={$lang}";

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Successfully fetched demo game list', ['data' => $data]);
                return $data;
            }

            Log::error('Failed to fetch demo game list', ['response' => $response->body()]);
            return [
                'error' => true,
                'message' => 'Failed to fetch demo game list',
                'details' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching demo game list', ['exception' => $e->getMessage()]);
            return [
                'error' => true,
                'message' => 'Exception occurred while fetching demo game list',
                'exception' => $e->getMessage()
            ];
        }
    }
}
