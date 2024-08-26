<?php 
namespace App\Http\Controllers\Api\Live22;

use App\Models\Admin\SeamlessTransaction;
use App\Models\Admin\SeamlessEvent;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PlaceBetWebhookRequest;

class PlaceBetController extends Controller
{
    public function placeBet(PlaceBetWebhookRequest $request)
    {
        DB::beginTransaction();
        try {
            // Retrieve the user based on the PlayerId
            $user = $request->getMember();

            if (!$user) {
                return response()->json(['message' => 'Invalid player'], 400);
            }

            // Create a SeamlessEvent (if required)
            $event = SeamlessEvent::create([
                'user_id' => $user->id,
                'game_type_id' => null, // Set this to null or fetch from another source if needed
                'game_list_id' => null, // Set this to null or fetch from another source if needed
                'request_time' => $request->getRequestTime(),
                'raw_data' => $request->all(),
            ]);

            // Store the transaction data in the seamless_transactions table
            $transaction = SeamlessTransaction::create([
                'seamless_event_id' => $event->id,
                'user_id' => $user->id,
                'game_type_id' => null, // Optional field, set to null
                'wager_id' => null, // Optional field, set to null
                'seamless_transaction_id' => null, // Optional field, set to null
                'transaction_amount' => null, // Optional field, set to null
                'valid_amount' => null, // Optional field, set to null
                'operator_id' => $request->get('OperatorId'),
                'request_date_time' => $request->get('RequestDateTime'),
                'signature' => $request->get('Signature'),
                'player_id' => $request->get('PlayerId'),
                'currency' => $request->get('Currency'),
                'round_id' => $request->get('RoundId'),
                'bet_id' => $request->get('BetId'),
                'bet_amount' => $request->get('BetAmount'),
                'exchange_rate' => $request->get('ExchangeRate'),
                'game_code' => $request->get('GameCode'),
                'game_type' => $request->get('GameType'),
                'tran_date_time' => $request->get('TranDateTime'),
                'auth_token' => $request->get('AuthToken'),
                'provider_time_zone' => $request->get('ProviderTimeZone'),
                'provider_tran_dt' => $request->get('ProviderTranDt'),
                'old_balance' => null, // Optional field, set to null
                'new_balance' => null, // Optional field, set to null
                'status' => 'Pending', // Default status
            ]);

            DB::commit();

            return response()->json(['message' => 'Bet placed successfully', 'transaction_id' => $transaction->id], 200);
        } catch (\Exception $e) {
    DB::rollBack();
    
    // Log the error with additional details
    Log::error('Failed to place bet', [
        'error' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile(),
    ]);

    return response()->json(['message' => 'Failed to place bet'], 500);
}
    }
}
