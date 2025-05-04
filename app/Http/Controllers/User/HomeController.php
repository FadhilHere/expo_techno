<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\KategoriTenant;
use App\Models\PreOrder; // Tambahkan model PreOrder
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    // Menampilkan halaman home
    public function showHomeView()
    {
        $tenants = Tenant::with(['tahunExpo', 'kategori', 'products'])
            ->orderBy('created_at', 'desc')
            ->limit(6)  // Ambil 6 tenant terbaru
            ->get()
            ->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'nama_tenant' => $tenant->nama_tenant,
                    'deskripsi' => $tenant->deskripsi,
                    'whatsapp_tenant' => $tenant->whatsapp_tenant,
                    'logo_url' => $tenant->logo ? asset('storage/' . $tenant->logo) : asset('assets/no_image.png'),
                    'tahun_expo' => $tenant->tahunExpo ? $tenant->tahunExpo->tahun : null,
                    'kategori' => $tenant->kategori ? $tenant->kategori->nama_kategori : null,
                    'product_count' => $tenant->products->count(),
                ];
            });

        // Ambil total tenant untuk statistik
        $totalTenants = Tenant::count();

        // Ambil total orders dari tabel pre_orders
        $totalOrders = PreOrder::count();

        // Ambil kategori untuk filter
        $kategoriTenants = KategoriTenant::orderBy('nama_kategori', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_kategori' => $item->nama_kategori,
                ];
            });

        return Inertia::render('user/HomeView', [
            'tenants' => $tenants,
            'kategoriTenants' => $kategoriTenants,
            'stats' => [
                'totalTenants' => $totalTenants,
                'totalOrders' => $totalOrders, // Tambahkan total orders
            ],
            'flash' => [
                'order_success' => session('order_success')
            ]
        ]);
    }

    // Menampilkan halaman detail tenant
    public function showTenantDetail($id)
    {
        $tenant = Tenant::with(['tahunExpo', 'kategori', 'products'])
            ->findOrFail($id);

        $tenantData = [
            'id' => $tenant->id,
            'nama_tenant' => $tenant->nama_tenant,
            'deskripsi' => $tenant->deskripsi,
            'whatsapp_tenant' => $tenant->whatsapp_tenant,
            'logo_url' => $tenant->logo ? asset('storage/' . $tenant->logo) : asset('assets/no_image.png'),
            'tahun_expo' => $tenant->tahunExpo ? $tenant->tahunExpo->tahun : null,
            'kategori' => $tenant->kategori ? $tenant->kategori->nama_kategori : null,
            'products' => $tenant->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'nama_produk' => $product->nama_produk,
                    'harga' => $product->harga,
                    'deskripsi' => $product->deskripsi,
                    'foto_url' => $product->foto_produk ? asset('storage/' . $product->foto_produk) : asset('assets/no_image.png'),
                ];
            }),
        ];

        return Inertia::render('user/TenantDetailView', [
            'tenant' => $tenantData
        ]);
    }
}
