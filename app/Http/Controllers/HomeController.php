<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function dashboard()
    {
        $files = auth()->user()->files()->latest()->get();
        $totalSize = auth()->user()->files()->sum('file_size');
        $totalFiles = $files->count();
        return view('pages.dashboard', compact('files', 'totalSize', 'totalFiles'));
    }
}
