<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public access
    }

    public function rules(): array
    {
        return [
            'aspirasi_id' => ['required', 'integer', 'exists:log_laporan_aspirasis,aspirasi_id'],
            'nik' => ['required', 'string', 'exists:log_laporan_aspirasis,nik'],
        ];
    }

    public function messages(): array
    {
        return [
            'aspirasi_id.exists' => 'Nomor Tiket tidak ditemukan.',
            'nik.exists' => 'Nomor Tiket dan NIK tidak cocok.',
        ];
    }
}
