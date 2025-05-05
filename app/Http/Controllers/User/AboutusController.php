<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AboutusController extends Controller
{
    // Menampilkan halaman about us
    public function showAboutusView()
    {
        return Inertia::render('user/AboutusView');
    }
}
