<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->insert([
            ['name' => 'Nước giải khát','parent_id'=> 0 ,'sub_id' => 0,'active' => 1],
            ['name' => 'Cà Phê','parent_id'=> 0 ,'sub_id' => 0,'active' => 1],
            ['name' => 'Nước Ép','parent_id'=> 0 ,'sub_id' => 0,'active' => 1],
        ]);
    }
}
