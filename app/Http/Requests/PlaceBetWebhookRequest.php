<?php

namespace App\Http\Requests;

use App\Models\Admin\GameType;
use App\Models\User;
use App\Models\GameList;
use Illuminate\Support\Facades\Log;
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
        // 'BetId' => 'required|integer',
        // 'RoundId' => 'required|integer',
        // 'GameCode' => 'required|string',
        // 'GameType' => 'required|string',
        // 'PlayerId' => 'required|string',
        // 'BetAmount' => 'required|numeric',
        // 'Currency' => 'required|string',
        // 'ExchangeRate' => 'required|numeric',
        // 'TranDateTime' => 'required|date_format:Y-m-d H:i:s',
        // 'AuthToken' => 'nullable|string',
        // 'ProviderTimeZone' => 'required|string',
        // 'ProviderTranDt' => 'required|date_format:Y-m-d\TH:i:s.uP',
        // 'OperatorId' => 'required|string',
        // 'RequestDateTime' => 'required|date_format:Y-m-d H:i:s',
        // 'Signature' => 'required|string',
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
        $game_type = GameType::where('name', $this->GetGameType())->first();

        return $game_type->id;
    }

    public function GetGameType()
    {
        return $this->get('GameType');
    }

    public function getBetAmount()
    {
        return $this->get('BetAmount');
    }
    public function getMethodName()
    {
        return str($this->url())->explode('/')->last();
    }

    public  function getExchangeRate()
    {
        return $this->get('ExchangeRate');
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

    public function getPlayerId()
    {
        return $this->get('PlayerId');
    }

    public function getTransactions()
    {
        $transactions = $this->get('Transactions', [$this->all()]);

        // Assuming Status and ProductID are part of the request, add them to the transactions
        foreach ($transactions as &$transaction) {
            $transaction['Status'] = $this->get('Status', 1); // Defaulting to 1 if not provided
            $transaction['ProductID'] = $this->get('ProductID', 'default_product_id'); // Default value if not provided
        }

        // Log the transactions for debugging
        Log::info('Retrieved Transactions', [
            'transactions' => $transactions
        ]);

        return $transactions;
    }

    public function getProviderTimeZone()
    {
        return $this->get('ProviderTimeZone');
    }

    public function getProviderTranDt()
    {
        return $this->get('ProviderTranDt');
    }

    public function getCurrency()
    {
        return $this->get('Currency');
    }

    public function getRoundId()
    {
        return $this->get('RoundId');
    }

    public function getUserId()
    {
        $player = $this->getPlayerId();

        $user = User::where('user_name', $player)->first();

        return $user->id;
    }

    public function getAuthToken()
    {
        return $this->get('AuthToken');
    }
    public  function  getTranDateTime()
    {
        return $this->get('TranDateTime');
    }
}
