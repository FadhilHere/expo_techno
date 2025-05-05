<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreOrder;
use App\Models\Tenant;
use App\Models\PreOrderItem;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Get the current date and time
        $now = Carbon::now();
        $startOfToday = Carbon::today();
        $endOfToday = Carbon::today()->endOfDay();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // 1. Calculate Total Revenue (current month)
        $currentMonthRevenue = PreOrder::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status_pesanan', 'confirmed') // Hanya hitung pesanan yang confirmed
            ->sum(DB::raw('(SELECT SUM(subtotal) FROM pre_order_items WHERE pre_order_id = pre_orders.id)'));

        // Last month revenue for comparison
        $lastMonthRevenue = PreOrder::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->where('status_pesanan', 'confirmed')
            ->sum(DB::raw('(SELECT SUM(subtotal) FROM pre_order_items WHERE pre_order_id = pre_orders.id)'));

        // Calculate percentage change for revenue
        $revenueChange = $lastMonthRevenue > 0
            ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // 2. Count Active Tenants (with at least one product)
        $activeTenants = Tenant::whereHas('products')->count();
        $lastMonthActiveTenants = Tenant::whereHas('products', function ($query) use ($startOfLastMonth, $endOfLastMonth) {
            $query->where('created_at', '<=', $endOfLastMonth);
        })->count();

        // Calculate percentage change for tenants
        $tenantChange = $lastMonthActiveTenants > 0
            ? round((($activeTenants - $lastMonthActiveTenants) / $lastMonthActiveTenants) * 100, 1)
            : 0;

        // 3. Count Total Orders (current month)
        $currentMonthOrders = PreOrder::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $lastMonthOrders = PreOrder::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        // Calculate percentage change for orders
        $orderChange = $lastMonthOrders > 0
            ? round((($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
            : 0;

        // 4. Count Today's Orders
        $todayOrders = PreOrder::whereBetween('created_at', [$startOfToday, $endOfToday])->count();

        // Yesterday's orders for comparison
        $yesterdayStart = Carbon::yesterday();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();
        $yesterdayOrders = PreOrder::whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])->count();

        // Calculate percentage change for today's orders
        $todayOrderChange = $yesterdayOrders > 0
            ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1)
            : 0;

        // Create dashboard stats object
        $dashboardStats = [
            'totalRevenue' => $currentMonthRevenue,
            'revenueChange' => $revenueChange,
            'activeTenants' => $activeTenants,
            'tenantChange' => $tenantChange,
            'totalOrders' => $currentMonthOrders,
            'orderChange' => $orderChange,
            'todayOrders' => $todayOrders,
            'todayOrderChange' => $todayOrderChange
        ];

        // Get 5 most recent orders with full details
        $recentOrders = PreOrder::with('tenant')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'created_at' => $order->created_at,
                    'tenant' => [
                        'id' => $order->tenant->id,
                        'nama_tenant' => $order->tenant->nama_tenant
                    ],
                    'nama_pemesan' => $order->nama_pemesan,
                    'nomor_wa' => $order->nomor_wa,
                    'status_pesanan' => $order->status_pesanan,
                    'total' => $order->total, // Gunakan accessor total
                ];
            });

        return Inertia::render('admin/Dashboard', [
            'title' => 'Dashboard',
            'recentOrders' => $recentOrders,
            'dashboardStats' => $dashboardStats
        ]);
    }
}
