@extends('layouts.app')

@section('title', 'Active Orders')
@section('page-title', 'Daftar Pesanan Aktif (Dalam Proses)')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Active Orders</li>
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
        <h6 class="m-0 fw-bold text-primary">
            <i class="fas fa-list-alt me-1"></i> Pesanan yang Membutuhkan Tindakan
        </h6>
        <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-1"></i> New Order (Manual)
        </a>
    </div>
    <div class="card-body">
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <div>
                Data di halaman ini diperbarui secara real-time. Total {{ $totalActiveOrders }} pesanan aktif.
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered" id="activeOrdersTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Waktu Dibuat</th>
                        <th>Tipe</th>
                        <th>Customer</th>
                        <th>Ringkasan Item</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activeOrders as $order)
                    <tr>
                        <td class="fw-bold">{{ $order->order_id }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('H:i:s d/M') }}<br><small class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}</small></td>
                        <td><span class="badge bg-secondary">{{ $order->type }}</span></td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->items_description }}</td>
                        <td class="fw-bold text-success">RM {{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            @php
                                $statusClass = [
                                    'Pending' => 'warning',
                                    'Cooking' => 'warning',
                                    'Ready' => 'info',
                                    'Waiting Runner' => 'danger'
                                ][$order->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ $order->status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                            @if ($order->status == 'Ready' || $order->status == 'Waiting Runner')
                                <button type="button" class="btn btn-sm btn-success" onclick="completeOrder({{ $order->id }})" title="Selesaikan Pesanan"><i class="fas fa-check"></i></button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">🎉 Semua pesanan sudah selesai. Tidak ada pesanan aktif saat ini.</td>
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
        $('#activeOrdersTable').DataTable({
            "order": [[ 1, "asc" ]],
            "pageLength": 10,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
            }
        });
    });

    function completeOrder(orderId) {
        if (confirm('Yakin ingin menandai pesanan ini sebagai Selesai (Completed)?')) {
            alert('Fitur Complete Order sedang diproses untuk Order ID: ' + orderId);
        }
    }

</script>
@endsection