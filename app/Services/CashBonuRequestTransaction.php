<?php

namespace App\Services;

use Spatie\LaravelData\Data;

class CashBonuRequestTransaction extends Data
{
    public function __construct(
        public int $Status,
        public ?string $ProductID = null,
        public ?string $GameCode = null,
        public ?string $GameType = null,
        public int $BetId,
        public ?string $TransactionID = null,
        public ?string $WagerID = null,
        public ?float $BetAmount = null,
        public ?float $TransactionAmount,
        public ?float $PayoutAmount = null,
        public ?float $ValidBetAmount = null
    ) {}
}
