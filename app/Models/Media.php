<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'alt_text',
        'caption',
        'type',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function getUrlAttribute()
    {
        $path = $this->file_path;

        // If it's already a full URL, return as is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // If it's empty, return empty
        if (empty($path)) {
            return '';
        }

        // If it starts with /storage/, use asset()
        if (str_starts_with($path, '/storage/')) {
            return asset($path);
        }

        // If it starts with storage/ (without leading slash), add slash
        if (str_starts_with($path, 'storage/')) {
            return asset('/'.$path);
        }

        // Otherwise, assume it's a storage path
        return asset('/storage/'.ltrim($path, '/'));
    }

    public function getSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
