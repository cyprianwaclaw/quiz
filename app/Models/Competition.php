<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Filter\Traits\Filterable;

class Competition extends Model
{
    use HasFactory;
    use Filterable;

    protected $fillable = [
        'category_id',
        'title',
        'description', 
        'image',
        'user_id',
    ];

    protected $appends = ['questions_count'];

    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
