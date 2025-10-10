<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Staff::query()->updateOrCreate([
            'store_id' => '1',
            'name' => "Inul Sukajan",
            'email' => 'inul@gmail.com',
            'phone' => '123456789',
        ]);
    }
}
