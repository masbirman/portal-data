<?php

namespace App\Http\Controllers;

use App\Models\DownloadRequest;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DownloadRequestController extends Controller
{
    public function index()
    {
        $wilayahs = Wilayah::orderBy('nama')->get();
        $jenjangs = \App\Models\JenjangPendidikan::orderBy('id')->get();
        return view('download-request.index', compact('wilayahs', 'jenjangs'));
    }

    public function store(Request $request)
    {
        // Rate limiting per IP (2 requests per day)
        $ipAddress = $request->ip();
        $whitelistedIps = explode(',', env('RATE_LIMIT_WHITELIST_IPS', ''));
        $whitelistedIps = array_map('trim', $whitelistedIps);

        // Check if IP is whitelisted
        if (!in_array($ipAddress, $whitelistedIps)) {
            $today = now()->startOfDay();
            $requestCount = DownloadRequest::where('ip_address', $ipAddress)
                ->where('created_at', '>=', $today)
                ->count();

            if ($requestCount >= 2) {
                $tomorrow = now()->addDay()->startOfDay();
                $hoursLeft = (int) now()->diffInHours($tomorrow);
                $minutesLeft = (int) (now()->diffInMinutes($tomorrow) % 60);

                return back()
                    ->withErrors([
                        'rate_limit' => "Anda telah mencapai batas maksimal pengajuan hari ini (2 pengajuan per hari). Silakan coba lagi besok atau dalam {$hoursLeft} jam {$minutesLeft} menit."
                    ])
                    ->withInput();
            }
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'instansi' => 'required|string|max:255',
            'tujuan_penggunaan' => 'required|string',
            'data_type' => 'required|in:asesmen_nasional,survei_lingkungan_belajar,tes_kemampuan_akademik',
            'tahun' => 'required|integer|min:2023|max:' . date('Y'),
            'wilayah_id' => 'required|integer|min:0',
            'jenjang_pendidikan_id' => 'required|integer|min:0',
            // 'cf-turnstile-response' => 'required|turnstile',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DownloadRequest::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'instansi' => $request->instansi,
            'tujuan_penggunaan' => $request->tujuan_penggunaan,
            'data_type' => $request->data_type,
            'tahun' => $request->tahun,
            'wilayah_id' => $request->wilayah_id == 0 ? null : $request->wilayah_id,
            'jenjang_pendidikan_id' => $request->jenjang_pendidikan_id == 0 ? null : $request->jenjang_pendidikan_id,
            'status' => 'pending',
            'ip_address' => $ipAddress,
        ]);

        return redirect()->route('download-request.success');
    }

    public function success()
    {
        return view('download-request.success');
    }

    public function download(Request $request, $token)
    {
        $downloadRequest = DownloadRequest::where('download_token', $token)->firstOrFail();

        if (!$downloadRequest->isTokenValid()) {
            abort(403, 'Token tidak valid atau sudah kadaluarsa');
        }

        // Mark as downloaded
        $downloadRequest->markAsDownloaded();

        // Generate filename
        $wilayah = $downloadRequest->wilayah_id == null ? 'Semua_Wilayah' : str_replace(' ', '_', $downloadRequest->wilayah->nama ?? 'Unknown');
        $jenjang = $downloadRequest->jenjang_pendidikan_id == null ? 'Semua_Jenjang' : ($downloadRequest->jenjangPendidikan->nama ?? 'Unknown');
        $filename = sprintf(
            'Data_%s_%s_%s_%s_%s.xlsx',
            ucfirst(str_replace('_', ' ', $downloadRequest->data_type)),
            $downloadRequest->tahun,
            $jenjang,
            $wilayah,
            now()->format('Ymd')
        );

        // Generate and download the requested data
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DataRequestExport(
                $downloadRequest->data_type,
                $downloadRequest->tahun,
                $downloadRequest->wilayah_id,
                $downloadRequest->jenjang_pendidikan_id
            ),
            $filename
        );
    }
}
