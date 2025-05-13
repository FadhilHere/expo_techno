<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AccountController extends Controller
{
    // Method Munculkan Halaman Account Beserta Data Secara Descending Kecuali Role Super Admin
    public function showAccountView()
    {
        $account = User::orderBy('created_at', 'desc')
            ->where('role', '!=', 'super_admin')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'username' => $item->username,
                    'role' => $item->role,
                    'is_active' => $item->is_active,
                    'tenant_id' => $item->tenant_id,
                ];
            });

        return Inertia::render('super_admin/AccountView', ['account' => $account]);
    }

    // Method Insert Data Account
    public function insertAccount(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,mahasiswa',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $account = new User();
        $account->username = $request->username;
        $account->password = Hash::make($request->password);
        $account->role = $request->role;
        $account->is_active = true;

        // Only set tenant_id if role is mahasiswa, otherwise set to null
        $account->tenant_id = $request->role === 'mahasiswa' ? $request->tenant_id : null;

        $account->save();

        return redirect()->route('super-admin.account')->with('success', 'Account berhasil ditambahkan');
    }

    // Method Update Data Account
    public function updateAccount(Request $request, $id)
    {
        $request->validate([
            'password' => 'nullable|string|min:8',
            'role' => 'nullable|string|in:admin,mahasiswa',
            'is_active' => 'nullable|boolean',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $account = User::findOrFail($id);

        if ($request->filled('password')) {
            $account->password = Hash::make($request->password);
        }

        if ($request->filled('role')) {
            $account->role = $request->role;

            // If role is not mahasiswa, set tenant_id to null
            if ($request->role !== 'mahasiswa') {
                $account->tenant_id = null;
            }
        }

        if ($request->has('is_active')) {
            $account->is_active = $request->is_active;
        }

        // Only update tenant_id if role is mahasiswa, otherwise keep it null
        if ($account->role === 'mahasiswa') {
            $account->tenant_id = $request->tenant_id; // This will be null if not provided
        } else {
            $account->tenant_id = null;
        }

        $account->save();

        return redirect()->route('super-admin.account')->with('success', 'Account berhasil diupdate');
    }

    // Method Delete Data Account
    public function deleteAccount($id)
    {
        $account = User::findOrFail($id);

        // Prevent deletion of super_admin account
        if ($account->role === 'super_admin') {
            return redirect()->route('super-admin.account')->with('error', 'Super Admin account tidak dapat dihapus');
        }

        $account->delete();

        return redirect()->route('super-admin.account')->with('success', 'Account berhasil dihapus');
    }

    // Method Update Multiple Account Status
    public function updateMultipleAccountStatus(Request $request)
    {
        // \Log::info('Multiple status update requested', [
        //     'account_ids' => $request->account_ids,
        //     'is_active' => $request->is_active,
        //     'wants_json' => $request->wantsJson(),
        //     'inertia' => $request->header('X-Inertia')
        // ]);

        $request->validate([
            'account_ids' => 'required|array',
            'account_ids.*' => 'exists:users,id',
            'is_active' => 'required|boolean'
        ]);

        // Prevent updating super_admin accounts
        $accounts = User::whereIn('id', $request->account_ids)
            ->where('role', '!=', 'super_admin')
            ->get();

        // \Log::info('Accounts found for status update', [
        //     'count' => $accounts->count(),
        //     'ids' => $accounts->pluck('id')->toArray()
        // ]);

        foreach ($accounts as $account) {
            $account->is_active = $request->is_active;
            $account->save();
        }

        $status = $request->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $message = 'Account yang dipilih berhasil ' . $status;

        // For AJAX requests, return JSON
        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'updated_count' => $accounts->count(),
                'ids' => $accounts->pluck('id')->toArray()
            ]);
        }

        // For Inertia requests, always return a redirect
        return redirect()->route('super-admin.account')->with('success', $message);
    }

    // Method Untuk Mengambil Data Tenant Nama dan Id
    public function getTenantData()
    {
        $tenants = Tenant::select('id', 'nama_tenant')->get();

        // Make sure we're returning with the proper keys for the dropdown
        $formattedTenants = $tenants->map(function($tenant) {
            return [
                'id' => $tenant->id,
                'name' => $tenant->nama_tenant // Map nama_tenant to name for the frontend
            ];
        });

        // For debugging
        // \Log::info('Tenant data requested', ['count' => $tenants->count(), 'data' => $formattedTenants]);

        return response()->json($formattedTenants);
    }
}
