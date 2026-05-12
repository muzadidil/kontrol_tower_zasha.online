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
                'icon' => 'bi-tools',
                'icon_color' => '#f59e0b',
                'features' => [
                    'order-tenaga', 'order-inden',
                    'maps',
                    'saldo', 'profil', 'pesanan-list',
                ],
            ],
            [
                'name' => 'Mitra JST',
                'description' => 'Mitra Jastip / Kurir (belanja & antar)',
                'icon' => 'bi-truck',
                'icon_color' => '#10b981',
                'features' => [
                    'order-jastip',
                    'maps',
                    'saldo', 'profil', 'pesanan-list',
                ],
            ],
            [
                'name' => 'Mitra WFH',
                'description' => 'Mitra Digital / Remote (desain, coding, voice over)',
                'icon' => 'bi-laptop',
                'icon_color' => '#7c3aed',
                'features' => [
                    'order-wfh', 'order-inden',
                    'portfolio',
                    'saldo', 'profil', 'pesanan-list',
                ],
            ],
            [
                'name' => 'Mitra SVC',
                'description' => 'Mitra Service / Teknisi (AC, TV, kelistrikan)',
                'icon' => 'bi-wrench-adjustable',
                'icon_color' => '#0ea5e9',
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
                    'icon'        => $data['icon'],
                    'icon_color'  => $data['icon_color'],
                    'is_default'  => true,
                    'is_active'   => true,
                ]
            );
            $role->syncFeatures($data['features']);
        }
    }
}
