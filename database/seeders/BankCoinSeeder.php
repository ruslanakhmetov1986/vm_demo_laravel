<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankCoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::table('bank_coins')->insert([
            'name' => 1,
            'amount' => 100,
        ]);

        DB::table('bank_coins')->insert([
            'name' => 2,
            'amount' => 100,
        ]);

        DB::table('bank_coins')->insert([
            'name' => 5,
            'amount' => 100,
        ]);

        DB::table('bank_coins')->insert([
            'name' => 10,
            'amount' => 100,
        ]);

    }
}
