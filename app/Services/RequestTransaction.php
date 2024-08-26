<?php

namespace App\Services;

use Spatie\LaravelData\Data;

class RequestTransaction extends Data
{
    public function __construct(
        public int $Status,
        public ?string $ProductID = null,
        public string $GameCode,
        public int $GameType,
        public int $BetId,
        public ?string $TransactionID = null,
        public ?string $WagerID = null,
        public ?float $BetAmount,
        public ?float $TransactionAmount,
        public ?float $PayoutAmount = null,
        public ?float $ValidBetAmount = null,
    ) {}
}
