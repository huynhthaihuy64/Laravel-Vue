<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromArray, WithHeadings, WithMapping
{
    public function __construct(
        protected array $data,
    ) {
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $collection = $this->data;
        return $collection;
    }

    /**
     * Returns headers for report
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Price',
            'Price_sale',
            'Active',
            'Created_at',
        ];
    }

    /**
     * Returns headers for report
     * @return array
     */
    public function map($item): array
    {
        return [
            $item['id'],
            $item['name'],
            $item['description'],
            $item['price'],
            $item['price_sale'],
            $item['active'] == 0 ? 'InActive' : 'Active',
            $item['created_at'],
        ];
    }
}
