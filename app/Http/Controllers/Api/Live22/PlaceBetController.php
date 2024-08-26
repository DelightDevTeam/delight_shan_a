<?php 
namespace App\Http\Controllers\Api\Live22;

use App\Models\User;
use App\Enums\StatusCode;
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

        $before_balance = $request->getMember()->wallet->balance;
        Log::info('Retrieved member balance', ['before_balance' => $before_balance]);

        $event = $this->createEvent($request);
        Log::info('SeamlessEvent created', ['event_id' => $event->id]);

        $seamless_transactions = $this->createWagerTransactions(
            $validator->getRequestTransactions(),
            $event,
            $request // Passing the request here
        );
        Log::info('Wager transactions created', ['transaction_count' => count($seamless_transactions)]);

        foreach ($seamless_transactions as $seamless_transaction) {
            $this->processTransfer(
                $request->getMember(),
                User::adminUser(),
                TransactionName::Stake,
                $seamless_transaction->transaction_amount,
                $seamless_transaction->rate,
                [
                    'wager_id' => $seamless_transaction->wager_id,
                    'event_id' => $request->getMessageID(),
                    'seamless_transaction_id' => $seamless_transaction->id,
                ]
            );
            Log::info('Processed transfer for transaction', ['transaction_id' => $seamless_transaction->id]);
        }

        $request->getMember()->wallet->refreshBalance();
        $after_balance = $request->getMember()->balanceFloat;
        Log::info('Refreshed member balance', ['after_balance' => $after_balance]);

        DB::commit();
        Log::info('Transaction committed successfully');

        return PlaceBetWebhookService::buildResponse(
            StatusCode::OK,
            $after_balance,
            $before_balance
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
