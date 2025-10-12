<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Regina Admin',
            'email' => 'regina@salon.com',
            'password' => bcrypt('regina123'),
            'role' => 'owner'
        ]);


        User::factory()->create([
            'name' => 'Algi',
            'email' => 'algi@salon.com',
            'password' => bcrypt('algi123'),
            'role' => 'admin'
        ]);
        $this->call([
            StoreSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            StaffSeeder::class,
        ]);
    }
}
