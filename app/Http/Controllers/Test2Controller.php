<?php

namespace App\Http\Controllers;

use App\Http\Services\PromptService;
use App\Http\Services\VocabularyService;
use App\Models\UserTest2;
use Gemini;
use Gemini\Data\Content;
use Gemini\Data\GenerationConfig;
use Gemini\Data\SafetySetting;
use Gemini\Enums\HarmBlockThreshold;
use Gemini\Enums\HarmCategory;
use Gemini\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Test2Controller extends Controller
{
    protected $vocabularyService;
    protected $promptService;
    const SHORT_QUIZ = 1;
    const LONG_QUIZ = 2;
    const SUGGESTION_QUIZ = 3;
    const MAKE_PROMPT_QUIZ = 4;
    const types = [
        self::SHORT_QUIZ => 'short_quiz',
        self::LONG_QUIZ => 'long_quiz',
        self::SUGGESTION_QUIZ => 'suggestion_quiz',
        self::MAKE_PROMPT_QUIZ => 'make_prompt_quiz',
    ];


    // constructor
    public function __construct(VocabularyService $vocabularyService, PromptService $promptService)
    {
        $this->vocabularyService = $vocabularyService;
        $this->promptService = $promptService;
    }

    public function setUpClient($histories = [])
    {
        $safetySettingDangerousContent = new SafetySetting(
            category: HarmCategory::HARM_CATEGORY_DANGEROUS_CONTENT,
            threshold: HarmBlockThreshold::BLOCK_NONE
        );
        
        $safetySettingHateSpeech = new SafetySetting(
            category: HarmCategory::HARM_CATEGORY_HATE_SPEECH,
            threshold: HarmBlockThreshold::BLOCK_NONE
        );

        $safetySettingHarassment = new SafetySetting(
            category: HarmCategory::HARM_CATEGORY_HARASSMENT,
            threshold: HarmBlockThreshold::BLOCK_NONE
        );
        
        $safetySettingSexuallyExplicit = new SafetySetting(
            category: HarmCategory::HARM_CATEGORY_SEXUALLY_EXPLICIT,
            threshold: HarmBlockThreshold::BLOCK_NONE
        );

        
        $generationConfig = new GenerationConfig(
            maxOutputTokens: 10000,
        );

        $YOUR_API_KEY= env('GEMINI_API_KEY');
        $client = Gemini::client($YOUR_API_KEY);
        // $response = $client->models()->list();
        // dd($response->models);
        // $response  = $client->models()->retrieve(ModelType::GEMINI_PRO_15_FLASK);
        // dd($response->model);

        return $client
            // ->geminiPro()
            ->geminiPro15Flask()
            ->withSafetySetting($safetySettingDangerousContent)
            ->withSafetySetting($safetySettingHateSpeech)
            ->withSafetySetting($safetySettingHarassment)
            ->withSafetySetting($safetySettingSexuallyExplicit)
            ->withGenerationConfig($generationConfig)
            ->startChat(history: $histories);
    }


    public function formatToJson($text)
    {
        $cleaned_json_string = str_replace('\"', '`', $text);
        $cleaned_json_string = str_replace('\\n', '', $cleaned_json_string);
        $cleaned_json_string = str_replace(["json", "```"], '', $cleaned_json_string);
        $cleaned_json_string = preg_replace('/\n+/', '', $cleaned_json_string);
        $cleaned_json_string = trim($cleaned_json_string, '"');

        return $cleaned_json_string;
    }

    public function test2(Request $request)
    {
        // randome from 1 - 4, not use 2
        $typeRandom = rand(1, 4);
        if ($typeRandom == 2) {
            while ($typeRandom == 2) {
                $typeRandom = rand(1, 4);
            }
        }


        $user = auth()->user();
        $userDayCompleted = $user->dayCompleteds()->where('is_completed', false)->first();
        
        if (!$userDayCompleted) {
            return redirect()->route('dashboard')->with('error', 'You cannot access final test of day. because you have not completed tasks.');
        }

        $dayNumber = $userDayCompleted->day_number;

        if ($dayNumber == 1 && (!$userDayCompleted->is_passed_quiz_story_1 || !$userDayCompleted->is_passed_quiz_story_2)) {
            return redirect()->route('dashboard')->with('error', 'You cannot access final test of day. because you have not completed tasks of day 1.');
        } 
        // elseif (($dayNumber == 2 || $dayNumber == 3 || $dayNumber == 5) &&
        //     (!$userDayCompleted->is_passed_quiz_story_1 || !$userDayCompleted->is_passed_quiz_story_2 || !$userDayCompleted->is_passed_quiz_story_3 || !$userDayCompleted->is_passed_quiz_story_4)
        // ) {
        elseif (($dayNumber == 2 || $dayNumber == 3 || $dayNumber == 5) &&
            (!$userDayCompleted->is_passed_quiz_story_1 || !$userDayCompleted->is_passed_quiz_story_2)
        ) {
            return redirect()->route('dashboard')->with('error', 'You cannot access final test of day. because you have not completed tasks of day ' . $dayNumber . '.');
        } elseif(($dayNumber == 4 || $dayNumber == 6) && !$userDayCompleted->is_passed_first_quiz) {
            return redirect()->route('dashboard')->with('error', 'You cannot access final test of day. You need to pass Test daily.');
        }


        switch ($typeRandom) {
            case self::LONG_QUIZ:
                $prompt = $this->promptService->promptGenTest2ByType2(auth()->user());
                break;
            case self::SUGGESTION_QUIZ:
                $prompt = $this->promptService->promptGenTest2ByType3(auth()->user());
                break;
            case self::MAKE_PROMPT_QUIZ:
                $prompt = $this->promptService->promptGenTest2ByType4(auth()->user());
                break;
            default:
                $prompt = $this->promptService->promptGenTest2ByType1(auth()->user());
                break;
        }

        $numberTry = 3;
        try {
            retryTest2:
            $histories = [];

            $chat = $this->setUpClient($histories);
            $response = $chat->sendMessage($prompt);

            $text = $response->text();

            $cleaned_json_string = $this->formatToJson($text);

            $histories[] = [
                "part" => $prompt,
                "role" => Role::USER,
                "sort_order" => 1,
            ];

            $histories[] = [
                "part" => $text,
                "role" => Role::MODEL,
                "sort_order" => 2,
            ];

            $userTest2 = UserTest2::create([
                'user_id' => auth()->id(),
                'type' => $typeRandom,
                'history' => json_encode($histories),
            ]);

            $data = $cleaned_json_string ? json_decode($cleaned_json_string, true) : [];

            if (empty($data)) {
                $numberTry--;
                if ($numberTry > 0) {
                    goto retryTest2;
                }
                return redirect()->back()->with('error', 'Something went wrong. Please try again.');
            }

            return view('test2', [
                'type' => $typeRandom,
                'data' => $data,
                'userTest2Id' => $userTest2->id
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            $numberTry--;
            if ($numberTry > 0) {
                goto retryTest2;
            }
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function test2Submit(Request $request)
    {
        $userTest2Id = $request->user_test2_id;
        $userTest2 = UserTest2::find($userTest2Id);
        $histories = json_decode($userTest2->history, true);
        $historyChat = [];
        foreach ($histories as $chat) {
            $role = $chat['role'] == 'user' ? Role::USER : Role::MODEL;
            $historyChat[] = Content::parse(part: $chat['part'], role: $role);
        }
        $data = $request->data;

        $type = $userTest2->type;

        switch ($type) {
            case self::SHORT_QUIZ:
                $prompt = $this->promptService->promptSubmitTest2ByType1($data);
                break;
            case self::LONG_QUIZ:
                $prompt = ''; // check client
                break;
            case self::SUGGESTION_QUIZ:
                $prompt = $this->promptService->promptSubmitTest2ByType3($data);
                break;
            case self::MAKE_PROMPT_QUIZ:
                $prompt = $this->promptService->promptSubmitTest2ByType4($data);
                break;
            default:
                $prompt = '';
                break;
        }

        $user = auth()->user() ?? abort(404);

        Log::info([
            'user' => $user->id,
            'type' => $type,
            'prompt' => $prompt,
        ]);

        if ($prompt === '') {
            return response()->json([
                'status' => 400,
                'message' => 'Something went wrong. Please try again.',
            ]);
        }

        $numberTry = 3;

        try {
            tryAgain:
            $historiesTmp = $histories;
            Log::info('try again ' . $numberTry);
            $chat = $this->setUpClient($historyChat);

            $response = $chat->sendMessage($prompt);

            $text = $response->text();
            $cleaned_json_string = $this->formatToJson($text);

            Log::info([
                'user' => $user->id,
                'text' => $text,
                'cleaned_json_string' => $cleaned_json_string,
            ]);


            $historiesTmp[] = [
                "part" => $prompt,
                "role" => Role::USER,
                "sort_order" => count($historiesTmp) + 1,
            ];

            $historiesTmp[] = [
                "part" => $text,
                "role" => Role::MODEL,
                "sort_order" => count($historiesTmp) + 1,
            ];

            $userTest2->update([
                'history' => json_encode($historiesTmp),
            ]);

            $data = $cleaned_json_string ? json_decode($cleaned_json_string, true) : [];

            if (empty($data)) {
                $numberTry--;
                if ($numberTry > 0) {
                    goto tryAgain;
                }
                return response()->json([
                    'status' => 400,
                    'message' => 'Something went wrong. Please try again.',
                ]);
            }

            return response()->json([
                'status' => 200,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            $numberTry--;
            if ($numberTry > 0) {
                goto tryAgain;
            }
            return response()->json([
                'status' => 400,
                'message' => 'Something went wrong. Please try again.',
            ]);
        }
    }
}
