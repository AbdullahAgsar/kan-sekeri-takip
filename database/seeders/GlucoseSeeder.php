<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Glucose;

class GlucoseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $glucoseData = [
            [
                'timestamp' => '2025-10-02T12:00:00',
                'value' => 99.0,
                'note' => '',
                'is_hungry' => true
            ],
            [
                'timestamp' => '2025-10-02T14:00:00',
                'value' => 119.0,
                'note' => '',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-03T12:00:00',
                'value' => 101.0,
                'note' => '8 saat',
                'is_hungry' => true
            ],
            [
                'timestamp' => '2025-10-03T14:00:00',
                'value' => 98.0,
                'note' => '',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-03T20:00:00',
                'value' => 111.0,
                'note' => '',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-04T12:00:00',
                'value' => 95.0,
                'note' => '',
                'is_hungry' => true
            ],
            [
                'timestamp' => '2025-10-04T13:30:00',
                'value' => 70.0,
                'note' => '',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-04T13:35:00',
                'value' => 82.0,
                'note' => 'tok sonrası',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-04T14:00:00',
                'value' => 91.0,
                'note' => 'tok sonrası',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-04T19:00:00',
                'value' => 89.0,
                'note' => '',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-04T21:00:00',
                'value' => 106.0,
                'note' => 'tok sonrası',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-05T23:00:00',
                'value' => 105.0,
                'note' => '',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-05T12:00:00',
                'value' => 85.0,
                'note' => '',
                'is_hungry' => true
            ],
            [
                'timestamp' => '2025-10-05T14:00:00',
                'value' => 90.0,
                'note' => '',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-05T20:00:00',
                'value' => 91.0,
                'note' => '',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-05T23:00:00',
                'value' => 101.0,
                'note' => '',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-06T12:00:00',
                'value' => 91.0,
                'note' => '',
                'is_hungry' => true
            ],
            [
                'timestamp' => '2025-10-06T14:00:00',
                'value' => 72.0,
                'note' => '',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-06T14:30:00',
                'value' => 86.0,
                'note' => 'toktan 30 dakika sonra',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-06T23:40:00',
                'value' => 85.0,
                'note' => '',
                'is_hungry' => false
            ],
            [
                'timestamp' => '2025-10-07T12:00:00',
                'value' => 85.0,
                'note' => '',
                'is_hungry' => true
            ],
            [
                'timestamp' => '2025-10-07T15:00:00',
                'value' => 101.0,
                'note' => '',
                'is_hungry' => false
            ]
        ];

        foreach ($glucoseData as $data) {
            Glucose::create([
                'value' => $data['value'],
                'note' => $data['note'],
                'is_hungry' => $data['is_hungry'],
                'measurement_datetime' => $data['timestamp']
            ]);
        }
    }
}
