<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:admin,user',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role' => 'required|in:admin,user',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    $data = $request->only(['name', 'email', 'role']);

    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
}


    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

public function editPassword(User $user)
{
    // Hanya admin atau user itu sendiri yang bisa edit
    if (auth()->user()->id !== $user->id && auth()->user()->role !== 'admin') {
        abort(403);
    }

    return view('users.password', compact('user'));
}

public function updatePassword(Request $request, User $user)
{
    if (auth()->user()->id !== $user->id && auth()->user()->role !== 'admin') {
        abort(403);
    }

    $request->validate([
        'password' => 'required|min:6|confirmed',
    ]);

    $user->update([
        'password' => Hash::make($request->password),
    ]);
    $request->validate([
        'password' => 'required|string|min:8|confirmed',
    ]);
    

    return redirect()->route('users.index')->with('success', 'Password berhasil diperbarui.');
}
}
