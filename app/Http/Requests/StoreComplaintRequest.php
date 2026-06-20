<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Usually open to residents, possibly checked via NIK later
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'exists:anggota_keluargas,nik'],
            'teks_keluhan' => ['required', 'string', 'min:10'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // Max 5MB per file
        ];
    }
}
