<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'nama_produk',
        'harga',
        'deskripsi',
        'foto_produk',
    ];

    /**
     * Get the tenant that owns this product.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the pre-order items for this product.
     */
    public function preOrderItems(): HasMany
    {
        return $this->hasMany(PreOrderItem::class, 'produk_id');
    }
}
