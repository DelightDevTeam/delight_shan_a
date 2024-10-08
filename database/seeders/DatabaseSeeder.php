<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            BannerSeeder::class,
            BannerTextSeeder::class,
            PaymentTypeSeeder::class,
            GameTypeSeeder::class,
            ProductTableSeeder::class,
            GameTypeProductTableSeeder::class,
            Live22GameListSeeder::class,
            BankTableSeeder::class,
            MediaTypeSeeder::class,
        ]);
    }
}
