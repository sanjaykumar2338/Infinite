<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PageContent extends Model
{
    protected $fillable = [
        'page_key',
        'section_key',
        'title',
        'subtitle',
        'body',
        'image_path',
        'button_text',
        'button_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        foreach (['http://', 'https://', '/'] as $prefix) {
            if (str_starts_with($this->image_path, $prefix)) {
                return $this->image_path;
            }
        }

        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return asset($this->image_path);
    }

    /**
     * @return Collection<int, self>
     */
    public static function forPage(string $page): Collection
    {
        return self::query()
            ->where('page_key', $page)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('section_key');
    }

    /**
     * @return Collection<int, self>
     */
    public static function activeForPage(string $page): Collection
    {
        return self::query()
            ->where('page_key', $page)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('section_key');
    }
}
