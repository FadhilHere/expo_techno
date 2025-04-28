<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tahun_expo_id',
        'kategori_id',
        'nama_tenant',
        'deskripsi',
        'whatsapp_tenant',
    ];

    /**
     * Get the tahun expo that owns this tenant.
     */
    public function tahunExpo(): BelongsTo
    {
        return $this->belongsTo(TahunExpo::class, 'tahun_expo_id');
    }

    /**
     * Get the kategori that owns this tenant.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriTenant::class, 'kategori_id');
    }

    /**
     * Get the products for this tenant.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the pre-orders for this tenant.
     */
    public function preOrders(): HasMany
    {
        return $this->hasMany(PreOrder::class);
    }
}
