<?php

namespace App\Http\Requests;

use App\Models\LeaveType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeaveTypeRequest extends FormRequest
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
        /** @var LeaveType $leaveType */
        $leaveType = $this->route('leaveType');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('leave_types', 'name')->ignore($leaveType)],
            'quota_days' => ['required', 'integer', 'min:0', 'max:365'],
            'deducts_balance' => ['boolean'],
            'requires_attachment' => ['boolean'],
        ];
    }
}
