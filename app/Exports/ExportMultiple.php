<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportMultiple implements FromCollection, WithHeadings
{
    public function __construct(
        protected array $data,
    ) {
    }

    public function collection()
    {
        $count = 1;
        $mappedData = collect($this->data)->map(function ($item) use (&$count) {
            $mappedRow = [
                __('Mã đối tượng') => $item['code'],
                __('Mã chỉ tiêu') => $item['goal_code'],
                __('Thời gian') => $item['time'],
                __('Tỷ trọng (%)') => $item['proportion'],
                __('Nội dung lỗi') => $item['message'] ?? '',
            ];
            $count++;
            return $mappedRow;
        });

        return $mappedData;
    }

    /**
     * Returns headers for report
     * @return array
     */
    public function headings(): array
    {
        return [
            'Mã đối tượng',
            'Mã chỉ tiêu',
            'Thời gian',
            'Tỷ trọng (%)',
            'Nội dung lỗi',
        ];
    }
}
