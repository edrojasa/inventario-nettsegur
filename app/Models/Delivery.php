<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends SnipeModel
{
    use HasFactory, SoftDeletes;

    // Nombre de la tabla explícito
    protected $table = 'deliveries';

    // Para evitar la protección por manipulación masiva
    protected $fillable = [
        'folio',
        'admin_id',
        'user_id',
        'location_id',
        'status',
        'notes',
        'pdf_path',
        'signature_path',
    ];

    /**
     * Relación: Administrador que genera la remisión.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Relación: Empleado asignado a la remisión.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Localidad a la que se destina la remisión.
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Relación N:M Polimórfica (Equipos)
     */
    public function assets()
    {
        return $this->morphedByMany(Asset::class, 'item', 'delivery_items')
                    ->withPivot('notes')
                    ->withTimestamps();
    }

    /**
     * Relación N:M Polimórfica (Herramientas / Accesorios)
     */
    public function accessories()
    {
        return $this->morphedByMany(Accessory::class, 'item', 'delivery_items')
                    ->withPivot('notes')
                    ->withTimestamps();
    }
}
