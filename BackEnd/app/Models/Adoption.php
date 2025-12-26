<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Adoption extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'adopter_id',
        'status',
        'notes',
        'adopted_at',
    ];

    protected $casts = [
        'adopted_at' => 'datetime',
    ];

    // Relationships
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function adopter()
    {
        return $this->belongsTo(User::class, 'adopter_id');
    }
}
