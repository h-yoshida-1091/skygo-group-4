<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // 名前検索
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        // 権限で絞り込み
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 並び替え
        $sort = $request->get('sort', 'desc');

        if ($sort === 'asc') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query
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

        $user->forceDelete();

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
            'role' => 'required',
        ]);

        $user->update([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'ユーザーを更新しました');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
            ],
            'password' => 'required|min:6',
            'role' => 'required',
            'department' => 'required',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'ユーザーを追加しました');
    }
}
