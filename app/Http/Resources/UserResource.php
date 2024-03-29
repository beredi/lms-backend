<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'paid' => $this->didUserPaid(),
            'roles' => $this->getRoleNames(),
            'reserved_books_count' => $this->getReservedBooks->count(),
            'borrowed_books_count' => $this->getBorrowedBooks->count(),
            'returned_books_count' => $this->getReturnedBooks->count(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'permissions' => $this->when($this->auth, function () {
                return $this->getPermissionsViaRoles()->pluck('name')->toArray();
            }),
        ];
    }
}
