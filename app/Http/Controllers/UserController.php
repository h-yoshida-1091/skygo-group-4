<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //ログイン画面表示
    public function showLoginForm()
    {
        return view('Account.login');
    }

    //ログイン処理
    public function login(Request $request)
    {
        // バリデーション
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // メールアドレスでユーザー検索
        $user = User::where('email', $request->email)->first();

        // ユーザーが存在しない、またはパスワードが一致しない場合
        if (!$user || !Hash::check($request->password, $user->password)) {

            return back()->withErrors([
                'email' => 'メールアドレスまたはパスワードが違います。',
            ])->withInput();
        }

        // セッションに保存
        session([
            'userId' => $user->id,
            'userName' => $user->name,
        ]);

        // ダッシュボードへ
        return redirect('/dashboard');
    }

    //ダッシュボード表示
    public function dashboard()
    {
        // ログインしていなければログイン画面へ
        if (!session()->has('userId')) {
            return redirect('/login');
        }

        return view('dashboard');
    }

    // ログアウト
    public function logout()
    {
        session()->flush();

        return redirect('/login');
    }
}
