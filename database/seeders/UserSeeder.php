<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $path = '/avatar/' . $faker->userName . '.jpg';
        Storage::disk('public')->put('/uploads/' . $path, file_get_contents($faker->imageUrl(400, 400, 'people')));
        User::create([
            'role_id' => '1',
            'email' => 'huynhthaihu64@gmail.com',
            'name' => 'Huy Huỳnh',
            'gender' => 0,
            'avatar' => '/storage/uploads'. $path,
            'password' => Hash::make('12345678'),
            'phone' => '0905463037',
            'address' => 'Da Nang',
            'department' => 'DEV',
            'birthday' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        User::create([
            'role_id' => '2',
            'email' => 'ch4ut1nhtr1@gmail.com',
            'name' => 'Huỳnh Huy',
            'gender' => 0,
            'avatar' => '/storage/uploads'. $path,
            'password' => Hash::make('12345678'),
            'phone' => '0905463037',
            'address' => 'Sài Gòn',
            'department' => 'FE',
            'birthday' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        User::create([
            'role_id' => '3',
            'email' => 'kinsatthu123@gmail.com',
            'name' => 'Huỳnh Thái Huy',
            'gender' => 0,
            'avatar' => '/storage/uploads'. $path,
            'password' => Hash::make('12345678'),
            'phone' => '0905463037',
            'address' => 'Hà Nội',
            'department' => 'BE',
            'birthday' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
