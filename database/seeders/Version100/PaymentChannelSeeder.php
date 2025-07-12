<?php

use App\Models\PaymentChannel;
use Illuminate\Database\Seeder;

class PaymentChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $channels = [
            [
                'name' => 'Vodacom M-Pesa',
                'code' => 'vodacom_mpesa',
                'icon' => 'mpesa.png',
                'is_active' => true,
                'description' => 'Vodacom Tanzania M-Pesa mobile money service',
                'config' => [
                    'short_code' => '123456',
                    'transaction_fee' => 0.01,
                    'max_amount' => 1000000,
                    'min_amount' => 10,
                    'ussd_code' => '*150*00#'
                ]
            ],
            [
                'name' => 'Airtel Money',
                'code' => 'airtel_money',
                'icon' => 'airtel.png',
                'is_active' => true,
                'description' => 'Airtel Tanzania mobile money service',
                'config' => [
                    'short_code' => '123456',
                    'transaction_fee' => 0.015,
                    'max_amount' => 1000000,
                    'min_amount' => 10,
                    'ussd_code' => '*150*60#'
                ]
            ],
            [
                'name' => 'Tigo Pesa',
                'code' => 'tigo_pesa',
                'icon' => 'tigo.png',
                'is_active' => true,
                'description' => 'Tigo Tanzania mobile money service',
                'config' => [
                    'short_code' => '123456',
                    'transaction_fee' => 0.02,
                    'max_amount' => 1000000,
                    'min_amount' => 10,
                    'ussd_code' => '*150*01#'
                ]
            ],
            [
                'name' => 'Halopesa',
                'code' => 'halopesa',
                'icon' => 'halotel.png',
                'is_active' => true,
                'description' => 'Halotel Tanzania mobile money service',
                'config' => [
                    'short_code' => '123456',
                    'transaction_fee' => 0.02,
                    'max_amount' => 1000000,
                    'min_amount' => 10,
                    'ussd_code' => '*150*99#'
                ]
            ],
            [
                'name' => 'CRDB Bank',
                'code' => 'crdb_bank',
                'icon' => 'crdb.png',
                'is_active' => true,
                'description' => 'CRDB Bank Tanzania',
                'config' => [
                    'account_prefix' => '015',
                    'transaction_fee' => 0.005,
                    'max_amount' => 5000000,
                    'min_amount' => 1000
                ]
            ],
            [
                'name' => 'NMB Bank',
                'code' => 'nmb_bank',
                'icon' => 'nmb.png',
                'is_active' => true,
                'description' => 'NMB Bank Tanzania',
                'config' => [
                    'account_prefix' => '011',
                    'transaction_fee' => 0.005,
                    'max_amount' => 5000000,
                    'min_amount' => 1000
                ]
            ],
            [
                'name' => 'Visa Card',
                'code' => 'visa_card',
                'icon' => 'visa.png',
                'is_active' => true,
                'description' => 'Visa card payments',
                'config' => [
                    'transaction_fee' => 0.025,
                    'max_amount' => 10000000,
                    'min_amount' => 1000,
                    'supported_currencies' => ['TZS', 'USD']
                ]
            ],
            [
                'name' => 'Mastercard',
                'code' => 'mastercard',
                'icon' => 'mastercard.png',
                'is_active' => true,
                'description' => 'Mastercard payments',
                'config' => [
                    'transaction_fee' => 0.025,
                    'max_amount' => 10000000,
                    'min_amount' => 1000,
                    'supported_currencies' => ['TZS', 'USD']
                ]
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'icon' => 'paypal.png',
                'is_active' => true,
                'description' => 'PayPal international payments',
                'config' => [
                    'transaction_fee' => 0.03,
                    'max_amount' => 5000000,
                    'min_amount' => 1000,
                    'supported_currencies' => ['USD', 'EUR']
                ]
            ],
            [
                'name' => 'Tanzania Postal Bank',
                'code' => 'tpb_bank',
                'icon' => 'tpb.png',
                'is_active' => false,
                'description' => 'Tanzania Postal Bank (Currently inactive)',
                'config' => [
                    'account_prefix' => '012',
                    'transaction_fee' => 0.01,
                    'max_amount' => 3000000,
                    'min_amount' => 1000
                ]
            ]
        ];

        foreach ($channels as $channel) {
            PaymentChannel::updateOrCreate([
                'name' => $channel['name'],
                'code' => $channel['code']
            ],[
                'icon' => $channel['icon'],
                'is_active' => $channel['is_active'],
                'description' => $channel['description'],
                'config' => $channel['config'],
            ]);
        }
    }
}
