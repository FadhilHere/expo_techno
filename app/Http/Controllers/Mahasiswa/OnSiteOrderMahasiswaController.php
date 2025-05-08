<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\OnSiteOrder;
use App\Models\OnSiteOrderItem;
use App\Models\Tenant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class OnSiteOrderMahasiswaController extends Controller
{
    /**
     * Display the on-site order page with tenants and orders.
     */
    public function index()
    {
        // Get the current user
        $user = Auth::user();

        // Get the tenant associated with the logged-in user
        $tenant = null;
        if ($user->tenant_id) {
            $tenant = Tenant::with([
                'products' => function ($query) {
                    $query->select('id', 'tenant_id', 'nama_produk', 'harga', 'deskripsi', 'foto_produk')
                        ->orderBy('nama_produk');
                }
            ])
                ->select('id', 'nama_tenant', 'logo', 'whatsapp_tenant', 'deskripsi')
                ->find($user->tenant_id);

            if ($tenant) {
                $tenant = [
                    'id' => $tenant->id,
                    'nama_tenant' => $tenant->nama_tenant,
                    'deskripsi' => $tenant->deskripsi,
                    'whatsapp_tenant' => $tenant->whatsapp_tenant,
                    'logo_url' => $tenant->logo ? asset('storage/' . $tenant->logo) : asset('assets/no_image.png'),
                    'products' => $tenant->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'nama_produk' => $product->nama_produk,
                            'harga' => $product->harga,
                            'deskripsi' => $product->deskripsi,
                            'foto_url' => $product->foto_produk
                                ? (strpos($product->foto_produk, 'product-photos/') === 0
                                    ? asset('storage/' . $product->foto_produk)
                                    : asset('storage/product-photos/' . $product->foto_produk))
                                : asset('assets/no_image.png'),
                        ];
                    }),
                ];
            }
        }

        // Create an array with just the tenant of the logged-in user
        $tenants = $tenant ? [$tenant] : [];

        // Get orders with items
        $orders = OnSiteOrder::with(['items'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'tenant_id' => $order->tenant_id,
                    'nama_pembeli' => $order->nama_pembeli,
                    'total_amount' => $order->total_amount,
                    'tanggal_pembelian' => Carbon::parse($order->tanggal_pembelian)->format('Y-m-d H:i:s'),
                    'catatan' => $order->catatan,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'produk_id' => $item->produk_id,
                            'nama_produk' => $item->nama_produk,
                            'harga_satuan' => $item->harga_satuan,
                            'qty' => $item->qty,
                            'subtotal' => $item->subtotal,
                        ];
                    }),
                ];
            });

        return Inertia::render('mahasiswa/OnSiteOrderMahasiswaView', [
            'tenants' => $tenants,
            'orders' => $orders,
        ]);
    }

    /**
     * Store a new on-site order.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'nama_pembeli' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:products,id',
            'items.*.nama_produk' => 'required|string|max:255',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        // Verify the tenant belongs to the user
        $user = Auth::user();
        if ($user->tenant_id != $validated['tenant_id']) {
            return back()->withErrors([
                'message' => 'Anda hanya dapat membuat pesanan dari tenant Anda sendiri.'
            ]);
        }

        // Calculate total amount
        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $totalAmount += $item['harga_satuan'] * $item['qty'];
        }

        try {
            DB::beginTransaction();

            // Create the order
            $order = OnSiteOrder::create([
                'tenant_id' => $validated['tenant_id'],
                'nama_pembeli' => $validated['nama_pembeli'],
                'total_amount' => $totalAmount,
                'tanggal_pembelian' => now(),
                'user_id' => Auth::id(),
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // Create order items
            foreach ($validated['items'] as $item) {
                OnSiteOrderItem::create([
                    'on_site_order_id' => $order->id,
                    'produk_id' => $item['produk_id'],
                    'nama_produk' => $item['nama_produk'],
                    'harga_satuan' => $item['harga_satuan'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['harga_satuan'] * $item['qty'],
                ]);
            }

            DB::commit();

            // Return success response
            return back()->with([
                'success' => 'Pesanan berhasil dibuat.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'message' => 'Terjadi kesalahan saat membuat pesanan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete an on-site order.
     */
    public function destroy($id)
    {
        $order = OnSiteOrder::findOrFail($id);

        // Check if the authenticated user owns this order
        if ($order->user_id !== Auth::id()) {
            return back()->withErrors([
                'message' => 'Anda tidak memiliki izin untuk menghapus pesanan ini.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Delete order items first
            OnSiteOrderItem::where('on_site_order_id', $order->id)->delete();

            // Delete the order
            $order->delete();

            DB::commit();

            return back()->with('success', 'Pesanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'message' => 'Terjadi kesalahan saat menghapus pesanan: ' . $e->getMessage()
            ]);
        }
    }
}
