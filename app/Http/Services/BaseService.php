<?php

namespace App\Http\Services;

use App\Models\UserKnown;
use App\Models\Vocabulary;
use Illuminate\Support\Facades\Log;

class BaseService
{
    public function __construct()
    {
        // 
    }

    public function getVocabulariesToGenStory($user, $dayNumber = 1)
    {
        $LIMIT_VOCABULARY = 7;
        $LIMIT_UN_KNOW = 6;
        $LIMIT_KNOW = 1;

        Log::info('Day number: ' . $dayNumber);
        if ($dayNumber === 2 || $dayNumber === 3 || $dayNumber === 5) {
            $LIMIT_VOCABULARY = 5;
            $LIMIT_UN_KNOW = 4;
        }

        Log::info('Limit vocabulary: ' . $LIMIT_VOCABULARY);

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

        Log::info('User knows: ' . json_encode($userKnows));
        Log::info('User un knows: ' . json_encode($userUnKnows));
        if (count($userUnKnows) >= $LIMIT_VOCABULARY && count($userKnows) <= $LIMIT_KNOW) {
            $userUnKnows = array_slice($userUnKnows, 0, $LIMIT_VOCABULARY - count($userKnows));
        }

        $data = array_merge($userUnKnows, $userKnows);

        Log::info('Data: ' . json_encode($data));
        if (count($data) < $LIMIT_VOCABULARY) {
            $vocabularies = Vocabulary::all(['id', 'en'])->shuffle()->take($LIMIT_VOCABULARY - count($data));
            foreach ($vocabularies as $vocabulary) {
                $data[] = $vocabulary->en;
            }
        }

        $data = collect($data)->shuffle()->toArray();
        $data = array_slice($data, 0, $LIMIT_VOCABULARY);

        return $data;
    }
}