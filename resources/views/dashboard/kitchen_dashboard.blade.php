@extends('layouts.app')

@section('title', 'Dashboard Kios')
@section('page-title', 'Dashboard Kios & Kitchen Overview')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard Kios</li>
@endsection

@section('header-action')
<div class="btn-group">
    <button class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>New Order (Manual)
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-2">Today's Orders</h6>
                        <h3 class="mb-0 text-primary">{{ $totalTodayOrders ?? 0 }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-shopping-cart fa-lg text-primary"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-info">
                        <i class="fas fa-sync-alt me-1"></i>Real-time Data
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-2">Kitchen Queue</h6>
                        <h3 class="mb-0 text-warning">{{ $kitchenQueue->count() ?? 0 }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-fire fa-lg text-warning"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i>Focus Required
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-2">Pending Orders</h6>
                        <h3 class="mb-0 text-danger">{{ $activeOrders->where('status', 'Pending')->count() }}</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-clock fa-lg text-danger"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-hourglass-half me-1"></i>Waiting Processing
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-2">Ready Orders</h6>
                        <h3 class="mb-0 text-success">{{ $activeOrders->where('status', 'Ready')->count() }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-check-circle fa-lg text-success"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-success">
                        <i class="fas fa-truck me-1"></i>Ready for Pickup
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Active Orders Table -->
    <div class="col-lg-8 mb-4">
        <div class="card dashboard-card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list-alt text-primary me-2"></i>Active Orders 
                        <span class="badge bg-primary ms-2">{{ $activeOrders->count() }}</span>
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">All Orders</a></li>
                            <li><a class="dropdown-item" href="#">Pending Only</a></li>
                            <li><a class="dropdown-item" href="#">Cooking Only</a></li>
                            <li><a class="dropdown-item" href="#">Ready Only</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="activeOrdersTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order ID</th>
                                <th>Type</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th class="pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orderListBody">
                            @forelse ($activeOrders as $order)
                            <tr data-order-id="{{ $order->id }}" class="order-row align-middle {{ $order->status == 'Pending' ? 'table-warning new-order' : '' }}">
                                <td class="ps-4 fw-bold">{{ $order->order_id }}</td>
                                <td>
                                    @php
                                        $typeBadge = [
                                            'Dine-in' => ['bg-primary', 'fa-utensils'],
                                            'Takeaway' => ['bg-success', 'fa-bag-shopping'],
                                            'Delivery' => ['bg-secondary', 'fa-truck']
                                        ][$order->type] ?? ['bg-dark', 'fa-question'];
                                    @endphp
                                    <span class="badge {{ $typeBadge[0] }}">
                                        <i class="fas {{ $typeBadge[1] }} me-1"></i>{{ $order->type }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium" title="{{ $order->items_description }}">
                                            {{ Str::limit($order->items_description, 25) }}
                                        </span>
                                        @if($order->customer_name)
                                            <small class="text-muted">Customer: {{ $order->customer_name }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="fw-bold text-nowrap">RM {{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'Pending' => ['warning', 'fa-clock'],
                                            'Cooking' => ['warning', 'fa-fire'], 
                                            'Ready' => ['info', 'fa-check'],
                                            'Waiting Runner' => ['danger', 'fa-person-walking'],
                                            'Completed' => ['success', 'fa-check-double'],
                                            'Cancelled' => ['secondary', 'fa-ban']
                                        ][$order->status] ?? ['light', 'fa-question'];
                                    @endphp
                                    <span class="badge bg-{{ $statusConfig[0] }} text-dark">
                                        <i class="fas {{ $statusConfig[1] }} me-1"></i>{{ $order->status }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}</small>
                                </td>
                                <td class="pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" data-order-id="{{ $order->id }}" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                    @if($order->status == 'Pending')
                                        <form action="{{ route('orders.update_status', $order->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="new_status" value="Cooking">
                                            <button type="submit" class="btn btn-warning text-dark" title="Start Cooking">
                                                <i class="fas fa-play me-1"></i>Start
                                            </button>
                                        </form>
                                        @elseif($order->status == 'Cooking')
                                        <form action="{{ route('orders.update_status', $order->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="new_status" value="Ready">
                                            <button type="submit" class="btn btn-info text-white" title="Mark as Ready">
                                                <i class="fas fa-check me-1"></i>Ready
                                            </button>
                                        </form>
                                        @elseif($order->status == 'Ready' || $order->status == 'Waiting Runner')
                                        <form action="{{ route('orders.update_status', $order->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="new_status" value="Completed">
                                            <button type="submit" class="btn btn-success" title="Complete Order" onclick="return confirm('Apakah Anda yakin pesanan ini sudah Selesai/Diambil?');">
                                                <i class="fas fa-flag-checkered"></i>
                                            </button>
                                        </form>
                                    @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <p class="mb-0">No active orders at the moment</p>
                                        <small>New orders will appear here automatically</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kitchen Queue -->
    <div class="col-lg-4 mb-4">
        <div class="card dashboard-card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h5 class="mb-0">
                    <i class="fas fa-fire text-warning me-2"></i>Kitchen Queue 
                    <span class="badge bg-warning text-dark ms-2">{{ $kitchenQueue->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="kitchen-queue-container" style="max-height: 500px; overflow-y: auto;">
                    @forelse ($kitchenQueue as $queueItem)
                    <div class="kitchen-item p-3 border-bottom {{ $queueItem->status == 'Pending' ? 'bg-light bg-opacity-50' : '' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong class="d-block">{{ $queueItem->order_id }}</strong>
                                @if($queueItem->customer_name)
                                    <small class="text-muted">By: {{ $queueItem->customer_name }}</small>
                                @endif
                            </div>
                            @if($queueItem->status == 'Pending')
                                <span class="badge bg-danger">
                                    <i class="fas fa-bell me-1"></i>NEW
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i>{{ $queueItem->status }}
                                </span>
                            @endif
                        </div>
                        
                        <p class="mb-2 small text-dark">{{ $queueItem->items_description }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-{{ $queueItem->type == 'Dine-in' ? 'utensils' : ($queueItem->type == 'Takeaway' ? 'bag-shopping' : 'truck') }} me-1"></i>
                                {{ $queueItem->type }}
                            </small>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($queueItem->created_at)->diffForHumans() }}
                            </small>
                        </div>
                        
                        @if($queueItem->status == 'Pending')
                        <div class="mt-2">
                            <form action="{{ route('orders.update_status', $queueItem->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="new_status" value="Cooking">
                                <button type="submit" class="btn btn-sm btn-warning w-100">
                                    <i class="fas fa-play me-1"></i>Start Cooking
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="text-success mb-3">
                            <i class="fas fa-mug-hot fa-2x"></i>
                        </div>
                        <h6 class="text-muted">Kitchen Queue Empty</h6>
                        <small class="text-muted">Time for a break!</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="orderDetailModalLabel">
                    Detail Pesanan <span id="modal-order-id" class="fw-bold"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <h6 class="text-primary"><i class="fas fa-info-circle me-1"></i> Informasi Umum</h6>
                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 35%;">Status:</th>
                                    <td id="modal-status"></td>
                                </tr>
                                <tr>
                                    <th scope="row">Tipe Pesanan:</th>
                                    <td id="modal-type"></td>
                                </tr>
                                <tr>
                                    <th scope="row">Pelanggan:</th>
                                    <td id="modal-customer-name"></td>
                                </tr>
                                <tr>
                                    <th scope="row">Waktu Pesan:</th>
                                    <td id="modal-created-at"></td>
                                </tr>
                                <tr>
                                    <th scope="row">Total Bayar:</th>
                                    <td id="modal-total-amount" class="fw-bold text-success"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-truck me-1"></i> Detail Delivery</h6>
                        <div id="delivery-details-card">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 35%;">Alamat:</th>
                                        <td id="modal-address"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Opsi Runner:</th>
                                        <td id="modal-runner-option"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Runner Fee:</th>
                                        <td id="modal-runner-fee" class="fw-bold"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="no-delivery-info" class="alert alert-light text-muted p-2 mt-2" style="display: none;">
                            Pesanan ini adalah Dine-in atau Takeaway.
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="text-primary"><i class="fas fa-list-ul me-1"></i> Daftar Item Pesanan</h6>
                <div id="modal-items-description" class="alert alert-info py-2">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.dashboard-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.kitchen-item {
    transition: background-color 0.3s ease;
    border-left: 4px solid transparent;
}

.kitchen-item:hover {
    background-color: #f8f9fa !important;
}

.kitchen-item:nth-child(odd) {
    border-left-color: #ffc107;
}

.kitchen-item:nth-child(even) {
    border-left-color: #fd7e14;
}

.table > :not(caption) > * > * {
    padding: 0.75rem 0.5rem;
}

@keyframes flash {
    0% { background-color: rgba(255, 193, 7, 0.3); }
    50% { background-color: rgba(255, 193, 7, 0.1); }
    100% { background-color: rgba(255, 193, 7, 0.3); }
}

.new-order {
    animation: flash 2s infinite;
}
</style>
<style>
.toast-container {
    z-index: 9999;
}

.toast {
    backdrop-filter: blur(10px);
}

@keyframes flash {
    0% { background-color: rgba(255, 193, 7, 0.3); }
    50% { background-color: rgba(255, 193, 7, 0.1); }
    100% { background-color: rgba(255, 193, 7, 0.3); }
}

.new-order {
    animation: flash 2s infinite;
}
</style>
@endpush

@section('scripts')
<script src="{{ asset('assets/js/kitchen.js') }}"></script>
@endsection