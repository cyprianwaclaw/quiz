<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\QuizDifficulty;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\User;
use App\Models\Competition;
use Illuminate\Support\Arr;


class CompetitionSeeder extends Seeder
{
    public function run()
    {
        $random_img = "https://picsum.photos/300/400";

        // Tworzenie przykładowych konkursów
        Competition::create([
            'category_id' => Category::inRandomOrder()->first()->id,  // Losowo przypisanie kategorii
            'user_id' => User::inRandomOrder()->first()->id,  // Losowo przypisanie użytkownika
            'title' => 'Konkurs matematyczny',
            'description' => 'Konkurs związany z rozwiązywaniem równań matematycznych.',
            'image' => $random_img,
            'time' => Carbon::now()->addDays(7),  // Konkurs za tydzień
            'date' => Carbon::now()->addDays(7),  // Data konkursu
            'difficulty' => Arr::random(QuizDifficulty::TYPES),
        ]);

        Competition::create([
            'category_id' => Category::inRandomOrder()->first()->id,  // Losowo przypisanie kategorii
            'user_id' => User::inRandomOrder()->first()->id,  // Losowo przypisanie użytkownika
            'title' => 'Konkurs programistyczny',
            'description' => 'Konkurs dla programistów, w którym rozwiązujemy algorytmy.',
            'image' => $random_img,
            'time' => Carbon::now()->addDays(14),  // Konkurs za dwa tygodnie
            'date' => Carbon::now()->addDays(14),  // Data konkursu
            'difficulty' => Arr::random(QuizDifficulty::TYPES),
        ]);

        Competition::create([
            'category_id' => Category::inRandomOrder()->first()->id,  // Losowo przypisanie kategorii
            'user_id' => User::inRandomOrder()->first()->id,  // Losowo przypisanie użytkownika
            'title' => 'Konkurs wiedzy ogólnej',
            'description' => 'Konkurs dotyczący wiedzy ogólnej na różne tematy.',
            'image' => $random_img,
            'time' => Carbon::now()->addDays(30),  // Konkurs za 30 dni
            'date' => Carbon::now()->addDays(30),  // Data konkursu
            'difficulty' => Arr::random(QuizDifficulty::TYPES),
        ]);
    }
}
