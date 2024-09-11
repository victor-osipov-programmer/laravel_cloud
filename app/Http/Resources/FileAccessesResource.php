<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileAccessesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $file = $request->file;
        
        return [
            'fullname' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'type' => $file->author_id == $this->id ? 'author' : 'co-author'
        ];
    }
}
