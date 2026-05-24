<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $settings = SiteSetting::query()->first();

        if ($settings && ! $settings->registration_enabled) {
            return view('auth.register', [
                'registrationDisabled' => true,
                'recaptchaEnabled' => $this->recaptchaEnabled(),
            ]);
        }

        return view('auth.register', [
            'registrationDisabled' => false,
            'recaptchaEnabled' => $this->recaptchaEnabled(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $settings = SiteSetting::query()->first();

        if ($settings && ! $settings->registration_enabled) {
            throw ValidationException::withMessages([
                'email' => 'Yeni üyelikler geçici olarak kapalıdır.',
            ]);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($this->recaptchaEnabled()) {
            $rules['g-recaptcha-response'] = ['required'];
        }

        $request->validate($rules, [
            'g-recaptcha-response.required' => 'Lütfen robot olmadığınızı doğrulayın.',
        ]);

        if (! $this->recaptchaEnabled() && app()->isProduction()) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Kayıt güvenlik doğrulaması yapılandırılmamış. Lütfen site yöneticisiyle iletişime geçin.',
            ]);
        }

        if ($this->recaptchaEnabled()) {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]);

            if (! $response->json('success')) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'reCAPTCHA doğrulaması başarısız oldu. Lütfen tekrar deneyin.',
                ]);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $this->sendVerificationMail($user);

        Auth::login($user);

        if ($settings?->email_verification_required) {
            return redirect()->route('verification.notice');
        }

        return redirect(route('dashboard', absolute: false));
    }

    private function recaptchaEnabled(): bool
    {
        return filled(config('services.recaptcha.site_key'))
            && filled(config('services.recaptcha.secret_key'));
    }

    private function sendVerificationMail(User $user): void
    {
        if (! $user instanceof MustVerifyEmail || $user->hasVerifiedEmail()) {
            return;
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $exception) {
            Log::error('User email verification mail could not be sent after registration.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'mailer' => config('mail.default'),
                'queue_connection' => config('queue.default'),
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
