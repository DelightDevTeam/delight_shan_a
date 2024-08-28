<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_results', function (Blueprint $table) {
            $table->id();
            $table->string('game_type');
            $table->string('game_name');
            $table->unsignedBigInteger('game_id');
            $table->string('result_type');
            $table->string('game_code');
            $table->decimal('bet_amount', 15, 4);
            $table->decimal('valid_bet_amount', 15, 4);
            $table->decimal('payout');
            $table->decimal('win_lose');
            $table->decimal('exchange_rate', 8, 4)->default('1.0000');
            $table->dateTime('tran_date_time');
            $table->string('auth_token', 500)->nullable();
            $table->string('provider_time_zone', 50);
            $table->dateTime('provider_tran_dt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_results');
    }
};
