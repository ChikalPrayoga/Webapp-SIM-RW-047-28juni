<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitContributionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Portal requests bypass authentication, but we can verify the session no_kk.
     */
    public function authorize(): bool
    {
        return session()->has('verified_no_kk') 
            && session('verified_no_kk') === $this->input('no_kk');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'no_kk' => 'required|string|exists:kartu_keluargas,no_kk',
            'iuran_type_id' => 'required|exists:iuran_types,id',
            'nominal' => 'required|numeric|min:1',
            'periode_bulan' => 'required|integer|between:1,12',
            'periode_tahun' => 'required|integer|min:2000',
        ];
    }
}
