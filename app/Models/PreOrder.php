<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'nama_pemesan',
        'nomor_wa',
        'status_pesanan',
        'catatan_tambahan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status_pesanan' => 'string',
    ];

    /**
     * Get the tenant that owns this pre-order.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the items for this pre-order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PreOrderItem::class);
    }

    /**
     * Calculate the total amount for this pre-order.
     */
    public function getTotalAttribute()
    {
        return $this->items->sum('subtotal');
    }
}
