<?php
// app/Models/UserSavedMaterial.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSavedMaterial extends Model
{
    protected $fillable = [
        'user_id',
        'material_id',
        'saved_at'
    ];

    protected $casts = [
        'saved_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}