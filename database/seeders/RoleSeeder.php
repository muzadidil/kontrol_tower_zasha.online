<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'name' => 'Mitra TNG',
                'description' => 'Mitra Tenaga Kerja (Tukang, Cleaning, Rewang, dll)',
                'features' => [
                    'order-tenaga', 'order-inden',
                    'maps',
                    'saldo', 'profil', 'pesanan-list',
                ],
            ],
            [
                'name' => 'Mitra JST',
                'description' => 'Mitra Jastip / Kurir (belanja & antar)',
                'features' => [
                    'order-jastip',
                    'maps',
                    'saldo', 'profil', 'pesanan-list',
                ],
            ],
            [
                'name' => 'Mitra WFH',
                'description' => 'Mitra Digital / Remote (desain, coding, voice over)',
                'features' => [
                    'order-wfh', 'order-inden',
                    'portfolio',
                    'saldo', 'profil', 'pesanan-list',
                ],
            ],
            [
                'name' => 'Mitra SVC',
                'description' => 'Mitra Service / Teknisi (AC, TV, kelistrikan)',
                'features' => [
                    'order-service',
                    'sparepart', 'maps',
                    'saldo', 'profil', 'pesanan-list',
                ],
            ],
        ];

        foreach ($defaults as $data) {
            $role = Role::updateOrCreate(
                ['name' => $data['name']],
                [
                    'description' => $data['description'],
                    'is_default'  => true,
                ]
            );
            $role->syncFeatures($data['features']);
        }
    }
}
