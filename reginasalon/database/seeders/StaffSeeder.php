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
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'Raymond Sanjaya',
                'email' => 'raymond@example.com',
                'phone' => '087812345602',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'Jocelyn Novia',
                'email' => 'jocelyn@example.com',
                'phone' => '087812345603',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'Eilleen May',
                'email' => 'eilleenmay@example.com',
                'phone' => '087812345604',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'James Anderson',
                'email' => 'james@example.com',
                'phone' => '087812345605',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'Angelina Lee',
                'email' => 'angelina@example.com',
                'phone' => '087812345606',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'Jonathan Prasetyo',
                'email' => 'jonathan@example.com',
                'phone' => '087812345607',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'Catherine Lim',
                'email' => 'catherine@example.com',
                'phone' => '087812345608',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'Felix Adrian',
                'email' => 'felix@example.com',
                'phone' => '087812345609',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
            [
                'name' => 'Stephanie Widjaja',
                'email' => 'stephanie@example.com',
                'phone' => '087812345610',
                'image' => 'staff/01K7A1D62FGSWZQ599NWKN3Y0M.png',
            ],
        ];

        foreach ($staffs as $data) {
            $staff = Staff::create([
                'store_id' => fake()->randomElement($storeIds),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'image' => $data['image'],
            ]);

            // services acak (1–10 service per staff)
            $serviceIds = Service::inRandomOrder()->limit(rand(1, 10))->pluck('id');
            $staff->services()->attach($serviceIds);
        }
    }
}
