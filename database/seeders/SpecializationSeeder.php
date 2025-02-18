<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('specializations')->insert([
            ['name' => 'Heart'],
            ['name' => 'Dental'],
            ['name' => 'Surgery'],
            ['name' => 'Pediatrics'],
            ['name' => 'Internal Medicine'],
            ]);
    }
}
