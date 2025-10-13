<?php

namespace Database\Seeders;

use App\Models\Service;
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
        $storeIds = \App\Models\Store::pluck('id')->toArray();

        if (empty($storeIds)) {
            $this->command->warn('Store not found.');
            return;
        }

        // 5 stuff dumm
        $staffs = [
            [
                'name' => 'Eilleen Tan',
                'email' => 'eilleen@example.com',
                'phone' => '087812345601',
                'description' => 'Friendly and professional stylist with great attention to detail.',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'Raymond Sanjaya',
                'email' => 'raymond@example.com',
                'phone' => '087812345602',
                'description' => 'Expert in modern men’s haircut and grooming.',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'Jocelyn Novia',
                'email' => 'jocelyn@example.com',
                'phone' => '087812345603',
                'description' => 'Specialized in hair coloring and treatment.',
                'image' => 'sstaff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'Eilleen May',
                'email' => 'eilleenmay@example.com',
                'phone' => '087812345604',
                'description' => 'Loves creative styling and customer satisfaction.',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'James Anderson',
                'email' => 'james@example.com',
                'phone' => '087812345605',
                'description' => 'Experienced barber with passion for precision cuts.',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
        ];

        foreach ($staffs as $data) {
            $staff = Staff::create([
                'store_id' => fake()->randomElement($storeIds),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'description' => $data['description'],
                'image' => $data['image'],
            ]);

            // services acak (1–10 service per staff)
            $serviceIds = Service::inRandomOrder()->limit(rand(1, 10))->pluck('id');
            $staff->services()->attach($serviceIds);
        }
    }
}
