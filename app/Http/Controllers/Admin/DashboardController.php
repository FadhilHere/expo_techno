<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreOrder;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Ambil 5 order terbaru
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
            'recentOrders' => $recentOrders
        ]);
    }
}
