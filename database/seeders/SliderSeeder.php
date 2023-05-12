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
        $faker = Faker::create();
        $path = '/slider/' . $faker->userName . '.jpg';
        Storage::disk('public')->put('/uploads/' . $path, file_get_contents($faker->imageUrl(400, 400, 'people')));
        DB::table('sliders')->insert([
            ['name' => 'Facebook','url'=> 'https://www.facebook.com/' ,'file' => $path, 'active' => 1],
            ['name' => 'Instagram','url'=> 'https://www.instagram.com/' ,'file' => $path, 'active' => 1],
            ['name' => 'Tik Tok','url'=> 'https://www.tiktok.com/en/' ,'file' => $path, 'active' => 1],
        ]);
    }
}
