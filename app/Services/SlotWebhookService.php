<?php

namespace App\Services;

use App\Enums\StatusCode;
use Illuminate\Support\Facades\Log;

class SlotWebhookService
{
    public static function buildResponse(StatusCode $responseCode, $balance, $before_balance)
    {
        // Current DateTime for ResponseDateTime
        $responseDateTime = now()->format('Y-m-d H:i:s');

        // Format the balance to four decimal places
        $formattedBalance = number_format((float)$balance, 4, '.', '');
        $formattedBeforeBalance = number_format((float)$before_balance, 4, '.', '');

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
            'Balance' => $formattedBalance,
            'BeforeBalance' => $formattedBeforeBalance,
        ]);

        // Return the structured response
        return [
            'Status' => $responseCode->value,
            'Description' => $description,
            'ResponseDateTime' => $responseDateTime,
            'Balance' => $formattedBalance,
        ];
    }
    // public static function buildResponse(StatusCode $responseCode, $balance, $before_balance)
    // {
    //     // Current DateTime for ResponseDateTime
    //     $responseDateTime = now()->format('Y-m-d H:i:s');

    //     // Map the response code to its exact description
    //     $description = match ($responseCode) {
    //         StatusCode::InvalidPlayerPassword => 'Invalid player / password',
    //         StatusCode::InvalidSignature => 'Invalid Signature',
    //         default => $responseCode->name,
    //     };

    //     // Log the response being built
    //     Log::info('Building final response', [
    //         'Status' => $responseCode->value,
    //         'Description' => $description,
    //         'ResponseDateTime' => $responseDateTime,
    //         'Balance' => $balance,
    //         'BeforeBalance' => $before_balance,
    //     ]);

    //     // Return the structured response
    //     return [
    //         'Status' => $responseCode->value,
    //         'Description' => $description,
    //         'ResponseDateTime' => $responseDateTime,
    //         'Balance' => $balance,
    //     ];
    // }

    
}
