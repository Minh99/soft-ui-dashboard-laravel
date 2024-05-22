<?php

namespace App\Http\Controllers;

use App\Http\Services\VocabularyService;
use App\Models\UserDayCompleted;
use App\Models\UserKnown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{

    /**
     * Vocabulary service
     *
     * @var VocabularyService
     */
    protected $vocabularyService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(VocabularyService $vocabularyService)
    {
        $this->vocabularyService = $vocabularyService;
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
            return redirect('quiz-first');
        }

        return view('dashboard', [
            'vocabularies' => $vocabularies,
            'count_total' => $count,
            'known_by_user' => $knownByUser,
        ]);
    }

    public function QuizFirst()
    {
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
        $userDay = new UserDayCompleted();
        $userDay->user_id = $user->id;
        $userDay->is_passed_first_quiz = true;
        $userDay->day_number = 1;
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
}
