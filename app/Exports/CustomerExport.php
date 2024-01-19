<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromArray, WithHeadings, WithMapping
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
            'User_ID',
            'Phone',
            'Address',
            'Email',
            'Content',
            'Total_Tax',
            'Created_At'
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
            $item['user_id'],
            $item['phone'],
            $item['address'],
            $item['email'],
            $item['content'],
            $item['total_tax'],
            Carbon::parse($item['created_at'])->format('Y-m-d'),
        ];
    }
}
