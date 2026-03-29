<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'notes'
    ];

    public function consumable()
    {
        return $this->belongsTo(Consumable::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'assigned_by')->withTrashed();
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'remision_id');
    }
}
