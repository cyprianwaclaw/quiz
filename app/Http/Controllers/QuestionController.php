<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswerUser;
use App\Models\Category;
use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        return view('questions.index')->with([
            'questions' => Question::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('questions.create')->with([
            'categories' => Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreQuestionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuestionRequest $request)
    {
        Question::create([
            'question' => $request->question,
            'category_id' => $request->category,
        ]);
        return redirect(route('questions.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $perPage = $request->input('per_page', 14);
        $page = $request->input('page', 1);

        $allQuestions = Question::all();

        $paginatedQuestions = $allQuestions->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            $paginatedQuestions
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        return view('questions.edit')->with([
            'question' => $question,
            'categories' => Category::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateQuestionRequest  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuestionRequest $request, Question $question)
    {
        $question->question = $request->input('question');
        $question->category_id = $request->input('category');
        $question->save();
        return redirect(route('questions.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Question $question)
    {
        $question->correct_answer_id = null;
        $question->save();
        $aus = AnswerUser::where('question_id', $question->id)->get();
        foreach ($aus as $au) {
            $au->delete();
        }
        foreach ($question->answers as $answer){
            $answer->delete();
        }
        $question->delete();
        return redirect(route('questions.index'));
    }

    public function setCorrectAsnwer(Request $request, Question $question)
    {
        $answer = Answer::findOrFail($request->input('answer_id'));
        $question->correct_answer_id = $answer->id;
        $question->save();
        return redirect(route('questions.show',$question));
    }
}
