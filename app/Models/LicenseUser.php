<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseUser extends Model
{
    use HasFactory;

    protected $table = 'license_users';

    protected $fillable = [
        'license_id',
        'username',
        'password',
    ];

    /**
     * Get the license that owns this user.
     */
    public function license()
    {
        return $this->belongsTo(License::class);
    }
}
