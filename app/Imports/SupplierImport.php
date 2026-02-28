<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class SupplierImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        if (empty(trim($row['name'] ?? '')) && empty(trim($row['email'] ?? ''))) {
            return null;
        }

        return new Supplier([
            'supplier_type' => $row['supplier_type'] ?? null,
            'name' => $row['name'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'country_code' => '+'.$row['country_code'] ?? null,
            'street_address' => $row['street_address'] ?? null,
            'city' => $row['city'] ?? null,
            'postal_code' => $row['postal_code'] ?? null,
            'country' => $row['country'] ?? null,

            'company_name' => $row['supplier_type'] == 'company' ? $row['company_name'] : null,
            'company_email' => $row['supplier_type'] == 'company' ? $row['company_email'] : null,
            'company_phone_number' => $row['supplier_type'] == 'company' ? $row['company_phone_number'] : null,
            'tax' => $row['supplier_type'] == 'company' ? $row['tax'] : null,

            'bank_name' => $row['bank_name'] ?? null,
            'bank_branch' => $row['bank_branch'] ?? null,
            'account_number' => $row['account_number'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.supplier_type' => ['required', Rule::in('individual', 'company')],
            '*.email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')],
            '*.phone' => ['required', 'max:12', Rule::unique('suppliers', 'phone')],
            '*.company_email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'company_email')],
            '*.company_phone_number' => ['nullable', 'max:12', Rule::unique('suppliers', 'company_phone_number')],
        ];
    }
}
