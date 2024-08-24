<?php 
namespace App\Services;

use App\Enums\StatusCode;
use App\Models\Admin\Wager;
use Illuminate\Support\Facades\Log;
use App\Services\RequestTransaction;
use App\Services\SlotWebhookService;
use App\Http\Requests\SlotWebhookRequest;
use App\Models\Admin\SeamlessTransaction;

class SlotWebhookValidator
{
    protected ?SeamlessTransaction $existingTransaction = null;
    protected ?Wager $existingWager = null;
    protected float $totalTransactionAmount = 0;
    protected float $before_balance = 0;
    protected float $after_balance = 0;
    protected array $response = [];

    /**
     * @var RequestTransaction[]
     */
    protected $requestTransactions = [];

    protected function __construct(protected SlotWebhookRequest $request) 
    {
        Log::info('SlotWebhookValidator initialized', ['request' => $request->all()]);
    }

    public function validate()
    {
        Log::info('Starting validation');

        if (! $this->isValidSignature()) {
            Log::warning('Invalid signature detected');
            return $this->response(StatusCode::InvalidSignature);
        }

        if (! $this->request->getMember()) {
            Log::warning('Invalid player detected');
            return $this->response(StatusCode::InvalidPlayer);
        }

        foreach ($this->request->getTransactions() as $transaction) {
            Log::info('Processing transaction', ['transaction' => $transaction]);

            $requestTransaction = RequestTransaction::from($transaction);
            $this->requestTransactions[] = $requestTransaction;

            if (! in_array($this->request->getMethodName(), ['bet', 'buyin', 'buyout']) && $this->isNewWager($requestTransaction)) {
                Log::warning('Invalid game ID detected', ['transaction' => $requestTransaction]);
                return $this->response(StatusCode::InvalidGameId);
            }

            $this->totalTransactionAmount += $requestTransaction->TransactionAmount;
        }

        Log::info('Validation passed');
        return $this;
    }

    protected function isValidSignature()
{
    //$method = $this->request->getMethodName();
    $method = 'GetBalance';
    $requestTime = $this->request->getRequestTime();
    $operatorCode = $this->request->getOperatorCode();
    $secretKey = $this->getSecretKey();
    $playerId = $this->request->getMemberName();

    // Log the values used for signature generation
    Log::info('Generating signature', [
        'method' => $method,
        'requestTime' => $requestTime,
        'operatorCode' => $operatorCode,
        'secretKey' => $secretKey,
        'playerId' => $playerId,
    ]);

    // Generate the signature
    $signature = md5($method . $requestTime . $operatorCode . $secretKey . $playerId);
    
    Log::info('Generated signature', ['signature' => $signature]);

    return $this->request->getSign() === $signature;
}


    protected function isNewWager(RequestTransaction $transaction)
    {
        Log::info('Checking if wager is new', ['wagerId' => $transaction->WagerID]);
        return ! $this->getExistingWager($transaction);
    }

    public function getExistingWager(RequestTransaction $transaction)
    {
        if (! isset($this->existingWager)) {
            $this->existingWager = Wager::where('seamless_wager_id', $transaction->WagerID)->first();
            Log::info('Existing wager fetched', ['existingWager' => $this->existingWager]);
        }

        return $this->existingWager;
    }

    protected function isNewTransaction(RequestTransaction $transaction)
    {
        Log::info('Checking if transaction is new', ['transactionId' => $transaction->TransactionID]);
        return ! $this->getExistingTransaction($transaction);
    }

    public function getExistingTransaction(RequestTransaction $transaction)
    {
        if (! isset($this->existingTransaction)) {
            $this->existingTransaction = SeamlessTransaction::where('seamless_transaction_id', $transaction->TransactionID)->first();
            Log::info('Existing transaction fetched', ['existingTransaction' => $this->existingTransaction]);
        }

        return $this->existingTransaction;
    }

    public function getAfterBalance()
    {
        if (! isset($this->after_balance)) {
            $this->after_balance = $this->getBeforeBalance() + $this->totalTransactionAmount;
            Log::info('Calculated after balance', ['after_balance' => $this->after_balance]);
        }

        return $this->after_balance;
    }

    public function getBeforeBalance()
    {
        if (! isset($this->before_balance)) {
            $this->before_balance = $this->request->getMember()->wallet->balance;
            Log::info('Fetched before balance', ['before_balance' => $this->before_balance]);
        }

        return $this->before_balance;
    }

    protected function hasEnoughBalance()
    {
        $hasEnough = $this->getAfterBalance() >= 0;
        Log::info('Checking if user has enough balance', ['hasEnough' => $hasEnough]);

        return $hasEnough;
    }

    public function getRequestTransactions()
    {
        Log::info('Returning request transactions', ['transactions' => $this->requestTransactions]);
        return $this->requestTransactions;
    }

    protected function getSecretKey()
    {
        $secretKey = config('game.api.secret_key');
        Log::info('Fetched secret key');
        return $secretKey;
    }

    protected function response(StatusCode $responseCode)
    {
        Log::info('Building response', ['responseCode' => $responseCode->name]);

        $this->response = SlotWebhookService::buildResponse(
            $responseCode,
            $this->request->getMember() ? $this->getAfterBalance() : 0,
            $this->request->getMember() ? $this->getBeforeBalance() : 0
        );

        return $this;
    }

    public function getResponse()
    {
        Log::info('Returning response', ['response' => $this->response]);
        return $this->response;
    }

    public function fails()
    {
        $fails = isset($this->response);
        Log::info('Checking if validation fails', ['fails' => $fails]);

        return $fails;
    }

    public static function make(SlotWebhookRequest $request)
    {
        return new self($request);
    }
}
