<?php

namespace App\Http\Requests;

use App\Enums\LetterTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'exists:anggota_keluargas,nik'],
            'jenis_surat' => ['required', new Enum(LetterTypeEnum::class)],
            'keperluan' => ['required', 'string', 'max:500'],
        ];
    }
}
