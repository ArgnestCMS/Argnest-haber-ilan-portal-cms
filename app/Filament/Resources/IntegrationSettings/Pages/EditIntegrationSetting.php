<?php

namespace App\Filament\Resources\IntegrationSettings\Pages;

use App\Filament\Resources\IntegrationSettings\IntegrationSettingResource;
use App\Models\IntegrationSetting;
use App\Models\SeoSetting;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EditIntegrationSetting extends EditRecord
{
    protected static string $resource = IntegrationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_test_mail')
                ->label('Test mail gönder')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->form([
                    TextInput::make('to')
                        ->label('Alıcı e-posta')
                        ->email()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        $this->record->applyToConfig();

                        Mail::raw('Bu mesaj Sistem Ayarları panelinden gönderilen test mailidir.', function ($message) use ($data): void {
                            $message
                                ->to($data['to'])
                                ->subject('Test Mail - ' . config('app.name'));
                        });

                        Notification::make()
                            ->title('Test mail gönderildi.')
                            ->success()
                            ->send();
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title('Test mail gönderilemedi.')
                            ->body('Mail ayarlarını kontrol edin. Hata detayları log dosyasına yazıldı.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $siteSetting = SiteSetting::query()->first();
        $seoSetting = SeoSetting::current();

        return array_merge($data, [
            'site_name' => $siteSetting?->site_name,
            'site_description' => $seoSetting->site_description ?: $siteSetting?->seo_description,
            'site_email' => $siteSetting?->email,
            'google_analytics' => $seoSetting->google_analytics ?: $siteSetting?->google_analytics,
            'google_tag_manager' => $seoSetting->google_tag_manager,
            'registration_enabled' => (bool) ($siteSetting?->registration_enabled ?? true),
            'email_verification_required' => (bool) ($siteSetting?->email_verification_required ?? false),
        ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $siteSetting = SiteSetting::query()->firstOrCreate([], [
            'site_name' => config('app.name'),
        ]);
        $seoSetting = SeoSetting::current();

        $siteSetting->fill([
            'site_name' => $data['site_name'] ?? $siteSetting->site_name,
            'email' => $data['site_email'] ?? $siteSetting->email,
            'seo_description' => $data['site_description'] ?? $siteSetting->seo_description,
            'registration_enabled' => (bool) ($data['registration_enabled'] ?? false),
            'email_verification_required' => (bool) ($data['email_verification_required'] ?? false),
        ])->save();

        $seoSetting->fill([
            'site_description' => $data['site_description'] ?? $seoSetting->site_description,
            'google_analytics' => $data['google_analytics'] ?? $seoSetting->google_analytics,
            'google_tag_manager' => $data['google_tag_manager'] ?? $seoSetting->google_tag_manager,
        ])->save();

        foreach ([
            'site_name',
            'site_description',
            'site_email',
            'google_analytics',
            'google_tag_manager',
            'registration_enabled',
            'email_verification_required',
            'app_url_info',
            'timezone_info',
        ] as $externalField) {
            unset($data[$externalField]);
        }

        foreach ([
            'mail_password',
            'recaptcha_secret_key',
            'webpush_vapid_private_key',
            'google_client_secret',
            'facebook_app_secret',
        ] as $secretField) {
            if (blank($data[$secretField] ?? null)) {
                unset($data[$secretField]);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record instanceof IntegrationSetting) {
            $this->record->applyToConfig();
        }

        Notification::make()
            ->title('Sistem ayarları kaydedildi.')
            ->body('Config cache kullanıyorsanız optimize:clear çalıştırmanız gerekebilir.')
            ->success()
            ->send();
    }
}
