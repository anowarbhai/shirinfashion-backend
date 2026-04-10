<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'button_text',
        'button_link',
        'button_color',
        'button_2_text',
        'button_2_link',
        'button_2_color',
        'text_align',
        'content_position',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url', 'button_color_classes', 'button_2_color_classes'];

    /**
     * Get active sliders ordered by order
     */
    public static function getActiveSliders()
    {
        return Cache::remember('active_sliders', 3600, function () {
            return self::where('is_active', true)
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Clear slider cache
     */
    public static function clearCache()
    {
        Cache::forget('active_sliders');
    }

    /**
     * Boot method to clear cache on save/delete
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        return asset('storage/' . $this->image);
    }

    /**
     * Get button color classes
     */
    public function getButtonColorClassesAttribute()
    {
        $colors = [
            'rose' => 'bg-rose-600 hover:bg-rose-700 text-white',
            'blue' => 'bg-blue-600 hover:bg-blue-700 text-white',
            'green' => 'bg-green-600 hover:bg-green-700 text-white',
            'purple' => 'bg-purple-600 hover:bg-purple-700 text-white',
            'orange' => 'bg-orange-600 hover:bg-orange-700 text-white',
            'dark' => 'bg-gray-900 hover:bg-gray-800 text-white',
            'white' => 'bg-white hover:bg-gray-100 text-gray-900',
            'outline' => 'bg-transparent border-2 border-white hover:bg-white hover:text-gray-900 text-white',
        ];

        return $colors[$this->button_color] ?? $colors['rose'];
    }

    /**
     * Get button 2 color classes
     */
    public function getButton2ColorClassesAttribute()
    {
        $colors = [
            'rose' => 'bg-rose-600 hover:bg-rose-700 text-white',
            'blue' => 'bg-blue-600 hover:bg-blue-700 text-white',
            'green' => 'bg-green-600 hover:bg-green-700 text-white',
            'purple' => 'bg-purple-600 hover:bg-purple-700 text-white',
            'orange' => 'bg-orange-600 hover:bg-orange-700 text-white',
            'dark' => 'bg-gray-900 hover:bg-gray-800 text-white',
            'white' => 'bg-white hover:bg-gray-100 text-gray-900',
            'outline' => 'bg-transparent border-2 border-white hover:bg-white hover:text-gray-900 text-white',
        ];

        return $colors[$this->button_2_color] ?? $colors['outline'];
    }

    /**
     * Get text alignment classes
     */
    public function getTextAlignClassAttribute()
    {
        $alignments = [
            'left' => 'text-left items-start',
            'center' => 'text-center items-center',
            'right' => 'text-right items-end',
        ];

        return $alignments[$this->text_align] ?? $alignments['left'];
    }

    /**
     * Get content position classes
     */
    public function getContentPositionClassAttribute()
    {
        $positions = [
            'top' => 'items-start pt-32',
            'center' => 'items-center',
            'bottom' => 'items-end pb-32',
        ];

        return $positions[$this->content_position] ?? $positions['center'];
    }
}
