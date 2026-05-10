<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Layanan;
use Illuminate\Database\Seeder;

class GameTopupDummySeeder extends Seeder
{
    public function run(): void
    {
        $ml = Kategori::updateOrCreate(
            ['kode' => 'mobile-legends'],
            [
                'nama'      => 'Mobile Legends',
                'sub_nama'  => 'Bang Bang',
                'thumbnail' => null,
                'server_id' => 1,
                'tipe'      => 'game',
                'status'    => 'active',
            ]
        );

        $ff = Kategori::updateOrCreate(
            ['kode' => 'free-fire'],
            [
                'nama'      => 'Free Fire',
                'sub_nama'  => null,
                'thumbnail' => null,
                'server_id' => 0,
                'tipe'      => 'game',
                'status'    => 'active',
            ]
        );

        $voucher = Kategori::updateOrCreate(
            ['kode' => 'google-play'],
            [
                'nama'      => 'Google Play',
                'sub_nama'  => 'Voucher',
                'thumbnail' => null,
                'server_id' => 0,
                'tipe'      => 'voucher',
                'status'    => 'active',
            ]
        );

        $mlProducts = [
            ['layanan' => 'Mobile Legends 86 Diamonds', 'provider_id' => 'DUMMY-ML-86',  'harga' => 25000],
            ['layanan' => 'Mobile Legends 172 Diamonds','provider_id' => 'DUMMY-ML-172', 'harga' => 49000],
            ['layanan' => 'Mobile Legends 257 Diamonds','provider_id' => 'DUMMY-ML-257', 'harga' => 73000],
        ];
        foreach ($mlProducts as $p) {
            Layanan::updateOrCreate(
                ['provider_id' => $p['provider_id']],
                [
                    'kategori_id' => $ml->id,
                    'layanan'     => $p['layanan'],
                    'provider'    => 'digiflazz',
                    'harga'       => $p['harga'],
                    'profit'      => 5,
                    'status'      => 'available',
                ]
            );
        }

        $ffProducts = [
            ['layanan' => 'Free Fire 70 Diamonds',  'provider_id' => 'DUMMY-FF-70',  'harga' => 10000],
            ['layanan' => 'Free Fire 140 Diamonds', 'provider_id' => 'DUMMY-FF-140', 'harga' => 19000],
            ['layanan' => 'Free Fire 355 Diamonds', 'provider_id' => 'DUMMY-FF-355', 'harga' => 49000],
        ];
        foreach ($ffProducts as $p) {
            Layanan::updateOrCreate(
                ['provider_id' => $p['provider_id']],
                [
                    'kategori_id' => $ff->id,
                    'layanan'     => $p['layanan'],
                    'provider'    => 'digiflazz',
                    'harga'       => $p['harga'],
                    'profit'      => 5,
                    'status'      => 'available',
                ]
            );
        }

        $voucherProducts = [
            ['layanan' => 'Google Play Rp 20.000',  'provider_id' => 'DUMMY-GP-20',  'harga' => 22000],
            ['layanan' => 'Google Play Rp 50.000',  'provider_id' => 'DUMMY-GP-50',  'harga' => 53000],
        ];
        foreach ($voucherProducts as $p) {
            Layanan::updateOrCreate(
                ['provider_id' => $p['provider_id']],
                [
                    'kategori_id' => $voucher->id,
                    'layanan'     => $p['layanan'],
                    'provider'    => 'digiflazz',
                    'harga'       => $p['harga'],
                    'profit'      => 5,
                    'status'      => 'available',
                ]
            );
        }

        $this->command->info('Seeded ' . Kategori::count() . ' kategoris and ' . Layanan::count() . ' layanans (dummy products with DUMMY- prefix provider_id).');
    }
}
