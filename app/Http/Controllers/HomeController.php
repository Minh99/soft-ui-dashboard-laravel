<?php

namespace App\Http\Controllers;

use App\Http\Services\BaseService;
use App\Http\Services\VocabularyService;
use App\Models\TopicUser;
use App\Models\User;
use App\Models\UserDayCompleted;
use App\Models\UserKnown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{

    /**
     * Vocabulary service
     *
     * @var VocabularyService
     */
    protected $vocabularyService;

    /**
     * BaseService service
     *
     * @var BaseService
     */
    protected $baseService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(VocabularyService $vocabularyService, BaseService $baseService)
    {
        $this->vocabularyService = $vocabularyService;
        $this->baseService = $baseService;
    }

    public function home()
    {
        return redirect('dashboard');
    }

    /**
     * Display the home page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $vocabularies = $this->vocabularyService->getVocabularies();
        $count = $this->vocabularyService->getVocabulariesCount();
        $knownByUser = UserKnown::where('user_id', Auth::id())->where('is_known', true)->count();

        $user = Auth::user();

        if ($user->is_first_login) {
            // TODO middleware
            return redirect('quiz-first');
        }

        $userDay = $user->dayCompleteds()->where('is_completed', false)->first();
        $userDays = $user->dayCompleteds()->orderBy('day_number', 'asc')->get();

        $dataUserDaysCountVoc = [];
        $countPre = 0;
        foreach ($userDays as $key => $userDay) {
            $dataUserDaysCountVoc[] = count(json_decode($userDay->vocabulary_ids, true) ?? []) - $countPre;
            $countPre = count(json_decode($userDay->vocabulary_ids, true) ?? []);
        }

        return view('dashboard', [
            'vocabularies' => $vocabularies,
            'count_total' => $count,
            'known_by_user' => $knownByUser,
            'userDay' => $userDay,
            'dataUserDaysCountVoc' => implode(',', $dataUserDaysCountVoc),
        ]);
    }

    public function QuizFirst()
    {
        $user = Auth::user();
        if (!$user->is_first_login) {
            return redirect('dashboard');
        }

        $quizTypeRandom = $this->vocabularyService->getQuizTypeRandom();

        return view('quiz-first', [
            'quizTypeRandom' => $quizTypeRandom,
        ]);
    }

    public function QuizFirstSubmit(Request $request)
    {
        $user = Auth::user();
        $user->is_first_login = false;
        $user->save();
        // TODO: DAY 1
        $userDay = UserDayCompleted::where('user_id', $user->id)->where('is_completed', false)->first();
        if (!$userDay) {
            $userDay = new UserDayCompleted();
            $userDay->user_id = $user->id;
            $userDay->is_passed_first_quiz = true;
            $userDay->day_number = 1;
            
        }

        $userDay->is_passed_first_quiz = true;
        $userDay->save();
        $userDay->refresh();

        $vocabularies = $this->vocabularyService->getVocabularies();
        $vocabularies = collect($vocabularies);
        $data = $request->except('_token');

        $listKnownByUser = [];
        $listUnknownByUser = [];
        foreach ($data as $key => $value) {
            // 28-en
            // 1-ko
            $id = explode('-', $key)[0];
            $type = explode('-', $key)[1];
            
            if ($type == 'en') {
                if ($value == 1) {
                    $listKnownByUser[] = intval($id);
                }
            } else {
                $vocabulary = $vocabularies->where('id', $id)->first();
                if ($vocabulary['en'] == $value) {
                    $listKnownByUser[] = intval($id);
                }
            }
        }

        $listUnknownByUser = $vocabularies->pluck('id')->diff($listKnownByUser)->toArray();

        DB::beginTransaction();
        try {
            $dataInsert = [];
            foreach ($listKnownByUser as $vocabularyId) {
                $dataInsert[] = [
                    'user_id' => $user->id,
                    'vocabulary_id' => $vocabularyId,
                    'is_known' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            foreach ($listUnknownByUser as $vocabularyId) {
                $dataInsert[] = [
                    'user_id' => $user->id,
                    'vocabulary_id' => $vocabularyId,
                    'is_known' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $userDay->vocabulary_ids = $listKnownByUser;
            $userDay->save();

            DB::table('user_knowns')->insert($dataInsert);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }

        return redirect('dashboard');
    }


    public function QuizFor($topicUserId)
    {
        $user = auth()->user();
       
        $topicUser = TopicUser::where('user_id', $user->id)->where('id', $topicUserId)->first();

        $quizTypeRandom = $this->vocabularyService->getQuizFor($topicUser);

        return view('quiz-for', [
            'topicUser' => $topicUser,
            'quizTypeRandom' => $quizTypeRandom,
        ]);
    }

    public function QuizForSubmit(Request $request)
    {
        $user = Auth::user();
        // Day1: 1 story - 10 words
        // Day2-3-5: 2 story - 15 words

        $userDay = UserDayCompleted::where('user_id', $user->id)->where('is_completed', false)->first();

        if (!$userDay) {
            $user->is_first_login = false;
            $user->save();
            $userDay = new UserDayCompleted();
            $userDay->user_id = $user->id;
            $userDay->is_passed_first_quiz = false;
            $userDay->day_number = 1;
            $userDay->save();
        } else {
            $isPassedFirstQuiz = $userDay->is_passed_first_quiz;
            $isPassedQuizStory1 = $userDay->is_passed_quiz_story_1;
            $isPassedQuizStory2 = $userDay->is_passed_quiz_story_2;
            $dayNumber = $userDay->day_number;

            switch ($dayNumber) {
                case 1:
                    if (!$isPassedQuizStory1) {
                        $userDay->is_passed_quiz_story_1 = true;
                    }
                    break;
                case 2:
                case 3:
                case 5:
                    if (!$isPassedQuizStory1) {
                        $userDay->is_passed_quiz_story_1 = true;
                    }
                    if ($isPassedQuizStory1 && !$isPassedQuizStory2) {
                        $userDay->is_passed_quiz_story_2 = true;
                    }
                    break;
                case 4:
                case 6:
                    $userDay->is_passed_first_quiz = true;
                    break;
                default:
                    break;
            }
        }

        $vocabularies = $this->vocabularyService->getVocabularies();
        $vocabularies = collect($vocabularies);
        $data = $request->except('_token');

        $listKnownByUser = [];
        foreach ($data as $key => $value) {
            // 28-en
            // 1-ko
            $id = explode('-', $key)[0];
            $type = explode('-', $key)[1];
            
            if ($type == 'en') {
                if ($value == 1) {
                    $listKnownByUser[] = intval($id);
                }
            } else {
                $vocabulary = $vocabularies->where('id', $id)->first();
                if ($vocabulary['en'] == $value) {
                    $listKnownByUser[] = intval($id);
                }
            }
        }

        DB::beginTransaction();
        try {
            foreach ($listKnownByUser as $vocabularyId) {
                DB::table('user_knowns')->updateOrInsert([
                    'user_id' => $user->id,
                    'vocabulary_id' => $vocabularyId,
                ], [
                    'is_known' => true,
                    'updated_at' => now(),
                ]);
            }
            $userDay->vocabulary_ids = array_merge(json_decode($userDay->vocabulary_ids, true), $listKnownByUser);
            $userDay->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }

        return redirect('dashboard');
    }

    // for days 4-6
    public function Quiz()
    {
        $user = auth()->user();
       
        $userDay = UserDayCompleted::where('user_id', $user->id)->where('is_completed', false)->first();
        
        $dayNumber = $userDay->day_number;

        $quizTypeRandom = null;

        switch ($dayNumber) {
            case 4:
            case 6:
                $quizTypeRandom = $this->vocabularyService->getQuiz($user, $dayNumber);
                break;
            default:
                break;
        }

        return view('quiz-for', [
            'quizTypeRandom' => $quizTypeRandom,
        ]);
    }

    public function markDone($topicUserId)
    {
        $canSubmit = Session::get('CanSubmit', false);
        if (!$canSubmit) {
            abort(404);
        }
        $user = auth()->user();
        $topicUser = TopicUser::where('user_id', $user->id)->where('id', $topicUserId)->first() ?? abort(404);
        $userDay = UserDayCompleted::where('user_id', $user->id)->where('is_completed', false)->first();

        if (!$userDay) {
            $user->is_first_login = false;
            $user->save();
            $userDay = new UserDayCompleted();
            $userDay->user_id = $user->id;
            $userDay->is_passed_first_quiz = false;
            $userDay->day_number = 1;
            $userDay->save();
        } else {
            $isPassedQuizStory1 = $userDay->is_passed_quiz_story_1;
            $isPassedQuizStory2 = $userDay->is_passed_quiz_story_2;
            $isPassedQuizStory3 = $userDay->is_passed_quiz_story_3;
            $isPassedQuizStory4 = $userDay->is_passed_quiz_story_4;
            $dayNumber = $userDay->day_number;

            switch ($dayNumber) {
                case 1:
                    if (!$isPassedQuizStory1) {
                        $userDay->is_passed_quiz_story_1 = true;
                    }
                    if ($isPassedQuizStory1 && !$isPassedQuizStory2) {
                        $userDay->is_passed_quiz_story_2 = true;
                    }
                    break;
                case 2:
                case 3:
                case 5:
                    if (!$isPassedQuizStory1) {
                        $userDay->is_passed_quiz_story_1 = true;
                    }
                    if ($isPassedQuizStory1 && !$isPassedQuizStory2) {
                        $userDay->is_passed_quiz_story_2 = true;
                    }
                    if ($isPassedQuizStory1 && $isPassedQuizStory2 && !$isPassedQuizStory3) {
                        $userDay->is_passed_quiz_story_3 = true;
                    }
                    if ($isPassedQuizStory1 && $isPassedQuizStory2 && $isPassedQuizStory3 && !$isPassedQuizStory4) {
                        $userDay->is_passed_quiz_story_4 = true;
                    }
                    break;
                case 4:
                case 6:
                    $userDay->is_passed_first_quiz = true;
                    break;
                default:
                    break;
            }
        }

        $dataWords = $topicUser->data ? json_decode($topicUser->data, true) : [];
        $dataWords = $dataWords['words'] ?? [];
        $words = collect($dataWords)->pluck('word');
        $ens = $words->toArray() ?? [];
        $vocabulariesIdByStory = $this->vocabularyService->getVocabulariesIdByEn($ens);

        $userDay->vocabulary_ids = array_values(array_unique(array_merge(json_decode($userDay->vocabulary_ids, true) ?? [], $vocabulariesIdByStory)));
        $userDay->save();

        UserKnown::where('user_id', $user->id)->whereIn('vocabulary_id', $userDay->vocabulary_ids)->update([
            'is_known' => true,
        ]);

        Session::forget('CanSubmit');

        return redirect('dashboard');
    }

    public function markDoneTest2()
    {
        $user = auth()->user();
        $userDay = UserDayCompleted::where('user_id', $user->id)->where('is_completed', false)->first();

        $userDay->is_passed_test_2 = true;
        $userDay->save();

        return redirect('dashboard')->with('success', 'You have completed the test.');
    }
}
