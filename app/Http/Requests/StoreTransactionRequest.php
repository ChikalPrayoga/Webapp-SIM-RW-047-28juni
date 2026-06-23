<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\FinancialTransaction;
use App\Enums\TransactionType;
use App\Enums\TransactionCategory;
use Illuminate\Validation\Rules\Enum;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', FinancialTransaction::class);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $amount = str_replace(',', '.', $this->amount);
            $this->merge([
                'amount' => $amount,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'transaction_type' => ['required', new Enum(TransactionType::class)],
            'category' => ['required', new Enum(TransactionCategory::class)],
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|min:5',
            'transaction_date' => 'required|date|before_or_equal:today',
        ];
    }
}
