<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Category;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with(['questions', 'category'])->paginate(20);
        return view('quizzes.index')->with([
            'quizzes' => $quizzes,
        ]);
    }

    public function store(Category $category, Request $request)
    {
        $answer_id = (int)$request->answer;
        // TODO: dodać sprawdzenie czy użytkownik nie odpowiedział już na to pytanie
        $answer = Answer::find($request->answer);
        $answer->users_answered()->attach(auth()->user(), ['question_id' => $answer->question_id]);
        return $this->index($category);

    }

    public function show(Quiz $quiz)
    {
        return view('quizzes.show',[
            'quiz' => $quiz,
            'questions' => $quiz->questions()->with('answers')->get(),

        ]);
    }

    public function activate(Quiz $quiz)
    {
        if(!auth()->user()->can('activate quiz'))
            return redirect(route('dashboard'))->with(['error' => 'Brak uprawnień']);

        $quiz->is_active = true;
        $quiz->save();
        return redirect(route('dashboard'))->with(['success' => 'Quiz aktywny']);
    }

    public function deactivate(Quiz $quiz)
    {
        if(!auth()->user()->can('deactivate quiz'))
            return redirect(route('dashboard'))->with(['error' => 'Brak uprawnień']);

        $quiz->is_active = false;
        $quiz->save();
        return redirect(route('dashboard'))->with(['success' => 'Quiz nieaktywny']);
    }

    public function getAll()
    {
        $question = auth()->user()->unansweredQuestions()->inRandomOrder()->where('category_id', $category->id)->first();
//        $question = auth()->user()->unansweredQuestions()->inRandomOrder()->first();
//        $question = auth()->user()->unansweredQuestions()->inRandomOrder()->get();
        return view('quiz')->with([
            'category' => $category,
            'question' => $question,
        ]);
    }

    public function destroy(Quiz $quiz)
    {
        if(!auth()->user()->can('delete quiz'))
            return redirect(route('quizzes.show', $quiz))->with(['error' => 'Brak uprawnień']);

        $quiz->quizSubmission->each->delete();
        foreach ($quiz->questions as $question) {
            $question->answers->each->delete();
        }
        $quiz->questions->each->delete();
        $quiz->delete();
        return redirect(route('quizzes.index'))->with(['success' => 'Quiz wraz z pytaniami został usunięty']);
    }
}
