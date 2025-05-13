<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpoHistory;
use Inertia\Inertia;

class ExpoUserController extends Controller
{
    public function showExpoHistory()
    {
        try {
            $histories = ExpoHistory::with(['tahunExpo', 'images'])
                ->orderBy('published_at', 'desc')
                ->get()
                ->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'title' => $history->title,
                        'content' => $history->content,
                        'cover_image_url' => $history->cover_image ? asset('storage/' . $history->cover_image) : asset('assets/no_image.png'),
                        'published_at' => $history->published_at,
                        'tahun_expo' => $history->tahunExpo->tahun,
                        'images' => $history->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'image_url' => asset('storage/' . $image->image_path),
                                'caption' => $image->caption,
                            ];
                        }),
                    ];
                });

            return Inertia::render('user/ExpoUserView', [
                'histories' => $histories
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function show($id)
    {
        try {
            $history = ExpoHistory::with(['tahunExpo', 'images'])
                ->findOrFail($id);

            $historyData = [
                'id' => $history->id,
                'title' => $history->title,
                'content' => $history->content,
                'cover_image_url' => $history->cover_image ? asset('storage/' . $history->cover_image) : asset('assets/no_image.png'),
                'published_at' => $history->published_at,
                'tahun_expo' => $history->tahunExpo->tahun,
                'images' => $history->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image_url' => asset('storage/' . $image->image_path),
                        'caption' => $image->caption,
                    ];
                }),
            ];

            return Inertia::render('user/ExpoHistoryDetailView', [
                'history' => $historyData
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Data history tidak ditemukan.');
        }
    }
}
