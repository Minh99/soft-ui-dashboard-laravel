<?php

namespace App\Http\Services;

use App\Models\Vocabulary;

class VocabularyService extends BaseService
{
    /**
     * Constructor
     */
    public function __construct()
    {
        
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
}