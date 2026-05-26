<?php

namespace App\Console\Commands;

use App\Models\Institution;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportInstitutions extends Command
{
    protected $signature = 'import:institutions';

    protected $description = 'Kurum listesini içeri aktar';

    public function handle()
    {
        $path = storage_path('app/institutions.txt');

        if (!file_exists($path)) {
            $this->error('institutions.txt bulunamadı!');
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $count = 0;

        foreach ($lines as $line) {

            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            Institution::firstOrCreate(
                ['name' => $line],
                [
                    'slug' => Str::slug($line),
                    'is_active' => true,
                ]
            );

            $count++;
        }

        $this->info($count . ' kurum eklendi.');
    }
}