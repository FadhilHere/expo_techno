<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of products for a specific tenant.
     */
    public function showProducts($tenantId)
    {
        // Get tenant details
        $tenant = Tenant::with(['tahunExpo', 'kategori'])
            ->findOrFail($tenantId);

        // Get products for this tenant
        $products = Product::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_produk' => $item->nama_produk,
                    'harga' => $item->harga,
                    'deskripsi' => $item->deskripsi,
                    'foto_produk' => $item->foto_produk,
                    'foto_produk_url' => $item->foto_produk ? asset('storage/' . $item->foto_produk) : asset('assets/no_image.png'),
                    'tenant_id' => $item->tenant_id,
                ];
            });

        // Format tenant data for view - Include deskripsi
        $tenantData = [
            'id' => $tenant->id,
            'nama_tenant' => $tenant->nama_tenant,
            'deskripsi' => $tenant->deskripsi,
            'tahun_expo' => $tenant->tahunExpo ? $tenant->tahunExpo->tahun : null,
            'kategori' => $tenant->kategori ? $tenant->kategori->nama_kategori : null,
        ];

        return Inertia::render('admin/ProductView', [
            'products' => $products,
            'tenant' => $tenantData
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function insertProduct(Request $request, $tenantId)
    {
        // Verify tenant exists
        $tenant = Tenant::findOrFail($tenantId);

        // Validate request
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
        ]);

        // Create new product
        $product = new Product();
        $product->tenant_id = $tenantId;
        $product->nama_produk = $request->nama_produk;
        $product->harga = $request->harga;
        $product->deskripsi = $request->deskripsi;

        // Process photo upload if exists
        if ($request->hasFile('foto_produk')) {
            $foto = $request->file('foto_produk');
            $fotoPath = $foto->store('product-photos', 'public');
            $product->foto_produk = $fotoPath;
        }

        $product->save();

        return redirect()->route('tenant.products', $tenantId)->with('success', 'Produk berhasil disimpan');
    }

    /**
     * Update the specified product.
     */
    public function updateProduct(Request $request, $tenantId, $productId)
    {
        // Validate request
        $validator = validator($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama_produk.required' => 'Nama produk harus diisi.',
            'harga.required' => 'Harga produk harus diisi.',
            'harga.numeric' => 'Harga produk harus berupa angka.',
            'harga.min' => 'Harga produk tidak boleh kurang dari 0.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Get product and verify it belongs to the tenant
            $product = Product::where('id', $productId)
                ->where('tenant_id', $tenantId)
                ->firstOrFail();

            // Update product
            $product->nama_produk = $request->nama_produk;
            $product->harga = $request->harga;

            if ($request->has('deskripsi')) {
                $product->deskripsi = $request->deskripsi;
            }

            // Process photo upload if exists
            if ($request->hasFile('foto_produk')) {
                // Delete old photo if exists
                if ($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)) {
                    Storage::disk('public')->delete($product->foto_produk);
                }

                $foto = $request->file('foto_produk');
                $fotoPath = $foto->store('product-photos', 'public');
                $product->foto_produk = $fotoPath;
            } else if ($request->boolean('remove_foto')) {
                // If user wants to remove photo
                if ($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)) {
                    Storage::disk('public')->delete($product->foto_produk);
                }
                $product->foto_produk = null;
            }

            $product->save();

            return redirect()->route('tenant.products', $tenantId)->with('success', 'Produk berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah produk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified product.
     */
    public function deleteProduct($tenantId, $productId)
    {
        try {
            // Get product and verify it belongs to the tenant
            $product = Product::where('id', $productId)
                ->where('tenant_id', $tenantId)
                ->firstOrFail();

            // Check if product has pre-order items
            if ($product->preOrderItems()->count() > 0) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus produk yang memiliki pre-order');
            }

            // Delete photo if exists
            if ($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)) {
                Storage::disk('public')->delete($product->foto_produk);
            }

            $product->delete();

            return redirect()->route('tenant.products', $tenantId)->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus produk');
        }
    }
}
