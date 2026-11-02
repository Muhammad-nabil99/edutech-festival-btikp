<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Daftar 17 Kabupaten dan Kota di Provinsi Sulawesi Tenggara.
     */
    public const REGION_LIST = [
        'Kota Kendari',
        'Kota Baubau',
        'Kabupaten Bombana',
        'Kabupaten Buton',
        'Kabupaten Buton Selatan',
        'Kabupaten Buton Tengah',
        'Kabupaten Buton Utara',
        'Kabupaten Kolaka',
        'Kabupaten Kolaka Timur',
        'Kabupaten Kolaka Utara',
        'Kabupaten Konawe',
        'Kabupaten Konawe Kepulauan',
        'Kabupaten Konawe Selatan',
        'Kabupaten Konawe Utara',
        'Kabupaten Muna',
        'Kabupaten Muna Barat',
        'Kabupaten Wakatobi',
    ];

    /**
     * Menampilkan formulir pendaftaran EduTech Festival BTIKP.
     */
    public function create(): View
    {
        return view('registrations.create', [
            'regions' => self::REGION_LIST,
        ]);
    }

    /**
     * Memproses penyimpanan pendaftaran baru.
     */
    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Normalisasi nomor WA
        $rawWa = preg_replace('/[^0-9]/', '', $validated['whatsapp_number']);
        if (str_starts_with($rawWa, '0')) {
            $formattedWa = '62' . substr($rawWa, 1);
        } elseif (str_starts_with($rawWa, '62')) {
            $formattedWa = $rawWa;
        } else {
            $formattedWa = '62' . $rawWa;
        }

        // Upload Foto Profil ke storage/app/public/profile-photos
        $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');

        // Simpan pendaftar dalam transaksi database
        $registration = DB::transaction(function () use ($validated, $formattedWa, $photoPath) {
            $regNumber = Registration::generateRegistrationNumber();

            return Registration::create([
                'registration_number' => $regNumber,
                'full_name' => trim($validated['full_name']),
                'school_name' => trim($validated['school_name']),
                'regency_city' => $validated['regency_city'],
                'email' => strtolower(trim($validated['email'])),
                'whatsapp_number' => $formattedWa,
                'learning_category' => $validated['learning_category'],
                'profile_photo_path' => $photoPath,
                'media_link' => trim($validated['media_link']),
            ]);
        });

        return redirect()
            ->route('registration.success', ['registration_number' => $registration->registration_number])
            ->with('status', 'Pendaftaran Anda berhasil dikirimkan!');
    }

    /**
     * Menampilkan halaman bukti pendaftaran (Success Card / Ticket).
     */
    public function success(string $registration_number): View
    {
        $registration = Registration::where('registration_number', $registration_number)->firstOrFail();

        return view('registrations.success', [
            'registration' => $registration,
        ]);
    }
}
