<?php

namespace App\Models;

use App\Models\Base\Product as BaseProduct;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Site;

class Product extends BaseProduct
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'status',
        'price_type',
        'price',
        'price_pwyw',
        'comparePrice',
        'enableOptions',
        'isDeal',
        'dealPrice',
        'dealEnds',
        'enableBid',
        'stock',
        'stock_settings',
        'sku',
        'productType',
        'banner',
        'featured_img',
        'media',
        'description',
        'external_product_link',
        'min_quantity',
        'ribbon',
        'seo',
        'api',
        'files',
        'extra',
        'position',
    ];

    protected $casts = [
        'price' => 'float',
        'dealPrice' => 'float',
        'dealEnds' => 'datetime',
        'stock' => 'integer',
        'stock_settings' => 'array',
        'media' => 'array',
        'seo' => 'array',
        'api' => 'array',
        'files' => 'array',
        'extra' => 'array',
        'ribbon' => 'array',
        'banner' => 'array',
        'featured_img' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured_img', '!=', null);
    }

    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->whereNull('stock')->orWhere('stock', '>', 0);
        });
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        if ($this->price_type === 2) { // PWYW
            return $this->price_pwyw ?? 'Pay What You Want';
        }
        
        return $this->price ? '$' . number_format($this->price, 2) : 'Free';
    }

    public function getIsOnSaleAttribute()
    {
        return $this->isDeal && $this->dealEnds && $this->dealEnds->isFuture();
    }

    public function getSalePriceAttribute()
    {
        if ($this->isOnSale && $this->dealPrice) {
            return $this->dealPrice;
        }
        return $this->price;
    }

    // CRUD Methods
    public static function createProduct($data)
    {
        $data['slug'] = \Str::slug($data['name']);
        return static::create($data);
    }

    public function updateProduct($data)
    {
        if (isset($data['name'])) {
            $data['slug'] = \Str::slug($data['name']);
        }
        return $this->update($data);
    }

    public function deleteProduct()
    {
        // Delete associated files
        if ($this->featured_img) {
            $this->deleteImage($this->featured_img);
        }
        if ($this->banner) {
            $this->deleteImage($this->banner);
        }
        
        return $this->delete();
    }

    private function deleteImage($imageData)
    {
        if (is_array($imageData) && isset($imageData['url'])) {
            $path = str_replace('/storage/', '', $imageData['url']);
            if (\Storage::disk('public')->exists($path)) {
                \Storage::disk('public')->delete($path);
            }
        }
    }

    // Validation Rules
    public static function getValidationRules($productId = null)
    {
        $uniqueRule = $productId ? 'unique:products,slug,' . $productId : 'unique:products,slug';
        
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|' . $uniqueRule,
            'price' => 'nullable|numeric|min:0',
            'price_type' => 'required|integer|in:1,2,3', // 1=fixed, 2=pwyw, 3=free
            'status' => 'required|integer|in:0,1',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
