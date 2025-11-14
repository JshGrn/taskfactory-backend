<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /** @var Task */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->resource->getKey(),
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'due_at' => $this->resource->due_at,
            'completed_at' => $this->resource->completed_at,
            'is_completed' => $this->resource->is_completed,
            'is_overdue' => $this->resource->is_overdue,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
