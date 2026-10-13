<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        // Find user by email
        $user = User::where('email', $credentials['email'])->first();

        // User not found
        if (!$user) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ]);
        }

        // Account disabled
        if (!$user->is_active) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Your account has been disabled. Please contact the administrator.',
                ]);
        }

        // Password incorrect
        if (!Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ]);
        }

        // Login user
        Auth::login($user, $remember);

        // Prevent session fixation
        $request->session()->regenerate();

        // Store login information
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'last_login_user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', 'Welcome back!');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        // Create user only ONCE
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),

            // Account settings
            'role' => 'user',
            'is_active' => true,

            // Login information
            'last_login_at' => null,
            'last_login_ip' => null,
            'last_login_user_agent' => null,

            // 2FA disabled initially
            'two_factor_enabled' => false,
        ]);

        // Automatically login after registration
        Auth::login($user);

        // Regenerate session
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Account created successfully!');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Destroy current session
        $request->session()->invalidate();

        // Prevent CSRF token reuse
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out.');
    }
}
