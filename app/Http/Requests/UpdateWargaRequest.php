<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Allowed if user has permission to edit_residents directly, OR if it triggers a change request workflow
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['sometimes', 'required', 'string', 'max:255'],
            'jenis_kelamin' => ['sometimes', 'required', 'in:L,P'],
            'tempat_lahir' => ['sometimes', 'required', 'string', 'max:255'],
            'tanggal_lahir' => ['sometimes', 'required', 'date'],
            'pekerjaan' => ['nullable', 'string', 'max:255'],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
            'status_hubungan_keluarga' => ['sometimes', 'required', 'string', 'max:50'],
            'status_sosio_ekonomi' => ['nullable', 'string', 'max:50'],
        ];
    }
}
