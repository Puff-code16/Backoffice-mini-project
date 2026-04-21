<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboardPage()
    {
        $months = [
            '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.', '05' => 'พ.ค.', '06' => 'มิ.ย.',
            '07' => 'ก.ค.', '08' => 'ส.ค.', '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
        ];

        // ดึงข้อมูลจาก MP_EMPLOYEES
        $users = DB::table('MP_USERS')
            ->select('FIRST_NAME', 'LAST_NAME', 'CREATED_AT')
            ->get();

        // นับจำนวนผู้ใช้ตามเดือน จาก MP_EMPLOYEES
        $userCountsByMonth = DB::table('MP_EMPLOYEES')
            ->selectRaw("TO_CHAR(CREATED_AT, 'MM') as month, COUNT(*) as count")
            ->whereRaw("EXTRACT(YEAR FROM CREATED_AT) = ?", [now()->year])
            ->groupByRaw("TO_CHAR(CREATED_AT, 'MM')")
            ->orderByRaw("TO_CHAR(CREATED_AT, 'MM')")
            ->pluck('count', 'month')
            ->toArray();

        // map เดือนที่ไม่มีข้อมูล = 0
        $userCountsByMonthFull = [];
        foreach ($months as $num => $name) {
            $userCountsByMonthFull[$name] = $userCountsByMonth[$num] ?? 0;
        }

        return view('backoffice.dashboard', compact('users', 'userCountsByMonthFull'));
    }
}
