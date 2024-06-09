<?php

namespace App\Http\Services;

use App\Models\UserKnown;
use App\Models\Vocabulary;

class VocabularyService extends BaseService
{
    protected $baseService;

    /**
     * Constructor
     */
    public function __construct(BaseService $baseService)
    {
        $this->baseService = $baseService;
    }

    /**
     * Get vocabularies
     * 
     * @return array
     */
    public function getVocabularies(): array
    {
        $products = Vocabulary::all();
        return $products->toArray();
    }

    /**
     * Get vocabularies count
     * 
     * @return int
     */
    public function getVocabulariesCount(): int
    {
        return Vocabulary::count();
    }

    public function getQuizTypeRandom()
    {
        $vocabularies = $this->getVocabularies();
        $vocabularies = collect($vocabularies);

        $vocabularies = $vocabularies->shuffle();
        $vocabularies = $vocabularies->toArray();

        $types = [
            'radio',
            // 'text-enter',
        ];

        foreach ($vocabularies as $key => $vocabulary) {
            $vocabularies[$key]['type'] = $types[array_rand($types)];
        }
        
        return $vocabularies;
    }

    public function getQuizFor($topicUser)
    {
        $data = $topicUser->data ? json_decode($topicUser->data, true) : [];
        $words = $data['words'] ?? [];
        $words = collect($words);
        $words = $words->pluck('word');

        $vocabularies = Vocabulary::whereIn('en', $words)->get();

        $vocabularies = $vocabularies->shuffle();
        $vocabularies = $vocabularies->toArray();

        $types = [
            'radio',
            // 'text-enter',
        ];

        foreach ($vocabularies as $key => $vocabulary) {
            $vocabularies[$key]['type'] = $types[array_rand($types)];
        }
        
        return $vocabularies;
    }

    public function getQuiz($user, $dayNumber = 4)
    {
        $LIMIT_VOCABULARY = Vocabulary::count();
        $LIMIT_UN_KNOW = $LIMIT_VOCABULARY;
        $LIMIT_KNOW = 2;

        $userKnowsCollection = UserKnown::where('user_id', $user->id)->get();

        $vocabularies = Vocabulary::all(['id', 'en'])->shuffle();

        $userUnKnows = [];
        $userKnows = [];

        foreach ($vocabularies as $vocabulary) {
            $know = $userKnowsCollection->where('vocabulary_id', $vocabulary->id)->first();
            if (!empty($know->is_known)) {
                if (count($userKnows) <= $LIMIT_KNOW) {
                    $userKnows[] = [
                        'id' => $vocabulary->id,
                        'en' => $vocabulary->en,
                        'is_known' => true,
                    ];
                }
            } else {
                if (count($userKnows) <= $LIMIT_VOCABULARY) {
                    $userUnKnows[] = [
                        'id' => $vocabulary->id,
                        'en' => $vocabulary->en,
                        'is_known' => false,
                    ];
                }
            }

            if (count($userUnKnows) >= $LIMIT_UN_KNOW && count($userKnows) >= $LIMIT_KNOW) {
                break;
            }

            if (count($userUnKnows) >= $LIMIT_VOCABULARY && count($userKnows) >= $LIMIT_KNOW) {
                break;
            }
        }

        if (count($userUnKnows) >= $LIMIT_VOCABULARY && count($userKnows) <= $LIMIT_KNOW) {
            $userUnKnows = array_slice($userUnKnows, 0, $LIMIT_VOCABULARY - count($userKnows));
        }

        $data = array_merge($userUnKnows, $userKnows);

        if (count($data) < $LIMIT_VOCABULARY) {
            $vocabularies = Vocabulary::all(['id', 'en'])->shuffle()->take($LIMIT_VOCABULARY - count($data));
            foreach ($vocabularies as $vocabulary) {
                $data[] = [
                    'id' => $vocabulary->id,
                    'en' => $vocabulary->en,
                    'is_known' => false,
                ];
            }
        }

        $types = [
            'radio',
            // 'text-enter',
        ];

        foreach ($data as $key => $vocabulary) {
            $data[$key]['type'] = $types[array_rand($types)];
        }
        
        return $data;
    }

    public function getVocabulariesIdByEn($ens = [])
    {
        $vocabulary = Vocabulary::whereIn('en', $ens)->get();
        return $vocabulary->pluck('id')->toArray();
    }
}