<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\WeightUnit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class ProductImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        $weight_unit_id = WeightUnit::where('name', $row['unit'])->first()->id;
        return new Product([
            'name' => $row['name'],
            'sale_price' => sprintf('%.2f', $row['sale_price']),
            'purchase_price' => sprintf('%.2f', $row['purchase_price']),
            'company_sale_price' => sprintf('%.2f', $row['company_sale_price']),
            'loyal_sale_price' => sprintf('%.2f', $row['loyal_sale_price']),
            'low_stock_limit' => $row['low_stock_limit'],
            'high_stock_limit' => $row['high_stock_limit'],
            'weight_unit_id' => $weight_unit_id,
            'description' => $row['description'],
            'created_by' => auth()->user()->id
        ]);
    }

    public function rules(): array
    {
        return [
            '*.name' => ['required', Rule::unique('products', 'name')],
            '*.unit' => ['required', Rule::exists('weight_units', 'name')],
            '*.sale_price' => ['required'],
            '*.purchase_price' => ['required'],
            '*.company_sale_price' => ['required'],
            '*.loyal_sale_price' => ['required'],
            '*.low_stock_limit' => ['required'],
            '*.high_stock_limit' => ['required'],
            '*.description' => ['nullable', 'string']
        ];
    }
}
