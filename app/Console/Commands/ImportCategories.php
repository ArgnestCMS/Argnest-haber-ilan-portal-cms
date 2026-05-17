<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportCategories extends Command
{
    protected $signature = 'import:categories';

    protected $description = 'Kategori listesini içeri aktar';

    public function handle()
    {
        $path = storage_path('app/categories.txt');

        if (!file_exists($path)) {
            $this->error('categories.txt bulunamadı!');
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            // Format: Kategori Adı | type
            // Örnek: KPSS-B İlanları | announcement
            $parts = explode('|', $line);

            $name = trim($parts[0]);
            $type = isset($parts[1]) ? trim($parts[1]) : 'announcement';

            Category::firstOrCreate(
                [
                    'name' => $name,
                    'type' => $type,
                ],
                [
                    'slug' => Str::slug($name),
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );

            $count++;
        }

        $this->info($count . ' kategori işlendi.');
    }
}