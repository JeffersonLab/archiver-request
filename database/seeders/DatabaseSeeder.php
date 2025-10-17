<?php

namespace Database\Seeders;

use App\Models\Staff;
use Database\Factories\StaffFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Staff::factory(20)->create();
    }
}
