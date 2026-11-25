<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkSettingRequest extends FormRequest
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
            'check_in' => ['required', 'date_format:H:i'],
            'check_out' => ['required', 'date_format:H:i'],
            'late_tolerance_minutes' => ['required', 'integer', 'min:0', 'max:480'],
            'working_days' => ['required', 'array', 'min:1'],
            'working_days.*' => ['integer', 'between:1,7'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['check_in', 'check_out'])) {
                    return;
                }

                $checkIn = Carbon::createFromFormat('H:i', $this->string('check_in')->toString());
                $checkOut = Carbon::createFromFormat('H:i', $this->string('check_out')->toString());

                if ($checkOut->lessThanOrEqualTo($checkIn)) {
                    $validator->errors()->add('check_out', 'Jam pulang harus setelah jam masuk.');
                }
            },
        ];
    }
}
