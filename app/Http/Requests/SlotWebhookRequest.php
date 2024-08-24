<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Services\SlotWebhookValidator;
use Illuminate\Foundation\Http\FormRequest;

class SlotWebhookRequest extends FormRequest
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
        $transaction_rules = [];

        if (in_array($this->getMethodName(), ['getbalance', 'buyin', 'buyout'])) {
            $transaction_rules['Transactions'] = ['nullable'];
            if ($this->getMethodName() !== 'getbalance') {
                $transaction_rules['Transaction'] = ['required'];
            }
        } else {
            $transaction_rules['Transactions'] = ['required'];
        }

        return [
            'PlayerId' => ['required'],
            'OperatorId' => ['required'],
            'RequestDateTime' => ['required'],
            'Signature' => ['required'],
            ...$transaction_rules,
        ];
    }

    public function check()
    {
        $validator = SlotWebhookValidator::make($this)->validate();

        return $validator;
    }

    public function getMember()
    {
        if (! isset($this->member)) {
            $playerId = $this->getMemberName();
            Log::info('Searching for user with PlayerId:', ['PlayerId' => $playerId]);
            $this->member = User::where('user_name', $playerId)->first();

            if (!$this->member) {
                Log::warning('No user found with PlayerId:', ['PlayerId' => $playerId]);
            } else {
                Log::info('User found:', ['UserId' => $this->member->id]);
            }
        }

        return $this->member;
    }

    public function getMemberName()
    {
        return $this->get('PlayerId');
    }

    public function getMethodName()
    {
         return str($this->url())->explode('/')->last();
        //return strtolower(str($this->url())->explode('/')->last());
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
