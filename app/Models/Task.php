<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'completed'];

    // Przykładowe relacje (jeśli są potrzebne):
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
    // public function comments()
    // {
    //     return $this->hasMany(Comment::class);
    // }

    public function scopeActive($query)
    {
        return $query->where('completed', true);
    }
}
