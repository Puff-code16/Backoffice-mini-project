<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $names = [
            'สมชาย ใจดี', 'สมหญิง สายใจ', 'ประยุทธ์ ขยัน', 'อรทัย สวยงาม', 'วรชัย เก่งมาก',
            'กิตติชัย รวยเร็ว', 'พรทิพย์ น่ารัก', 'สุชาติ ใจเย็น', 'สุรีย์ สายลม', 'ณัฐวุฒิ ทันสมัย'
        ];
        $emails = [
            'somchai@example.com', 'somying@example.com', 'prayut@example.com', 'ornthai@example.com', 'worachai@example.com',
            'kittichai@example.com', 'pornthip@example.com', 'suchart@example.com', 'suree@example.com', 'natthawut@example.com'
        ];

        // สร้างผู้ใช้แต่ละเดือน
        foreach (range(1, 12) as $month) {
            User::create([
                'name' => $names[$month % count($names)],
                'email' => $emails[$month % count($emails)],
                'password' => Hash::make('password'),
                'status' => $month % 2 == 0 ? 'inactive' : 'active',
                'created_at' => now()->setMonth($month)->setDay(10)->setTime(12, 0, 0),
                'updated_at' => now()->setMonth($month)->setDay(10)->setTime(12, 0, 0),
            ]);
        }
    }
}
