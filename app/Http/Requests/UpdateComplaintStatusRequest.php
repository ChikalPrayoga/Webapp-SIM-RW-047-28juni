<?php

namespace App\Http\Requests;

use App\Enums\ComplaintStatusEnum;
use App\Enums\ComplaintCategoryEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateComplaintStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('complaint'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(ComplaintStatusEnum::class)],
            'category' => ['nullable', new Enum(ComplaintCategoryEnum::class)],
            'priority' => ['nullable', 'string', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
