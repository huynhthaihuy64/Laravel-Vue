<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SliderExport implements FromArray, WithHeadings, WithMapping
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
            'Url',
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
            $item['url'],
            $item['active'] == 0 ? 'InActive' : 'Active',
            $item['created_at'],
        ];
    }
}
