<?php

namespace App\Http\Controllers;

use App\Http\Services\VocabularyService;
use App\Models\Topic;
use App\Models\TopicUser;
use App\Models\UserKnown;
use App\Models\Vocabulary;
use Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role as EnumsRole;
use GeminiAPI\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class TopicController extends Controller
{
    protected $vocabularyService;

    public function __construct(VocabularyService $vocabularyService)
    {
        $this->vocabularyService = $vocabularyService;
    }
    
    function index(){
        $topics = Topic::all();
        $client = new \GuzzleHttp\Client();
        foreach ($topics as $topic) {
            $isSkip = $topic->image && $topic->updated_at && $topic->updated_at->diffInHours(now()) < 12;
            if ($isSkip) {
                continue;
            }
            $response = $client->get('https://pixabay.com/api/', [
                'query' => [
                    'key' => '44038167-920858f2070ae1100a5d17d8d',
                    'q' => $topic->keywords,
                    'image_type' => 'photo',
                    'pretty' => 'false',
                ]
            ]);
            $body = $response->getBody();
            $data = json_decode($body);
            $count = count($data->hits);
            $random = rand(0, $count - 1);
            $topic->image = $data->hits[$random]->webformatURL;
            $topic->save();       
        }

        return view('pages.topics.index', ['topics' => $topics]);
    }

    function getTopicByUser($id, $typeQuiz) {
        $user = auth()->user();
       
        $topicUser = TopicUser::where('user_id', $user->id)->where('id', $id)->first();

        $canView = Session::get('CanSubmit', false);

        if (!$topicUser || !$canView) {
            return redirect()->route('topics')->with('error', 'Topic not found. Please try again.');
        }

        $data = empty($topicUser->data) ? [] : json_decode($topicUser->data, true);
        
        $content = $data['content'] ?? "";
        $words = $data['words'] ?? [];
        $questions = $data['questions'] ?? [];

        return view('pages.topics.detail', ['topicUser' => $topicUser, 'content' => $content, 'words' => $words, 'questions' => $questions, 'typeQuiz' => $typeQuiz]);
    }
}
