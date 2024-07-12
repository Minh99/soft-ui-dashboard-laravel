<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;

class InfoUserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        $users = User::all()->where('is_admin', false)->sortByDesc('created_at');
        return view('user-management', ['users' => $users]);
    }

    public function store(Request $request, $id = null)
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        if ($request->isMethod('get')) {
            if ($id) {
                $user = User::findOrFail($id);
                $user->password = '';
                return view('profile', ['user' => $user]);
            }

            return view('profile');
        }

        

        $data = $request->all();
        
        if ($id) {
            $user = User::findOrFail($id);
            if ($data['password'] == '') {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);
        } else {
            request()->validate([
                'name' => ['required', 'max:50'],
                'email' => [
                    'required',
                    'email',
                    'max:50',
                    Rule::unique('users')
                ],
                'password' => [$id ? '' : 'required'],
            ]);

            $data['password'] = Hash::make($data['password']);

            User::create($data);
        }
        
        return redirect()->route('user-management');
    }

    public function randomColor()
    {
        $color = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        return $color;
    }

    public function report()
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        $users = User::all()->where('is_admin', false)->sortByDesc('created_at');

        $users = $users->map(function ($user) {
            $userDays = $user->dayCompleteds()->orderBy('day_number', 'asc')->get();
            $dataUserDaysCountVocReport = [];
            $countPre = 0;
            foreach ($userDays as $key => $userDay) {
                $uniques = array_unique(json_decode($userDay->vocabulary_ids, true) ?? []);
                $dataUserDaysCountVocReport[] = count($uniques) - $countPre;
                $countPre = count($uniques);
            }

            $user->dataUserDaysCountVocReport = implode(',', $dataUserDaysCountVocReport);
            $user->color = $this->randomColor();

            return $user;
        });

        $users = $users->toArray();

        return view('user-report', ['users' => $users]);
    }

    public function reportDetail($id)
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        $user = User::findOrFail($id);
        $userDays = $user->dayCompleteds()->orderBy('day_number', 'asc')->get();

        $dataUserDaysCountVocReport = [];
        $countPre = 0;
        foreach ($userDays as $key => $userDay) {
            $uniques = array_unique(json_decode($userDay->vocabulary_ids, true) ?? []);
            $dataUserDaysCountVocReport[] = count($uniques) - $countPre;
            $countPre = count($uniques);
        }

        $countVocabulary = Vocabulary::count();

        return view('user-report-detail', [
            'user' => $user,
            'dataUserDaysCountVocReport' => implode(',', $dataUserDaysCountVocReport),
            'countVocabulary' => $countVocabulary,
        ]);
    }


    public function vocabularies()
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        $vocabularies = Vocabulary::all()->sortByDesc('id');
        return view('vocabularies', ['vocabularies' => $vocabularies]);
    }

    public function createVocabulary(Request $request)
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        $data = $request->except('_token');
        $id = $request->input('uid');
        
        if ($id) {
            $vocabulary = Vocabulary::findOrFail($id);
            $vocabulary->update($data);
        } else {
            Vocabulary::create($data);
        }

        return redirect()->route('vocabularies');
    }

    public function topicsManagement()
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        $topics = Topic::all()->sortByDesc('id');
        return view('topics-management', ['topics' => $topics]);
    }

    public function createTopicsManagement(Request $request)
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        $data = $request->except('_token');
        $id = $request->input('uid');
        $data['slug'] = uniqid('topic-') . '-slug';
        
        if ($id) {
            $Topic = Topic::findOrFail($id);
            $Topic->update($data);
        } else {
            Topic::create($data);
        }

        return redirect()->route('topicsManagement');
    }

    public function deleteTopicsManagement(Request $request)
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        $id = $request->input('uid');
        $Topic = Topic::findOrFail($id);
        $Topic->delete();

        return redirect()->route('topicsManagement')->with('success', 'Topic deleted successfully');
    }

    public function delete($id)
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user-management')->with('success', 'User deleted successfully');
    }
}
