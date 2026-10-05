<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncrementPromotionReportExport implements FromCollection, WithStyles, WithHeadings, WithEvents, WithCustomStartCell
{
    protected $data;

    public function __construct($data)
    {
        $this->data = collect($data);
    }
    public function startCell(): string
    {
        return 'A2';
    }
    public function collection()
    {
        return $this->data->map(function ($row) {
            return [
                'employee_code'        => $row->employee_code,
                'employee_name'        => $row->employee_name,
                'joining_date'         => $row->joining_date,
                'effective_month'      => $row->effective_month,
                'location_name'        => $row->location_name,
                'designation_name'     => $row->designation_name,
                'new_designation_name' => $row->new_designation_name,
                'department_name'      => $row->depertment_name,
                'section_name'         => $row->section_name,
                'category_name'        => $row->category_name,
                'apply_for'            => $row->apply_for,
                'salary_amount'        => $row->salary_amount,
                'increase_amount'      => $row->increase_amount,
                'new_salary_amount'    => $row->new_salary_amount,
                'notes'                => $row->notes,

            ];
        });
    }

    public function headings(): array
    {
        return [
            'Employee Code',
            'Name',
            'Joining Date',
            'Increment Date',
            'Location',
            'Designation',
            'New Designation',
            'Department',
            'Sub Department',
            'Category',
            'Status',
            'Salary',
            'Increase',
            'New Salary',
            'Increment Notes',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // Title
                $event->sheet->mergeCells('A1:N1');
                $event->sheet->setCellValue('A1', 'Increment & Promotion List');

                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                    ],
                ]);

                // Auto Width
                foreach (range('A', 'N') as $column) {
                    $event->sheet->getColumnDimension($column)
                        ->setAutoSize(true);
                }

                // Header Background
                $event->sheet->getStyle('A2:O2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Data Border
                $lastRow = $event->sheet->getHighestRow();

                $event->sheet->getStyle("A2:O{$lastRow}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                // Number format
                $event->sheet->getStyle("H3:J{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
            },
        ];
    }
}
