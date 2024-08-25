<?php

namespace App\Services;

use App\Enums\StatusCode;
use Illuminate\Support\Facades\Log;

class SlotWebhookService
{
    // public static function buildResponse(StatusCode $responseCode, $balance, $before_balance)
    // {
    //     Log::info('Building final response', [
    //         'ErrorCode' => $responseCode->value,
    //         'ErrorMessage' => $responseCode->name,
    //         'Balance' => $balance,
    //         'BeforeBalance' => $before_balance,
    //     ]);

    //     return [
    //         'Status' => $responseCode->value,
    //         'Description' => $responseCode->name,
    //         'Balance' => $balance,
    //         //'BeforeBalance' => $before_balance,
    //     ];
    // }

    public static function buildResponse(StatusCode $responseCode, $balance, $before_balance)
    {
        // Current DateTime for ResponseDateTime
        $responseDateTime = now()->format('Y-m-d H:i:s');

        // Map the response code to its exact description
        $description = match ($responseCode) {
            StatusCode::InvalidPlayerPassword => 'Invalid player / password',
            StatusCode::InvalidSignature => 'Invalid Signature',
            default => $responseCode->name,
        };

        // Log the response being built
        Log::info('Building final response', [
            'Status' => $responseCode->value,
            'Description' => $description,
            'ResponseDateTime' => $responseDateTime,
            'Balance' => $balance,
            'BeforeBalance' => $before_balance,
        ]);

        // Return the structured response
        return [
            'Status' => $responseCode->value,
            'Description' => $description,
            'ResponseDateTime' => $responseDateTime,
            'Balance' => $balance,
        ];
    }

    // public static function buildResponse(StatusCode $responseCode, $balance, $before_balance)
    // {
    //     return response()->json([
    //         'Status' => $responseCode->value,
    //         'Description' => $responseCode->name,
    //         'ResponseDateTime' => now()->setTimezone('UTC')->format('Y-m-d H:i:s'),
    //         'Balance' => $balance,
    //         'BeforeBalance' => $before_balance,
    //     ], $responseCode->value);
    // }
}
