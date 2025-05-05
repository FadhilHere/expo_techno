<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriTenant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KategoriTenantController extends Controller
{
    /**
     * Display a listing of the Kategori Tenant.
     */
    public function showKategoriTenant()
    {
        $kategoriTenant = KategoriTenant::orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_kategori' => $item->nama_kategori,
                ];
            });

        return Inertia::render('admin/KategoriTenantView', [
            'kategoriTenant' => $kategoriTenant
        ]);
    }

    /**
     * Store a newly created Kategori Tenant.
     */
    public function insertKategoriTenant(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategoriTenant = new KategoriTenant();
        $kategoriTenant->nama_kategori = $request->nama_kategori;
        $kategoriTenant->save();

        return redirect()->route('kategori-tenant')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Update the specified Kategori Tenant.
     */
    public function updateKategoriTenant(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        try {
            $kategoriTenant = KategoriTenant::findOrFail($id);
            $kategoriTenant->nama_kategori = $request->nama_kategori;
            $kategoriTenant->save();

            return redirect()->route('kategori-tenant')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified Kategori Tenant.
     */
    public function deleteKategoriTenant($id)
    {
        try {
            $kategoriTenant = KategoriTenant::findOrFail($id);

            // Check if there are any tenants associated with this category
            if ($kategoriTenant->tenants()->count() > 0) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus kategori yang memiliki tenant');
            }

            $kategoriTenant->delete();

            return redirect()->route('kategori-tenant')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data');
        }
    }
}
