<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SliderExport implements FromArray, WithHeadings, WithMapping, WithDrawings, WithEvents, WithStyles
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
            'Image',
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
            '',
            $item['active'] == 0 ? 'InActive' : 'Active',
            Carbon::parse($item['created_at'])->format('Y-m-d'),
        ];
    }

    public function drawings()
    {
        $drawings = [];
        foreach ($this->data as $index => $item) {
            $drawing = new Drawing();
            $drawing->setPath(public_path($item['file']));
            $drawing->setHeight(90);
            $drawing->setWidth(90);
            $drawing->setCoordinates('D' . ($index + 2));
            $drawings[] = $drawing;
        }

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rows = count($this->data) + 1;
                for ($row = 2; $row <= $rows; $row++) {
                    $event->sheet->getDelegate()->getRowDimension($row)->setRowHeight(90);
                }
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(12);
            },
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            'A2:I' . (count($this->data) + 1) => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]
        ];
    }
}
