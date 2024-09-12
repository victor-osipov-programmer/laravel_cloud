<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'file_id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'accesses' => $this->users->map(function ($user) {
                return [
                    'fullname' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'type' => $this->author_id == $user->id ? 'author' : 'co-author'
                ];
            })
        ];
    }
}
