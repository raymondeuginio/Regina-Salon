<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Store;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $store = Store::query()->where('name', 'Regina Salon - Citra 8')->first();
        // Map nama kategori layanan ke primary key-nya agar setiap layanan
        // dapat mereferensikan kolom service_category_id yang valid.
        $categories = ServiceCategory::query()->pluck('id', 'name');

        if (! $store) {
            return;
        }

        $catalog = [
            'Hair Service' => [
                [
                    'name' => "Women's Haircut",
                    'duration' => 60,
                    'price' => 125000,
                    'description' => 'Potong rambut khusus wanita termasuk konsultasi gaya singkat.',
                ],
                [
                    'name' => "Men's Haircut",
                    'duration' => 45,
                    'price' => 80000,
                    'description' => 'Potong rambut pria dengan penataan dasar sesuai permintaan.',
                ],
                [
                    'name' => "Children's Haircut (under 12 y.o)",
                    'duration' => 45,
                    'price' => 100000,
                    'description' => 'Potong rambut anak usia di bawah 12 tahun dengan pendekatan ramah.',
                ],
                [
                    'name' => "Women's Haircut + Wash + Blow",
                    'duration' => 75,
                    'price' => 160000,
                    'description' => 'Paket potong rambut wanita lengkap dengan cuci dan blow.',
                ],
                [
                    'name' => 'Wash + Natural Blow',
                    'duration' => 45,
                    'price' => 65000,
                    'description' => 'Cuci rambut dilanjutkan dengan blow natural.',
                ],
                [
                    'name' => 'Wash + Variated Blow',
                    'duration' => 60,
                    'price' => 100000,
                    'description' => 'Cuci rambut dan blow variatif sesuai request.',
                ],
                [
                    'name' => 'Hair Styling',
                    'duration' => 60,
                    'price' => 150000,
                    'description' => 'Penataan rambut untuk acara spesial atau styling tertentu.',
                ],
                [
                    'name' => 'Variated Blow',
                    'duration' => 45,
                    'price' => 65000,
                    'description' => 'Blow rambut dengan variasi styling pilihan.',
                ],
            ],
            'Creambath & Hair Mask' => [
                [
                    'name' => 'Creambath Traditional',
                    'duration' => 60,
                    'price' => 70000,
                    'description' => 'Creambath klasik untuk menyegarkan kulit kepala dan rambut.',
                ],
                [
                    'name' => 'Hairmask by Musk',
                    'duration' => 60,
                    'price' => 150000,
                    'description' => 'Perawatan hairmask Musk untuk melembapkan rambut.',
                ],
                [
                    'name' => 'Hairmask by Alfa',
                    'duration' => 75,
                    'price' => 200000,
                    'description' => 'Perawatan hairmask Alfa untuk memperkuat rambut.',
                ],
                [
                    'name' => 'Hairmask by Montibello',
                    'duration' => 90,
                    'price' => 350000,
                    'description' => 'Hairmask premium Montibello untuk rambut rusak.',
                ],
                [
                    'name' => 'Hair Detox + Scalp',
                    'duration' => 75,
                    'price' => 250000,
                    'description' => 'Detoks rambut sekaligus perawatan kulit kepala intensif.',
                ],
            ],
            'Nail Treatment' => [
                [
                    'name' => 'Manicure',
                    'duration' => 45,
                    'price' => 70000,
                    'description' => 'Perawatan kuku tangan termasuk shaping dan perapihan kutikula.',
                ],
                [
                    'name' => 'Pedicure',
                    'duration' => 60,
                    'price' => 150000,
                    'description' => 'Perawatan kuku kaki lengkap dengan scrub ringan.',
                ],
                [
                    'name' => 'Manicure + Pedicure',
                    'duration' => 90,
                    'price' => 200000,
                    'description' => 'Kombinasi manicure dan pedicure untuk perawatan menyeluruh.',
                ],
                [
                    'name' => 'Manicure + Gel Polos',
                    'duration' => 90,
                    'price' => 350000,
                    'description' => 'Manicure dilanjutkan aplikasi gel polish polos.',
                ],
                [
                    'name' => 'Pedicure + Gel Polos',
                    'duration' => 90,
                    'price' => 250000,
                    'description' => 'Pedicure lengkap dengan finishing gel polish polos.',
                ],
            ],
            'Nail Gel' => [
                [
                    'name' => 'Express Manicure + Gel Polos',
                    'duration' => 60,
                    'price' => 80000,
                    'description' => 'Manicure cepat dengan aplikasi gel polish polos.',
                ],
                [
                    'name' => 'Express Manicure + Gel Polos + Overlay',
                    'duration' => 75,
                    'price' => 130000,
                    'description' => 'Manicure express dengan gel polos dan overlay penguat.',
                ],
                [
                    'name' => 'Remove Gel',
                    'duration' => 20,
                    'price' => 4000,
                    'description' => 'Lepas gel polish dengan teknik aman untuk kuku.',
                ],
                [
                    'name' => 'Remove Gel Extension',
                    'duration' => 30,
                    'price' => 6000,
                    'description' => 'Lepas gel extension secara hati-hati tanpa merusak kuku.',
                ],
            ],
            'Nail Art' => [
                [
                    'name' => 'Glitter',
                    'duration' => 15,
                    'price' => 5000,
                    'description' => 'Aplikasi glitter untuk aksen kuku.',
                ],
                [
                    'name' => 'Cat Eye',
                    'duration' => 15,
                    'price' => 5000,
                    'description' => 'Efek cat eye dengan finishing magnetik.',
                ],
                [
                    'name' => 'French',
                    'duration' => 20,
                    'price' => 5000,
                    'description' => 'French manicure klasik untuk tampilan elegan.',
                ],
                [
                    'name' => 'Ombre',
                    'duration' => 20,
                    'price' => 5000,
                    'description' => 'Gradasi warna ombre untuk kuku.',
                ],
                [
                    'name' => 'Chrome',
                    'duration' => 20,
                    'price' => 7000,
                    'description' => 'Finishing chrome berkilau untuk kuku.',
                ],
                [
                    'name' => 'Line / Doting',
                    'duration' => 15,
                    'price' => 3000,
                    'description' => 'Detail garis atau dotting art sederhana.',
                ],
                [
                    'name' => 'Marble, Embos',
                    'duration' => 25,
                    'price' => 6000,
                    'description' => 'Teknik marble atau emboss untuk efek tekstur.',
                ],
                [
                    'name' => 'Extension',
                    'duration' => 30,
                    'price' => 7000,
                    'description' => 'Perpanjangan kuku dengan material tambahan.',
                ],
                [
                    'name' => '3D Building Art',
                    'duration' => 30,
                    'price' => 10000,
                    'description' => 'Harga mulai 10K - 20K tergantung desain (cek di toko).',
                ],
                [
                    'name' => '3D Flower Art',
                    'duration' => 30,
                    'price' => 20000,
                    'description' => 'Harga mulai 20K - 30K tergantung desain (cek di toko).',
                ],
                [
                    'name' => '3D Shell Art',
                    'duration' => 30,
                    'price' => 25000,
                    'description' => 'Aksen 3D berbentuk shell untuk kuku.',
                ],
            ],
            'Add On Accessories' => [
                [
                    'name' => 'Dry Flower',
                    'duration' => 10,
                    'price' => 7000,
                    'description' => 'Aplikasi aksesori bunga kering.',
                ],
                [
                    'name' => 'Foil / Gold Flake',
                    'duration' => 10,
                    'price' => 5000,
                    'description' => 'Penambahan foil atau serpihan emas.',
                ],
                [
                    'name' => 'List',
                    'duration' => 10,
                    'price' => 6000,
                    'description' => 'Aksen garis list dekoratif.',
                ],
                [
                    'name' => 'Earth Stone',
                    'duration' => 10,
                    'price' => 8000,
                    'description' => 'Aksesori batu earth tone.',
                ],
                [
                    'name' => 'Diamond',
                    'duration' => 10,
                    'price' => 3000,
                    'description' => 'Aksen berlian imitasi kecil.',
                ],
                [
                    'name' => 'Swaroski',
                    'duration' => 10,
                    'price' => 3000,
                    'description' => 'Crystal Swaroski untuk kuku.',
                ],
                [
                    'name' => 'Pearl',
                    'duration' => 10,
                    'price' => 3000,
                    'description' => 'Aksesori mutiara kecil.',
                ],
                [
                    'name' => 'Charm',
                    'duration' => 10,
                    'price' => 4000,
                    'description' => 'Penambahan charm dekoratif.',
                ],
                [
                    'name' => '3D Accessories',
                    'duration' => 15,
                    'price' => 10000,
                    'description' => 'Aksesori 3D tambahan sesuai request.',
                ],
            ],
            'Body Treatment' => [
                [
                    'name' => 'Refleksi (1 jam)',
                    'duration' => 60,
                    'price' => 90000,
                    'description' => 'Pijat refleksi selama 60 menit.',
                ],
                [
                    'name' => 'Refleksi (1,5 jam)',
                    'duration' => 90,
                    'price' => 120000,
                    'description' => 'Pijat refleksi berdurasi 90 menit.',
                ],
                [
                    'name' => 'Refleksi (2 jam)',
                    'duration' => 120,
                    'price' => 160000,
                    'description' => 'Pijat refleksi lengkap selama 120 menit.',
                ],
                [
                    'name' => 'Massage (1 jam)',
                    'duration' => 60,
                    'price' => 150000,
                    'description' => 'Pijat relaksasi 60 menit.',
                ],
                [
                    'name' => 'Massage (1,5 jam)',
                    'duration' => 90,
                    'price' => 210000,
                    'description' => 'Pijat relaksasi 90 menit dengan fokus tambahan.',
                ],
                [
                    'name' => 'Lulur Full Body',
                    'duration' => 90,
                    'price' => 120000,
                    'description' => 'Perawatan lulur menyeluruh untuk seluruh tubuh.',
                ],
            ],
            'Facial Treatment' => [
                [
                    'name' => 'Facial by Biokos',
                    'duration' => 60,
                    'price' => 125000,
                    'description' => 'Facial menggunakan rangkaian produk Biokos.',
                ],
                [
                    'name' => 'Totok Wajah',
                    'duration' => 45,
                    'price' => 100000,
                    'description' => 'Totok wajah untuk merilekskan otot dan sirkulasi.',
                ],
                [
                    'name' => 'Totok Wajah + Masker',
                    'duration' => 60,
                    'price' => 120000,
                    'description' => 'Totok wajah dilanjut masker perawatan.',
                ],
                [
                    'name' => 'Totok Wajah + Lulur',
                    'duration' => 90,
                    'price' => 260000,
                    'description' => 'Totok wajah ditambah lulur untuk hasil maksimal.',
                ],
                [
                    'name' => 'Ratus (15 menit)',
                    'duration' => 15,
                    'price' => 50000,
                    'description' => 'Perawatan ratus selama 15 menit.',
                ],
            ],
            'Coloring' => [
                [
                    'name' => 'Basic Color',
                    'duration' => 150,
                    'price' => 350000,
                    'description' => 'Harga mulai 350K - 900K tergantung kondisi rambut (cek di toko).',
                ],
                [
                    'name' => 'Toning',
                    'duration' => 120,
                    'price' => 350000,
                    'description' => 'Harga mulai 350K hingga 900K tergantung kondisi rambut (cek di toko).',
                ],
                [
                    'name' => 'Fashion Color',
                    'duration' => 180,
                    'price' => 500000,
                    'description' => 'Harga mulai 500K - 1.100K tergantung kondisi rambut (cek di toko).',
                ],
                [
                    'name' => 'Bleaching',
                    'duration' => 180,
                    'price' => 400000,
                    'description' => 'Harga mulai 400K - 1.000K tergantung kondisi rambut (cek di toko).',
                ],
            ],
            'Grey Coverage' => [
                [
                    'name' => 'Root Touch Up (Short Hair)',
                    'duration' => 90,
                    'price' => 200000,
                    'description' => 'Sentuhan warna ulang khusus rambut pendek.',
                ],
                [
                    'name' => 'Medium Hair',
                    'duration' => 105,
                    'price' => 300000,
                    'description' => 'Cakupan uban untuk rambut panjang medium.',
                ],
                [
                    'name' => 'Long Hair',
                    'duration' => 120,
                    'price' => 450000,
                    'description' => 'Cakupan uban pada rambut panjang.',
                ],
            ],
            'Highlight' => [
                [
                    'name' => '1/4 Head',
                    'duration' => 150,
                    'price' => 500000,
                    'description' => 'Harga mulai 500K, dapat meningkat sesuai kebutuhan highlight.',
                ],
                [
                    'name' => '1/2 Head',
                    'duration' => 180,
                    'price' => 700000,
                    'description' => 'Harga mulai 700K, dapat meningkat sesuai kebutuhan highlight.',
                ],
                [
                    'name' => '3/4 Head',
                    'duration' => 195,
                    'price' => 900000,
                    'description' => 'Harga mulai 900K, dapat meningkat sesuai kebutuhan highlight.',
                ],
                [
                    'name' => 'Highlight + Color',
                    'duration' => 210,
                    'price' => 1200000,
                    'description' => 'Harga mulai 1.200K, dapat meningkat sesuai kebutuhan highlight.',
                ],
                [
                    'name' => 'Balayage + Color',
                    'duration' => 240,
                    'price' => 1300000,
                    'description' => 'Harga mulai 1.300K, dapat meningkat sesuai kebutuhan balayage.',
                ],
                [
                    'name' => '1/2 Balayage',
                    'duration' => 210,
                    'price' => 1000000,
                    'description' => 'Harga mulai 1.000K, dapat meningkat sesuai kebutuhan balayage.',
                ],
            ],
            'Keratin Treatment & Curl' => [
                [
                    'name' => 'Keratin',
                    'duration' => 210,
                    'price' => 700000,
                    'description' => 'Harga mulai 700K - 1.600K tergantung kondisi rambut (cek di toko).',
                ],
                [
                    'name' => 'Smoothing',
                    'duration' => 210,
                    'price' => 400000,
                    'description' => 'Harga mulai 400K - 700K tergantung kondisi rambut (cek di toko).',
                ],
                [
                    'name' => 'Curl',
                    'duration' => 180,
                    'price' => 300000,
                    'description' => 'Harga mulai 300K - 500K tergantung kondisi rambut (cek di toko).',
                ],
                [
                    'name' => 'Korean Perm',
                    'duration' => 210,
                    'price' => 800000,
                    'description' => 'Harga mulai 800K - 1.300K tergantung kondisi rambut (cek di toko).',
                ],
            ],
        ];

        foreach ($catalog as $categoryName => $services) {
            $categoryId = $categories[$categoryName] ?? null;

            if (! $categoryId) {
                continue;
            }

            foreach ($services as $service) {
                Service::query()->updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'service_category_id' => $categoryId,
                        'name' => $service['name'],
                    ],
                    [
                        'description' => $service['description'] ?? null,
                        'duration' => $service['duration'],
                        'price' => $service['price'],
                    ]
                );
            }
        }
    }
}
