<?php
namespace App\Http\Controllers\Api\Live22;

use App\Http\Requests\GameResultWebhookRequest;
use App\Models\User;
use App\Enums\StatusCode;
use App\Services\GameResultWebhookService;
use App\Services\GameResultWebhookValidator;
use App\Traits\UseWebhook;
use Illuminate\Http\Request;
use App\Enums\TransactionName;
use App\Models\Admin\GameResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PlaceBetWebhookService;
use App\Http\Requests\PlaceBetWebhookRequest;

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

                // Return Invalid Player response
                return GameResultWebhookService::buildResponse(
                    StatusCode::InvalidPlayerPassword,
                    0, // Balance is 0 in case of invalid player
                    0
                );
            }

            $oldBalance = $player->wallet->balance;
            Log::info('Retrieved member balance', ['old_balance' => $oldBalance]);

            $validator = $request->check();

            if ($validator->fails()) {
                    return $validator->getResponse();
            }

            // Check for duplicate ResultId
            $existingTransaction = GameResult::where('result_id', $request->ResultID())->first();
            if ($existingTransaction) {
                Log::warning('Duplicate ResultID detected', ['result_id' => $request->ResultID()]);

                // Return the duplicate result response
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
                'bet_id' => $request->GetBetID(),
                'round_id' => $request->getRoundId(),
                'game_code' => $request->GetGameCode(),
                'game_type' => $request->GetGameType(),
                'game_name' => $request->input('GameName'), // Assuming it's coming in the request
                'result_type' => $request->GetResultType(),
                'bet_amount' => $request->getBetAmount(),
                'valid_bet_amount' => $request->input('ValidBetAmount'), // Assuming it's coming in the request
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
