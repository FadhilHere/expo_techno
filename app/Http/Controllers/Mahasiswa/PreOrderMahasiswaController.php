<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreOrder;
use Inertia\Inertia;

class PreOrderMahasiswaController extends Controller
{
    // Method menampilkan data pre order dari tenant yang dimiliki oleh siswa
    public function showPreOrderMahasiswa()
    {
        $preOrders = PreOrder::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_pemesan' => $item->nama_pemesan,
                    'nomor_wa' => $item->nomor_wa,
                    'status_pesanan' => $item->status_pesanan,
                    'catatan_tambahan' => $item->catatan_tambahan,
                    'created_at' => $item->created_at->format('d-m-Y H:i'),
                ];
            });

        return Inertia::render('mahasiswa/PreOrderMahasiswaView', [
            'preOrders' => $preOrders
        ]);
    }

    // Method menampilkan data pre order secara detail
    public function showPreOrderMahasiswaDetail($id)
    {
        $preOrder = PreOrder::with(['items.product'])
            ->where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (!$preOrder) {
            return response()->json(['error' => 'Pre-order tidak ditemukan'], 404);
        }

        $preOrderData = [
            'id' => $preOrder->id,
            'nama_pemesan' => $preOrder->nama_pemesan,
            'nomor_wa' => $preOrder->nomor_wa,
            'status_pesanan' => $preOrder->status_pesanan,
            'catatan_tambahan' => $preOrder->catatan_tambahan,
            'total' => $preOrder->total,
            'created_at' => $preOrder->created_at->format('d-m-Y H:i'),
            'items' => $preOrder->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_produk' => $item->nama_produk,
                    'harga_satuan' => $item->harga_satuan,
                    'qty' => $item->qty,
                    'subtotal' => $item->subtotal,
                ];
            }),
        ];

        return response()->json($preOrderData);
    }

    // Method update status pre order
    public function updateStatusPreOrder(Request $request, $id)
    {
        $preOrder = PreOrder::find($id);
        $preOrder->status_pesanan = $request->status;
        $preOrder->save();
        return redirect()->back()->with('success', 'Status pre order berhasil diubah');
    }
}
