<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'species',
        'breed',
        'age',
        'gender',
        'description',
        'location',
        'price',
        'status',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function adoptions()
    {
        return $this->hasMany(Adoption::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}
