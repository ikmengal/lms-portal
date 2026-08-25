<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AccountActivationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Routing\Controllers\HasMiddleware; // 1. Import this
use Illuminate\Routing\Controllers\Middleware;    // 2. Import this

class AuthController extends Controller
{
    // 4. Define middleware statically using the new approach
    public static function middleware(): array
    {
        return [
            new Middleware('guest', only: [
                'showLogin', 'showRegister', 'register', 'login',
                'showForgotPassword', 'sendResetLink', 'showResetForm', 'resetPassword',
                'activate', 'showResendForm', 'resendActivation',
            ]),
            new Middleware('auth', only: ['logout']),
        ];
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Block unactivated accounts with a helpful message
        $pending = User::where('email', $credentials['email'])->first();
        if ($pending && ! $pending->hasActivated()) {
            return back()
                ->withErrors(['email' => 'Please activate your account first. Check your inbox for the activation link.'])
                ->with('showResend', $pending->email)
                ->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Check if user has any role
            if ($user->getRoleNames()->isEmpty()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your account does not have an assigned role. Please contact support.',
                ])->onlyInput('email');
            }
            return redirect()->intended(route('dashboard'))
            ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign default student role
        $user->assignRole('student');

        // Send branded welcome email with activation link (no auto-login until activated)
        $activationUrl = URL::temporarySignedRoute(
            'activation.verify', now()->addMinutes(60), ['user' => $user->id]
        );
        $user->notify(new AccountActivationNotification($activationUrl));

        return redirect()->route('login')
            ->with('success', 'Account created! We sent an activation link to ' . $user->email . '. Please activate your account before logging in.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Always return the same response to avoid revealing whether an account exists
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', __($status));
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    public function showResetForm(string $token, Request $request)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Invalidate all other sessions for this user
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', __($status));
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    public function activate(Request $request, User $user)
    {
        if ($user->hasActivated()) {
            return redirect()->route('login')
                ->with('success', 'Your account is already activated. You can log in.');
        }

        $user->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('login')
            ->with('success', 'Account activated successfully! You can now log in.');
    }

    public function showResendForm(Request $request)
    {
        return view('auth.resend-activation', [
            'email' => $request->query('email'),
        ]);
    }

    public function resendActivation(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user && ! $user->hasActivated()) {
            $activationUrl = URL::temporarySignedRoute(
                'activation.verify', now()->addMinutes(60), ['user' => $user->id]
            );
            $user->notify(new AccountActivationNotification($activationUrl, isResend: true));
        }

        // Same response regardless of account state (no user enumeration)
        return back()->with('success', 'If that email needs activation, we have sent a new activation link.');
    }
}
