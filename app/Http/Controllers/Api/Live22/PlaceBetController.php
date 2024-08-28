<?php
namespace App\Http\Controllers\Api\Live22;

use App\Models\User;
use App\Enums\StatusCode;
use App\Traits\UseWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Enums\TransactionName;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\SeamlessEvent;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PlaceBetWebhookService;
use App\Models\Admin\SeamlessTransaction;
use App\Http\Requests\PlaceBetWebhookRequest;

class PlaceBetController extends Controller
{
    use UseWebhook;

    public function placeBet(PlaceBetWebhookRequest $request)
{
    DB::beginTransaction();
    try {
        Log::info('Starting placeBet method');

        $validator = $request->check();
        Log::info('Validator check passed');

        if ($validator->fails()) {
            Log::warning('Validation failed');
            return $validator->getResponse();
        }

        $oldBalance = $request->getMember()->wallet->balance;
        $newBalance = $request->getMember()->wallet->balance;

          $oldBalance = $request->getMember()->wallet->balance;
            Log::info('Retrieved member balance', ['old_balance' => $oldBalance]);

            // Check for duplicate BetId
            $existingTransaction = SeamlessTransaction::where('bet_id', $request->getBetId())->first();
            if ($existingTransaction) {
                Log::warning('Duplicate BetId detected', ['bet_id' => $request->getBetId()]);

                // Return the duplicate bet response
                return PlaceBetWebhookService::buildResponse(
                    StatusCode::DuplicateTransaction,
                    $oldBalance,
                    $oldBalance - $request->getBetAmount()
                );
            }

        Log::info('Retrieved member balance', ['old_balance' => $oldBalance]);

            $this->processTransfer(
                $request->getMember(),
                User::adminUser(),
                TransactionName::Stake,
                $request->getBetAmount(),
                $request->getExchangeRate(),
            );

            SeamlessTransaction::create([
            'user_id' => $request->getUserId(),
            'game_type_id' => $request->getGameTypeID(),
            'transaction_amount' => $request->getBetAmount(),
            'valid_amount' => $request->getBetAmount(),
            'operator_id' => $request->getOperatorCode(),
            'request_date_time' => $request->getRequestTime(),
            'signature' => $request->getSign(),
            'player_id' => $request->getPlayerId(),
            'currency' => $request->getCurrency(),
            'round_id' => $request->getRoundId(),
            'bet_id' => $request->getBetId(),
            'bet_amount' => $request->getBetAmount(),
            'exchange_rate' => $request->getExchangeRate(),
            'game_code' => $request->getGameCode(),
            'tran_date_time' => $request->getTranDateTime(),
            'auth_token' => $request->getAuthToken(),
            'provider_time_zone' => $request->getProviderTimeZone(),
            'provider_tran_dt' => $request->getProviderTranDt(),
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
        ]);
        Log::info('Refreshed member balance', ['new_balance' => $newBalance]);

        DB::commit();
        Log::info('Transaction committed successfully');

        return PlaceBetWebhookService::buildResponse(
            StatusCode::OK,
            $oldBalance,
            $newBalance
        );
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to place bet', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);
        return response()->json(['message' => 'Failed to place bet'], 500);
    }
}

}
