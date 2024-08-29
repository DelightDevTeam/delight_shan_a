<?php
namespace App\Http\Controllers\Api\Live22;

use App\Models\User;
use App\Enums\StatusCode;
use App\Traits\UseWebhook;
use Illuminate\Http\Request;
use App\Enums\TransactionName;
use App\Models\Admin\GameResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PlaceBetWebhookService;
use App\Models\Admin\SeamlessTransaction;
use App\Services\GameResultWebhookService;
use App\Services\GameResultWebhookValidator;
use App\Http\Requests\PlaceBetWebhookRequest;
use App\Http\Requests\GameResultWebhookRequest;

class GameResultController extends Controller
{
    use UseWebhook;

    
    public function gameResult(GameResultWebhookRequest $request)
{
    DB::beginTransaction();
    try {
        Log::info('Starting GameResult method');

        // Validate Player
        $player = $request->getMember();
        if (!$player) {
            Log::warning('Invalid player detected', [
                'PlayerId' => $request->getPlayerId()
            ]);

            return GameResultWebhookService::buildResponse(
                StatusCode::InvalidPlayerPassword,
                0,
                0
            );
        }

        $oldBalance = $player->wallet->balance;
        Log::info('Retrieved member balance', ['old_balance' => $oldBalance]);

        $validator = $request->check();
        if ($validator->fails()) {
            return $validator->getResponse();
        }

        //$existingTransaction = SeamlessTransaction::where('bet_id', $request->getBetId())->first();
          $existingTransaction = 20000017109;
          Log::info('existing_transaction'. $existingTransaction);
        // Log::info('Checking existing transaction', [
        //     'bet_id' => $request->getBetId(),
        //     'existing_transaction' => $existingTransaction ? $existingTransaction->toArray() : null
        // ]);

        if (!$existingTransaction) {
            Log::warning('BetId not found in SeamlessTransaction', [
                'bet_id' => $request->getBetId(),
                'seamless_transactions_count' => SeamlessTransaction::count(),
                'seamless_transactions_last_entry' => SeamlessTransaction::orderBy('created_at', 'desc')->first()
            ]);

            return GameResultWebhookService::buildResponse(
                StatusCode::BetTransactionNotFound,
                $oldBalance,
                $oldBalance
            );
        }

        // Check for duplicate ResultId
        $existingResult = GameResult::where('result_id', $request->ResultID())->first();
        if ($existingResult) {
            Log::warning('Duplicate ResultID detected', ['result_id' => $request->ResultID()]);

            return GameResultWebhookService::buildResponse(
                StatusCode::DuplicateTransaction,
                $oldBalance,
                $oldBalance
            );
        }

        // Process Transfer
        $this->processTransfer(
            $player,
            User::adminUser(),
            TransactionName::Stake,
            $request->getBetAmount(),
            $request->getExchangeRate()
        );

        $newBalance = $player->wallet->refreshBalance()->balance;
        GameResult::create([
            'user_id' => $request->getUserId(),
            'operator_id' => $request->getOperatorCode(),
            'request_date_time' => $request->getRequestTime(),
            'signature' => $request->getSign(),
            'player_id' => $request->getPlayerId(),
            'currency' => $request->getCurrency(),
            'result_id' => $request->ResultID(),
            'bet_id' => $request->getBetId(),
            'round_id' => $request->getRoundId(),
            'game_code' => $request->GetGameCode(),
            'game_type' => $request->GetGameType(),
            'game_name' => $request->input('GameName'),
            'result_type' => $request->GetResultType(),
            'bet_amount' => $request->getBetAmount(),
            'valid_bet_amount' => $request->input('ValidBetAmount'),
            'payout' => $request->getPayout(),
            'win_lose' => $request->getWinLose(),
            'exchange_rate' => $request->getExchangeRate(),
            'tran_date_time' => $request->getTranDateTime(),
            'provider_time_zone' => $request->getProviderTimeZone(),
            'provider_tran_dt' => $request->getProviderTranDt(),
            'round_type' => $request->getRoundType(),
        ]);

        Log::info('Refreshed member balance', ['new_balance' => $newBalance]);

        DB::commit();
        Log::info('Transaction committed successfully');

        return GameResultWebhookService::buildResponse(
            StatusCode::OK,
            $oldBalance,
            $newBalance
        );
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to game result', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);
        return response()->json(['message' => 'Failed to game result'], 500);
    }
}

}
