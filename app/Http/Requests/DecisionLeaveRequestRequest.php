<?php

namespace App\Http\Requests;

use App\Enums\ApprovalAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DecisionLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::enum(ApprovalAction::class)],
            'note' => [
                Rule::requiredIf(fn (): bool => $this->string('action')->toString() === ApprovalAction::Reject->value),
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Tindakan wajib dipilih.',
            'action.enum' => 'Tindakan tidak valid.',
            'note.required' => 'Catatan penolakan wajib diisi.',
            'note.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}
