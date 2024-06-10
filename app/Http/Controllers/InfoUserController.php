<?php

namespace App\Http\Controllers;

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

    public function report()
    {
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        $users = User::all()->where('is_admin', false)->sortByDesc('created_at');

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
            $dataUserDaysCountVocReport[] = count(json_decode($userDay->vocabulary_ids, true) ?? []) - $countPre;
            $countPre = count(json_decode($userDay->vocabulary_ids, true) ?? []);
        }

        $countVocabulary = Vocabulary::count();

        return view('user-report-detail', [
            'user' => $user,
            'dataUserDaysCountVocReport' => implode(',', $dataUserDaysCountVocReport),
            'countVocabulary' => $countVocabulary,
        ]);
    }
}
