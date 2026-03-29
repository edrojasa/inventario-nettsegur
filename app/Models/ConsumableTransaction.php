<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Entrega agrupada de consumible (auditoría / historial).
 * Complementa la lógica nativa; no sustituye consumables_users.
 */
class ConsumableTransaction extends Model
{
    use HasFactory;

    protected $table = 'consumable_transactions';

    protected $fillable = [
        'consumable_id',
        'type',
        'user_id',
        'location_id',
        'remision_id',
        'quantity',
        'status',
        'assigned_by',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Consumible entregado en esta transacción.
     */
    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class, 'consumable_id');
    }

    /**
     * Usuario destinatario cuando type = user (incluye eliminados para historial).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Ubicación destinatario cuando type = location (incluye eliminadas para historial).
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id')->withTrashed();
    }

    /**
     * Usuario que registró la entrega (assigned_by).
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by')->withTrashed();
    }

    /**
     * Remisión asociada, si la entrega formó parte de un flujo Delivery (remision_id).
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class, 'remision_id')->withTrashed();
    }
}
