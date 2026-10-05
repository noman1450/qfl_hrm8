<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CostToTheCompanyExport implements FromCollection, WithStyles, WithHeadings, WithEvents, WithCustomStartCell
{
    protected $data;
    protected $monthFrom;
    protected $monthTo;
    protected $builtRows = [];

    public function __construct($data, $monthFrom, $monthTo)
    {
        $this->data = collect($data);
        $this->monthFrom = $monthFrom;
        $this->monthTo = $monthTo;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function collection()
    {
        $groups = [];
        $order = [];
        $totalEmployees = 0;
        $totalGross     = 0;
        $totalBonus     = 0;
        $totalPF        = 0;
        $totalTransport = 0;
        $totalHouseRent = 0;
        $totalFixed     = 0;
        $totalCost      = 0;

        foreach ($this->data as $row) {
            if (!isset($groups[$row->level_name])) {
                $groups[$row->level_name] = ['rows' => [], 'subtotal' => [
                    'employees' => 0, 'gross' => 0, 'bonus' => 0, 'pf' => 0,
                    'transport' => 0, 'house_rent' => 0, 'fixed' => 0, 'cost' => 0,
                ]];
                $order[] = $row->level_name;
            }

            $location = [
                null,
                $row->location_name,
                $row->NoOfEmployee,
                $row->gross_salary,
                $row->two_festival_bonus,
                $row->pf_contribution,
                $row->transport_outOfPocket,
                $row->houseRent_allowance,
                $row->fixed_allowance,
                $row->existing_cost,
            ];
            $groups[$row->level_name]['rows'][] = $location;

            $groups[$row->level_name]['subtotal']['employees']  += $row->NoOfEmployee;
            $groups[$row->level_name]['subtotal']['gross']      += $row->gross_salary;
            $groups[$row->level_name]['subtotal']['bonus']      += $row->two_festival_bonus;
            $groups[$row->level_name]['subtotal']['pf']         += $row->pf_contribution;
            $groups[$row->level_name]['subtotal']['transport']  += $row->transport_outOfPocket;
            $groups[$row->level_name]['subtotal']['house_rent'] += $row->houseRent_allowance;
            $groups[$row->level_name]['subtotal']['fixed']      += $row->fixed_allowance;
            $groups[$row->level_name]['subtotal']['cost']       += $row->existing_cost;

            $totalEmployees += $row->NoOfEmployee;
            $totalGross     += $row->gross_salary;
            $totalBonus     += $row->two_festival_bonus;
            $totalPF        += $row->pf_contribution;
            $totalTransport += $row->transport_outOfPocket;
            $totalHouseRent += $row->houseRent_allowance;
            $totalFixed     += $row->fixed_allowance;
            $totalCost      += $row->existing_cost;
        }

        $rows = [];
        $sl = 0;

        foreach ($order as $levelName) {
            $sl++;
            $sub = $groups[$levelName]['subtotal'];
            $rows[] = [
                $sl,
                $levelName,
                $sub['employees'],
                $sub['gross'],
                $sub['bonus'],
                $sub['pf'],
                $sub['transport'],
                $sub['house_rent'],
                $sub['fixed'],
                $sub['cost'],
            ];

            foreach ($groups[$levelName]['rows'] as $location) {
                $rows[] = $location;
            }
        }

        $rows[] = [
            '',
            'TOTAL',
            $totalEmployees,
            $totalGross,
            $totalBonus,
            $totalPF,
            $totalTransport,
            $totalHouseRent,
            $totalFixed,
            $totalCost,
        ];

        $this->builtRows = $rows;

        return collect($rows);
    }

    public function headings(): array
    {
        $dateFrom = $this->monthFrom;
        $dateTo = $this->monthTo;

        return [
            ['Cost to the Company (Employee Segment Wise)'],
            ["From: {$dateFrom}  To: {$dateTo}"],
            [
                'Sl. No.',
                'Details',
                'No. of Employees',
                'Gross Salary',
                'Two Festival Bonus',
                'PF Com. Contribution',
                'Out of Pocket & Transport',
                'House Rent',
                'Fixed Allowance',
                'Total Cost',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true, 'size' => 11]],
            3 => ['font' => ['bold' => true, 'size' => 10]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('A1:J1');
                $event->sheet->mergeCells('A2:J2');

                $event->sheet->getStyle('A3:J3')->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFD9E1F2'],
                    ],
                ]);

                foreach (range('A', 'J') as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }

                $lastRow = $event->sheet->getHighestRow();

                $event->sheet->getStyle("A3:J{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Number format for amount columns (D to I)
                $event->sheet->getStyle("D4:J{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // Group subtotal rows: bold + light background
                $rowNum = 4;
                foreach ($this->builtRows as $index => $builtRow) {
                    if ($builtRow[0] !== null && $builtRow[0] !== '' && isset($builtRow[1]) && $builtRow[1] !== 'TOTAL') {
                        $event->sheet->getStyle("A{$rowNum}:J{$rowNum}")
                            ->getFont()->setBold(true);
                        $event->sheet->getStyle("A{$rowNum}:J{$rowNum}")
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFEEF2FB');
                    }
                    $rowNum++;
                }

                // TOTAL row styling
                $event->sheet->getStyle("A{$lastRow}:J{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFFFF2CC'],
                    ],
                ]);
            },
        ];
    }
}