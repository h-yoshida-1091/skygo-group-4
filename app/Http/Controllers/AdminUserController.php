<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    // 一覧（検索＋ページネーション）
    public function index(Request $request)
    {
        $query = User::query();

        // 名前検索
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $users = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('admin.users.index', compact('users'));
    }

    // 削除
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $authId = Auth::id(); // ← 明示的に取得（安全）

        // 自分自身削除防止
        if ($authId !== null && $authId === $user->id) {
            return redirect()
                ->back()
                ->with('error', '自分自身は削除できません');
        }

        // 管理者保護（追加推奨）
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

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
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
