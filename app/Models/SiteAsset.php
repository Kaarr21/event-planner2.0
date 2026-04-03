<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteAsset extends Model
{
    protected $fillable = [
        'key',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'asset_type',
        'alt_text',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
