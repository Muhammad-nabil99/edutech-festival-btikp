<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $regencies = [
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

        $hasTempPhoto = $this->filled('temp_photo') && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->input('temp_photo'));

        return [
            'full_name' => ['required', 'string', 'min:3', 'max:150'],
            'school_name' => ['required', 'string', 'min:3', 'max:150'],
            'regency_city' => ['required', 'string', 'in:' . implode(',', $regencies)],
            'email' => ['required', 'string', 'email:rfc', 'max:150', 'unique:registrations,email'],
            'whatsapp_number' => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{7,11}$/'],
            'learning_category' => ['required', 'in:gim_edukasi,media_pembelajaran_interaktif'],
            'profile_photo' => $hasTempPhoto 
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']
                : ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'temp_photo' => ['nullable', 'string'],
            'media_link' => ['required', 'url', 'max:500'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.min' => 'Nama lengkap minimal 3 karakter.',
            'full_name.max' => 'Nama lengkap maksimal 150 karakter.',
            'school_name.required' => 'Asal sekolah wajib diisi.',
            'school_name.min' => 'Asal sekolah minimal 3 karakter.',
            'school_name.max' => 'Asal sekolah maksimal 150 karakter.',
            'regency_city.required' => 'Silakan pilih Kabupaten/Kota asal Anda.',
            'regency_city.in' => 'Kabupaten/Kota yang dipilih harus berada di wilayah Sulawesi Tenggara.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar sebelumnya.',
            'whatsapp_number.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'whatsapp_number.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format contoh: 08123456789 atau +628123456789.',
            'learning_category.required' => 'Silakan pilih salah satu kategori pembelajaran.',
            'learning_category.in' => 'Pilihan kategori harus berupa Gim Edukasi atau Media Pembelajaran Interaktif.',
            'profile_photo.required' => 'Foto profil wajib diunggah.',
            'profile_photo.image' => 'File foto profil harus berupa gambar.',
            'profile_photo.mimes' => 'Format foto profil yang diizinkan adalah JPG, JPEG, PNG, atau WEBP.',
            'profile_photo.max' => 'Ukuran file foto profil maksimal 2 MB.',
            'media_link.required' => 'Tautan / Link Media karya wajib diisi.',
            'media_link.url' => 'Format link media harus berupa URL yang valid (diawali http:// atau https://).',
            'media_link.max' => 'Panjang link media maksimal 500 karakter.',
        ];
    }
}
