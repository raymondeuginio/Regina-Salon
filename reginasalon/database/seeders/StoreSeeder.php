<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::query()->updateOrCreate(
            ['name' => 'Regina Salon - Citra 8'],
            [
                'address' => 'Ruko Citra 8 Extension Blok F1 No. 1',
                'district' => 'Kalideres',
                'city' => 'Jakarta Barat',
                'province' => 'DKI Jakarta',
                'postal_code' => '11830',
                'phone' => null,
            ]
        );
    }
}
