<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\View\View;

class SchoolDirectoryController extends Controller
{
    /**
     * Display the school directory listing page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('public.sekolah-index');
    }

    /**
     * Display the school detail page.
     *
     * @param Sekolah $sekolah
     * @return View
     */
    public function show(Sekolah $sekolah): View
    {
        // Eager load relationships for the detail page
        $sekolah->load([
            'wilayah',
            'jenjangPendidikan',
            'pelaksanaanAsesmen.siklusAsesmen',
        ]);

        return view('public.sekolah-detail', compact('sekolah'));
    }
}
