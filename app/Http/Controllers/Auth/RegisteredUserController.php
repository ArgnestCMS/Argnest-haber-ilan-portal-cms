<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $settings = SiteSetting::query()->first();

        if ($settings && ! $settings->registration_enabled) {
            abort(403, 'Yeni üyelikler geçici olarak kapalıdır.');
        }

        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $settings = SiteSetting::query()->first();

        if ($settings && ! $settings->registration_enabled) {
            throw ValidationException::withMessages([
                'email' => 'Yeni üyelikler geçici olarak kapalıdır.',
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => ['required'],
        ], [
            'g-recaptcha-response.required' => 'Lütfen robot olmadığınızı doğrulayın.',
        ]);

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        if (! $response->json('success')) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'reCAPTCHA doğrulaması başarısız oldu. Lütfen tekrar deneyin.',
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($settings?->email_verification_required) {
            return redirect()->route('verification.notice');
        }

        return redirect(route('dashboard', absolute: false));
    }
}