<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VmSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::table('vm_settings')->insert([
            'key' => 'deposit',
            'val' => 0,
        ]);

        DB::table('vm_settings')->insert([
            'key' => 'user_wallet_total',
            'val' => 0,
        ]);

        DB::table('vm_settings')->insert([
            'key' => 'vm_bank_total',
            'val' => 0,
        ]);
    }
}
