<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Hair Service',
            'Creambath & Hair Mask',
            'Nail Treatment',
            'Nail Gel',
            'Nail Art',
            'Add On Accessories',
            'Body Treatment',
            'Facial Treatment',
            'Coloring',
            'Grey Coverage',
            'Highlight',
            'Keratin Treatment & Curl',
        ];

        foreach ($categories as $name) {
            ServiceCategory::query()->firstOrCreate(['name' => $name]);
        }
    }
}
