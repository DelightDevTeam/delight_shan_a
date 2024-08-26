<?php

namespace App\Http\Controllers\Api\Live22;

use App\Enums\StatusCode;
use App\Enums\TransactionName;
use App\Http\Controllers\Api\V1\Live22\Traits\UseWebhook;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceBetWebhookRequest;
use App\Models\User;
use App\Services\PlaceBetWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlaceBetController extends Controller
{
    use UseWebhook;

    public function placeBet(PlaceBetWebhookRequest $request)
    {
        DB::beginTransaction();
        try {
            $validator = $request->check();

            if ($validator->fails()) {
                return $validator->getResponse();
            }

            $before_balance = $request->getMember()->wallet->balance;

            // Create the event using the request data
            $event = $this->createEvent($request);

            // Pass the request as the third argument to createWagerTransactions
            $seamless_transactions = $this->createWagerTransactions(
                $validator->getRequestTransactions(),
                $event,
                $request // Passing the request here
            );

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
            }

            $request->getMember()->wallet->refreshBalance();

            $after_balance = $request->getMember()->balanceFloat;

            DB::commit();

            return PlaceBetWebhookService::buildResponse(
                StatusCode::OK,
                $after_balance,
                $before_balance
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
