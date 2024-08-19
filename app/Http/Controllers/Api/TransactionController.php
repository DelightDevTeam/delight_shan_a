<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Models\Admin\Deposit;
use App\Models\Admin\ReportTransaction;
use App\Models\Admin\WithdrawRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TransactionController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        foreach ($request->players as $playerData) {
            $player = User::findOrFail($playerData['player_id']);

            try {
                ReportTransaction::create([
                    'user_id' => $playerData['player_id'],
                    'game_type_id' => $request->game_type_id,
                    'transaction_amount' => $playerData['amount_changed'],
                    'status' => $playerData['win_lose_status'],
                    'bet_amount' => $playerData['bet_amount'],
                    'valid_amount' => $playerData['bet_amount'],
                ]);
                if ($playerData['win_lose_status'] == 1) {
                    $player->wallet->balance += $playerData['amount_changed'];
                    $player->wallet->save();
                } else {
                    $player->wallet->balance -= $playerData['amount_changed'];
                    $player->wallet->save();
                }

                $result[] = ['player_id' => $player->id, 'balance' => $player->wallet->balance];
            } catch (\Exception $e) {
                return $this->error('Transaction failed', $e->getMessage(), 500);
            }
        }

        return $this->success($result, 'Transaction Success');
    }
}
