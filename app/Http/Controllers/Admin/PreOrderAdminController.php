<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreOrder;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PreOrderAdminController extends Controller
{
    // Method untuk menampilkan halaman daftar pre-order
    public function showPreOrders()
    {
        $preOrders = PreOrder::with(['tenant', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tenant_id' => $item->tenant_id,
                    'tenant_name' => $item->tenant ? $item->tenant->nama_tenant : null,
                    'nama_pemesan' => $item->nama_pemesan,
                    'nomor_wa' => $item->nomor_wa,
                    'status_pesanan' => $item->status_pesanan,
                    'catatan_tambahan' => $item->catatan_tambahan,
                    'total' => $item->total, // Menggunakan accessor method
                    'item_count' => $item->items->count(),
                    'created_at' => $item->created_at->format('d-m-Y H:i'),
                    // Menyertakan data items untuk modal detail
                    'items' => $item->items->map(function ($orderItem) {
                        return [
                            'id' => $orderItem->id,
                            'produk_id' => $orderItem->produk_id,
                            'nama_produk' => $orderItem->nama_produk,
                            'harga_satuan' => $orderItem->harga_satuan,
                            'qty' => $orderItem->qty,
                            'subtotal' => $orderItem->subtotal,
                        ];
                    }),
                ];
            });

        // Hapus bagian tenant untuk filter karena tidak diperlukan
        return Inertia::render('admin/PreOrderView', [
            'preOrders' => $preOrders,
        ]);
    }

    // Method untuk update status pre-order - FIX UNTUK UPDATE STATUS
    public function updatePreOrderStatus(Request $request, $id)
    {
        // Log detail request untuk debugging
        Log::info('Update PreOrder Status Request', [
            'id' => $id,
            'request' => $request->all(),
        ]);

        // Validasi input dengan nilai enum yang benar
        $validated = $request->validate([
            'status_pesanan' => 'required|string|in:pending,confirmed,canceled',
        ]);

        $newStatus = $validated['status_pesanan'];

        try {
            // Verifikasi ID sebelum query
            if (!is_numeric($id)) {
                throw new \Exception("ID pesanan tidak valid");
            }

            // Cari data pre-order
            $preOrder = PreOrder::find($id);

            if (!$preOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            $oldStatus = $preOrder->status_pesanan;

            // Pastikan status baru berbeda dengan status lama
            if ($oldStatus === $newStatus) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status tidak berubah'
                ]);
            }

            // Gunakan model Eloquent untuk update
            $preOrder->status_pesanan = $newStatus;
            $saved = $preOrder->save();

            if (!$saved) {
                throw new \Exception("Gagal menyimpan perubahan status pesanan");
            }

            // Log detail untuk debugging
            Log::info('Status updated successfully', [
                'id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'time' => now()->toDateTimeString()
            ]);

            // Return response JSON untuk AJAX request
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diubah',
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update PreOrder status', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return error response untuk AJAX request
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status pesanan: ' . $e->getMessage()
            ], 500);
        }
    }


    // Method untuk menghapus pre-order
    public function deletePreOrder($id)
    {
        try {
            Log::info('Deleting pre-order', ['id' => $id]);

            $preOrder = PreOrder::findOrFail($id);

            // Hapus semua item terkait dengan pre-order ini terlebih dahulu
            foreach ($preOrder->items as $item) {
                $item->delete();
            }

            // Kemudian hapus pre-order itu sendiri
            $preOrder->delete();

            Log::info('Pre-order deleted successfully', ['id' => $id]);

            return redirect()->route('pre-orders')->with('success', 'Pesanan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Failed to delete PreOrder', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Gagal menghapus pesanan: ' . $e->getMessage());
        }
    }
}
