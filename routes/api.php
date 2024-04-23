<?php

use App\Http\Controllers\API\AnswerController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\SampleController;
use App\Http\Controllers\API\PayoutController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\QuizController;
use App\Http\Controllers\API\QuizSubmissionController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserPlanController;
use App\Http\Controllers\API\UserSettingsController;
use App\Http\Controllers\API\UserStatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum','role:admin']], function () {
    Route::post('user/givePremium', [UserPlanController::class, 'givePremium']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function(){
    Route::prefix('quizzes')->group(function (){
        Route::get('fast-two',[QuizController::class, 'fastTwo']);

        Route::get('popular',[QuizController::class, 'popular']);
        Route::get('latest',[QuizController::class, 'latest']);
        Route::get('for-you', [QuizController::class, 'forYou']);
        // nowy endpoint do sortowania
        Route::get('all', [QuizController::class, 'getAll']);
    });

    Route::apiResource('quizzes', QuizController::class,["as" => "api"]);
    Route::get('quizzes/{id}/questions', [QuizController::class, 'getQuestions']);
    Route::apiResource('categories', CategoryController::class,["as" => "api"]);

    Route::apiResource('questions', QuestionController::class,["as" => "api"]);
    Route::prefix('questions')->group(function () {
        Route::get('{id}/answers', [QuestionController::class, 'getAnswers']);
        Route::delete('{id}/answers', [QuestionController::class, 'destroyAnswers']);
    });

    Route::apiResource('answers', AnswerController::class,["as" => "api"]);
    Route::get('answers/{id}/question', [AnswerController::class, 'getQuestion']);

    Route::get('user/current', [AuthController::class, 'getCurrentUser']);
    Route::get('user/getInvitationToken', [AuthController::class, 'getInvitationToken']);
    Route::post('user/uploadAvatar', [UserSettingsController::class, 'uploadUserPhoto']);
    Route::get('user/quizzes', [UserController::class, 'getUserQuizzes']);
    Route::get('user/getPlan', [UserPlanController::class, 'getUserPlan']);
    Route::get('user/hasPremium', [UserPlanController::class, 'userHasPremium']);

    Route::prefix('quiz')->group(function(){
        Route::get('{quiz}', [QuizController::class, 'show']);
        Route::get('{quiz}/activate', [QuizController::class, 'activate']);
        Route::get('{quiz}/deactivate', [QuizController::class, 'deactivate']);
        Route::get('{quiz}/start', [QuizSubmissionController::class, 'start']);
        Route::post('submission/{quiz_submission}/answerQuestion', [QuizSubmissionController::class, 'answer_question']);
        Route::get('submission/{quiz_submission}/getNextQuestion', [QuizSubmissionController::class, 'getNextQuestion']);
    });

    Route::prefix('user')->group(function (){
        Route::get('getInvitedUsers', [UserController::class, 'getInvitedUsers']);
        Route::get('stats', [UserStatsController::class, 'show']);
        Route::get('settings', [UserSettingsController::class, 'show']);
        Route::post('settings', [UserSettingsController::class, 'update']);
    });
    Route::get('plans', [PlanController::class, 'index']);
    Route::post('buy-plan', [UserPlanController::class, 'buyPlan']);
    Route::get('payments', [PaymentController::class, 'index']);

    Route::get('payments/{payment}/download', [PaymentController::class, 'downloadInvoice']);
    Route::get('hello',[SampleController::class, 'hello']);
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/sort', [TaskController::class, 'sort']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('payouts/{user?}', [PayoutController::class, 'index']);
    // Route::get('payouts/{user?}', function () {
    //     dd(request('page'));
    // })->name('payouts.index')->middleware(['auth']);
    Route::post('payouts/{payout}/setStatus', [PayoutController::class, 'setStatus']);
    Route::post('payouts', [PayoutController::class, 'store']);
});
Route::post('payment/status', [PaymentController::class, 'status']);
