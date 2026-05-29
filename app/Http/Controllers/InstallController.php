<?php

namespace App\Http\Controllers;

use App\Services\InstallerService;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\View;
use Throwable;

class InstallController extends Controller
{
    public function __construct(private readonly InstallerService $installer)
    {
    }

    public function welcome(): View
    {
        return view('install.welcome');
    }

    public function database(): View
    {
        return $this->databaseView();
    }

    public function install(Request $request): View|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'DB_HOST' => ['required', 'string', 'max:255'],
            'DB_PORT' => ['required', 'integer', 'min:1', 'max:65535'],
            'DB_DATABASE' => ['required', 'string', 'max:255'],
            'DB_USERNAME' => ['required', 'string', 'max:255'],
            'DB_PASSWORD' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->databaseView(
                $request->except('DB_PASSWORD'),
                $validator->errors(),
            );
        }

        $data = $validator->validated();

        try {
            $result = $this->installer->install($data);

            return $this->completeView([
                'database' => $result['database'],
                'statements' => $result['imported_statements'],
            ]);
        } catch (Throwable $exception) {
            $this->installer->logFailure($exception);

            return $this->databaseView(
                $request->except('DB_PASSWORD'),
                new MessageBag([
                    'install' => $this->installer->friendlyError($exception),
                    'detail' => $exception->getMessage(),
                ]),
            );
        }
    }

    public function complete(Request $request): View
    {
        abort_unless(File::exists(storage_path('app/installed.lock')), 404);

        return view('install.complete', [
            'adminUrl' => url('/admin/login'),
            'siteUrl' => url('/'),
            'importedStatements' => (int) $request->query('statements', 0),
            'database' => (string) $request->query('database', env('DB_DATABASE', '')),
        ]);
    }

    private function completeView(array $data = []): View
    {
        return view('install.complete', [
            'adminUrl' => url('/admin/login'),
            'siteUrl' => url('/'),
            'importedStatements' => (int) ($data['statements'] ?? 0),
            'database' => (string) ($data['database'] ?? env('DB_DATABASE', '')),
        ]);
    }

    private function databaseView(array $input = [], ?MessageBag $messages = null): ViewContract
    {
        $defaults = [
            'DB_HOST' => env('DB_HOST', 'localhost'),
            'DB_PORT' => env('DB_PORT', '3306'),
            'DB_DATABASE' => env('DB_DATABASE', ''),
            'DB_USERNAME' => env('DB_USERNAME', ''),
            'DB_PASSWORD' => '',
        ];

        $errors = new ViewErrorBag();

        if ($messages !== null && $messages->isNotEmpty()) {
            $errors->put('default', $messages);
        }

        return view('install.database', [
            'defaults' => array_replace($defaults, $input),
            'errors' => $errors,
            'backupExists' => File::exists(database_path('install/backup.sql')),
        ]);
    }
}
