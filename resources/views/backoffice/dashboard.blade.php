@extends('layouts.backoffice')

@section('content')

@php
    
    if (!isset($bookings)) {
        $mockData = collect([
            ['month' => 'ม.ค.', 'total_bookings' => 120, 'total_seats' => 95,  'cancels' => 10, 'checkin_success' => 80,  'no_show' => 15],
            ['month' => 'ก.พ.', 'total_bookings' => 150, 'total_seats' => 120, 'cancels' => 15, 'checkin_success' => 90,  'no_show' => 15],
            ['month' => 'มี.ค.', 'total_bookings' => 180, 'total_seats' => 140, 'cancels' => 20, 'checkin_success' => 110, 'no_show' => 10],
            ['month' => 'เม.ย.', 'total_bookings' => 200, 'total_seats' => 160, 'cancels' => 18, 'checkin_success' => 120, 'no_show' => 22],
            ['month' => 'พ.ค.', 'total_bookings' => 170, 'total_seats' => 130, 'cancels' => 12, 'checkin_success' => 100, 'no_show' => 18],
            ['month' => 'มิ.ย.', 'total_bookings' => 210, 'total_seats' => 170, 'cancels' => 25, 'checkin_success' => 140, 'no_show' => 20],
            ['month' => 'ก.ค.', 'total_bookings' => 230, 'total_seats' => 190, 'cancels' => 15, 'checkin_success' => 150, 'no_show' => 25],
            ['month' => 'ส.ค.', 'total_bookings' => 250, 'total_seats' => 200, 'cancels' => 18, 'checkin_success' => 170, 'no_show' => 20],
            ['month' => 'ก.ย.', 'total_bookings' => 240, 'total_seats' => 185, 'cancels' => 22, 'checkin_success' => 160, 'no_show' => 25],
            ['month' => 'ต.ค.', 'total_bookings' => 260, 'total_seats' => 210, 'cancels' => 20, 'checkin_success' => 180, 'no_show' => 30],
            ['month' => 'พ.ย.', 'total_bookings' => 270, 'total_seats' => 220, 'cancels' => 19, 'checkin_success' => 190, 'no_show' => 25],
            ['month' => 'ธ.ค.', 'total_bookings' => 300, 'total_seats' => 250, 'cancels' => 30, 'checkin_success' => 200, 'no_show' => 20],
        ]);

        $bookings = $mockData;
        $labels   = $mockData->pluck('month');
        $checkin  = $mockData->pluck('checkin_success');
        $cancels  = $mockData->pluck('cancels');
        $noShow   = $mockData->pluck('no_show');
    }
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">รายงานการจอง</h5>
                </div>
                <div class="card-body">

                    {{-- ตารางข้อมูล --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>เดือน</th>
                                    <th>จำนวนการจอง</th>
                                    <th>จำนวนที่นั่งถูกจอง</th>
                                    <th>การยกเลิก</th>
                                    <th>Check-in สำเร็จ</th>
                                    <th>No Show</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $row)
                                    <tr>
                                        <td class="text-center">{{ $row['month'] }}</td>
                                        <td class="text-end">{{ $row['total_bookings'] }}</td>
                                        <td class="text-end">{{ $row['total_seats'] }}</td>
                                        <td class="text-end">{{ $row['cancels'] }}</td>
                                        <td class="text-end">{{ $row['checkin_success'] }}</td>
                                        <td class="text-end">{{ $row['no_show'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">ไม่มีข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- กราฟ --}}
                    <h6 class="mt-4">สถิติรายเดือน</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card p-2">
                                <canvas id="chartCheckin" height="120"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card p-2">
                                <canvas id="chartCancels" height="120"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card p-2">
                                <canvas id="chartNoShow" height="120"></canvas>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = {!! json_encode($labels) !!};

    new Chart(document.getElementById('chartCheckin'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Check-in สำเร็จ',
                data: {!! json_encode($checkin) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('chartCancels'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'การยกเลิก',
                data: {!! json_encode($cancels) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('chartNoShow'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'No Show',
                data: {!! json_encode($noShow) !!},
                backgroundColor: 'rgba(255, 206, 86, 0.6)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>
@endpush
