<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BooksExport implements FromCollection, WithMapping, WithHeadings, WithStyles
{
    protected $books;

    public function __construct(Collection $books)
    {
        $this->books = $books;
    }

    public function collection()
    {
        return $this->books;
    }

    public function map($book): array
    {
        $authors = '';
        $first = true;
        foreach ($book->authors as $author) {
            if ($first) {
                $first = false;
                $authors .= $author->name;
            }
            $authors .= ' ' . $author->name;
        }

        return [
            $book->book_id,
            $book->title,
            $authors,
            $book->description,
            $book->pages,
            $book->year,

        ];
    }

    public function headings(): array
    {
        return [
            'Číslo knihy',
            'Názov',
            'Autor',
            'Popis',
            'Počet strán',
            'Rok vydania',
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
