<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TahunExpo;
use App\Models\KategoriTenant;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
class TenantMahasiswaController extends Controller
{

    // Menampilkan data tenant mahasiswa berdasarkan user tenant_id dan sekaligus product tenant
    public function showTenantMahasiswa()
    {
        $tenants = Tenant::with(['tahunExpo', 'kategori'])
            ->where('id', auth()->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_tenant' => $item->nama_tenant,
                    'deskripsi' => $item->deskripsi,
                    'whatsapp_tenant' => $item->whatsapp_tenant,
                    'logo' => $item->logo,
                    'logo_url' => $item->logo ? asset('storage/' . $item->logo) : asset('assets/no_image.png'),
                    'tahun_expo_id' => $item->tahun_expo_id,
                    'tahun_expo' => $item->tahunExpo ? $item->tahunExpo->tahun : null,
                    'kategori_id' => $item->kategori_id,
                    'kategori' => $item->kategori ? $item->kategori->nama_kategori : null,
                ];
            });

        $products = Product::where('tenant_id', auth()->user()->tenant_id)
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
                ];
            });

        return Inertia::render('mahasiswa/TenantMahasiswaView', [
            'tenants' => $tenants,
            'products' => $products
        ]);

    }

    // Menambahkan Product Baru
    public function insertProduct(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $product = new Product();
        $product->tenant_id = auth()->user()->tenant_id;
        $product->nama_produk = $request->nama_produk;
        $product->harga = $request->harga;
        $product->deskripsi = $request->deskripsi;

        if ($request->hasFile('foto_produk')) {
            $foto = $request->file('foto_produk');
            $fotoPath = $foto->store('product-photos', 'public');
            $product->foto_produk = $fotoPath;
        }

        $product->save();

        return redirect()->route('mahasiswa.tenant', auth()->user()->tenant_id)->with('success', 'Produk berhasil disimpan');
    }

    // Mengupdate Product - Accept any method - POST or PUT - for compatibility
    public function updateProduct(Request $request, $id, $productId)
    {
        // Log request info for debugging
        // Log::info('Update Product Request', [
        //     'method' => $request->method(),
        //     'has_method_field' => $request->has('_method'),
        //     'method_field' => $request->input('_method'),
        //     'all_inputs' => $request->all()
        // ]);

        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $product = Product::where('tenant_id', auth()->user()->tenant_id)
            ->where('id', $productId)
            ->firstOrFail();

        // Update product data from the request
        $product->nama_produk = $request->input('nama_produk');
        $product->harga = $request->input('harga');
        $product->deskripsi = $request->input('deskripsi');

        // Process photo upload if exists
        if($request->hasFile('foto_produk')){
            // delete old photo if exists
            if($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)){
                Storage::disk('public')->delete($product->foto_produk);
            }

            $foto = $request->file('foto_produk');
            $fotoPath = $foto->store('product-photos', 'public');
            $product->foto_produk = $fotoPath;
        } else if($request->boolean('remove_foto')){
            // If user wants to remove photo
            if($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)){
                Storage::disk('public')->delete($product->foto_produk);
            }
            $product->foto_produk = null;
        }

        $product->save();

        return redirect()->back()->with('success', 'Produk berhasil diubah');
    }

    // Menghapus Product
    public function deleteProduct($id, $productId)
    {
        Log::info('Attempting to delete product', [
            'tenant_id' => $id,
            'product_id' => $productId,
            'user_id' => auth()->id()
        ]);

        $product = Product::where('id', $productId)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->firstOrFail();

        // Delete photo if exists
        if($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)){
            Log::info('Deleting product photo', [
                'product_id' => $productId,
                'photo_path' => $product->foto_produk
            ]);
            Storage::disk('public')->delete($product->foto_produk);
        }

        $product->delete();

        Log::info('Product deleted successfully', [
            'product_id' => $productId,
            'product_name' => $product->nama_produk
        ]);

        return redirect()->route('mahasiswa.tenant', auth()->user()->tenant_id)->with('success', 'Produk berhasil dihapus');
    }



}
