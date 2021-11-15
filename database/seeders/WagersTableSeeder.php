<?php

namespace Database\Seeders;

use App\Models\Wager;
use Illuminate\Database\Seeder;

class WagersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Wager::factory()
            ->count(200)
            ->create();
    }
}
