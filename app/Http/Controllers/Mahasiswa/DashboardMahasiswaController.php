<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\OnSiteOrder;
use App\Models\PreOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardMahasiswaController extends Controller
{
    public function index()
    {
        // Dapatkan user yang sedang login
        $user = Auth::user();

        // Pastikan user adalah tim_mhs dan memiliki tenant_id
        if ($user->role !== 'mahasiswa' || !$user->tenant_id) {
            return redirect()->route('dashboard'); // Redirect ke dashboard utama jika bukan tim_mhs
        }

        $tenantId = $user->tenant_id;

        // Hitung statistik untuk tenant
        $stats = $this->getTenantStats($tenantId);

        // Dapatkan 5 pesanan terbaru (gabungan PreOrder dan OnSiteOrder)
        $recentOrders = $this->getRecentOrders($tenantId);

        return Inertia::render('mahasiswa/DashboardMahasiswaView', [
            'tenantStats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * Mendapatkan statistik untuk tenant tertentu
     */
    private function getTenantStats($tenantId)
    {
        // Hitung total pendapatan dari pre-order yang sudah dikonfirmasi
        $preOrderTotal = PreOrder::where('tenant_id', $tenantId)
            ->where('status_pesanan', 'confirmed')
            ->with('items')
            ->get()
            ->sum(function ($order) {
                return $order->total;
            });

        // Hitung total pendapatan dari on-site order (semua on-site sudah pasti confirmed)
        $onSiteOrderTotal = OnSiteOrder::where('tenant_id', $tenantId)
            ->sum('total_amount');

        // Total pendapatan
        $totalRevenue = $preOrderTotal + $onSiteOrderTotal;

        // Hitung jumlah pesanan pre-order yang sudah dikonfirmasi
        $confirmedPreOrders = PreOrder::where('tenant_id', $tenantId)
            ->where('status_pesanan', 'confirmed')
            ->count();

        // Total pesanan yang sudah dikonfirmasi (pre-order + on-site)
        $totalOnSiteOrders = OnSiteOrder::where('tenant_id', $tenantId)->count();
        $confirmedOrders = $confirmedPreOrders + $totalOnSiteOrders;

        // Hitung jumlah pesanan pre-order yang masih pending
        $pendingOrders = PreOrder::where('tenant_id', $tenantId)
            ->where('status_pesanan', 'pending')
            ->count();

        // Breakdown jumlah pesanan berdasarkan tipe
        $preOrderCount = PreOrder::where('tenant_id', $tenantId)->count();
        $onSiteOrderCount = OnSiteOrder::where('tenant_id', $tenantId)->count();

        return [
            'totalRevenue' => $totalRevenue,
            'confirmedOrders' => $confirmedOrders,
            'pendingOrders' => $pendingOrders,
            'preOrderCount' => $preOrderCount,
            'onSiteOrderCount' => $onSiteOrderCount,
        ];
    }

    /**
     * Mendapatkan 5 pesanan terbaru (gabungan pre-order dan on-site)
     */
    private function getRecentOrders($tenantId)
    {
        // Ambil 5 pre-order terbaru
        $preOrders = PreOrder::where('tenant_id', $tenantId)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'created_at' => $order->created_at,
                    'nama_pemesan' => $order->nama_pemesan,
                    'nomor_wa' => $order->nomor_wa,
                    'total' => $order->total,
                    'status_pesanan' => $order->status_pesanan,
                    'type' => 'pre-order',
                ];
            });

        // Ambil 5 on-site order terbaru
        $onSiteOrders = OnSiteOrder::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'created_at' => $order->created_at,
                    'nama_pemesan' => $order->nama_pembeli,
                    'nomor_wa' => '-', // On-site order mungkin tidak memiliki nomor WA
                    'total' => $order->total_amount,
                    'status_pesanan' => 'confirmed', // Karena on-site selalu confirmed
                    'type' => 'on-site',
                ];
            });

        // Gabungkan, urutkan berdasarkan created_at, dan ambil 5 terbaru
        return $preOrders->concat($onSiteOrders)
            ->sortByDesc('created_at')
            ->values()
            ->take(5);
    }
}
