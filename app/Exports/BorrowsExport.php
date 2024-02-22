<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BorrowsExport implements FromCollection, WithMapping, WithHeadings, WithStyles
{
    protected $borrows;

    public function __construct(Collection $borrows)
    {
        $this->borrows = $borrows;
    }

    public function collection()
    {
        return $this->borrows;
    }

    public function map($borrow): array
    {
        $reservedDate = $borrow->reserved ? Date::excelToDateTimeObject(Date::stringToExcel($borrow->reserved)) : null;
        $borrowedDate = $borrow->borrowed ? Date::excelToDateTimeObject(Date::stringToExcel($borrow->borrowed)) : null;
        $returnedDate = $borrow->returned ? Date::excelToDateTimeObject(Date::stringToExcel($borrow->returned)) : null;
        $deadlineDate = $borrow->deadline ? Date::excelToDateTimeObject(Date::stringToExcel($borrow->deadline)) : null;
        $dateFormat = 'd. m. Y';
        return [
            $borrow->book->book_id,
            $borrow->book->title,
            $reservedDate ? $reservedDate->format($dateFormat) : null,
            $borrowedDate ? $borrowedDate->format($dateFormat) : null,
            $returnedDate ? $returnedDate->format($dateFormat) : null,
            $deadlineDate ? $deadlineDate->format($dateFormat) : null,
        ];
    }

    public function headings(): array
    {
        return [
            'Book ID',
            'Book',
            'Reserved',
            'Borrowed',
            'Returned',
            'Deadline'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF'
                    ]
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '808080']
                ]
            ],
        ];
    }
}
