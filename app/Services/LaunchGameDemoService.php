<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class LaunchGameDemoService
{
    /**
     * Build and redirect to the game demo URL.
     *
     * @param array $params
     * @return \Illuminate\Http\RedirectResponse
     */
    public function launchGameDemo(array $params)
    {
        $baseUrl = config('game.api.url'); // Make sure to define this in your configuration
        $url = $this->buildDemoUrl($baseUrl, $params);

        // Log the URL for audit purposes
        Log::info("Redirecting to game demo URL: {$url}");

        // Perform the redirect
        return redirect()->away($url);
    }

    /**
     * Build the demo game URL from parameters.
     *
     * @param string $baseUrl
     * @param array $params
     * @return string
     */
    protected function buildDemoUrl(string $baseUrl, array $params)
    {
        // Validate required parameters
        $requiredParams = ['opId', 'currency', 'gameCode'];
        foreach ($requiredParams as $param) {
            if (!array_key_exists($param, $params)) {
                throw new \InvalidArgumentException("Missing required parameter: {$param}");
            }
        }

        // Construct the query string
        $query = http_build_query([
            'opId' => $params['opId'],
            'currency' => $params['currency'],
            'gameCode' => $params['gameCode'],
            'redirectUrl' => $params['redirectUrl'] ?? null,
            'lang' => $params['lang'] ?? 'en-us' // Defaulting to English if not specified
        ]);

        return "{$baseUrl}?{$query}";
    }
}
