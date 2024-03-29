<?php

namespace App\Http\Resources;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BookCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lowestYear = Book::whereNotNull('year')->min('year');
        $highestYear = Book::whereNotNull('year')->max('year');

        return [
            'data' => BookResource::collection($this->collection),
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'total' => $this->total(),
                'lowestYearOfBooks' => $lowestYear,
                'highestYearOfBooks' => $highestYear,
            ],
        ];
    }
}
