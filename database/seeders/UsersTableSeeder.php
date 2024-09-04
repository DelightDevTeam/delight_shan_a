<?php

namespace Database\Seeders;

use App\Enums\TransactionName;
use App\Enums\UserType;
use App\Models\Admin\Wallet;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $admin = $this->createUser(UserType::Admin, 'Owner', 'shan', '09123456789');
        (new WalletService)->deposit($admin, 10 * 100_000, TransactionName::CapitalDeposit);

        $senior = $this->createUser(UserType::Senior, 'Senior', 'SKM34564', '09112345674', $admin->id);
        (new WalletService)->transfer($admin, $senior, 5 * 100_000, TransactionName::CreditTransfer);

        $master = $this->createUser(UserType::Master, 'Master', 'SKM234343', '09112345674', $senior->id);
        (new WalletService)->transfer($senior, $master, 5 * 100_000, TransactionName::CreditTransfer);

        $agent = $this->createUser(UserType::Agent, 'Agent', 'SKM324343', '09112345674', $master->id);
        (new WalletService)->transfer($master, $agent, 5 * 100_000, TransactionName::CreditTransfer);

        $player = $this->createUser(UserType::Player, 'Player 1', 'Player001', '09111111111', $agent->id);
        (new WalletService)->transfer($agent, $player, 30000.0000, TransactionName::CreditTransfer);
    }

    private function createUser(UserType $type, string $name, string $user_name, string $phone, ?int $parent_id = null, ?string $referral_code = null): User
    {
        $user = User::create([
            'name' => $name,
            'user_name' => $user_name,
            'phone' => $phone,
            'password' => Hash::make('delightmyanmar'),
            'agent_id' => $parent_id,
            'status' => 1,
            'type' => $type->value,
        ]);

        // Create a wallet for the user
        Wallet::create(['user_id' => $user->id]);

        return $user;
    }
}
