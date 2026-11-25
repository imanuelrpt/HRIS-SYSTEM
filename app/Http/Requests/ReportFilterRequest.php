<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewReports', Attendance::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'month.required' => 'Bulan wajib diisi.',
            'month.integer' => 'Bulan harus berupa angka.',
            'month.between' => 'Bulan harus antara 1 sampai 12.',
            'year.required' => 'Tahun wajib diisi.',
            'year.integer' => 'Tahun harus berupa angka.',
            'year.between' => 'Tahun harus antara 2000 sampai 2100.',
            'department_id.integer' => 'Departemen tidak valid.',
            'department_id.exists' => 'Departemen tidak ditemukan.',
            'user_id.integer' => 'Karyawan tidak valid.',
            'user_id.exists' => 'Karyawan tidak ditemukan.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'month' => $this->filled('month') ? $this->integer('month') : now()->month,
            'year' => $this->filled('year') ? $this->integer('year') : now()->year,
        ]);
    }
}
