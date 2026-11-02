<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard statistik dan daftar data pendaftar.
     */
    public function index(Request $request): View
    {
        $query = Registration::query();

        // Filter Pencarian kata kunci
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('school_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        // Filter Kategori Pembelajaran
        if ($category = $request->input('category')) {
            if (in_array($category, ['gim_edukasi', 'media_pembelajaran_interaktif'])) {
                $query->where('learning_category', $category);
            }
        }

        // Filter Kabupaten / Kota
        if ($regency = $request->input('regency')) {
            if (in_array($regency, RegistrationController::REGION_LIST)) {
                $query->where('regency_city', $regency);
            }
        }

        $registrations = $query->latest('id')->paginate(12)->withQueryString();

        // Data Statistik
        $stats = [
            'total' => Registration::count(),
            'gim_edukasi' => Registration::where('learning_category', 'gim_edukasi')->count(),
            'media_interaktif' => Registration::where('learning_category', 'media_pembelajaran_interaktif')->count(),
            'distinct_regions' => Registration::distinct('regency_city')->count('regency_city'),
        ];

        return view('admin.dashboard', [
            'registrations' => $registrations,
            'stats' => $stats,
            'regions' => RegistrationController::REGION_LIST,
            'filters' => [
                'search' => $request->input('search', ''),
                'category' => $request->input('category', ''),
                'regency' => $request->input('regency', ''),
            ],
        ]);
    }

    /**
     * Ekspor data pendaftar ke file CSV (dapat langsung dibuka di Excel).
     */
    public function export(): StreamedResponse
    {
        $fileName = 'rekap_pendaftar_edutech_btikp_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk kompatibilitas Microsoft Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header Kolom CSV
            fputcsv($handle, [
                'No. Registrasi',
                'Nama Lengkap',
                'Asal Sekolah',
                'Kabupaten / Kota',
                'Email',
                'Nomor WhatsApp',
                'Pilihan Pembelajaran',
                'Link Media Karya',
                'Tanggal Pendaftaran',
            ], ';');

            // Baris Data
            Registration::orderBy('id', 'asc')->chunk(200, function ($batch) use ($handle) {
                foreach ($batch as $row) {
                    fputcsv($handle, [
                        $row->registration_number,
                        $row->full_name,
                        $row->school_name,
                        $row->regency_city,
                        $row->email,
                        "'" . $row->whatsapp_number,
                        $row->category_label,
                        $row->media_link,
                        $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '-',
                    ], ';');
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Hapus data pendaftaran.
     */
    public function destroy(Registration $registration): RedirectResponse
    {
        // Hapus file foto dari disk jika ada
        if ($registration->profile_photo_path && Storage::disk('public')->exists($registration->profile_photo_path)) {
            Storage::disk('public')->delete($registration->profile_photo_path);
        }

        $regNum = $registration->registration_number;
        $registration->delete();

        return back()->with('status', "Data pendaftaran {$regNum} berhasil dihapus.");
    }
}
