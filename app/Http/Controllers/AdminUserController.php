<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('userId')) {
            return redirect('/login');
        }

        if (session('userRole') !== 'admin') {
            return redirect('/dashboard');
        }

        $query = User::query();

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $users = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('admin.users.index', compact('users'));
    }

    public function destroy(int $id)
    {
        if (!session()->has('userId')) {
            return redirect('/login');
        }

        if (session('userRole') !== 'admin') {
            return redirect('/dashboard');
        }

        $user = User::findOrFail($id);

        $loginUserId = session('userId');

        if ((int)$loginUserId === (int)$user->id) {
            return redirect()
                ->back()
                ->with('error', '自分自身は削除できません');
        }

        if ($user->role === 'admin') {
            return redirect()
                ->back()
                ->with('error', '管理者ユーザーは削除できません');
        }

        $user->delete();

        return redirect()
            ->back()
            ->with('success', 'ユーザーを削除しました');
    }

    public function edit(int $id)
    {
        if (!session()->has('userId')) {
            return redirect('/login');
        }

        if (session('userRole') !== 'admin') {
            return redirect('/dashboard');
        }

        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, int $id)
    {
        if (!session()->has('userId')) {
            return redirect('/login');
        }

        if (session('userRole') !== 'admin') {
            return redirect('/dashboard');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'ユーザーを更新しました');
    }
}