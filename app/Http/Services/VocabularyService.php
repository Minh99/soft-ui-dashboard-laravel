<?php

namespace App\Http\Services;

use App\Models\Vocabulary;

class VocabularyService
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
        $vocabularies = $vocabularies->take(3);
        $vocabularies = $vocabularies->toArray();

        $types = [
            'radio',
            'text-enter',
        ];

        foreach ($vocabularies as $key => $vocabulary) {
            $vocabularies[$key]['type'] = $types[array_rand($types)];
        }
        
        return $vocabularies;
    }
}