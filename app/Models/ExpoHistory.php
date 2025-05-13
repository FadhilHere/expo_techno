<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpoHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tahun_expo_id',
        'title',
        'content',
        'cover_image',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Get the tahun expo associated with this history.
     */
    public function tahunExpo(): BelongsTo
    {
        return $this->belongsTo(TahunExpo::class);
    }

    /**
     * Get the images for this history.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ExpoHistoryImage::class);
    }

    /**
     * Get the cover image URL attribute.
     *
     * @return string
     */
    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }

        return asset('assets/no_image.png');
    }
}
