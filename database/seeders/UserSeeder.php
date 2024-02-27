<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'role_id' => '1',
            'email' => 'huynhthaihuy64@gmail.com',
            'name' => 'Huy Huỳnh',
            'gender' => 0,
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
