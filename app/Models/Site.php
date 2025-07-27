<?php

namespace App\Models;

use App\Models\Base\Site as BaseSite;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Site extends BaseSite
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address', // Changed from 'domain'
        '_slug', // Changed from 'slug'
        'status',
        'settings',
        'seo',
        'header',
        'footer',
        'logo',
        'favicon',
        'theme',
        'custom_css',
        'custom_js',
        'analytics_code',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_card',
        'published',
        'is_admin',
        'is_admin_selected',
        'created_by',
        'email',
        'current_edit_page',
        'ai_generate',
        'ai_generate_prompt',
        'workspace_permission',
        'ai_completed',
        'is_template',
        'banned',
    ];

    protected $casts = [
        'settings' => 'array',
        'seo' => 'array',
        'header' => 'array',
        'footer' => 'array',
        'logo' => 'array',
        'favicon' => 'array',
        'og_image' => 'array',
        'twitter_card' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Courses are not directly related to sites - they belong to users
    // public function courses()
    // {
    //     return $this->hasMany(Course::class);
    // }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDomain($query, $domain)
    {
        return $query->where('address', $domain);
    }

    // Accessors
    public function getFullUrlAttribute()
    {
        if ($this->address) {
            return 'https://' . $this->address;
        }
        return config('app.url') . '/' . $this->_slug;
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo && is_array($this->logo) && isset($this->logo['url'])) {
            return $this->logo['url'];
        }
        return $this->logo; // Return as string if not array
    }

    public function getFaviconUrlAttribute()
    {
        if ($this->favicon && is_array($this->favicon) && isset($this->favicon['url'])) {
            return $this->favicon['url'];
        }
        return $this->favicon; // Return as string if not array
    }

    public function getOgImageUrlAttribute()
    {
        if ($this->og_image && is_array($this->og_image) && isset($this->og_image['url'])) {
            return $this->og_image['url'];
        }
        return $this->og_image; // Return as string if not array
    }

    // CRUD Methods
    public static function createSite($data)
    {
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Ensure unique slug
        $originalSlug = $data['slug'];
        $counter = 1;
        while (static::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return static::create($data);
    }

    public function updateSite($data)
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Ensure unique slug (excluding current site)
        if (isset($data['slug'])) {
            $originalSlug = $data['slug'];
            $counter = 1;
            while (static::where('slug', $data['slug'])->where('id', '!=', $this->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }
        
        return $this->update($data);
    }

    public function deleteSite()
    {
        // Delete associated files
        if ($this->logo) {
            $this->deleteImage($this->logo);
        }
        if ($this->favicon) {
            $this->deleteImage($this->favicon);
        }
        if ($this->og_image) {
            $this->deleteImage($this->og_image);
        }
        
        // Delete associated pages, products, courses
        $this->pages()->delete();
        $this->products()->delete();
        $this->courses()->delete();
        
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
    public static function getValidationRules($siteId = null)
    {
        $uniqueDomainRule = $siteId ? 'unique:sites,domain,' . $siteId : 'unique:sites,domain';
        $uniqueSlugRule = $siteId ? 'unique:sites,slug,' . $siteId : 'unique:sites,slug';
        
        return [
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255|' . $uniqueDomainRule,
            'slug' => 'nullable|string|max:255|' . $uniqueSlugRule,
            'status' => 'required|integer|in:0,1',
            'user_id' => 'required|exists:users,id',
            'settings' => 'nullable|array',
            'seo' => 'nullable|array',
        ];
    }

    // Helper Methods
    public function isPublished()
    {
        return $this->status === 1;
    }

    public function hasCustomDomain()
    {
        return !empty($this->domain);
    }

    public function getSetting($key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting($key, $value)
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->update(['settings' => $settings]);
    }

    public function getSeoSetting($key, $default = null)
    {
        return data_get($this->seo, $key, $default);
    }

    public function setSeoSetting($key, $value)
    {
        $seo = $this->seo ?? [];
        $seo[$key] = $value;
        $this->update(['seo' => $seo]);
    }
}
