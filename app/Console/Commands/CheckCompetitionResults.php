<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckCompetitionResults extends Command
{
    protected $signature = 'competition:check-results';
    protected $description = 'Sprawdzenie wyników konkursów w określonym czasie';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $competitions = Competition::where('time_end', '=', Carbon::now())->get();

        foreach ($competitions as $competition) {
            // Sprawdź wyniki konkursu
            $this->checkCompetitionResults($competition);
        }
    }

    protected function checkCompetitionResults(Competition $competition)
    {
        $competitionSubmissions = CompetitionSubmission::where('competition_id', $competition->id)
            ->orderByDesc('correct_answers') // Sortowanie po poprawnych odpowiedziach (malejąco)
            ->orderBy('duration')           // Sortowanie po czasie (rosnąco)
            ->select('id', 'user_id', 'correct_answers', 'duration')
            ->get()
            ->map(function ($submission, $index) {
                $submission->place = $index + 1; // Dodaj pole `place` z miejscem (indeks + 1)
                return $submission;
            });

        foreach ($competitionSubmissions as $submission) {
            $user = User::find($submission->user_id);

            if ($user) {
                // Przyznawanie punktów na podstawie miejsca z pól konkursu
                switch ($submission->place) {
                    case 1:
                        $user->points += $competition->first_points;
                        break;
                    case 2:
                        $user->points += $competition->second_points;
                        break;
                    case 3:
                        $user->points += $competition->third_points;
                        break;
                }

                // Zapisz zaktualizowane punkty użytkownika
                $user->save();
            }

            // Zaktualizuj miejsce w tabeli `competition_submissions`
            $competitionSubmission = CompetitionSubmission::find($submission->id);
            if ($competitionSubmission) {
                $competitionSubmission->place = $submission->place;
                $competitionSubmission->save();
            }
        }

        Log::info('Konkurs zakończony:', [
            'competition_id' => $competition->id,
            'competition_time' => $competition->time_end,
            'submissions' => $competitionSubmissions,
        ]);
    }
}
