<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\CatatanIuranWarga;

class RejectContributionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $id = $this->route('id') ?? $this->input('iuran_id');
        $contribution = CatatanIuranWarga::find($id);

        return $contribution && Gate::allows('verify', $contribution);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'iuran_id' => 'nullable|exists:catatan_iuran_wargas,iuran_id',
            'rejection_notes' => 'required|string|min:5',
        ];
    }
}
