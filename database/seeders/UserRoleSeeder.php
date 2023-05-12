<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_roles')->insert([
            ['name' => 'Supper Admin','code'=>'supper_admin'],
            ['name' => 'Admin','code'=>'admin'],
            ['name' => 'Member','code'=>'member'],
        ]);
    }
}
