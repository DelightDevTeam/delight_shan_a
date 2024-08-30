<?php

namespace App\Http\Controllers\Api\Live22;

use App\Models\User;
use App\Enums\StatusCode;
use App\Traits\UseWebhook;
use Illuminate\Http\Request;
use App\Enums\TransactionName;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PlaceBetWebhookService;
use App\Services\RollBackWebhookService;
use App\Models\Admin\SeamlessTransaction;
use App\Http\Requests\RollBackWebhookRequest;

class RollBackController extends Controller
{
    use UseWebhook;

    public function rollBack(RollBackWebhookRequest $request)
{
    DB::beginTransaction();
    try {
        Log::info('Starting RollBack method');

        // Validate Player
        $player = $request->getMember();
        if (! $player) {
            Log::warning('Invalid player detected', [
                'PlayerId' => $request->getPlayerId(),
            ]);

            // Return Invalid Player response
            return PlaceBetWebhookService::buildResponse(
                StatusCode::InvalidPlayerPassword,
                0, // Balance is 0 in case of invalid player
                0
            );
        }

        $oldBalance = $player->wallet->balance;
        Log::info('Retrieved member balance', ['old_balance' => $oldBalance]);

        $validator = $request->check();
        Log::info('Validator check passed');

        if ($validator->fails()) {
            Log::warning('Validation failed');

            return RollBackWebhookService::buildResponse(
                StatusCode::InvalidSignature,
                0,
                0
            );
        }

        // Check if a transaction with the same BetId and RollbackType already exists
        $existingTransaction = SeamlessTransaction::where('bet_id', $request->getBetId())
            ->where('rollback_type', $request->getRollbackType())
            ->first();
            
        if ($existingTransaction) {
            Log::warning('Duplicate Rollback BetId detected', ['rollback_type' => $request->getRollbackType()]);

            // Return the duplicate transaction response
            return RollBackWebhookService::buildResponse(
                StatusCode::DuplicateTransaction,
                0,
                0,
            );
        }

        // Process Transfer
        $this->processTransfer(
            $player,
            User::adminUser(),
            TransactionName::Rollback,
            $request->getBetAmount(),
            $request->getExchangeRate()
        );

        $newBalance = $player->wallet->refreshBalance()->balance;

        // Create the rollback transaction
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
            'rollback_type' => $request->getRollbackType()
        ]);

        Log::info('Refreshed member balance', ['new_balance' => $newBalance]);

        DB::commit();
        Log::info('Transaction committed successfully');

        return RollBackWebhookService::buildResponse(
            StatusCode::OK,
            number_format($oldBalance, 4, '.', ''),
            number_format($newBalance + $request->getBetAmount(), 4, '.', '')
        );
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to place bet', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);

        return response()->json(['message' => 'Failed to Rollback'], 500);
    }
}

}
