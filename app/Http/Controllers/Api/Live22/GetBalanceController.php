<?php 
namespace App\Http\Controllers\Api\Live22;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\SlotWebhookRequest;
use App\Services\SlotWebhookService;
use App\Services\SlotWebhookValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetBalanceController extends Controller
{
    public function getBalance(SlotWebhookRequest $request)
    {
        Log::info('GetBalance request initiated', ['request_data' => $request->all()]);

        DB::beginTransaction();
        try {
            // Validate the request using the SlotWebhookValidator
            Log::info('Starting validation process');
            $validator = SlotWebhookValidator::make($request)->validate();

            if ($validator->fails()) {
                Log::warning('Validation failed', ['response' => $validator->getResponse()]);
                return response()->json($validator->getResponse(), 400);
            } else {
                Log::info('Validation passed, no failure detected');
            }


            Log::info('Validation passed, preparing balance response');
            $balance = $request->getMember()->wallet->balance;
            $response = SlotWebhookService::buildResponse(StatusCode::OK, $balance, $balance);

            Log::info('Returning response', ['response' => $response]);

            DB::commit();
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An error occurred during GetBalance', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
