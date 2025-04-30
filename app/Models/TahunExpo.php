<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunExpo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tahun_expo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tahun',
        'deskripsi',
        'photo',
    ];

    /**
     * Get the tenants for this tahun expo.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'tahun_expo_id');
    }

    /**
     * Get the photo URL attribute.
     *
     * @return string
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }

        return asset('assets/no_image.png');
    }
}