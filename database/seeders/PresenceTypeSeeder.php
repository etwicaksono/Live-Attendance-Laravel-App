<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PresenceTypeSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('presence_types')->insert([
      [
        'id' => 1,
        'type' => 'On Time',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id' => 2,
        'type' => 'Late',
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
