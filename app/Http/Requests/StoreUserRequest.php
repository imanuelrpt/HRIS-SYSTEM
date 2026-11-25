<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\Position;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'nik' => ['nullable', 'string', 'max:30', Rule::unique('users', 'nik')->whereNull('deleted_at')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::enum(Role::class)],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'manager_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', Role::Manager->value),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'join_date' => ['nullable', 'date_format:Y-m-d'],
            'is_active' => ['boolean'],
            'must_change_password' => ['boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['department_id', 'position_id'])) {
                    return;
                }

                $position = Position::query()->find($this->integer('position_id'));

                if ($position !== null
                    && $this->filled('department_id')
                    && $position->department_id !== $this->integer('department_id')) {
                    $validator->errors()->add('position_id', 'Jabatan harus sesuai dengan departemen yang dipilih.');
                }
            },
        ];
    }
}
