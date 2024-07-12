<?php

namespace App\Http\Controllers;

use App\Http\Services\PromptService;
use App\Http\Services\VocabularyService;
use App\Models\Topic;
use App\Models\TopicUser;
use App\Models\UserDayCompleted;
use App\Models\UserKnown;
use Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\HarmBlockThreshold;
use Gemini\Data\SafetySetting;
use Gemini\Enums\HarmCategory;
use Gemini\Enums\ModelType;
use Illuminate\Support\Facades\Session;

class StoryController extends Controller
{

    /**
     * Vocabulary service
     *
     * @var VocabularyService
     */
    protected $vocabularyService;
    protected $promptService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
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

        Log::info($cleaned_json_string);
        
        return $cleaned_json_string;
    }

    /**
     * Generate story
     *
     * @param Request $request
     * @param $topicId
     * @return json
     */
    public function generate(Request $request, $topicId)
    {
        $LIMIT_WORDS_GEN = PromptService::LIMIT_WORDS_GEN;
        $topic = Topic::find($topicId);
        $topicName = $topic->name;
        $topicId = $topic->id;

        $user = auth()->user();
        Session::put('CanSubmit', true);

        if (!$user) {
            return json_encode([
                'status' => 404,
                'message' => 'User not found!',
            ]);
        }
        
        list($vocabulariesToGenStory, $prompt, $typeQuiz) = $this->promptService->promptGenStory($user, $topicName);

        Log::info('vocabulariesToGenStory: ', [
            'user' => $user->id,
            'vocabulariesToGenStory' => $vocabulariesToGenStory,
            'prompt' => $prompt,
            'typeQuiz' => $typeQuiz,
        ]);
        
        $basePrompt = [
            [
                "part" => "The stories you write about what I have to say should be $LIMIT_WORDS_GEN words. Is that clear?",
                "role" => Role::USER,
                "sort_order" => 1,
            ],
            [
                "part" => "Yes, I understand. The stories I write about your input should be $LIMIT_WORDS_GEN words long.",
                "role" => Role::MODEL,
                "sort_order" => 2,
            ],
            [
                "part" => $prompt,
                "role" => Role::USER,
                "sort_order" => 3,
            ]
        ];

        $numberTry = 3;

        try {
            $histories = [
                Content::parse(part: $basePrompt[0]['part'], role: Role::USER),
                Content::parse(part: $basePrompt[1]['part'], role: Role::MODEL),
            ];

            tryGenStory:
            
            $basePromptTmp = $basePrompt;
            Log::info('Number try: '. $numberTry);
            $basePromptTmp = $basePrompt;
            $chat = $this->setUpClient($histories);
            $response = $chat->sendMessage($prompt);

            $text = $response->text();

            $cleaned_json_string = $this->formatToJson($text);

            $basePromptTmp[] = [
                "part" => $text,
                "role" => Role::MODEL,
                "sort_order" => 4,
            ];

            $gen = TopicUser::updateOrCreate(
                [
                    'topic_id' => $topicId,
                    'user_id' => $user->id,
                    'topic_name' => $topicName,
                ],
                [
                    'data' => $cleaned_json_string,
                    'history_chat' => json_encode($basePromptTmp),
                ]
            );

            $genId = $gen->id;

            return json_encode([
                'status' => 200,
                'message' => 'Success',
                'genId' => $genId,
                'typeQuiz' => $typeQuiz,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $numberTry -= 1;
            if ($numberTry > 0) goto tryGenStory;
            return json_encode([
                'status' => 400,
                'message' => 'An error occurred. Please try again.',
            ]);
        }
    }

    public function generateQuestion(Request $request, $topicUserId)
    {
        $user = auth()->user();

        if (!$user) {
            return json_encode([
                'status' => 404,
                'message' => 'User not found!',
            ]);
        }

        $topicUser = TopicUser::where('user_id', $user->id)->where('id', $topicUserId)->first();

        if (!$topicUser) {
            return json_encode([
                'status' => 404,
                'message' => 'Topic not found!',
            ]);
        }

        $historyChat = empty($topicUser->history_chat) ? [] : json_decode($topicUser->history_chat, true);

        $prompt = "Tạo một câu hỏi siêu dễ cho cuộc hội thoại đã tạo ra, câu hỏi phải liên quan đến nội dung của cuộc hội thoại.";
        
        $jsonExample = "
            {
                question: Question,
                answer: Answer
            }
        ";

        $prompt .= "\n =>Trả về format Json như mẫu sau (lưu ý không chứa các ký tự đặc biệt làm hỏng format json): " . $jsonExample;

        $histories = [];

        foreach ($historyChat as $chat) {
            $role = $chat['role'] == 'user' ? Role::USER : Role::MODEL;
            $histories[] = Content::parse(part: $chat['part'], role: $role);
        }

        $numberTry = 3;

        try {
            tryQuestion:
            Log::info('Number try question: '. $numberTry);
            $historyChatTmp = $historyChat;
            $chat = $this->setUpClient($histories);
            $response = $chat->sendMessage($prompt);

            $text = $response->text();
           
            $cleaned_json_string = $this->formatToJson($text);

            $historyChatTmp[] = [
                "part" => $prompt,
                "role" => Role::USER,
                "sort_order" => count($historyChatTmp) + 1,
            ];

            $historyChatTmp[] = [
                "part" => $text,
                "role" => Role::MODEL,
                "sort_order" => count($historyChatTmp) + 1,
            ];

            TopicUser::where('id', $topicUserId)->update([
                'history_chat' => $historyChatTmp,
            ]);

            return json_encode([
                'status' => 200,
                'message' => 'Success',
                'data' => json_decode($cleaned_json_string),
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            // dd($e);
            $numberTry -= 1;
            if ($numberTry > 0) goto tryQuestion;
            return json_encode([
                'status' => 400,
                'message' => 'An error occurred. Please try again.',
            ]);
        }
    }

    public function generateAnswer(Request $request, $topicUserId)
    {
        $user = auth()->user();

        if (!$user) {
            return json_encode([
                'status' => 404,
                'message' => 'User not found!',
            ]);
        }

        $topicUser = TopicUser::where('user_id', $user->id)->where('id', $topicUserId)->first();

        if (!$topicUser) {
            return json_encode([
                'status' => 404,
                'message' => 'Topic not found!',
            ]);
        }

        $historyChat = empty($topicUser->history_chat) ? [] : json_decode($topicUser->history_chat, true);

        $answer = $request->query('answer') ?? '';
        $question = $request->query('question') ?? '';

        $prompt = "Trả lời cho câu hỏi bạn đã tạo ra: [$question]. \nCâu trả lời của tôi là: [$answer]" . 
            "\nUser query:" .
            "\n- Nếu câu trả lời đúng, Hãy chúc mừng theo nhiều kiểu khác nhau.
            \nKết quả Trả về một chuỗi JSON (không chứa các ký tự đặc biệt làm hỏng format JSON):\n
            {
                \"is_correct\": true,
                \"comment\": \"Chúc mừng bằng tiếng anh.\",
                \"correct_answer\": \"câu trả lời đúng\"
            }\n
            \n- Nếu câu trả lời sai sẽ có 5 cách trả lời sau:
            \n1.Câu trả lời đúng là: [câu trả lời đúng]
            \n2.Câu trả lời chưa chính xác, câu trả lời đúng là: [câu trả lời đúng]. Động viên bạn làm tốt hơn lần sau.
            \n3.Câu trả lời chưa chính xác, câu trả lời đúng là: [câu trả lời đúng].
            \n4.Sai mất rồi, câu trả lời đúng phải là: [câu trả lời đúng]. Động viên bạn làm tốt hơn lần sau.
            \n5.Câu trả lời chưa chính xác, câu trả lời đúng là: [câu trả lời đúng]. Bạn cần phải đọc kỹ hơn.
            \nKết quả Trả về một chuỗi JSON (không chứa các ký tự đặc biệt làm hỏng format JSON):\n
            {
                \"is_correct\": false,
                \"comment\": \"Trả lời bằng tiếng anh theo 5 cách trên\",
                \"correct_answer\": \"câu trả lời đúng\"
            }\n
        ";

        $histories = [];
        
        foreach ($historyChat as $chat) {
            $role = $chat['role'] == 'user' ? Role::USER : Role::MODEL;
            $histories[] = Content::parse(part: $chat['part'], role: $role);
        }

        $numberTry = 3;

        try {
            tryAnswer:
            Log::info('Number try answer: '. $numberTry);
            $historyChatTmp = $historyChat;
            $chat = $this->setUpClient($histories);
            $response = $chat->sendMessage($prompt);

            $text = $response->text();
            $cleaned_json_string = $this->formatToJson($text);
            Log::info('Generated answer: ', [
                'user' => $user->id,
                'text' => $text,
                'cleaned_json_string' => $cleaned_json_string,
            ]);

            $historyChatTmp[] = [
                "part" => $prompt,
                "role" => Role::USER,
                "sort_order" => count($historyChatTmp) + 1,
            ];

            $historyChatTmp[] = [
                "part" => $text,
                "role" => Role::MODEL,
                "sort_order" => count($historyChatTmp) + 1,
            ];

            TopicUser::where('id', $topicUserId)->update([
                'history_chat' => $historyChatTmp,
            ]);

            return json_encode([
                'status' => 200,
                'message' => 'Success',
                'data' => json_decode($cleaned_json_string),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // dd($e);
            $numberTry -= 1;
            if ($numberTry > 0) goto tryAnswer;
            return json_encode([
                'status' => 400,
                'message' => 'An error occurred. Please try again.',
            ]);
        }
    }
}
