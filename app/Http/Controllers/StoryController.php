<?php

namespace App\Http\Controllers;

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

class StoryController extends Controller
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

    /**
     * Generate story
     *
     * @param Request $request
     * @param $topicId
     * @return json
     */
    public function generate(Request $request, $topicId)
    {
        $topic = Topic::find($topicId);
        $topicName = $topic->name;
        $topicId = $topic->id;
        $YOUR_API_KEY= env('GEMINI_API_KEY');
        $client = Gemini::client($YOUR_API_KEY);
        $user = auth()->user();

        if (!$user) {
            return json_encode([
                'status' => 404,
                'message' => 'User not found!',
            ]);
        }

        $LIMIT_WORDS_GEN = 300;
        
        $vocabulariesToGenStory = $this->vocabularyService->getVocabulariesToGenStory($user);
        Log::info('vocabulariesToGenStory', $vocabulariesToGenStory);

        $vocabulariesToGenStory = implode(', ', $vocabulariesToGenStory);

        $prompt = "Create a story about the topic [$topicName], using the following words: [$vocabulariesToGenStory]";
        $formattedPrompt = "\n1. Content of the story. The story should be $LIMIT_WORDS_GEN words long.
        \n 2. List of words used in the story [word , explanation], the explanation must be in the story.";
        
        $jsonExample = "
            {
                content: Content of the story. The story should be $LIMIT_WORDS_GEN words long,
                words: [
                    {
                        word: value 2,
                        explanation: value 3
                    },
                    {
                        word: value 4,
                        explanation: value 5
                    }
                ]
            }
        ";

        $prompt .= $formattedPrompt;
        $prompt .= "\n => Based on the content above, Please provide a response in a structured JSON format that matches the following model: " . $jsonExample;

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

        try {
            $chat = $client
                ->geminiPro()
                ->startChat(history: [
                    Content::parse(part: $basePrompt[0]['part'], role: Role::USER),
                    Content::parse(part: $basePrompt[1]['part'], role: Role::MODEL),
                ]);

            $response = $chat->sendMessage($prompt);

            $text = $response->text();
            $text = str_replace(["\`\`\`", "json", "```"], '', $text);
            $text = preg_replace('/\n+/', '', $text);
            Log::info($text);

            $basePrompt[] = [
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
                    'data' => $text,
                    'history_chat' => $basePrompt,
                ]
            );

            $genId = $gen->id;

            return json_encode([
                'status' => 200,
                'message' => 'Success',
                'genId' => $genId,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // dd($e);
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

        $YOUR_API_KEY= env('GEMINI_API_KEY');
        $client = Gemini::client($YOUR_API_KEY);

        $prompt = "Create a question based on the story you wrote and the words you used. The question should be related to the words and their explanations. The question must have an answer, and the answer must be one of the words used in the story.";
        
        $jsonExample = "
            {
                question: Question,
                answer: Answer
            }
        ";

        $prompt .= "\n => Please provide a response in a structured JSON format that matches the following model: " . $jsonExample;

        $histories = [];

        foreach ($historyChat as $chat) {
            $role = $chat['role'] == 'user' ? Role::USER : Role::MODEL;
            $histories[] = Content::parse(part: $chat['part'], role: $role);
        }

        try {
            $chat = $client
                ->geminiPro()
                ->startChat(history: $histories);

            $response = $chat->sendMessage($prompt);

            $text = $response->text();
            $text = str_replace(["\`\`\`", "json", "```"], '', $text);
            $text = preg_replace('/\n+/', '', $text);
            Log::info($text);

            $historyChat[] = [
                "part" => $prompt,
                "role" => Role::USER,
                "sort_order" => count($historyChat) + 1,
            ];

            $historyChat[] = [
                "part" => $text,
                "role" => Role::MODEL,
                "sort_order" => count($historyChat) + 1,
            ];

            TopicUser::where('id', $topicUserId)->update([
                'history_chat' => $historyChat,
            ]);

            return json_encode([
                'status' => 200,
                'message' => 'Success',
                'data' => json_decode($text),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // dd($e);
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

        $YOUR_API_KEY= env('GEMINI_API_KEY');
        $client = Gemini::client($YOUR_API_KEY);
        $answer = $request->query('answer') ?? '';

        // nếu câu trả lời sai vui lòng nhận xét và cung cấp câu trả lời đúng theo 1 trong format sau:
        // - Câu trả lời đúng là: [câu trả lời đúng]
        // - Câu trả lời chưa chính xác, câu trả lời đúng là: [câu trả lời đúng]. Động viên bạn làm tốt hơn lần sau.
        // - Câu trả lời chưa chính xác, câu trả lời đúng là: [câu trả lời đúng]. [lý do câu trả lời đúng]
        // - Sai mất rồi, câu trả lời đúng phải là: [câu trả lời đúng]. Động viên bạn làm tốt hơn lần sau.
        // - Câu trả lời chưa chính xác, câu trả lời đúng là: [câu trả lời đúng]. Bạn cần phải học kỹ hơn.
        // nếu câu trả lời đúng, chúc mừng bạn đã trả lời đúng theo nhiều kiểu khác nhau.

        $prompt = "Is this answer [$answer] correct?" . 
            "If the answer is correct, congratulations on answering correctly in many different ways." .
            " \nIf the answer is incorrect, please provide feedback and provide the correct answer in one of the following formats:" .
            " \n- The correct answer is: [correct answer]" .
            " \n- The answer is incorrect. Encourage you to do better next time." .
            " \n- The answer is incorrect. reason for the correct answer" .
            " \n- Wrong, the correct answer must be: [correct answer]. Encourage you to do better next time." .
            " \n- The answer is incorrect. You need to study more.";
        $jsonExample = '
            {
                "is_correct": true | false,
                "comment": "based format above",
                "correct_answer": "the correct answer is: [correct answer]"
            }
        ';

        $prompt .= "\n => Please provide a response in a structured JSON format that matches the following model: " . $jsonExample;

        $histories = [];
        
        foreach ($historyChat as $chat) {
            $role = $chat['role'] == 'user' ? Role::USER : Role::MODEL;
            $histories[] = Content::parse(part: $chat['part'], role: $role);
        }

        try {
            $chat = $client
                ->geminiPro()
                ->startChat(history: $histories);

            $response = $chat->sendMessage($prompt);

            $text = $response->text();
            Log::info($text);

            $historyChat[] = [
                "part" => $prompt,
                "role" => Role::USER,
                "sort_order" => count($historyChat) + 1,
            ];

            $historyChat[] = [
                "part" => $text,
                "role" => Role::MODEL,
                "sort_order" => count($historyChat) + 1,
            ];

            TopicUser::where('id', $topicUserId)->update([
                'history_chat' => $historyChat,
            ]);

            return json_encode([
                'status' => 200,
                'message' => 'Success',
                'data' => json_decode($text),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // dd($e);
            return json_encode([
                'status' => 400,
                'message' => 'An error occurred. Please try again.',
            ]);
        }
    }
}
