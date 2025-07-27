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
            'email' => $this->email,
            'title' => $this->title,
            'role' => $this->role,
            'status' => $this->status,
            'avatar' => $this->avatar,
            'email_verified_at' => $this->email_verified_at,
            'settings' => $this->settings,
            'store' => $this->store,
            'wallet_settings' => $this->wallet_settings,
            'last_activity' => $this->lastActivity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'sites_count' => $this->whenLoaded('sites', function () {
                return $this->sites->count();
            }),
            'products_count' => $this->whenLoaded('products', function () {
                return $this->products->count();
            }),
            'courses_count' => $this->whenLoaded('courses', function () {
                return $this->courses->count();
            }),
            
            // Include relationships when requested
            'sites' => SiteResource::collection($this->whenLoaded('sites')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            
            // Additional computed fields
            'is_admin' => $this->role === 1,
            'is_active' => $this->status === 1,
            'has_verified_email' => !is_null($this->email_verified_at),
        ];
    }
} 