<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunExpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TahunExpoController extends Controller
{
    // Method Munculkan Halaman Tahun Expo Beserta Data Secara Descending
    public function showTahunExpo()
    {
        $tahunExpo = TahunExpo::orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tahun' => $item->tahun,
                    'deskripsi' => $item->deskripsi,
                    // 'photo' => $item->photo,
                    // 'photo_url' => $item->photo_url,
                ];
            });

        return Inertia::render('admin/TahunExpoView', [
            'tahunExpo' => $tahunExpo
        ]);
    }

    // Method Insert Data Tahun Expo
    public function insertTahunExpo(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string|max:4',
            'deskripsi' => 'nullable|string|max:255',
            // 'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $tahunExpo = new TahunExpo();
        $tahunExpo->tahun = $request->tahun;
        $tahunExpo->deskripsi = $request->deskripsi;

        // // Proses upload foto jika ada
        // if ($request->hasFile('photo')) {
        //     $photo = $request->file('photo');
        //     $photoPath = $photo->store('tahun-expo', 'public');
        //     $tahunExpo->photo = $photoPath;
        // }

        $tahunExpo->save();

        return redirect()->route('tahun-expo')->with('success', 'Data berhasil disimpan');
    }

    // Method Update Data Tahun Expo (FIXED - Dengan Debug dan Error Handling yang Lebih Baik)
    public function updateTahunExpo(Request $request, $id)
    {
        // Logging untuk debug
//        Log::info('Update TahunExpo Request', [
//            'id' => $id,
//            'all_request' => $request->all(),
//            'has_tahun' => $request->has('tahun'),
//            'tahun_value' => $request->input('tahun'),
//        ]);

        // Validasi dasar, dengan pesan kustom
        $validator = validator($request->all(), [
            'tahun' => 'required|string|max:4',
            'deskripsi' => 'nullable|string|max:255',
            // 'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'tahun.required' => 'The tahun field is required.',
        ]);

        if ($validator->fails()) {
            //            Log::warning('TahunExpo validation failed', [
//                'errors' => $validator->errors()->toArray()
//            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Alternatif: Jika semua cara gagal, kita bisa asumsikan nilai tahun dari frontend
        // Gunakan ini hanya jika benar-benar diperlukan
        $tahun = $request->input('tahun');
        if (empty($tahun) && $request->has('id')) {
            $existingData = TahunExpo::find($id);
            if ($existingData) {
                $tahun = $existingData->tahun;
                Log::info('Using existing tahun value', ['tahun' => $tahun]);
            }
        }

        try {
            $tahunExpo = TahunExpo::findOrFail($id);

            // Update data
            $tahunExpo->tahun = $tahun ?: $request->input('tahun');

            if ($request->has('deskripsi')) {
                $tahunExpo->deskripsi = $request->input('deskripsi');
            }

            // Proses upload foto jika ada
            // if ($request->hasFile('photo')) {
            //     // Hapus foto lama jika ada
            //     if ($tahunExpo->photo && Storage::disk('public')->exists($tahunExpo->photo)) {
            //         Storage::disk('public')->delete($tahunExpo->photo);
            //     }

            //     $photo = $request->file('photo');
            //     $photoPath = $photo->store('tahun-expo', 'public');
            //     $tahunExpo->photo = $photoPath;
            // } else if ($request->input('remove_photo') == '1') {
            //     // Jika user ingin menghapus foto
            //     if ($tahunExpo->photo && Storage::disk('public')->exists($tahunExpo->photo)) {
            //         Storage::disk('public')->delete($tahunExpo->photo);
            //     }
            //     $tahunExpo->photo = null;
            // }

            $tahunExpo->save();

            //            Log::info('TahunExpo updated successfully', ['id' => $id, 'tahun' => $tahunExpo->tahun]);

            return redirect()->route('tahun-expo')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            //            Log::error('Failed to update TahunExpo', [
//                'id' => $id,
//                'error' => $e->getMessage(),
//                'trace' => $e->getTraceAsString()
//            ]);

            return redirect()->back()->with('error', 'Gagal mengubah data: ' . $e->getMessage())->withInput();
        }
    }

    // Method Delete Data Tahun Expo
    public function deleteTahunExpo($id)
    {
        try {
            $tahunExpo = TahunExpo::findOrFail($id);

            // Hapus foto jika ada
            // if ($tahunExpo->photo && Storage::disk('public')->exists($tahunExpo->photo)) {
            //     Storage::disk('public')->delete($tahunExpo->photo);
            // }

            $tahunExpo->delete();

            return redirect()->route('tahun-expo')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            //            Log::error('Failed to delete TahunExpo', [
//                'id' => $id,
//                'error' => $e->getMessage()
//            ]);

            return redirect()->back()->with('error', 'Gagal menghapus data');
        }
    }
    // Get current year's expo data
    // Get current year's expo data
    public function getCurrentTahunExpo()
    {
        $currentYear = date('Y');
        $tahunExpo = TahunExpo::where('tahun', $currentYear)->first();

        if (!$tahunExpo) {
            // If not found, get most recent year
            $tahunExpo = TahunExpo::orderBy('tahun', 'desc')->first();
        }

        return response()->json($tahunExpo);
    }
}
