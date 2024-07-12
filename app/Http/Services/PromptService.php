<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserDayCompleted;
use App\Models\UserKnown;
use App\Models\Vocabulary;

class PromptService extends BaseService
{
    protected $baseService;
    const LIMIT_WORDS_GEN = 150;
    const TYPE_PROMPT_1 = 1; // 1 : tự luận, 2 : trăc nghiệm
    const TYPE_PROMPT_2 = 2;

    /**
     * Constructor
     */
    public function __construct(BaseService $baseService)
    {
        $this->baseService = $baseService;
    }

    


    public function promptGenStory($user, $topicName = null)
    {
        $userDayCompleted = UserDayCompleted::where('is_completed', false)
            ->where('user_id', $user->id)
            ->first();

        $dayNumber = $userDayCompleted ? $userDayCompleted->day_number : 1;
        if ($userDayCompleted) {
            switch (true):
                case $dayNumber == 1 && !empty($userDayCompleted->words_to_gen_story_1):
                    $vocabularies = $userDayCompleted->words_to_gen_story_1;
                    break;
                case $dayNumber == 2:
                    if (!empty($userDayCompleted->words_to_gen_story_1) 
                        && !empty($userDayCompleted->words_to_gen_story_2)
                        // && !empty($userDayCompleted->words_to_gen_story_3)
                        // && !empty($userDayCompleted->words_to_gen_story_4)
                    )
                    {
                        // random 1 trong 2
                        $random = rand(1, 2);
                        $vocabularies = $userDayCompleted->{"words_to_gen_story_$random"};
                    } else if (!empty($userDayCompleted->words_to_gen_story_1) && !$userDayCompleted->is_passed_quiz_story_1) {
                        $vocabularies = $userDayCompleted->words_to_gen_story_1;
                    } else if (!empty($userDayCompleted->words_to_gen_story_2) && $userDayCompleted->is_passed_quiz_story_1 && !$userDayCompleted->is_passed_quiz_story_2) {
                        $vocabularies = $userDayCompleted->words_to_gen_story_2;
                    // } else if (!empty($userDayCompleted->words_to_gen_story_3) && $userDayCompleted->is_passed_quiz_story_1 && $userDayCompleted->is_passed_quiz_story_2 && !$userDayCompleted->is_passed_quiz_story_3) {
                    //     $vocabularies = $userDayCompleted->words_to_gen_story_3;
                    // } else if (!empty($userDayCompleted->words_to_gen_story_4) && $userDayCompleted->is_passed_quiz_story_1 && $userDayCompleted->is_passed_quiz_story_2 && $userDayCompleted->is_passed_quiz_story_3 && !$userDayCompleted->is_passed_quiz_story_4) {
                    //     $vocabularies = $userDayCompleted->words_to_gen_story_4;
                    } else {
                        goto autoGenVocabulary;
                    }
                    break;
                default:
                    goto autoGenVocabulary;
            endswitch;
        } else {
            autoGenVocabulary:
            $vocabularies = $this->baseService->getVocabulariesToGenStory($user, $dayNumber);
            $vocabularies = implode(',', $vocabularies);
            if ($userDayCompleted) {
                switch (true):
                    case $dayNumber == 1:
                        $userDayCompleted->words_to_gen_story_1 = $vocabularies;
                        break;
                    case $dayNumber == 2:
                        if (empty($userDayCompleted->words_to_gen_story_1) && !$userDayCompleted->is_passed_quiz_story_1) {
                            $userDayCompleted->words_to_gen_story_1 = $vocabularies;
                        } elseif (empty($userDayCompleted->words_to_gen_story_2) && $userDayCompleted->is_passed_quiz_story_1 && !$userDayCompleted->is_passed_quiz_story_2) {
                            $userDayCompleted->words_to_gen_story_2 = $vocabularies;
                        // } elseif (empty($userDayCompleted->words_to_gen_story_3) && $userDayCompleted->is_passed_quiz_story_1 && $userDayCompleted->is_passed_quiz_story_2 && !$userDayCompleted->is_passed_quiz_story_3) {
                        //     $userDayCompleted->words_to_gen_story_3 = $vocabularies;
                        // } elseif (empty($userDayCompleted->words_to_gen_story_4) && $userDayCompleted->is_passed_quiz_story_1 && $userDayCompleted->is_passed_quiz_story_2 && $userDayCompleted->is_passed_quiz_story_3 && !$userDayCompleted->is_passed_quiz_story_4) {
                        //     $userDayCompleted->words_to_gen_story_4 = $vocabularies;
                        } else {
                            $userDayCompleted->words_to_gen_story_1 = $vocabularies;
                        }
                        break;
                    default:
                        break;
                endswitch;

                $userDayCompleted->save();
            }
        }

        $LIMIT_WORDS_GEN = self::LIMIT_WORDS_GEN;
        $random = rand(self::TYPE_PROMPT_1, self::TYPE_PROMPT_2);
        $countVoc = count(explode(',', $vocabularies));
        // tính toán phần trăm của countVoc => lấy ra con số của 150% của countVoc
        $countVoc = $countVoc;

        if ($random === self::TYPE_PROMPT_1) {
            $prompt = "Tôi đang đi dạy học sinh các từ vựng tiếng anh sau: [$vocabularies]
                \nHãy tạo một cuộc hội thoại tiếng anh siêu đơn giản giữa 2 hay nhiều người nói về 1 chủ đề [$topicName],
                nội dung chủ đề chỉ xoay quanh các từ vựng tiếng anh đã cho như sau [$vocabularies]
                và chỉ khoảng 10 đến 12 câu, nội dung trả về là dạng html và bôi đậm các từ vựng đã cho để học sinh dễ nhìn và nhận biết,
                fomat mong muốn như sau:\n- content : <p><strong>Shara</strong>: Hello, how do you <strong>feel</strong></p><p><strong>Tom</strong: Im fine thank</p>\n.
                \nKết quả trả dưới dạng JSON (không chứa các ký tự đặc biệt làm hỏng format JSON):
                \nexplanation sẽ chỉ bao gồmm 2 item là 2 ngôn ngữ là tiếng anh và tiếng hàn
                \nOutput:\n{ \"content\": \"Content story\", \"words\": [{\"word\":\"love\", \"explanation\": {\"en\": \"love is love\", \"ko\": \"사랑은 사랑입니다\"}}] }
            ";
        } else {
            $prompt = "Tôi đang đi dạy học sinh các từ vựng tiếng anh sau: [$vocabularies]\n
            \nHãy tạo một cuộc hội thoại tiếng anh siêu đơn giản giữa 2 hay nhiều người nói về 1 chủ đề [$topicName],
            nội dung chủ đề chỉ xoay quanh các từ vựng tiếng anh đã cho như sau [$vocabularies] 
            và chỉ khoảng 10 đến 12 câu, nội dung trả về là dạng html và bôi đậm các từ vựng đã cho để học sinh dễ nhìn và nhận biết,
            fomat mong muốn như sau:\n- content : <p><strong>Shara</strong>: Hello, how do you <strong>feel</strong></p><p><strong>Tom</strong: Im fine thank</p>\n.
            \nKèm theo $countVoc câu hỏi trắc nghiệm siêu dễ xoay quanh nội dung cuộc hội thoại, làm sao để học sinh cấp 3 có thể dễ dàng trả lời,
            \nOutput:mỗi câu hỏi có 4 đáp án, mỗi đáp án là 1 object, các đáp án là các từ vựng để học sinh chọn và chỉ có 1 đáp án đúng, và quy định đáp án là A, B, C hoặc D.\n
            \nKết quả trả dưới dạng JSON (không chứa các ký tự đặc biệt làm hỏng format JSON):\nOutput:\n{ \"content\": \"Content story là\", \"words\": [{\"word\":\"love\", \"explanation\": {\"en\": \"love is love\", \"ko\": \"사랑은 사랑입니다\"}}], \"questions\": [{\"question\": \"how to xxx\", \"answers\": [ {\"A\" : \"xxx\"}, {\"B\" : \"xxx\"}, {\"C\" : \"xxx\"}, {\"D\" : \"xxx\"}], \"answer_correct\" : \"A\"}] }
            \nGiải thích:\n- content: là dạng html\n- explanation: sẽ chỉ bao gôm 2 item là 2 ngôn ngữ là tiếng anh và tiếng hàn, \n- answers: sẽ là 4 đáp án và mỗi đáp án sẽ là 1 object, \n-answer_correct là đáp án đúng\n- question: là câu hỏi trắc nghiệm và đúng y nguyên format mẫu";
        }

        return [$vocabularies, $prompt, $random];
    }

    public function getEnsToGenTest2($take = 5)
    {
        $user = auth()->user();
        $userDays = $user->dayCompleteds()->orderBy('day_number', 'asc')->get();

        $vocabularies = [];
        $idsBefore = [];
        $allId = [];
        // day 1: 1, 2, 3, 4, 5 => day 1: 1, 2, 3, 4, 5
        // day 2: 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 => so sánh với day 1, lấy ra các id mới => day 2: 6, 7, 8, 9, 10
        // day 3: 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15 => so sánh với day 2, lấy ra các id mới => day 3: 11, 12, 13, 14, 15
        foreach ($userDays as $dayCompleted) {
            $dayNumber = $dayCompleted->day_number;
            $vocabularies[$dayNumber] = array_values(array_diff(json_decode($dayCompleted->vocabulary_ids, true) ?? [], $idsBefore));
            $idsBefore = array_merge($idsBefore, json_decode($dayCompleted->vocabulary_ids, true) ?? []);
            $allId = array_merge($allId, json_decode($dayCompleted->vocabulary_ids, true) ?? []);
        }
        $allId = array_values(array_unique($allId));

        $userDayNotCompleted = UserDayCompleted::where('is_completed', false)
            ->where('user_id', $user->id)
            ->first();

        if ($userDayNotCompleted->day_number === 3) {
            // $take = 7;
            $vocabularies1 = UserKnown::where('user_id', $user->id)->where('is_known', false)->pluck('vocabulary_id');
            $vocabularies2 = UserKnown::where('user_id', $user->id)->where('is_known', true)->pluck('vocabulary_id');
            $data = count($vocabularies1) >= $take ? $vocabularies1->take($take) : ($vocabularies1->merge($vocabularies2)->take($take));
            $data = $data->toArray();
        } else {
            $data = count($vocabularies[$userDayNotCompleted->day_number]) ? $vocabularies[$userDayNotCompleted->day_number] : $allId;
        }

        $vocabularies = Vocabulary::whereIn('id', $data)->pluck('en');
        $vocabularies = $vocabularies ? $vocabularies->shuffle() : [];
        $vocabularies = count($vocabularies) >= $take ? $vocabularies->take($take) : $vocabularies;

        return is_array($vocabularies) ? $vocabularies : $vocabularies->toArray();
    }

    public function promptGenTest2ByType1()
    {
        $take = 3;
        $ens = $this->getEnsToGenTest2();
        $ensImplode = implode(', ', $ens);
        $prompt = "Tôi đang học các từ vựng [$ensImplode], Giúp tôi tạo $take câu đơn khác nhau,  mỗi câu đơn có bao gồm các dấu chấm blank để điền từ vựng đúng, và tôi sẽ điền các từ vựng sau chỗ trống\nUser query:\n- Các từ vựng: [$ensImplode]\n- Mỗi câu chỉ một chỗ trống\n- Mỗi câu chỉ bao gồm 1-2 options\n- Trả về format JSON\n\noutput:\n[\n    {\n    \"id\": \"1\",\n \"sentence\": \"My ______ is a furry friend who loves to play fetch.\",\n      \"options\": [\"cat\", \"dog\", \"love\"]\n    },\n    {\n    \"id\": \"2\",\n  \"sentence\": \"I ______ spending time with my family and friends.\",\n      \"options\": [\"cat\", \"dog\", \"love\"]\n    },\n    {\n   \"id\": \"3\",\n   \"sentence\": \"The ______ purred contentedly on the couch.\",\n      \"options\": [\"cat\", \"dog\", \"love\"]\n    }\n  ]";
        
        return $prompt;
    }

    public function promptSubmitTest2ByType1($data)
    {
        $input = json_encode($data, JSON_PRETTY_PRINT);
        $prompt = "Hãy nhận xét câu trả lời sau khi điền vào chỗ trống. Lời nhận xét bao gồm giải thích, đúng hoặc sai, và đưa ra câu trả lời chính xác nếu câu trả lời là sai\n\nUser query:\n- input:\n $input \n- Câu giải thích của bạn phải bằng tiếng hàn\n- Output sample:\n {\n    \"id\": 1,\n    \"sentence\": \"My ______ is a furry friend who loves to play fetch.\",\n    \"user_answer\": \"love\",\n    \"comment\": \"정답입니다!  개는 털이 많은 친구이고 공놀이를 좋아합니다.\",\n    \"correct\": true,\n    \"correct_answer\" : \"dog\"\n  }";

        return $prompt;
    }

    public function promptGenTest2ByType2()
    {
        $take = 3;
        $ens = $this->getEnsToGenTest2($take);
        $ens1 = array_slice($ens, 0, $take / 2);
        $ens2 = array_slice($ens, $take / 2);
        $ens1Implode = implode(', ', $ens1);
        $ens2Implode = implode(', ', $ens2);
        $prompt = "Tôi đang dạy cho học sinh các từ: [$ens1Implode] và [$ens2Implode].\nTạo giúp tôi 2 đoạn văn mẫu để điền từ vựng thích hợp vào chỗ trống. mỗi mẫu lấy 1 danh sách từ vựng tương ứng đã cho và sẽ có các vị trí dấu ____ (để học sinh điền) và đảm bảo mỗi đoạn văn luôn luôn có 4 chỗ trống. Hãy làm sao cho đoạn văn lấy được có nghĩa nhất có thể và 2 đoạn văn sẽ cần điền đầy đủ các từ vựng đã đưa ra.\nNote:\n- Lưu ý là Graph là nội dung mà bạn tạo ra\n- Nếu có nhiều hơn từ xuất hiện, luôn luôn tạo ra 4 khoảng trống ____ .\n- dữ liệu trả về dưới dạng json.\n- options là lựa chọn để học sinh sẽ dựa vào.\n- correct_answer là kết quả lần lượt từng khoảng trống.\noutput:\n[{\n\"graph: \"xxx\",\n\"options: [\"x\",\"x\",\"x\",\"x\"]\n\"correct_answer:  [\"x\",\"x\",\"x\",\"x\"]\n}]";
        
        return $prompt;
    }


    public function promptGenTest2ByType3()
    {
        $take = 3;
        $ens = $this->getEnsToGenTest2($take);
        $ensImplode = implode(', ', $ens);

        $prompt = "Tôi đang dạy cho học sinh người Hàn của tôi các từ vựng tiếng anh sau: [$ensImplode]\nHãy giúp rôi tạo ra cho học sinh của mình $take câu bằng tiếng Hàn, mỗi câu tiếng Hàn liên quan chính xác tới từ vựng tiếng Anh sử dụng để làm mẫu,học sinh sẽ dịch nó ra thành tiếng anh (câu tiếng anh phải chứa các từ tôi đã cho, và buộc phải sát nghĩa với từ vựng đã dùng), mỗi câu chứa 1 - 2 từ tiếng anh.\n- Example: \"이것은 가져가실 건가요, 아니면 배달인가요? ( take-out, delivery)\"\n- Output:- trả về data dưới dạng json (không chứa các ký tự đặc biệt làm hỏng format JSON):\n[{\"id\":1,\"sentences\":\"xxx\",\"suggest_words\":[\"x\",\"x\"]},{\"id\":2,\"sentences\":\"xxx\",\"suggest_words\":[\"x\",\"x\"]},{\"id\":3,\"sentences\":\"xxx\",\"suggest_words\":[\"x\",\"x\"]},{\"id\":4,\"sentences\":\"xxx\",\"suggest_words\":[\"x\",\"x\"]},{\"id\":5,\"sentences\":\"xxx\",\"suggest_words\":[\"x\",\"x\"]}]\n- lưu ý: sentences là 100% tiếng hàn\n- các câu không sử dụng lại các từ đã có";
        
        return $prompt;
    }

    public function promptSubmitTest2ByType3($data)
    {
        $input = json_encode($data, JSON_PRETTY_PRINT);
        $prompt = "Tôi cho học sinh các câu tiếng Hàn và các từ gợi ý, học sinh của tôi phải chuyển các câu tiếng hàn sang tiếng anh (có bao gồm từ gợi ý đã cho).\nĐây là kết quả học sinh đã làm:\n$input\n- Hãy giúp tôi kiểm tra, đưa ra nhận xét và các câu gợi ý khác dựa theo ouput:\n- Output: trả về data dưới dạng json (không chứa các ký tự đặc biệt làm hỏng format JSON):\nExample:\n[\n  {\n    \"id\" : 1,\n    \"sentences\": \"오늘 수업 출석률이 좋네요.\",\n    \"suggest_words\": [\"attendance\"],\n   \"student_answer\": \"The attendance rate in class today is good.\",\n   \"result\": true or false,\n   \"example\": [\"xxx\", \"xxx\"],\n   \"explain\" : \"abcdef\"\n, \"how_to_fail\" : \"use words 'yyy' not correct...\"  },\n  {\n    \"id\" : 2,\n    \"sentences\": \"숙제는 완벽하게 해 왔어요?\",\n    \"suggest_words\": [\"assignment\", \"perfect\"],\n    \"student_answer\": \"Did you do your homework perfectly?\",\n   \"result\": true or false,\n   \"example\": [\"xxx\", \"xxx\"],\n   \"explain\" : \"abcdef\"\n ,\"how_to_fail\" : \"use words 'yyy' not correct...\" }\n]\n\n- Lưu ý: câu trả lời của học sinh phải bao gồm toàn bộ từ trong suggest_words và câu trả lời phải có nghĩa, đúng chính tả và cấu trúc ngữ pháp (từ vựng có thể tuỳ biến theo cấu trúc) thì mới được xem là đúng, ngược lại thì là sai\n- Nếu học sinh sử dụng các từ khác tương tự hay đồng nghĩa thì cũng là sai. PHẢI  SỬ DỤNG TOÀN BỘ TỪ TRONG suggest_words, đặc biệt nếu cả câu chỉ dùng lại từ suggest_words cũng không chấp nhận, buộc phải đặt một câu hoàn chỉnh.\n- Kể cả câu trả lời đúng cũng sẽ trả về 1-2 example để học sinh xem vào tham khảo.\n- Explain là 1 đoạn giải thích bằng tiếng Hàn cho câu sentences, hay giải thích làm sao cho học sinh dễ hiểu và có thể sử dụng chính xác từ vựng, ví dụ: nên dùng cấu trúc thì nào thì hợp lý, và đặt câu ra làm sao cho đúng ngữ pháp.\n- how_to_fail là để nhận xét câu trả lời của học sinh, tại sao sai, sai ngữ pháp hay sai chính tả hay sai bất cứ cái gì và nên sửa như thế nào, dùng danh xưng bạn và tôi và dùng tiếng hàn.\nVí dụ:\n{\n    \"sentences\": \"숙제는 완벽하게 해 왔어요?\",\n    \"suggest_words\": [\"assignment\", \"perfect\"],\n    \"student_answer\": \"Did you finish your perfectly?\",\n    \"result\": false, // do câu trả lời không dùng từ assignment\n    \"example\": [\n      \"Did you finish your assignment perfectly?\",\n      \"Is your assignment perfect?\"\n    ],\n    \"explain\": \"The sentence correctly uses both 'assignment' and 'perfect' to describe the quality of the student's work. The sentence also uses the correct grammar and structure. Another way to phrase the sentence is: 'Have you finished your assignment perfectly?'\"\n, \"how_to_fail\" : \"use words 'yyy' not correct...\" }  }";

        return $prompt;
    }

    public function promptGenTest2ByType4()
    {
        $take = 3;
        $ens = $this->getEnsToGenTest2($take);
        $ensImplode = implode(', ', $ens);
        $count = count($ens);
        $prompt = " Tôi đang dạy học sinh các từ vựng sau: [$ensImplode]\nGiúp tôi tạo ra $count câu hỏi bằng tiếng Anh để học sinh tập đặt câu với từ vựng đã cho, nhớ bao dấu nháy * cho từ vựng để dễ nhận thấy\nví dụ: please help me make a sentence with *Love*\n- Kết quả trả về là 1 chuỗi chỉ bao gồm JSON, và nhớ không chứa dấu \" trong chuỗi tránh làm hỏng format\n- Output sample:\n[\n {\n  \"id\" : 1,\n  \"question\": \"please help me make a sentence with `Love`\",\n }\n]";
        
        return $prompt;
    }
    
    
    public function promptSubmitTest2ByType4($data)
    {
        $input = json_encode($data, JSON_PRETTY_PRINT);
        $prompt = "Giúp tôi nhận xét các câu trả lời của học sinh, và đưa ra nhận xét không cần quá khắn khe miễn sao câu trả lời đúng ngữ pháp và chứa từ vựng\nInput:\n $input \n\n- Kết quả trả về chỉ bao gốm chuỗi JSOn, và nhớ không chứa dấu \" trong chuỗi tránh làm hỏng format\nOuput sample:\n[\n  {\n    \"id\": 1,\n    \"question\": \"Can you make a sentence using the word `Love`?\"\n    \"user_answer\": \"I love you\",\n    \"correct\" true or false,\n    \"comment\": \"comment something\"\n  },\n  {\n    \"id\": 2,\n    \"question\": \"Please write a sentence about your `Friend`.\"\n   \"user_answer\": \"I love you\",\n    \"correct\" true or false,\n    \"comment\": \"comment something\"\n  }\n]";

        return $prompt;
    }
}