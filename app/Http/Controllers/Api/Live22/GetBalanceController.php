<?php

namespace App\Http\Controllers\Api\Live22;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\SlotWebhookRequest;
use App\Services\GameService;
use App\Services\SlotWebhookService;
use App\Services\SlotWebhookValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetBalanceController extends Controller
{
    public function getBalance(SlotWebhookRequest $request)
    {
        //return 'this is direct respond';
        Log::info('GetBalance request initiated', ['request_data' => $request->all()]);

        DB::beginTransaction();
        try {
            // Validate the request using the SlotWebhookValidator
            Log::info('Starting validation process');
            $validator = SlotWebhookValidator::make($request)->validate();

            Log::info('Validation passed, preparing balance response');
            $balance = $request->getMember()->wallet->balance;
            $response = SlotWebhookService::buildResponse(StatusCode::OK, $balance, $balance);

            Log::info('Returning response', ['response' => $response]);

            return response()->json($response);

            // Get the balance from the member's wallet
            // $member = $request->getMember();
            // if ($member && $member->wallet) {
            //     $balance = $member->wallet->balance;
            //     Log::info('Retrieved member balance', ['balance' => $balance]);
            // } else {
            //     Log::warning('Member or wallet not found');
            //     return response()->json([
            //         'message' => 'Member or wallet not found',
            //     ], 404);
            // }

            DB::commit();

            // Build and return the success response
            Log::info('Returning successful response');

            return SlotWebhookService::buildResponse(
                StatusCode::OK,
                $balance,
                $balance
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An error occurred during GetBalance', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Handle any unexpected exceptions and return an error message
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
