<?php

namespace Database\Seeders;

use App\Enums\QuizDifficulty;
use App\Models\Category;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = Category::where('name','Jedzenie')->firstOrFail();
        $random_img = "https://picsum.photos/300/400";
        $quiz = Quiz::create([
            'title' => "Jak dobrze znasz kuchnię polską?",
            'description' => "Jaka jest Twoja znajomość tradycyjnych polskich potraw?",
            'image' => $random_img,
            'time' => 5 * rand(1, 4),
            'difficulty' => Arr::random(QuizDifficulty::TYPES),
            'is_active' => rand(0, 1),
            'user_id' => 1,

        ]);
        $category->quizzes()->save($quiz);

        $quiz = Quiz::create([
            'title' => "Czy wiesz, co jesz?",
            'description' => "Czy znasz nazwy klasycznych i mniej klasycznych potraw?",
            'image' => $random_img,
            'time' => 5 * rand(1, 4),
            'difficulty' => Arr::random(QuizDifficulty::TYPES),
            'is_active' => rand(0, 1),
            'user_id' => 1,
        ]);
        $category->quizzes()->save($quiz);

        $category = Category::where('name','Zwierzęta')->firstOrFail();
        $quiz = Quiz::create([
            'title' => "Quiz o zwierzętach - sprawdź swoją wiedzę!",
            'description' => "Sprawdź swoją wiedzę w krótkim quizie o zwierzętach!",
            'image' => $random_img,
            'time' => 5 * rand(1, 4),
            'difficulty' => Arr::random(QuizDifficulty::TYPES),
            'is_active' => rand(0, 1),
            'user_id' => 1,
        ]);
        $category->quizzes()->save($quiz);

        $quiz = Quiz::create([
            'title' => "Jak dobrze znasz kocią mowę ciała?",
            'description' => "Czy potrafisz dogadać się z kotami? Wiesz, co sygnalizują swoim zachowaniem?",
            'image' => $random_img,
            'time' => 5 * rand(1, 4),
            'difficulty' => Arr::random(QuizDifficulty::TYPES),
            'is_active' => rand(0, 1),
            'user_id' => 2,
        ]);
        $category->quizzes()->save($quiz);


    }
}
