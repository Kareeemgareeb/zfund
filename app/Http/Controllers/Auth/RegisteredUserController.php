<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'national_id' => ['required', 'regex:/^[0-9]{12}$/D', 'unique:users,national_id'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'phone_number' => ['required', 'regex:/^[0-9]{10}$/D'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'account_type' => ['required', 'in:beneficiary,donor'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            $user = User::create([
                'national_id' => $validated['national_id'],
                'username' => $validated['username'],
                'phone_number' => $validated['phone_number'],
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($validated['password']),
                'status' => 'ACTIVE',
            ]);
            $role = \App\Models\Role::where('name', $validated['account_type'])->firstOrFail();
            $user->roles()->attach($role->id);
            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
