<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TahunExpo;
use App\Models\KategoriTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TenantController extends Controller
{
    // Method Munculkan Halaman Tenant Beserta Data Secara Descending
    public function showTenant()
    {
        $tenants = Tenant::with(['tahunExpo', 'kategori'])
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

        // Get data for dropdowns
        $tahunExpos = TahunExpo::orderBy('tahun', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tahun' => $item->tahun,
                ];
            });

        $kategoriTenants = KategoriTenant::orderBy('nama_kategori', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_kategori' => $item->nama_kategori,
                ];
            });

        return Inertia::render('admin/TenantView', [
            'tenants' => $tenants,
            'tahunExpos' => $tahunExpos,
            'kategoriTenants' => $kategoriTenants
        ]);
    }

    // Method Insert Data Tenant
    public function insertTenant(Request $request)
    {
        // Log untuk debug
        Log::info('Insert Tenant Request', [
            'all_request' => $request->all(),
            'has_logo' => $request->hasFile('logo'),
        ]);

        $request->validate([
            'tahun_expo_id' => 'required|exists:tahun_expo,id', // Perbaikan nama tabel
            'kategori_id' => 'required|exists:kategori_tenants,id',
            'nama_tenant' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'whatsapp_tenant' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $tenant = new Tenant();
        $tenant->tahun_expo_id = $request->tahun_expo_id;
        $tenant->kategori_id = $request->kategori_id;
        $tenant->nama_tenant = $request->nama_tenant;
        $tenant->deskripsi = $request->deskripsi;
        $tenant->whatsapp_tenant = $request->whatsapp_tenant;

        // Proses upload logo jika ada
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('tenant-logos', 'public');
            $tenant->logo = $logoPath;
        }

        $tenant->save();

        return redirect()->route('tenant')->with('success', 'Data berhasil disimpan');
    }

    // Method Update Data Tenant
    public function updateTenant(Request $request, $id)
    {
        // Log untuk debug
        Log::info('Update Tenant Request', [
            'id' => $id,
            'all_request' => $request->all(),
            'has_file' => $request->hasFile('logo'),
            'remove_logo' => $request->input('remove_logo'),
        ]);

        // Validasi dasar, dengan pesan kustom
        $validator = validator($request->all(), [
            'tahun_expo_id' => 'required|exists:tahun_expo,id', // Perbaikan nama tabel
            'kategori_id' => 'required|exists:kategori_tenants,id',
            'nama_tenant' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'whatsapp_tenant' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'tahun_expo_id.required' => 'The tahun expo field is required.',
            'tahun_expo_id.exists' => 'The selected tahun expo is invalid.',
            'kategori_id.required' => 'The kategori field is required.',
            'kategori_id.exists' => 'The selected kategori is invalid.',
            'nama_tenant.required' => 'The nama tenant field is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $tenant = Tenant::findOrFail($id);

            // Update data
            $tenant->tahun_expo_id = $request->tahun_expo_id;
            $tenant->kategori_id = $request->kategori_id;
            $tenant->nama_tenant = $request->nama_tenant;

            if ($request->has('deskripsi')) {
                $tenant->deskripsi = $request->deskripsi;
            }

            if ($request->has('whatsapp_tenant')) {
                $tenant->whatsapp_tenant = $request->whatsapp_tenant;
            }

            // Proses upload logo jika ada
            if ($request->hasFile('logo')) {
                Log::info('Processing logo upload', ['filename' => $request->file('logo')->getClientOriginalName()]);

                // Hapus logo lama jika ada
                if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
                    Storage::disk('public')->delete($tenant->logo);
                }

                $logo = $request->file('logo');
                $logoPath = $logo->store('tenant-logos', 'public');
                $tenant->logo = $logoPath;
            } else if ($request->boolean('remove_logo')) {
                Log::info('Removing logo');

                // Jika user ingin menghapus logo
                if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
                    Storage::disk('public')->delete($tenant->logo);
                }
                $tenant->logo = null;
            }

            $tenant->save();

            return redirect()->route('tenant')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            Log::error('Failed to update Tenant', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Gagal mengubah data: ' . $e->getMessage())->withInput();
        }
    }

    // Method Delete Data Tenant
    public function deleteTenant($id)
    {
        try {
            Log::info('Deleting tenant', ['id' => $id]);

            $tenant = Tenant::findOrFail($id);

            // Nonaktifkan dulu pengecekan relasi untuk debugging
            /*
            // Check jika tenant memiliki products atau preOrders
            if ($tenant->products()->count() > 0 || $tenant->preOrders()->count() > 0) {
                Log::warning('Cannot delete tenant with products or pre-orders', [
                    'id' => $id,
                    'product_count' => $tenant->products()->count(),
                    'preorder_count' => $tenant->preOrders()->count()
                ]);
                return redirect()->back()->with('error', 'Tidak dapat menghapus tenant yang memiliki produk atau pre-order');
            }
            */

            // Hapus logo jika ada
            if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
                Storage::disk('public')->delete($tenant->logo);
            }

            // Hapus semua produk terkait terlebih dahulu
            foreach ($tenant->products as $product) {
                // Hapus foto produk jika ada
                if ($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)) {
                    Storage::disk('public')->delete($product->foto_produk);
                }
                $product->delete();
            }

            // Hapus semua preorder terkait terlebih dahulu jika ada
            if (method_exists($tenant, 'preOrders')) {
                foreach ($tenant->preOrders as $preOrder) {
                    $preOrder->delete();
                }
            }

            // Sekarang hapus tenant
            $tenant->delete();

            Log::info('Tenant deleted successfully', ['id' => $id]);

            return redirect()->route('tenant')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Failed to delete Tenant', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
