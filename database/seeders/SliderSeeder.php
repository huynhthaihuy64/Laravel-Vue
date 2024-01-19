<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sliders')->insert([
            ['name' => 'Facebook','url'=> 'https://www.facebook.com/' ,'file' => '/storage/Slider/facebook.jpeg', 'active' => 1, 'sort_by' => 1],
            ['name' => 'Instagram','url'=> 'https://www.instagram.com/' ,'file' => '/storage/Slider/Instagram.png', 'active' => 1, 'sort_by' => 2],
            ['name' => 'Tik Tok','url'=> 'https://www.tiktok.com/en/' ,'file' => '/storage/Slider/download.png', 'active' => 1, 'sort_by' => 1],
            ['name' => 'Laravel', 'url' => 'https://laravel.com/', 'file' => '/storage/Slider/laravel.png', 'active' => 1, 'sort_by' => 1],
        ]);
    }
}
