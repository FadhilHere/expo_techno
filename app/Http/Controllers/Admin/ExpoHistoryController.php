<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpoHistory;
use App\Models\ExpoHistoryImage;
use App\Models\TahunExpo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ExpoHistoryController extends Controller
{
    // Menampilkan Data Expo History
    public function index()
    {
        try {
            $histories = ExpoHistory::with(['tahunExpo', 'images'])
                ->orderBy('published_at', 'desc')
                ->get()
                ->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'tahun_expo_id' => $history->tahun_expo_id,
                        'title' => $history->title,
                        'content' => $history->content,
                        'cover_image' => $history->cover_image,
                        'cover_image_url' => $history->cover_image ? asset('storage/' . $history->cover_image) : asset('assets/no_image.png'),
                        'published_at' => $history->published_at,
                        'tahun_expo' => $history->tahunExpo,
                        'images' => $history->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'image_path' => $image->image_path,
                                'image_url' => asset('storage/' . $image->image_path),
                                'caption' => $image->caption,
                            ];
                        }),
                    ];
                });

            $tahunExpo = TahunExpo::orderBy('tahun', 'desc')->get();

            return Inertia::render('admin/ExpoHistoryView', [
                'histories' => $histories,
                'tahunExpoOptions' => $tahunExpo
            ]);
        } catch (\Exception $e) {
            Log::error('Error in index ExpoHistory: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    // Menampilkan Form Tambah Expo History
    public function create()
    {
        return inertia('admin/expo-history/Create');
    }

    // Menyimpan Data Expo History Baru
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validasi input
            $validator = Validator::make($request->all(), [
                'tahun_expo_id' => 'required|exists:tahun_expo,id',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'cover_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'published_at' => 'required|date',
                'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'captions.*' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            // Upload dan simpan cover image
            $coverPath = $request->file('cover_image')->store('tahun-expo/history', 'public');

            // Buat record ExpoHistory
            $history = ExpoHistory::create([
                'tahun_expo_id' => $request->tahun_expo_id,
                'title' => $request->title,
                'content' => $request->content,
                'cover_image' => $coverPath,
                'published_at' => $request->published_at,
            ]);

            // Upload dan simpan additional images jika ada
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $index => $image) {
                    $imagePath = $image->store('tahun-expo/history/gallery', 'public');

                    ExpoHistoryImage::create([
                        'expo_history_id' => $history->id,
                        'image_path' => $imagePath,
                        'caption' => $request->captions[$index] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('expo-history.index')->with('success', 'History berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in store ExpoHistory: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    // Menampilkan Form Edit Expo History
    public function edit($id)
    {
        try {
            $history = ExpoHistory::with(['tahunExpo', 'images'])->findOrFail($id);

            return inertia('admin/expo-history/Edit', [
                'history' => $history,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in edit ExpoHistory: ' . $e->getMessage());
            return back()->with('error', 'Data history tidak ditemukan.');
        }
    }

    // Mengupdate Data Expo History
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $history = ExpoHistory::findOrFail($id);

            // Validasi input
            $validator = Validator::make($request->all(), [
                'tahun_expo_id' => 'required|exists:tahun_expo,id',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'published_at' => 'required|date',
                'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'captions.*' => 'nullable|string|max:255',
                'images_to_delete' => 'nullable|array',
                'images_to_delete.*' => 'numeric'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            // Update cover image jika ada
            if ($request->hasFile('cover_image')) {
                if ($history->cover_image) {
                    Storage::disk('public')->delete($history->cover_image);
                }
                $coverPath = $request->file('cover_image')->store('tahun-expo/history', 'public');
                $history->cover_image = $coverPath;
            }

            // Update data history
            $history->update([
                'tahun_expo_id' => $request->tahun_expo_id,
                'title' => $request->title,
                'content' => $request->content,
                'published_at' => $request->published_at,
            ]);

            // Handle penghapusan gambar yang ditunda
            if ($request->has('images_to_delete')) {
                foreach ($request->images_to_delete as $imageId) {
                    $image = ExpoHistoryImage::where('expo_history_id', $history->id)
                        ->where('id', $imageId)
                        ->first();

                    if ($image) {
                        Storage::disk('public')->delete($image->image_path);
                        $image->delete();
                    }
                }
            }

            // Tambah gambar baru jika ada
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $index => $image) {
                    $imagePath = $image->store('tahun-expo/history/gallery', 'public');

                    ExpoHistoryImage::create([
                        'expo_history_id' => $history->id,
                        'image_path' => $imagePath,
                        'caption' => $request->captions[$index] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('expo-history.index')->with('success', 'History berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in update ExpoHistory: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    // Menghapus Data Expo History
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $history = ExpoHistory::with('images')->findOrFail($id);

            // Hapus cover image
            if ($history->cover_image) {
                Storage::disk('public')->delete($history->cover_image);
            }

            // Hapus semua gambar terkait
            foreach ($history->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Hapus history
            $history->delete();

            DB::commit();
            return redirect()->route('expo-history.index')->with('success', 'History berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in destroy ExpoHistory: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    // Menghapus Gambar dari Gallery
    public function deleteImage($imageId)
    {
        try {
            DB::beginTransaction();

            $image = ExpoHistoryImage::findOrFail($imageId);

            // Hapus file gambar
            Storage::disk('public')->delete($image->image_path);

            // Hapus record dari database
            $image->delete();

            DB::commit();
            return back()->with('success', 'Gambar berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in deleteImage: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus gambar.');
        }
    }

    // Mengupdate Caption Gambar
    public function updateImageCaption(Request $request, $imageId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'caption' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            $image = ExpoHistoryImage::findOrFail($imageId);
            $image->update(['caption' => $request->caption]);

            return back()->with('success', 'Caption berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error in updateImageCaption: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui caption.');
        }
    }
}
