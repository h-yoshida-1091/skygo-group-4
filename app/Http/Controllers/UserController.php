<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function edit()
    {
        $user = User::find(session('userId'));

        if (!$user) {
            return redirect('/login');
        }

        return view('Account.edit', compact('user'));
    }

    public function showLoginForm()
    {
        return view('Account.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'メールアドレスまたはパスワードが違います。',
            ])->withInput();
        }

        $request->session()->regenerate();

        session([
            'userId' => $user->id,
            'userName' => $user->name,
            'userRole' => $user->role,
        ]);

        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        return redirect('/dashboard');
    }

    public function update(Request $request)
    {
        $user = User::find(session('userId'));

        if (!$user) {
            return redirect('/login');
        }

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

        $user->save();

        session([
            'userName' => $user->name,
        ]);

        return redirect()->route('account.edit')
            ->with('success', 'アカウントを更新しました');
    }

    public function dashboard()
    {
        if (!session()->has('userId')) {
            return redirect('/login');
        }

        return view('dashboard');
    }

    public function logout(Request $request)
    {
        session()->forget([
            'userId',
            'userName',
            'userRole',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}