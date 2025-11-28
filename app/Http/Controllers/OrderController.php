<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // 1. Menampilkan Halaman Menu Digital (QR Code Scan)
    public function showMenu()
    {
        return view('pages.customer.menu'); // Menggunakan file Blade yang kita buat di atas
    }

    // 2. Menerima Pesanan dari Pelanggan (QR Scan)
    public function submitCustomerOrder(Request $request)
    {
        $validated = $request->validate([
            'order_type' => 'required|in:Dine-in,Takeaway,Delivery',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20', // HP wajib untuk semua
            
            // Fields opsional/kondisional untuk Delivery
            'delivery_address' => 'nullable|string|max:255',
            'runner_option' => 'nullable|in:Normal,Prioritas',
            'runner_fee' => 'nullable|numeric',
        ]);

        $orderItems = [];
        $totalMenuPrice = 0; // Hanya harga menu
        $rawItems = [];

        $menuData = [
            'item_1' => ['name' => 'Ayam Penyet', 'price' => 10.00],
            'item_2' => ['name' => 'Ayam Gepuk', 'price' => 10.00],
            'item_3' => ['name' => 'Ayam Geprek', 'price' => 10.00],
            'item_4' => ['name' => 'Tea Ice', 'price' => 3.50],
            'item_5' => ['name' => 'Tea O Ice', 'price' => 3.00],
            'item_6' => ['name' => 'Milo Ice', 'price' => 4.50],
            'item_7' => ['name' => 'Milo O Ice', 'price' => 4.00],
        ];

        // 1. Proses Item dan Level Pedas
        for ($i = 1; $i <= 7; $i++) {
            $key = "item_$i";
            $qty = (int) $request->input("qty_$i", 0);
            
            if ($qty > 0 && isset($menuData[$key])) {
                $item = $menuData[$key];
                $subtotal = $qty * $item['price'];
                $totalMenuPrice += $subtotal;
                
                $itemNameWithLevel = $item['name'];
                
                // Cek Level Pedas (hanya untuk item 1, 2, 3)
                if ($i >= 1 && $i <= 3) {
                    $level = $request->input("level_$i", 'Level 0');
                    $itemNameWithLevel .= " ($level)";
                }

                $orderItems[] = [
                    'item_name' => $itemNameWithLevel,
                    'quantity' => $qty,
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ];
                $rawItems[] = "$qty x " . $itemNameWithLevel;
            }
        }
        
        if (empty($orderItems)) {
            return redirect()->back()->with('error', 'Pesanan harus memilih minimal 1 menu!');
        }
        
        // 2. Hitung Total Akhir
        $runnerFee = 0.00;
        $runnerOption = null;
        $deliveryAddress = null;
        $status = 'Pending';
        
        if ($validated['order_type'] === 'Delivery') {
             // Wajibkan alamat dan opsi runner untuk delivery
             if (empty($validated['delivery_address']) || empty($validated['runner_option'])) {
                 return redirect()->back()->with('error', 'Alamat dan Opsi Runner wajib diisi untuk Delivery.');
             }

             $runnerFee = (float) $validated['runner_fee'];
             $runnerOption = $validated['runner_option'];
             $deliveryAddress = $validated['delivery_address'];
             $status = 'Waiting Runner'; // Status khusus untuk delivery
        }
        
        $totalFinal = $totalMenuPrice + $runnerFee;

        // --- SIMULASI PENYIMPANAN KE DATABASE (Table: orders) ---
        $order = DB::table('orders')->insertGetId([
            'order_id' => 'QR-' . time(),
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'type' => $validated['order_type'],
            'total_amount' => $totalFinal,
            'status' => $status, 
            'source' => 'QR Scan',
            'items_description' => implode(', ', $rawItems),
            'runner_option' => $runnerOption, // Tambahan field
            'delivery_address' => $deliveryAddress, // Tambahan field
            'runner_fee' => $runnerFee, // Tambahan field
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // --- SIMULASI NOTIFIKASI KE KIOS ---
        // Di sini Anda akan mengirim event real-time (Laravel Echo/Pusher)
        // agar Dashboard Kios langsung me-refresh atau menambahkan item baru.
        // event(new NewOrderArrived(Order::find($order))); 
        
        return view('pages.customer.success', [
            'orderId' => 'QR-' . time(), 
            'total' => $totalFinal,
            'name' => $validated['customer_name']
        ]);
    }
    
    // 3. Menampilkan Dashboard Kios (Kitchen)
    public function showKitchen()
    {
        $activeOrders = DB::table('orders')
                          ->whereIn('status', ['Pending', 'Cooking', 'Ready', 'Waiting Runner'])
                          ->orderBy('created_at', 'asc') 
                          ->get();
                          
        
        $kitchenQueue = DB::table('orders')
                         ->whereIn('status', ['Pending', 'Cooking'])
                         ->orderBy('created_at', 'asc')
                         ->limit(5)
                         ->get();

        return view('dashboard.kitchen_dashboard', [
            'activeOrders' => $activeOrders,
            'kitchenQueue' => $kitchenQueue,
            'totalTodayOrders' => DB::table('orders')->whereDate('created_at', today())->count(),
        ]);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'new_status' => 'required|in:Cooking,Ready,Completed,Waiting Runner',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        $order->status = $request->new_status;
        
        if ($order->type == 'Delivery' && $request->new_status == 'Ready') {
            $order->status = 'Waiting Runner';
        }
        
        $order->save();
        return back()->with('success', "Status pesanan {$order->order_id} berhasil diubah menjadi {$order->status}.");
    }

    public function getActiveOrdersJson() 
    {
        try {
            $activeOrders = Order::whereNotIn('status', ['Completed', 'Cancelled'])
                ->orderBy('created_at', 'desc')
                ->get(); // HAPUS with(['items']) karena tidak ada relasi
            
            return response()->json([
                'success' => true,
                'orders' => $activeOrders
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getActiveOrdersJson: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'orders' => []
            ]);
        }
    }

    public function getOrderCounts()
    {
        try {
            $counts = \App\Models\Order::select('status', \DB::raw('count(*) as total'))
                    ->whereIn('status', ['Pending', 'Cooking', 'Ready', 'Waiting Runner'])
                    ->groupBy('status')
                    ->pluck('total', 'status');

            return response()->json([
                'pending' => $counts['Pending'] ?? 0,
                'cooking' => $counts['Cooking'] ?? 0,
                'ready' => $counts['Ready'] ?? 0,
                'waiting_runner' => $counts['Waiting Runner'] ?? 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'pending' => 0,
                'cooking' => 0,
                'ready' => 0,
                'waiting_runner' => 0,
            ]);
        }
    }

    public function getOrderDetails($id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json(['error' => 'Pesanan tidak ditemukan.'], 404);
            }

            $statusConfig = [
                'Pending' => ['warning', 'fa-clock'], 
                'Cooking' => ['warning', 'fa-fire'], 
                'Ready' => ['info', 'fa-check'],
                'Waiting Runner' => ['danger', 'fa-person-walking'],
                'Completed' => ['success', 'fa-check-double'],
                'Cancelled' => ['secondary', 'fa-ban']
            ];
            $statusInfo = $statusConfig[$order->status] ?? ['light', 'fa-question'];

            return response()->json([
                'order_id' => $order->order_id,
                'status_html' => '<span class="badge bg-' . $statusInfo[0] . ' text-dark"><i class="fas ' . $statusInfo[1] . ' me-1"></i>' . $order->status . '</span>',
                'type' => $order->type,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'created_at' => \Carbon\Carbon::parse($order->created_at)->translatedFormat('d M Y, H:i:s') . ' (' . \Carbon\Carbon::parse($order->created_at)->diffForHumans() . ')',
                'total_amount' => 'RM ' . number_format($order->total_amount, 2),
                'items_description' => nl2br(e($order->items_description)),
                'delivery' => $order->type == 'Delivery',
                'delivery_address' => nl2br(e($order->delivery_address ?? '-')),
                'runner_option' => $order->runner_option ?? '-',
                'runner_fee' => $order->runner_fee ? 'RM ' . number_format($order->runner_fee, 2) : 'RM 0.00',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data pesanan.'], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            
            $validStatuses = ['Pending', 'Cooking', 'Ready', 'Waiting Runner', 'Completed', 'Cancelled'];
            $newStatus = $request->input('new_status');
            
            // Validasi status
            if (!in_array($newStatus, $validStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ], 400);
            }
            
            $oldStatus = $order->status;
            $order->status = $newStatus;
            $order->save();
            
            // Log the status change
            \Log::info("Order {$order->order_id} status changed from {$oldStatus} to {$newStatus}");
            
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diupdate',
                'order' => [
                    'id' => $order->id,
                    'order_id' => $order->order_id,
                    'status' => $order->status,
                    'status_html' => $this->getStatusHtml($order->status)
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error updating order status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getStatusHtml($status)
    {
        $statusConfig = [
            'Pending' => ['warning', 'fa-clock'], 
            'Cooking' => ['warning', 'fa-fire'], 
            'Ready' => ['info', 'fa-check'],
            'Waiting Runner' => ['danger', 'fa-person-walking'],
            'Completed' => ['success', 'fa-check-double'],
            'Cancelled' => ['secondary', 'fa-ban']
        ];
        
        $statusInfo = $statusConfig[$status] ?? ['light', 'fa-question'];
        
        return '<span class="badge bg-' . $statusInfo[0] . ' text-dark">' .
            '<i class="fas ' . $statusInfo[1] . ' me-1"></i>' . $status . '</span>';
    }


    public function activeOrders()
    {
        $activeStatuses = ['Pending', 'Cooking', 'Ready', 'Waiting Runner'];

        $activeOrders = Order::whereIn('status', $activeStatuses)
                             ->orderBy('created_at', 'asc') 
                             ->get();

        $totalActiveOrders = $activeOrders->count();

        return view('pages.orders.active_orders', compact('activeOrders', 'totalActiveOrders'));
    }

    public function completeOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'Completed';
        $order->save();

        return redirect()->route('orders.active')->with('success', 'Pesanan berhasil diselesaikan.');
    }

    public function orderHistory(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::today()->subDays(7)->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::today()->endOfDay();
        $historyOrders = Order::whereIn('status', ['Completed', 'Cancelled'])
                              ->whereBetween('updated_at', [$startDate, $endDate]) // Filter berdasarkan tanggal selesai/update
                              ->orderBy('updated_at', 'desc')
                              ->get();
        
        if ($request->input('export')) {
            return response()->json(['message' => 'Export data akan dijalankan...'], 200);
        }

        return view('pages.orders.order_history', [
            'historyOrders' => $historyOrders,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d')
        ]);
    }
    
    public function show($id)
    {
        $order = Order::findOrFail($id);
        // Tampilkan detail pesanan
        return view('order_detail', compact('order'));
    }
}