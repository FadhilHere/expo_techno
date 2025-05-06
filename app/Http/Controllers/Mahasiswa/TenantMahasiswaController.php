<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
class TenantMahasiswaController extends Controller
{
    // Menampilkan halaman template tenant mahasiswa dengan inertia
    public function index()
    {
        return Inertia::render('mahasiswa/TenantMahasiswaView');
    }
}
