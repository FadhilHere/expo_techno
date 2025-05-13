<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TenantFeedback;
use App\Models\TahunExpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TenantFeedbackUserController extends Controller
{
    public function index()
    {
        try {
            DB::beginTransaction();

            $feedbacks = TenantFeedback::with(['tenant', 'tahunExpo'])
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($feedback) {
                    return [
                        'id' => $feedback->id,
                        'tenant_name' => $feedback->tenant->nama_tenant,
                        'tenant_logo' => $feedback->tenant->logo ? asset('storage/' . $feedback->tenant->logo) : asset('assets/no_image.png'),
                        'tahun_expo' => [
                            'id' => $feedback->tahunExpo->id,
                            'tahun' => $feedback->tahunExpo->tahun,
                        ],
                        'link_type' => $feedback->link_type,
                        'link_url' => $feedback->link_url,
                        'description' => $feedback->description,
                    ];
                });

            $tahunExpo = TahunExpo::orderBy('tahun', 'desc')
                ->get(['id', 'tahun']);

            DB::commit();

            return Inertia::render('user/TenantFeedbackUserView', [
                'feedbacks' => $feedbacks,
                'tahunExpoOptions' => $tahunExpo
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in index TenantFeedbackUser: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function getByTahunExpo($tahunExpoId)
    {
        try {
            DB::beginTransaction();

            $feedbacks = TenantFeedback::with(['tenant', 'tahunExpo'])
                ->where('tahun_expo_id', $tahunExpoId)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($feedback) {
                    return [
                        'id' => $feedback->id,
                        'tenant_name' => $feedback->tenant->nama_tenant,
                        'tenant_logo' => $feedback->tenant->logo ? asset('storage/' . $feedback->tenant->logo) : asset('assets/no_image.png'),
                        'tahun_expo' => [
                            'id' => $feedback->tahunExpo->id,
                            'tahun' => $feedback->tahunExpo->tahun,
                        ],
                        'link_type' => $feedback->link_type,
                        'link_url' => $feedback->link_url,
                        'description' => $feedback->description,
                    ];
                });

            DB::commit();

            return response()->json([
                'status' => 'success',
                'feedbacks' => $feedbacks
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in getByTahunExpo TenantFeedbackUser: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memuat data.'
            ], 500);
        }
    }

    public function getByLinkType($type)
    {
        try {
            DB::beginTransaction();

            if (!in_array($type, ['youtube', 'instagram', 'tiktok'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tipe link tidak valid.'
                ], 400);
            }

            $feedbacks = TenantFeedback::with(['tenant', 'tahunExpo'])
                ->where('link_type', $type)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($feedback) {
                    return [
                        'id' => $feedback->id,
                        'tenant_name' => $feedback->tenant->nama_tenant,
                        'tenant_logo' => $feedback->tenant->logo ? asset('storage/' . $feedback->tenant->logo) : asset('assets/no_image.png'),
                        'tahun_expo' => [
                            'id' => $feedback->tahunExpo->id,
                            'tahun' => $feedback->tahunExpo->tahun,
                        ],
                        'link_type' => $feedback->link_type,
                        'link_url' => $feedback->link_url,
                        'description' => $feedback->description,
                    ];
                });

            DB::commit();

            return response()->json([
                'status' => 'success',
                'feedbacks' => $feedbacks
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in getByLinkType TenantFeedbackUser: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memuat data.'
            ], 500);
        }
    }
}
