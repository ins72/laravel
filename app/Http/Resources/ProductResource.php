<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'price_type' => $this->price_type,
            'price' => $this->price,
            'price_pwyw' => $this->price_pwyw,
            'compare_price' => $this->comparePrice,
            'enable_options' => $this->enableOptions,
            'is_deal' => $this->isDeal,
            'deal_price' => $this->dealPrice,
            'deal_ends' => $this->dealEnds,
            'enable_bid' => $this->enableBid,
            'stock' => $this->stock,
            'stock_settings' => $this->stock_settings,
            'sku' => $this->sku,
            'product_type' => $this->productType,
            'banner' => $this->banner,
            'featured_img' => $this->featured_img,
            'media' => $this->media,
            'description' => $this->description,
            'external_product_link' => $this->external_product_link,
            'min_quantity' => $this->min_quantity,
            'ribbon' => $this->ribbon,
            'seo' => $this->seo,
            'api' => $this->api,
            'files' => $this->files,
            'extra' => $this->extra,
            'position' => $this->position,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Computed attributes
            'formatted_price' => $this->formatted_price,
            'is_on_sale' => $this->is_on_sale,
            'sale_price' => $this->sale_price,
            'is_in_stock' => $this->stock === null || $this->stock > 0,
            
            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'site' => new SiteResource($this->whenLoaded('site')),
            
            // Additional computed fields
            'price_display' => $this->getPriceDisplay(),
            'availability_status' => $this->getAvailabilityStatus(),
            'discount_percentage' => $this->getDiscountPercentage(),
        ];
    }
    
    /**
     * Get price display string
     */
    private function getPriceDisplay(): string
    {
        if ($this->price_type === 2) {
            return $this->price_pwyw ?? 'Pay What You Want';
        }
        
        if ($this->price_type === 3 || $this->price === 0) {
            return 'Free';
        }
        
        if ($this->is_on_sale && $this->dealPrice) {
            return '$' . number_format($this->dealPrice, 2);
        }
        
        return $this->price ? '$' . number_format($this->price, 2) : 'Free';
    }
    
    /**
     * Get availability status
     */
    private function getAvailabilityStatus(): string
    {
        if ($this->stock === null) {
            return 'available';
        }
        
        if ($this->stock > 0) {
            return 'in_stock';
        }
        
        return 'out_of_stock';
    }
    
    /**
     * Get discount percentage
     */
    private function getDiscountPercentage(): ?float
    {
        if ($this->is_on_sale && $this->price && $this->dealPrice) {
            return round((($this->price - $this->dealPrice) / $this->price) * 100, 2);
        }
        
        return null;
    }
} 