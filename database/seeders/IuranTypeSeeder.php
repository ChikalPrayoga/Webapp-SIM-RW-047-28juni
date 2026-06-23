<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IuranTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('iuran_types')->insert([
            [
                'name' => 'Iuran Bulanan Wajib',
                'description' => 'Iuran rutin bulanan wajib bagi warga.',
                'default_nominal' => 25000.00,
                'type' => 'WAJIB',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Iuran Kematian',
                'description' => 'Iuran kematian untuk warga.',
                'default_nominal' => 10000.00,
                'type' => 'WAJIB',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Iuran Sosial',
                'description' => 'Sumbangan sukarela untuk kegiatan sosial.',
                'default_nominal' => 0.00,
                'type' => 'SUKARELA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
