<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('Account.edit', compact('user'));
    }

    // ログイン画面表示
    public function showLoginForm()
    {
        return view('Account.login');
    }

    // ログイン処理（Laravel標準Auth）
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 認証処理
        if (Auth::attempt($credentials)) {

            // セッション再生成（重要）
            $request->session()->regenerate();

            $user = Auth::user();

            // 管理者振り分け
            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが違います。',
        ])->withInput();
    }


    public function update(Request $request)
    {
        $user = Auth::user();

        /** @var \App\Models\User $user */

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:6',
        ], [
            'name.required' => '名前は必須です',
            'email.required' => 'メールアドレスは必須です',
            'email.email' => 'メールアドレス形式が正しくありません',
            'password.min' => 'パスワードは6文字以上にしてください',
        ]);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save(); // ← これでOK

        return redirect()->route('account.edit')
            ->with('success', 'アカウントを更新しました');
    }

    // ダッシュボード表示
    public function dashboard()
    {
        return view('dashboard');
    }

    // ログアウト
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
