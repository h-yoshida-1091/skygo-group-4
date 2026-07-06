<?php

namespace App\Http\Controllers;

use App\Models\UserCharacter;
use Illuminate\Http\Request;

class UserCharacterController extends Controller
{
    public function index()
    {
        if (!session()->has('userId')) {
            return redirect('/login');
        }

        $character = UserCharacter::where('user_id', session('userId'))->first();

        return view('character.select');
    }

    public function select(Request $request)
    {
        if (!session()->has('userId')) {
            return redirect('/login');
        }

        $request->validate([
            'character_type' => 'required',
            'nickname' => 'nullable|max:50',
        ]);

        UserCharacter::create([
            'user_id' => session('userId'),
            'character_type' => $request->character_type,
            'nickname' => $request->nickname,
            'level' => 1,
            'exp' => 0,
            'title' => '新人ワーカー',
            'total_work_time' => 0,
            'login_count' => 0,
            'clock_in_voice' => $request->character_type . '_in.mp3',
            'clock_out_voice' => $request->character_type . '_out.mp3',
            'image' => $request->character_type . '.png',
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', '相棒を選択しました');
    }

    public function update(Request $request)
    {
        if (!session()->has('userId')) {
            return redirect('/login');
        }

        $request->validate([
            'nickname' => 'nullable|max:50',
            'title' => 'required|max:50',
        ]);

        $character = UserCharacter::where('user_id', session('userId'))->firstOrFail();

        $character->update([
            'nickname' => $request->nickname,
            'title' => $request->title,
        ]);

        return redirect()
            ->route('character.index')
            ->with('success', '相棒情報を更新しました');
    }
}