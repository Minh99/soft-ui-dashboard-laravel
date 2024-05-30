<?php

namespace App\Http\Services;

use App\Models\UserKnown;
use App\Models\Vocabulary;

class BaseService
{
    public function __construct()
    {
        // 
    }

    public function getVocabulariesToGenStory($user)
    {
        $LIMIT_VOCABULARY = 15;
        $LIMIT_UN_KNOW = 13;
        $LIMIT_KNOW = 2;

        $userKnowsCollection = UserKnown::where('user_id', $user->id)->get();

        $vocabularies = Vocabulary::all(['id', 'en'])->shuffle();

        $userUnKnows = [];
        $userKnows = [];

        foreach ($vocabularies as $vocabulary) {
            $know = $userKnowsCollection->where('vocabulary_id', $vocabulary->id)->first();
            if (!empty($know->is_known)) {
                if (count($userKnows) <= $LIMIT_KNOW) {
                    $userKnows[] = $vocabulary->en;
                }
            } else {
                if (count($userKnows) <= $LIMIT_VOCABULARY) {
                    $userUnKnows[] = $vocabulary->en;
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

        return array_merge($userUnKnows, $userKnows);
    }
}