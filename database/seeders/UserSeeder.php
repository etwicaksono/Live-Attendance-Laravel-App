<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('users')->insert([
      [
        'name' => 'Admin User',
        'username' => 'admin',
        'password' => Hash::make('admin'),
        'role' => 'admin',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Regular User',
        'username' => 'user1',
        'password' => Hash::make('user'),
        'role' => 'user',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ]
    ]);
  }
}
