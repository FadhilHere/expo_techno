<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantFeedback;
use App\Models\Tenant;
use App\Models\TahunExpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TenantFeedbackController extends Controller
{
    public function index()
    {
        try {
            $feedbacks = TenantFeedback::with(['tenant', 'tahunExpo'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($feedback) {
                    return [
                        'id' => $feedback->id,
                        'tenant' => [
                            'id' => $feedback->tenant->id,
                            'name' => $feedback->tenant->nama_tenant,
                            'logo' => $feedback->tenant->logo ? asset('storage/' . $feedback->tenant->logo) : asset('assets/no_image.png')
                        ],
                        'tahun_expo' => [
                            'id' => $feedback->tahunExpo->id,
                            'tahun' => $feedback->tahunExpo->tahun,
                        ],
                        'link_type' => $feedback->link_type,
                        'link_url' => $feedback->link_url,
                        'description' => $feedback->description,
                        'is_active' => $feedback->is_active,
                    ];
                });

            $tenants = Tenant::orderBy('nama_tenant')
                ->get(['id', 'nama_tenant as name', 'logo'])
                ->map(function ($tenant) {
                    return [
                        'id' => $tenant->id,
                        'name' => $tenant->name,
                        'logo' => $tenant->logo ? asset('storage/' . $tenant->logo) : asset('assets/no_image.png')
                    ];
                });

            $tahunExpo = TahunExpo::orderBy('tahun', 'desc')
                ->get(['id', 'tahun']);

            return Inertia::render('admin/TenantFeedbackView', [
                'feedbacks' => $feedbacks,
                'tenants' => $tenants,
                'tahunExpoOptions' => $tahunExpo
            ]);
        } catch (\Exception $e) {
            Log::error('Error in index TenantFeedback: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'tenant_id' => 'required|exists:tenants,id',
                'tahun_expo_id' => 'required|exists:tahun_expo,id',
                'link_type' => 'required|string|in:youtube,instagram,tiktok',
                'link_url' => 'required|url',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            TenantFeedback::create($request->all());

            DB::commit();
            return redirect()->route('tenant-feedback.index')->with('success', 'Feedback berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in store TenantFeedback: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $feedback = TenantFeedback::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tenant_id' => 'required|exists:tenants,id',
                'tahun_expo_id' => 'required|exists:tahun_expo,id',
                'link_type' => 'required|string|in:youtube,instagram,tiktok',
                'link_url' => 'required|url',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            $feedback->update($request->all());

            DB::commit();
            return redirect()->route('tenant-feedback.index')->with('success', 'Feedback berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in update TenantFeedback: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $feedback = TenantFeedback::findOrFail($id);
            $feedback->delete();

            DB::commit();
            return redirect()->route('tenant-feedback.index')->with('success', 'Feedback berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in destroy TenantFeedback: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function toggleActive($id)
    {
        try {
            DB::beginTransaction();

            $feedback = TenantFeedback::findOrFail($id);
            $feedback->update(['is_active' => !$feedback->is_active]);

            DB::commit();
            return back()->with('success', 'Status feedback berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in toggleActive TenantFeedback: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengubah status.');
        }
    }
}
