<?php

namespace App\Http\Controllers;

use App\Models\DownloadRequest;
use Illuminate\Http\Request;

class DownloadRequestTrackingController extends Controller
{
    public function index()
    {
        return view('download-request.tracking');
    }

    public function check(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $requests = DownloadRequest::where('email', $request->email)
            ->with(['wilayah', 'jenjangPendidikan'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($requests->isEmpty()) {
            return back()->with('error', 'Tidak ada pengajuan ditemukan dengan email tersebut.');
        }

        return view('download-request.tracking', compact('requests'));
    }
}
