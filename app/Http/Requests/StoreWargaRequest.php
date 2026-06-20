<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\AnggotaKeluarga::class);
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'size:16', 'unique:anggota_keluargas,nik'],
            'no_kk' => ['required', 'string', 'size:16', 'exists:kartu_keluargas,no_kk'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'pekerjaan' => ['nullable', 'string', 'max:255'],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
            'status_hubungan_keluarga' => ['required', 'string', 'max:50'],
            'status_sosio_ekonomi' => ['nullable', 'string', 'max:50'],
        ];
    }
}
