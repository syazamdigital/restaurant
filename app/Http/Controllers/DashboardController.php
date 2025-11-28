<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $today = Carbon::today();
        $startDate = Carbon::today()->subDays(6);
        $ordersCompletedToday = Order::whereDate('created_at', $today)
                                     ->where('status', 'Completed')
                                     ->get();
                                     
        $revenueToday = $ordersCompletedToday->sum('total_amount');
        $ordersTodayCount = $ordersCompletedToday->count();
        $aov = $ordersTodayCount > 0 ? $revenueToday / $ordersTodayCount : 0;
        $activeOrdersCount = Order::whereDate('created_at', $today)
                                  ->whereIn('status', ['Pending', 'Cooking', 'Ready', 'Waiting Runner'])
                                  ->count();

        $topOrderTypes = Order::select('type', DB::raw('COUNT(*) as total'))
                            ->whereMonth('created_at', Carbon::now()->month)
                            ->groupBy('type')
                            ->orderByDesc('total')
                            ->get();

        $dailyRevenues = Order::select(
                                 DB::raw('DATE(created_at) as date'), 
                                 DB::raw('SUM(total_amount) as revenue')
                            )
                            ->where('created_at', '>=', $startDate)
                            ->where('status', 'Completed')
                            ->groupBy('date')
                            ->orderBy('date', 'ASC')
                            ->get()
                            ->map(function ($item) {
                                return ['date' => $item->date, 'revenue' => (float)$item->revenue];
                            });

        $recentOrders = Order::orderByDesc('created_at')->limit(5)->get();

        return view('pages.dashboard', [
            'revenueToday' => $revenueToday,
            'ordersTodayCount' => $ordersTodayCount,
            'aov' => $aov,
            'activeOrdersCount' => $activeOrdersCount,
            'topOrderTypes' => $topOrderTypes,
            'dailyRevenues' => $dailyRevenues,
            'recentOrders' => $recentOrders,
        ]);
    }
}
