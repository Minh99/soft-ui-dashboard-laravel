<?php

namespace App\Http\Controllers;

use App\Http\Services\VocabularyService;
use App\Models\UserWithChat;
use App\Models\Vocabulary;
use Gemini;
use Gemini\Data\GenerationConfig;
use Gemini\Data\SafetySetting;
use Gemini\Enums\HarmBlockThreshold;
use Gemini\Enums\HarmCategory;
use Gemini\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;

class UserWithChatController extends Controller
{
    protected $vocabularyService;

    public function __construct(VocabularyService $vocabularyService)
    {
        $this->vocabularyService = $vocabularyService;
    }

    public function index()
    {
        $user = auth()->user();
        $vocabulariesKnow = $user->userKnowns()->where('is_known', true)->get();
        $vocabularies = Vocabulary::all()->pluck('en', 'id');

        $vocabulariesKnow2 = [];
        $vocabularies->map(function ($vocabulary, $key) use ($vocabulariesKnow, &$vocabulariesKnow2) {
            $vocabulariesKnow2[$key] = [
                'en' => $vocabulary,
                'is_known' => $vocabulariesKnow->contains('vocabulary_id', $key)
            ];
        });

        return view('user-with-chat', [
            'user' => $user,
            'vocabularies' => $vocabulariesKnow2,
        ]);
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

    public function exchangeStartWord(Request $request, $word)
    {
        $user = auth()->user();

        $record = UserWithChat::where('user_id', $user->id)->where('word', $word)->first();

        if (!$record) {
            $prompt = "How to ask questions with the word [$word]\n
                User Query:\n
                Return content markdown format";

            $chat = $this->setUpClient();
            $response = $chat->sendMessage($prompt);
            $text = $response->text();

            $histories = [
                [
                    "part" => $prompt,
                    "role" => Role::USER,
                    "sort_order" => 1,
                ],
                [
                    "part" => $text,
                    "role" => Role::MODEL,
                    "sort_order" => 2,
                ]
            ];

            $record = UserWithChat::create([
                'user_id' => $user->id,
                'word' => $word,
                'history' => json_encode($histories)
            ]);

        } else {
            $histories = empty($record->history) ? [] : json_decode($record->history, true);
            if (!count($histories)) {
                $prompt = "How to ask questions with the word [$word]\n
                    User Query:\n
                    Return content markdown format";

                $chat = $this->setUpClient();
                $response = $chat->sendMessage($prompt);
                $text = $response->text();

                $histories = [
                    [
                        "part" => $prompt,
                        "role" => Role::USER,
                        "sort_order" => 1,
                    ],
                    [
                        "part" => $text,
                        "role" => Role::MODEL,
                        "sort_order" => 2,
                    ]
                ];
                
                $record->history = json_encode($histories);
                $record->save();
            }
        }

        $histories = array_map(function ($history) {
            //  convert part markdown to html
            return [
                'part' => Markdown::parse($history['part'])->toHtml(),
                'role' => $history['role'],
                'sort_order' => $history['sort_order']
            ];
        }, $histories);

        $record->history = json_encode($histories);

        return json_encode([
            'status' => 200,
            'message' => 'Chat exchanged successfully',
            'data' => json_encode($record)
        ]);
    }

    public function exchangeQuestion(Request $request, $id)
    {
        $user = auth()->user();
        $record = UserWithChat::find($id);
        $question = $request->input('question');

        $text = '';
        if (!$record || $question == '') {
            return json_encode([
                'status' => 400,
                'message' => 'Chat exchanged fail',
                'data' => null
            ]);
        } else {
            $histories = empty($record->history) ? [] : json_decode($record->history, true);
            // $prompt = "1. Check if this question is correct or not, what is its explanation and grammatical structure\nQuestion: [$question]?\n2. Some examples to answer\n=> Return content markdown format";
            $prompt = $question;
            $chat = $this->setUpClient();
            $response = $chat->sendMessage($prompt);
            $text = $response->text();

            $histories[] = [
                "part" => $question,
                "role" => Role::USER,
                "sort_order" => count($histories) + 1,
            ];

            $histories[] = [
                "part" => $text,
                "role" => Role::MODEL,
                "sort_order" => count($histories) + 1,
            ];
            
            $record->history = json_encode($histories);
            $record->save();
        }

        return json_encode([
            'status' => 200,
            'message' => 'Chat exchanged question successfully',
            'text' => Markdown::parse($text)->toHtml(),
        ]);
    }
}
