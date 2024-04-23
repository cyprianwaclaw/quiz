<?php

namespace App\Models;

use App\Filters\QuizFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Filter\Traits\Filterable;

/**
 * App\Models\Quiz
 *
 * @method static create(array $input)
 * @property int $id
 * @property Category,id $category_id
 * @property string $title
 * @property string $description
 * @property mixed $questions
 * @property string $image
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category|null $category
 * @property-read int|null $questions_count
 * @method static \Database\Factories\QuizFactory factory(...$parameters)
 * @method static Builder|Quiz newModelQuery()
 * @method static Builder|Quiz newQuery()
 * @method static Builder|Quiz query()
 * @method static Builder|Quiz whereCategoryId($value)
 * @method static Builder|Quiz whereCreatedAt($value)
 * @method static Builder|Quiz whereDescription($value)
 * @method static Builder|Quiz whereId($value)
 * @method static Builder|Quiz whereImage($value)
 * @method static Builder|Quiz whereTitle($value)
 * @method static Builder|Quiz whereUpdatedAt($value)
 * @method static Builder|Quiz inRandomOrder()
 * @method static Builder|Quiz active()
 * @method static Builder|Quiz inactive()
 * @method static Builder|Quiz popular()
 * @method static Builder|Quiz latest()
 * @method static Builder|Quiz forYou()
 * @method static Builder|Quiz filter()
 * @mixin \Eloquent
 */
class Quiz extends Model
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

    // dodawanie liczby pytan do kazdego po relacji questions_count
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

    public function quizSubmission()
    {
        return $this->hasMany(QuizSubmission::class);
    }

    /**
     * Scope a query to only include active quizzes.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeActive(Builder $query)
    {
        $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive quizzes.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeInactive(Builder $query)
    {
        $query->where('is_active', false);
    }

    /**
     * Scope a query to only include quizzes with specify category.
     *
     * @param Builder $query
     * @param int $category_id
     * @return void
     */
    public function scopeCategory(Builder $query, int $category_id)
    {
        $query->where('category_id', $category_id);
    }

    /* public function scopeFilter(Builder $builder, $request): Builder
    {
        return (new QuizFilter($request))->filter($builder);
    }*/

    public function scopePopular(Builder $builder)
    {
        /* $builder->select()->orderByDesc('total')
            ->selectSub(
                QuizSubmission::selectRaw('count(*)')
                    ->whereNotNull('ended_at')
                    ->whereRaw('quizzes.id = quiz_submissions.quiz_id')
                    ->groupBy('quiz_id')
            ,'total');*/
        $builder->select('quizzes.*')
            ->addSelect('total.total')
            ->leftJoinSub(
                QuizSubmission::select('quiz_id')
                    ->selectRaw('count(*) as total')
                    ->whereNotNull('ended_at')
                    ->groupBy('quiz_id'),
                'total',
                function ($join) {
                    $join->on('quizzes.id', '=', 'total.quiz_id');
                }
            )
            ->orderByDesc('total');
    }

    public function scopeLatest(Builder $builder)
    {
        $builder->orderByDesc('created_at');
    }

    public function scopeForYou(Builder $builder)
    {
        $builder->inRandomOrder();
    }
}
