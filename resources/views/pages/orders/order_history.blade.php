@extends('layouts.app')

@section('title', 'Order History')
@section('page-title', 'Riwayat Pesanan Selesai dan Dibatalkan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Order History</li>
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
        <h6 class="m-0 fw-bold text-success">
            <i class="fas fa-book me-1"></i> Semua Riwayat Transaksi
        </h6>
        <form method="GET" action="{{ route('orders.history') }}" class="d-flex align-items-center">
            <input type="date" name="start_date" class="form-control form-control-sm me-2" value="{{ request('start_date', Carbon\Carbon::today()->subDays(7)->format('Y-m-d')) }}">
            <span class="me-2">s/d</span>
            <input type="date" name="end_date" class="form-control form-control-sm me-2" value="{{ request('end_date', Carbon\Carbon::today()->format('Y-m-d')) }}">
            <button type="submit" class="btn btn-sm btn-secondary me-2"><i class="fas fa-search"></i> Cari</button>
            <a href="{{ route('orders.history', ['export' => 1, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-sm btn-outline-success"><i class="fas fa-file-excel"></i> Export</a>
        </form>
    </div>
    <div class="card-body">
        @if ($startDate && $endDate)
            <p class="text-muted">Menampilkan data dari {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} hingga {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}.</p>
        @endif
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="orderHistoryTable" width="100%" cellspacing="0">
                <thead class="table-dark">
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal Selesai</th>
                        <th>Tipe</th>
                        <th>Ringkasan Item</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($historyOrders as $order)
                    <tr>
                        <td class="fw-bold">{{ $order->order_id }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->updated_at)->format('H:i:s d/M/Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $order->type }}</span></td>
                        <td>{{ Str::limit($order->items_description, 50) }}</td>
                        <td class="fw-bold {{ $order->status == 'Completed' ? 'text-success' : 'text-danger' }}">
                            RM {{ number_format($order->total_amount, 2) }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->status == 'Completed' ? 'success' : 'danger' }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada riwayat pesanan dalam periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#orderHistoryTable').DataTable({
            "order": [[ 1, "desc" ]],
            "pageLength": 25,
            "paging": true,
            "searching": true,
            "info": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
            }
        });
    });
</script>
@endsection