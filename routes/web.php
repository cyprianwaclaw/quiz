<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserPlanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationMail;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(\route('login'));
});
// Route::get('/preview-email', function () {
//     return new VerificationMail("fdfd");
// });
Route::middleware(['auth', 'verified'])->group(function(){

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('questions', QuestionController::class);
    Route::resource('answers', AnswerController::class);
    Route::get('/quiz/{category}', [QuizController::class, 'index'])->name('quiz');
    Route::post('/quiz/{category}', [QuizController::class, 'store']);
    Route::post('/buy-plan', [UserPlanController::class, 'buyPlan'])->name('buy-plan');

    // ustaw odpowiedź jako prawidłową:
    // api: Route::patch('questions/{question}/answers/{answer}/mark_as_correct', [\App\Http\Controllers\QuestionController::class, 'markAsCorrect'])->name('mark_answer_as_correct');
    Route::post('questions/{question}/set_correct_asnwer', [QuestionController::class, 'setCorrectAsnwer'])->name('set_correct_asnwer');
    Route::get('questions/{question}/answers/create', [AnswerController::class, 'create']);

    Route::get('quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::get('quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::post('quizzes/{quiz}/activate', [QuizController::class, 'activate'])->name('quizzes.activate');
    Route::post('quizzes/{quiz}/deactivate', [QuizController::class, 'deactivate'])->name('quizzes.deactivate');
    Route::get('payout/{payout}', [PayoutController::class, 'show'])->name('payout.show');
    Route::post('payout/{payout}', [PayoutController::class, 'setStatus'])->name('payout.setStatus');
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');

});

Route::post('payment/status', [PaymentController::class, 'status']);

require __DIR__.'/auth.php';
