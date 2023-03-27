<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_roles')->insert([
            ['name' => 'Supper Admin'],
            ['name' => 'Admin'],
            ['name' => 'Member']
        ]);
        // \App\Models\User::factory(10)->create();
    }
}
