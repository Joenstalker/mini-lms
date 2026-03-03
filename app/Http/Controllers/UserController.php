<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderByRaw('id = ? DESC', [Auth::id()])
            ->latest()
            ->get();

        if ($request->ajax()) {
            return view('users.partials.table', compact('users'));
        }

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'message' => "Admin account for {$user->name} created successfully.",
                    'user'    => $user,
                ]);
            }

            return redirect()->route('users.index')->with('success', "Admin account for {$user->name} created.");
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Failed to create admin account.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to create admin account: ' . $e->getMessage());
        }
    }

    public function show(User $user, Request $request)
    {
        if ($request->ajax()) {
            return view('users.show_content', compact('user'));
        }
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            if ($request->ajax()) {
                return response()->json([
                    'message' => "Admin account for {$user->name} updated successfully.",
                    'user'    => $user,
                ]);
            }

            return redirect()->route('users.index')->with('success', "Admin account for {$user->name} updated.");
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Failed to update admin account.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to update admin account: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            if ($request->ajax()) {
                return response()->json(['message' => 'You cannot delete your own account.'], 403);
            }
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        try {
            $name = $user->name;
            $user->delete();

            if ($request->ajax()) {
                return response()->json(['message' => "Admin account for {$name} has been removed."]);
            }

            return redirect()->route('users.index')->with('success', "Admin account for {$name} removed.");
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Failed to remove admin account.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to remove admin account.');
        }
    }
}
