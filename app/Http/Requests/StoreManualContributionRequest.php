<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\CatatanIuranWarga;
use App\Models\KartuKeluarga;

class StoreManualContributionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!Gate::allows('create', CatatanIuranWarga::class)) {
            return false;
        }

        $user = $this->user();
        $userArea = $user->position?->area_code;
        if (!empty($userArea)) {
            $noKk = $this->input('no_kk');
            if ($noKk) {
                $kk = KartuKeluarga::where('no_kk', $noKk)->first();
                return $kk && $kk->rt_code === $userArea;
            }
        }

        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('nominal')) {
            $nominal = str_replace(',', '.', $this->nominal);
            $this->merge([
                'nominal' => $nominal,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'no_kk' => 'required|string|exists:kartu_keluargas,no_kk',
            'iuran_type_id' => 'required|exists:iuran_types,id',
            'nominal' => 'required|numeric|min:0.01',
            'periode_bulan' => 'required|integer|between:1,12',
            'periode_tahun' => 'required|integer|min:2000',
            'tanggal_pembayaran' => 'required|date|before_or_equal:today',
        ];
    }
}
