<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ProcessDayNumber
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user) {
            $userDayCompleted = $user->dayCompleteds()->where('is_completed', false)->first();

            if ($userDayCompleted) {
                $isOverNewDay = $userDayCompleted->created_at->format('Y-m-d') !== now()->format('Y-m-d');
                $isPassedQuizStory1 = $userDayCompleted->is_passed_quiz_story_1;
                $isPassedQuizStory2 = $userDayCompleted->is_passed_quiz_story_2;
                $isPassedQuizStory3 = $userDayCompleted->is_passed_quiz_story_3;
                $isPassedQuizStory4 = $userDayCompleted->is_passed_quiz_story_4;
                $isPassedTest2 = $userDayCompleted->is_passed_test_2;
                $isPassedFirstQuiz = $userDayCompleted->is_passed_first_quiz;
                $dayNumber = $userDayCompleted->day_number;

                // dd($isOverNewDay, $isPassedQuizStory1, $isPassedQuizStory2, $dayNumber, $isPassedFirstQuiz);
                $isInsert = false;
                switch ($dayNumber) {
                    case 1:
                        if ($isPassedQuizStory1 && $isPassedQuizStory2 && $isOverNewDay && $isPassedTest2) {
                            $userDayCompleted->is_completed = true;
                            $userDayCompleted->save();
                            $isInsert = true;
                        }
                        break;
                    case 2:
                    case 3:
                    case 5:
                        if ($isPassedQuizStory1 && $isPassedQuizStory2 && $isPassedQuizStory3 && $isPassedQuizStory4 && $isOverNewDay && $isPassedTest2) {
                            $userDayCompleted->is_completed = true;
                            $userDayCompleted->save();
                            $isInsert = true;
                        }
                        break;
                    case 4:
                    case 6:
                        if ($isPassedFirstQuiz && $isPassedTest2) {
                            if ($dayNumber == 4 && $isOverNewDay) {
                                $userDayCompleted->is_completed = true;
                                $userDayCompleted->save();
                                $isInsert = true;
                            }
                            if ($isPassedFirstQuiz && $dayNumber == 6) {
                                $userDayCompleted->is_completed = true;
                                $userDayCompleted->save();
                            }
                        }
                        break;
                    default:
                        break;
                }

                if ($isInsert && $dayNumber < 6) {
                    DB::table('user_day_completeds')->insert([
                        'user_id' => $user->id,
                        'day_number' => $dayNumber + 1,
                        'is_completed' => false,
                        'is_passed_first_quiz' => false,
                        'is_passed_quiz_story_1' => false,
                        'is_passed_quiz_story_2' => false,
                        'vocabulary_ids' => $userDayCompleted->vocabulary_ids,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                DB::table('user_day_completeds')->insert([
                    'user_id' => $user->id,
                    'day_number' => 1,
                    'is_completed' => false,
                    'is_passed_first_quiz' => false,
                    'is_passed_quiz_story_1' => false,
                    'is_passed_quiz_story_2' => false,
                    'vocabulary_ids' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}
