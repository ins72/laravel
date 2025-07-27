<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'filename',
        'original_name',
        'path',
        'url',
        'size',
        'mime_type',
        'type',
        'alt_text',
        'description',
        'tags',
        'file_info',
    ];

    protected $casts = [
        'tags' => 'array',
        'file_info' => 'array',
        'size' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    public function scopeDocuments($query)
    {
        return $query->where('mime_type', 'like', 'application/%');
    }

    // Accessors
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsImageAttribute()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getIsVideoAttribute()
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    public function getIsDocumentAttribute()
    {
        return str_starts_with($this->mime_type, 'application/');
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->is_image) {
            // For images, you might want to generate thumbnails
            // This is a placeholder - implement based on your thumbnail system
            return $this->url;
        }
        return null;
    }

    // CRUD Methods
    public static function createMedia($data)
    {
        return static::create($data);
    }

    public function updateMedia($data)
    {
        return $this->update($data);
    }

    public function deleteMedia()
    {
        // Delete file from storage
        if (\Storage::disk('public')->exists($this->path)) {
            \Storage::disk('public')->delete($this->path);
        }

        return $this->delete();
    }

    // Validation Rules
    public static function getValidationRules($mediaId = null)
    {
        return [
            'user_id' => 'required|exists:users,id',
            'filename' => 'required|string|max:255',
            'original_name' => 'required|string|max:255',
            'path' => 'required|string|max:500',
            'url' => 'required|url|max:500',
            'size' => 'required|integer|min:0',
            'mime_type' => 'required|string|max:100',
            'type' => 'nullable|string|max:50',
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'file_info' => 'nullable|array',
        ];
    }

    // Helper Methods
    public function getFileExtension()
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    public function getFileNameWithoutExtension()
    {
        return pathinfo($this->original_name, PATHINFO_FILENAME);
    }

    public function hasTag($tag)
    {
        return is_array($this->tags) && in_array($tag, $this->tags);
    }

    public function addTag($tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag($tag)
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, function($t) use ($tag) {
            return $t !== $tag;
        });
        $this->update(['tags' => array_values($tags)]);
    }
} 