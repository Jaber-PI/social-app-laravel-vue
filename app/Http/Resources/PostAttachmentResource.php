<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostAttachmentResource extends JsonResource
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
            'filename' => $this->filename,
            'mime' => $this->mime_type,
            'size' => $this->file_size,
            'url' => Storage::url($this->file_path),
            'uploaded_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
