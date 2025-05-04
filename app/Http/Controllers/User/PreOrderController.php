<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PreOrderController extends Controller
{
    /**
     * Store a newly created PreOrder.
     */
    public function InsertPreOrder(Request $request)
    {
        // Validasi input
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'nama_pemesan' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:20',
            'catatan_tambahan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Buat pre-order
            $preOrder = new PreOrder();
            $preOrder->tenant_id = $request->tenant_id;
            $preOrder->nama_pemesan = $request->nama_pemesan;
            $preOrder->nomor_wa = $request->nomor_wa;
            $preOrder->status_pesanan = 'pending';
            $preOrder->catatan_tambahan = $request->catatan_tambahan;
            $preOrder->save();

            // Daftar items untuk pesan sukses
            $total = 0;
            $orderItems = [];

            // Tambahkan item pre-order
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id']);
                $subtotal = $product->harga * $item['quantity'];
                $total += $subtotal;

                $preOrderItem = new PreOrderItem();
                $preOrderItem->pre_order_id = $preOrder->id;
                $preOrderItem->produk_id = $product->id;
                $preOrderItem->nama_produk = $product->nama_produk;
                $preOrderItem->harga_satuan = $product->harga;
                $preOrderItem->qty = $item['quantity'];
                $preOrderItem->subtotal = $subtotal;
                $preOrderItem->save();

                // Tambahkan ke daftar items untuk pesan sukses
                $orderItems[] = [
                    'nama_produk' => $product->nama_produk,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'harga_satuan' => $product->harga
                ];
            }

            DB::commit();

            // Ambil tenant untuk data sukses
            $tenant = Tenant::findOrFail($request->tenant_id);

            // Redirect ke home dengan flash message
            // Di PreOrderController
            return redirect()->route('home')->with([
                'order_success' => [
                    'tenant_name' => $tenant->nama_tenant,
                    'items' => $orderItems,
                    'catatan_tambahan' => $preOrder->catatan_tambahan,
                    'total' => $total
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pre-order error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat pre-order: ' . $e->getMessage());
        }
    }
}
