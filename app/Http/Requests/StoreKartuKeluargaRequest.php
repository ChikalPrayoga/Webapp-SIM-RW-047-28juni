<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKartuKeluargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\KartuKeluarga::class);
    }

    public function rules(): array
    {
        return [
            'no_kk' => ['required', 'string', 'size:16', 'unique:kartu_keluargas,no_kk'],
            'rt_code' => ['required', 'string', 'max:5'],
            'alamat_lengkap' => ['required', 'string'],
            'blok' => ['nullable', 'string', 'max:10'],
            'nomor_rumah' => ['nullable', 'string', 'max:10'],
            'status_kepemilikan_rumah' => ['nullable', 'string', 'max:50'],
        ];
    }
}
