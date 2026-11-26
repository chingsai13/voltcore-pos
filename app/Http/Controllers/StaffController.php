<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::whereIn('role', ['admin', 'staff'])->get();
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username'  => 'required|string|max:50|unique:users,username',
            'full_name' => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:admin,staff',
            'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($validated);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member added successfully.');
    }

    public function edit(User $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff)
    {
        $validated = $request->validate([
            'username'  => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($staff->id)],
            'full_name' => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($staff->id)],
            'password'  => ['nullable', 'string', 'min:6'],
            'role'      => ['required', 'in:admin,staff'],
            'avatar'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp|max:2048'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($staff->avatar && Storage::disk('public')->exists($staff->avatar)) {
                Storage::disk('public')->delete($staff->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $staff->update($validated);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $staff)
    {
        if ($staff->id === auth()->id()) {
            return redirect()->route('admin.staff.index')->with('error', 'You cannot delete your own account.');
        }

        if ($staff->sales()->exists()) {
            return redirect()->route('admin.staff.index')->with('error', 'Cannot delete staff member: they have recorded sales transactions.');
        }

        if ($staff->avatar && Storage::disk('public')->exists($staff->avatar)) {
            Storage::disk('public')->delete($staff->avatar);
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff member removed successfully.');
    }
}
