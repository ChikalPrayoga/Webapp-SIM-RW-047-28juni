<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\IuranType;
use App\Enums\ContributionType;
use Illuminate\Validation\Rules\Enum;

class StoreIuranTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', IuranType::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->route('iuran_type') ? $this->route('iuran_type')->id : null;

        return [
            'name' => 'required|string|max:100|unique:iuran_types,name,' . $id,
            'description' => 'nullable|string',
            'default_nominal' => 'required|numeric|min:0',
            'type' => ['required', new Enum(ContributionType::class)],
            'is_active' => 'required|boolean',
        ];
    }
}
