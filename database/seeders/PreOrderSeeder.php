<?php

namespace Database\Seeders;

use App\Models\PreOrder;
use App\Models\PreOrderItem;
use Illuminate\Database\Seeder;

class PreOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some pre-orders
        $preOrders = [
            [
                'tenant_id' => 1,
                'nama_pemesan' => 'Budi Santoso',
                'nomor_wa' => '628567891234',
                'status_pesanan' => 'confirmed',
                'catatan_tambahan' => 'Tolong dibungkus dengan baik.',
                'items' => [
                    ['produk_id' => 1, 'qty' => 1],
                    ['produk_id' => 2, 'qty' => 2],
                ],
            ],
            [
                'tenant_id' => 2,
                'nama_pemesan' => 'Siti Rahayu',
                'nomor_wa' => '628765432109',
                'status_pesanan' => 'pending',
                'catatan_tambahan' => 'Untuk acara ulang tahun tanggal 15 Mei.',
                'items' => [
                    ['produk_id' => 3, 'qty' => 1],
                ],
            ],
            [
                'tenant_id' => 3,
                'nama_pemesan' => 'Ahmad Hidayat',
                'nomor_wa' => '628123987456',
                'status_pesanan' => 'confirmed',
                'catatan_tambahan' => null,
                'items' => [
                    ['produk_id' => 5, 'qty' => 3],
                    ['produk_id' => 6, 'qty' => 1],
                ],
            ],
            [
                'tenant_id' => 4,
                'nama_pemesan' => 'Maya Indah',
                'nomor_wa' => '628543219876',
                'status_pesanan' => 'canceled',
                'catatan_tambahan' => 'Dibatalkan karena perubahan alamat pengiriman.',
                'items' => [
                    ['produk_id' => 7, 'qty' => 2],
                ],
            ],
            [
                'tenant_id' => 5,
                'nama_pemesan' => 'Rudi Hartono',
                'nomor_wa' => '628987654321',
                'status_pesanan' => 'pending',
                'catatan_tambahan' => 'Minta info lebih lanjut mengenai produk.',
                'items' => [
                    ['produk_id' => 9, 'qty' => 1],
                    ['produk_id' => 10, 'qty' => 1],
                ],
            ],
        ];

        foreach ($preOrders as $orderData) {
            $items = $orderData['items'];
            unset($orderData['items']);

            // Create pre-order
            $preOrder = PreOrder::create($orderData);

            // Create pre-order items
            foreach ($items as $item) {
                $product = \App\Models\Product::find($item['produk_id']);

                PreOrderItem::create([
                    'pre_order_id' => $preOrder->id,
                    'produk_id' => $item['produk_id'],
                    'nama_produk' => $product->nama_produk,
                    'harga_satuan' => $product->harga,
                    'qty' => $item['qty'],
                    'subtotal' => $product->harga * $item['qty'],
                ]);
            }
        }
    }
}
