<?php

namespace App\Services;

use App\Enums\StatusCode;

class SlotWebhookService
{
    public static function buildResponse(StatusCode $responseCode, $balance, $before_balance)
    {
        return response()->json([
            'Status' => $responseCode->value,
            'Description' => $responseCode->name,
            'ResponseDateTime' => now()->setTimezone('UTC')->format('Y-m-d H:i:s'),
            'Balance' => $balance,
            'BeforeBalance' => $before_balance,
        ], $responseCode->value);
    }
}
