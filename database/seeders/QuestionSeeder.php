<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['quiz_id' => 1, 'question' => 'Utożsamiana z rekuzą i czarną polewką czernina to zupa na bazie:',
                'answers' => [
                    ['answer' => 'Kaczej krwi', 'correct' => true],
                    ['answer' => 'Nerek drobiowych'],
                    ['answer' => 'Wątroby wołowej'],
                    ['answer' => 'Gęsiej żółci'],
                ]
            ],

            ['quiz_id' => 1, 'question' => 'Żurek od białego barszczu różni się przede wszystkim:',
                'answers' => [
                    ['answer' => 'Rodzajem zakwasu', 'correct' => true],
                    ['answer' => 'Dodatkiem jajka'],
                    ['answer' => 'Dodatkiem białej kiełbasy'],
                    ['answer' => 'Dodatkiem warzyw'],
                ]
            ],
            ['quiz_id' => 1, 'question' => 'Na bazie pokazanej na zdjęciu rośliny przyrządzimy zupę:',
                'answers' => [
                    ['answer' => 'Szczawiową', 'correct' => true],
                    ['answer' => 'Kalarepową'],
                    ['answer' => 'Chłodnik'],
                    ['answer' => 'Botwinkę'],
                ]
            ],
            ['quiz_id' => 1, 'question' => 'Technikę przyrządzania kefiru kuchnia polska zawdzięcza:',
                'answers' => [
                    ['answer' => 'Tatarom', 'correct' => true],
                    ['answer' => 'Czechom'],
                    ['answer' => 'Rusinom'],
                    ['answer' => 'Niemcom'],
                ]
            ],
            ['quiz_id' => 1, 'question' => 'O "chlebie nadobnym, jarzynkach pięknie przyprawionych, krupeczkach bieluchnych a drobniuczko usianych, o kureczkach tłuściuchnych podanych na obrusku białym..."',
                'answers' => [
                    ['answer' => 'Mikołaj Rej', 'correct' => true],
                    ['answer' => 'Jan Kochanowski'],
                    ['answer' => 'Szymon Szymonowic'],
                ]
            ],
            ['quiz_id' => 1, 'question' => 'Nudelzupa to śląskie określenie:',
                'answers' => [
                    ['answer' => 'Rosołu z makaronem', 'correct' => true],
                    ['answer' => 'Pomidorowej z ryżem'],
                    ['answer' => 'Barszczu czerwonego'],
                    ['answer' => 'Zupy nic'],
                ]
            ],

            // quiz nr 2
            ['quiz_id' => 2, 'question' => 'Jak nazywa się pieróg nadziewany farszem z mięsa, jarzyn i ryżu?',
                'answers' => [
                    ['answer' => 'Kulebiak', 'correct' => true],
                    ['answer' => 'Chinkali'],
                    ['answer' => 'Knysz'],
                    ['answer' => 'Pyza'],
                ]
            ],
            ['quiz_id' => 2, 'question' => 'Podaj nazwę marynowanej mieszaniny ogórków, dyni, marchwi, cebuli i zielonych pomidorów:',
                'answers' => [
                    ['answer' => 'Pikle', 'correct' => true],
                    ['answer' => 'Multilla'],
                    ['answer' => 'Warzywnik'],
                    ['answer' => 'Quesadilla'],
                ]
            ],
            ['quiz_id' => 2, 'question' => 'Jak nazywa się ciastko sławiące warszawską Trasę?',
                'answers' => [
                    ['answer' => 'Wuzetka', 'correct' => true],
                    ['answer' => 'Babka'],
                    ['answer' => 'Szarlotka'],
                    ['answer' => 'Bajaderka'],
                ]
            ],
            ['quiz_id' => 2, 'question' => 'Smażona ryba z dodatkiem poszatkowanych duszonych warzyw i sosu warzywnego to:',
                'answers' => [
                    ['answer' => 'Ryba po grecku', 'correct' => true],
                    ['answer' => 'Krupnik'],
                    ['answer' => 'Ryba po żydowsku'],
                    ['answer' => 'Rolmops'],
                ]
            ],
            ['quiz_id' => 2, 'question' => 'Boeuf Strogonow przyrządzamy z:',
                'answers' => [
                    ['answer' => 'Polędwicy', 'correct' => true],
                    ['answer' => 'Karkówki'],
                    ['answer' => 'Łopatki'],
                    ['answer' => 'Podrobów'],
                ]
            ],
            ['quiz_id' => 2, 'question' => 'Ryż z przyprawami korzennymi i jarzynami z dodatkiem baraniny i smażonych ryb to:',
                'answers' => [
                    ['answer' => 'Pilaw', 'correct' => true],
                    ['answer' => 'Hummus'],
                    ['answer' => 'Archi'],
                    ['answer' => 'Lawasz'],
                ]
            ],
            ['quiz_id' => 2, 'question' => 'Popularne w Polsce leczo pochodzi z kuchni:',
                'answers' => [
                    ['answer' => 'Węgierskiej', 'correct' => true],
                    ['answer' => 'Rosyjskiej'],
                    ['answer' => 'Hiszpańskiej'],
                    ['answer' => 'Francuskiej'],
                ]
            ],
            ['quiz_id' => 2, 'question' => 'Zwinięty w rulon marynowany śledź to:',
                'answers' => [
                    ['answer' => 'Rolmops', 'correct' => true],
                    ['answer' => 'Pikiel'],
                    ['answer' => 'Zwijka'],
                    ['answer' => 'Sałaka'],
                ]
            ],
            ['quiz_id' => 2, 'question' => 'Bundz to rodzaj sera wyrabianego z mleka:',
                'answers' => [
                    ['answer' => 'Owczego', 'correct' => true],
                    ['answer' => 'Kobylego'],
                    ['answer' => 'Koziego'],
                    ['answer' => 'Krowiego'],
                ]
            ],
            ['quiz_id' => 2, 'question' => 'Jak nazywa się panierowany kotlet mielony z mięsa kurzego i cielęcego?',
                'answers' => [
                    ['answer' => 'Pożarski', 'correct' => true],
                    ['answer' => 'Kurciel'],
                    ['answer' => 'Rzymski'],
                    ['answer' => 'Krokiet'],
                ]
            ],

//            quiz nr 3
            ['quiz_id' => 3, 'question' => 'Gdzie poza Europą i Azją można spotkać rysia?',
                'answers' => [
                    ['answer' => 'W Ameryce Północnej', 'correct' => true],
                    ['answer' => 'W Australii'],
                    ['answer' => 'W Ameryce Południowej'],
                    ['answer' => 'Na Antarktydzie'],
                    ['answer' => 'W Afryce'],
                ]
            ],
            ['quiz_id' => 3, 'question' => 'Które zwierzęta żyją w watahach?',
                'answers' => [
                    ['answer' => 'Wilki', 'correct' => true],
                    ['answer' => 'Pstrągi'],
                    ['answer' => 'Lwy'],
                    ['answer' => 'Łosie'],
                ]
            ],
            ['quiz_id' => 3, 'question' => 'Który z poniższych ptaków sprawi, że populacja komarów zacznie maleć?',
                'answers' => [
                    ['answer' => 'Jerzyk', 'correct' => true],
                    ['answer' => 'Wróbel'],
                    ['answer' => 'Sroka'],
                    ['answer' => 'Kukułka'],
                ]
            ],
            ['quiz_id' => 3, 'question' => 'Okres godowy których zwierząt nazywamy tarłem?',
                'answers' => [
                    ['answer' => 'Ryb', 'correct' => true],
                    ['answer' => 'Gadów'],
                    ['answer' => 'Ssaków'],
                    ['answer' => 'Płazów'],
                ]
            ],
            ['quiz_id' => 3, 'question' => 'Wśród kangurów większe są:',
                'answers' => [
                    ['answer' => 'Samce', 'correct' => true],
                    ['answer' => 'Samice'],
                ]
            ],
            ['quiz_id' => 3, 'question' => 'Autotomia to reakcja obronna polegająca na odruchowym odrzuceniu przez zwierzę części ciała, kiedy jest ono w niebezpieczeństwie. U którego z poniższych zwierząt możemy ją zaobserwować?',
                'answers' => [
                    ['answer' => 'Gekona', 'correct' => true],
                    ['answer' => 'Sowy'],
                    ['answer' => 'Surykatki'],
                    ['answer' => 'Suma'],
                ]
            ],
            ['quiz_id' => 3, 'question' => 'Czy żyrafy żyją na wolności poza Afryką?',
                'answers' => [
                    ['answer' => 'Nie', 'correct' => true],
                    ['answer' => 'Tak'],
                ]
            ],
            ['quiz_id' => 3, 'question' => 'Czy dzik jest jedynym przedstawicielem dziko żyjących świniowatych w Europie?',
                'answers' => [
                    ['answer' => 'Tak', 'correct' => true],
                    ['answer' => 'Nie'],
                ]
            ],
            ['quiz_id' => 3, 'question' => 'Czy kiwi potrafią latać?',
                'answers' => [
                    ['answer' => 'Nie', 'correct' => true],
                    ['answer' => 'Tak'],
                ]
            ],
            ['quiz_id' => 3, 'question' => 'Czy ważki są drapieżnikami?',
                'answers' => [
                    ['answer' => 'Tak', 'correct' => true],
                    ['answer' => 'Nie'],
                ]
            ],
            ['quiz_id' => 3, 'question' => 'Czy w Polsce na wolności żyją kraby?',
                'answers' => [
                    ['answer' => 'Tak', 'correct' => true],
                    ['answer' => 'Nie'],
                ]
            ],
            ['quiz_id' => 3, 'question' => 'Czy anakonda jest wężem jadowitym?',
                'answers' => [
                    ['answer' => 'Nie', 'correct' => true],
                    ['answer' => 'Tak'],
                ]
            ],

//            quiz nr 4
            ['quiz_id' => 4, 'question' => 'Co to znaczy, jeśli kot ma ogon podkulony pod siebie?',
                'answers' => [
                    ['answer' => 'Jest przestraszony', 'correct' => true],
                    ['answer' => 'Jest zadowolony'],
                    ['answer' => 'Jest zły'],
                    ['answer' => 'Jest zmęczony'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Jeśli kot macha końcówką ogona, to...',
                'answers' => [
                    ['answer' => 'Chce się bawić / cieszy się, że Cię widzi', 'correct' => true],
                    ['answer' => 'Jest zły'],
                    ['answer' => 'Jest przestraszony'],
                    ['answer' => 'Jest śpiący'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Jeśli kot ma wyprostowany ogon skierowany ku górze, to:',
                'answers' => [
                    ['answer' => 'Jest odprężony i zrelaksowany', 'correct' => true],
                    ['answer' => 'Boi się'],
                    ['answer' => 'Jest wściekły'],
                    ['answer' => 'Cieszy się, że Cię widzi'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Jeśli kot macha ogonem, to:',
                'answers' => [
                    ['answer' => 'Jest niezadowolony / zdenerwowany', 'correct' => true],
                    ['answer' => 'Jest smutny'],
                    ['answer' => 'Jest szczęśliwy'],
                    ['answer' => 'Cieszy się, że Cię widzi'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Jeśli kotu drży ogon, to:',
                'answers' => [
                    ['answer' => 'Jest bardzo, bardzo szczęśliwy', 'correct' => true],
                    ['answer' => 'Jest smutny'],
                    ['answer' => 'Jest bardzo wystraszony'],
                    ['answer' => 'Jest bardzo, bardzo zły'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Jeśli kot ma ogon opuszczony w dół, ale uniesiony u nasady, to:',
                'answers' => [
                    ['answer' => 'Jest agresywny / będzie się bronić', 'correct' => true],
                    ['answer' => 'Jest spokojny'],
                    ['answer' => 'Jest zły'],
                    ['answer' => 'Jest podekscytowany'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Jeśli kot ma ogon w poziomie, to:',
                'answers' => [
                    ['answer' => 'Nie boi się', 'correct' => true],
                    ['answer' => 'Jest niepewny'],
                    ['answer' => 'Jest zadowolony'],
                    ['answer' => 'Boi się'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Jeśli kot ma podniesiony i nastroszony ogon, to:',
                'answers' => [
                    ['answer' => 'Jest wściekły', 'correct' => true],
                    ['answer' => 'Jest bardzo zadowolony'],
                    ['answer' => 'Jest odprężony'],
                    ['answer' => 'Jest smutny'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Jeśli kot kładzie uszy po sobie, to:',
                'answers' => [
                    ['answer' => 'Jest przestraszony', 'correct' => true],
                    ['answer' => 'Jest smutny'],
                    ['answer' => 'Jest szczęśliwy'],
                    ['answer' => 'Jest zdenerwowany / gotowy do ataku'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Jeśli kot ma uszy stojące lekko pod kątem na boki, to:',
                'answers' => [
                    ['answer' => 'Jest spokojny / zrelaksowany', 'correct' => true],
                    ['answer' => 'Jest bardzo, bardzo zły'],
                    ['answer' => 'Jest zainteresowany'],
                    ['answer' => 'Nasłuchuje'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Uszy prosto stojące do góry oznaczają, że kot:',
                'answers' => [
                    ['answer' => 'Jest zaalarmowany / zaciekawiony', 'correct' => true],
                    ['answer' => 'Jest bardzo szczęśliwy'],
                    ['answer' => 'Jest zły / gotowy do ataku'],
                    ['answer' => 'Jest spokojny / zrelaksowany'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Kot ma uszy skierowane do góry i obraca nimi na wszystkie strony:',
                'answers' => [
                    ['answer' => 'Bada otoczenie', 'correct' => true],
                    ['answer' => 'Jest wystraszony'],
                    ['answer' => 'Poluje'],
                    ['answer' => 'Jest zdenerwowany'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Co to znaczy, kiedy kot zaczyna pomału wbijać pazurki w posłanie lub miejsce, na którym leży, na zmianę łapkami, mrucząc przy tym?',
                'answers' => [
                    ['answer' => 'Przypomina sobie dzieciństwo', 'correct' => true],
                    ['answer' => 'Uspokaja się'],
                    ['answer' => 'Daje Ci do zrozumienia, żebyś go przytulił / pogłaskał'],
                    ['answer' => 'Układa sobie posłanie'],
                ]
            ],
            ['quiz_id' => 4, 'question' => 'Kiedy kot ociera się o Ciebie lub o jakiś mebel policzkami:',
                'answers' => [
                    ['answer' => 'Oznacza tę rzecz / osobę jako swoją własność lub oznacza teren', 'correct' => true],
                    ['answer' => 'Pokazuje Ci, jak bardzo Cię kocha i szanuje'],
                    ['answer' => 'Chce, żebyś go pogłaskał'],
                    ['answer' => 'Namawia Cię do wspólnej zabawy'],
                ]
            ],
        ];
//        'answers' => []
        foreach ($data as $item) {
            $q = Question::create([
                'quiz_id' => $item['quiz_id'],
                'question' => $item['question'],
            ]);
            $q->answers()->createMany($item['answers']);
//            $q->correct_answer_id = $q->answers->first()->id;
            $q->save();
        }
    }
}
