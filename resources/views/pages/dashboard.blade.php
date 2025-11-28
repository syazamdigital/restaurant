@extends('layouts.app')

@section('title', 'Dashboard Owner')
@section('page-title', 'Kinerja Kios & Analisis Penjualan')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard Owner</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card border-start border-primary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Pendapatan Hari Ini</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">RM {{ number_format($revenueToday ?? 0, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card border-start border-success border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Jumlah Pesanan Hari Ini</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $ordersTodayCount ?? 0 }} Orders</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card border-start border-warning border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Rata-rata Nilai Pesanan (AOV)</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">RM {{ number_format($aov ?? 0, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-basket fa-2x text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card border-start border-info border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">Pesanan Aktif (Kitchen Queue)</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $activeOrdersCount ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-fire fa-2x text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card dashboard-card shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-chart-line me-1"></i> Tren Pendapatan 7 Hari Terakhir
                </h6>
            </div>
            <div class="card-body">
                <canvas id="salesChart" style="height:350px"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card dashboard-card shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="m-0 fw-bold text-success">
                    <i class="fas fa-tags me-1"></i> Jenis Pesanan Terpopuler (Bulan Ini)
                </h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="topSellingItemsList">
                    @forelse ($topOrderTypes as $typeData)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">{{ $loop->iteration }}.</small>
                                <strong>{{ $typeData->type }}</strong>
                            </div>
                            <span class="badge bg-success rounded-pill">{{ $typeData->total }} Orders</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">Belum ada data jenis pesanan bulan ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card dashboard-card shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="m-0 fw-bold text-info">
                    <i class="fas fa-history me-1"></i> 5 Pesanan Terakhir
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Waktu</th>
                                <th>Item (Summary)</th>
                                <th>Tipe</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                            <tr>
                                <td>{{ $order->order_id }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}</td>
                                <td>
                                    <span title="{{ $order->items_description }}">
                                        {{ Str::limit($order->items_description, 40) }}
                                    </span>
                                </td>
                                <td><span class="badge bg-secondary">{{ $order->type }}</span></td>
                                <td class="fw-bold text-success">RM {{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'Completed' => 'success',
                                            'Cancelled' => 'danger',
                                            'Pending' => 'warning',
                                            'Ready' => 'info',
                                            'Cooking' => 'warning'
                                        ][$order->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ $order->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Tidak ada pesanan terbaru.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 text-end">
                <a href="" class="btn btn-sm btn-outline-primary">Lihat Semua Pesanan <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Asumsi Anda menggunakan Chart.js untuk grafik --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    const dailyRevenues = [
        { date: '2025-11-22', revenue: 150.50 },
        { date: '2025-11-23', revenue: 180.20 },
        { date: '2025-11-24', revenue: 220.00 },
        { date: '2025-11-25', revenue: 190.75 },
        { date: '2025-11-26', revenue: 250.90 },
        { date: '2025-11-27', revenue: 280.40 },
        { date: '2025-11-28', revenue: {{ $revenueToday ?? 0 }} } // Data hari ini
    ];

    const labels = dailyRevenues.map(data => {
        const date = new Date(data.date);
        return date.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric' });
    });
    const dataPoints = dailyRevenues.map(data => data.revenue);
    
    const salesData = {
        labels: labels,
        datasets: [{
            label: 'Pendapatan (RM)',
            data: dataPoints,
            backgroundColor: 'rgba(52, 152, 219, 0.5)', 
            borderColor: 'rgba(52, 152, 219, 1)',
            borderWidth: 2,
            tension: 0.3,
            fill: true
        }]
    };

    const config = {
        type: 'line',
        data: salesData,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, ticks) {
                            return 'RM ' + value;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    };
    const salesChart = new Chart(
        document.getElementById('salesChart'),
        config
    );
</script>
@endsection