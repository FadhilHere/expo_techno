<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
class HomeController extends Controller
{
    // Menampilkan halaman home
    public function showHomeView()
    {
        return Inertia::render('user/HomeView');
    }
}
