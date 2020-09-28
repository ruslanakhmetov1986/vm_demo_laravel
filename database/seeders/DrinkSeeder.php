<?php

namespace Database\Seeders;

use App\Models\Drink;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DrinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::table('drinks')->insert([
            'name' => 'Чай',
            'amount' => 10,
            'price' => 13,
        ]);

        DB::table('drinks')->insert([
            'name' => 'Кофе',
            'amount' => 18,
            'price' => 20,
        ]);

        DB::table('drinks')->insert([
            'name' => 'Кофе с молоком',
            'amount' => 21,
            'price' => 20,
        ]);

        DB::table('drinks')->insert([
            'name' => 'Сок',
            'amount' => 35,
            'price' => 15,
        ]);

    }
}
