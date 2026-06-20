<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::with('house')->latest()->paginate(20),
            'roles' => array_map(fn ($r) => ['value' => $r->value, 'label' => $r->label()], UserRole::cases()),
            'houses' => \App\Models\House::with('village')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'name_bn' => 'nullable|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|string',
            'house_id' => 'nullable|exists:houses,id',
            'locale' => 'in:bn,en',
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return back()->with('success', 'User created.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'name_bn' => 'nullable|string',
            'role' => 'required|string',
            'house_id' => 'nullable|exists:houses,id',
            'is_active' => 'boolean',
            'locale' => 'in:bn,en',
        ]);
        $user->update($data);
        return back()->with('success', 'User updated.');
    }
}
