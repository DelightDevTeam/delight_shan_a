<?php

namespace App\Http\Requests;

use App\Models\GameList;
use App\Models\User;
use App\Services\PlaceBetWebhookValidator;
use Illuminate\Foundation\Http\FormRequest;

class PlaceBetWebhookRequest extends FormRequest
{
    private ?User $member;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

        ];
    }

    public function check()
    {
        $validator = PlaceBetWebhookValidator::make($this)->validate();

        return $validator;
    }

    public function getMember()
    {
        $playerId = $this->getMemberName();

        return User::where('user_name', $playerId)->first();
    }

    public function getMemberName()
    {
        return $this->get('PlayerId');
    }

    public function getProductID()
    {
        return $this->get('ProductID');
    }

    public function getGameListID()
    {
        $game_code = $this->GetGameCode();

        return GameList::where('game_code', $game_code)->first();

    }

    public function GetGameCode()
    {
        return $this->get('GameCode');
    }

    public function GetBetID()
    {
        return $this->get('BetId');
    }

    public function getGameTypeID()
    {
        $game_type = $this->GetGameType();

        return GameList::where('game_type', $game_type)->first();
    }

    public function GetGameType()
    {
        return $this->get('GameType');
    }

    public function getMethodName()
    {
        return strtolower(str($this->url())->explode('/')->last());
    }

    public function getOperatorCode()
    {
        return $this->get('OperatorId');
    }

    public function getRequestTime()
    {
        return $this->get('RequestDateTime');
    }

    public function getSign()
    {
        return $this->get('Signature');
    }

    public function test()
    {
        return 'test';
    }

    public function getTransactions()
    {
        $transactions = $this->get('Transactions', []);

        if ($transactions) {
            return $transactions;
        }

        $transaction = $this->get('Transaction', []);

        if ($transaction) {
            return [$transaction];
        }

        return [];
    }
}
