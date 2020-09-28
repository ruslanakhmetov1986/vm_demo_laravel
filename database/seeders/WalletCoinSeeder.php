<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletCoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::table('wallet_coins')->insert([
            'name' => 1,
            'amount' => 10,
        ]);

        DB::table('wallet_coins')->insert([
            'name' => 2,
            'amount' => 30,
        ]);

        DB::table('wallet_coins')->insert([
            'name' => 5,
            'amount' => 20,
        ]);

        DB::table('wallet_coins')->insert([
            'name' => 10,
            'amount' => 15,
        ]);
    }
}
