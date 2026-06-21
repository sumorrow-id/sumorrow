<?php

namespace App\Console\Commands;

use App\Models\Mountain;
use App\Models\MountainImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class UpdateMountainImages extends Command
{
    protected $signature = 'mountains:update-images {--dry-run : Preview changes without writing}';

    protected $description = 'Fetch HD cover images for all mountains from Wikipedia and fix off-subject or empty images';

    /** Mountains whose current image is clearly off-subject and must be replaced */
    private const OFF_SUBJECT = [
        'Bratan',       // temple photo
        'Dieng',        // temple complex
        'Krakatau',     // historical lithograph
        'Patuha',       // 1920s Dutch colonial factory
        'Geureudong',   // wrong mountain (Bur ni Telong)
        'Weh',          // island map PNG
        'Ijen',         // sulfur miners, no mountain view
        'Bismo',        // old Dutch colonial photo
        'Jailolo',      // likely the town, not the peak
        'Guntur',       // settlement photo
        'Tidore',       // island aerial
        'Sangeang Api', // island aerial
    ];

    /** HD Unsplash fallback pool — confirmed photos used elsewhere in the app */
    private const FALLBACK_POOL = [
        'https://images.unsplash.com/photo-1627916538356-9a2ebfb1d200?auto=format&fit=crop&q=85&w=2000',
        'https://images.unsplash.com/photo-1543886576-9d32d001cedc?auto=format&fit=crop&q=85&w=2000',
        'https://images.unsplash.com/photo-1506501139174-099022df5260?auto=format&fit=crop&q=85&w=2000',
        'https://images.unsplash.com/photo-1589182373726-e4f658ab50f0?auto=format&fit=crop&q=85&w=2000',
        'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=85&w=2000',
        'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&q=85&w=2000',
        'https://images.unsplash.com/photo-1454496522488-7a8e488e8606?auto=format&fit=crop&q=85&w=2000',
        'https://images.unsplash.com/photo-1486870591958-9b9d0d1dda99?auto=format&fit=crop&q=85&w=2000',
    ];

    public function handle(): int
    {
        $jsonPath = database_path('data/mountains_seeder.json');
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('DRY RUN — no changes will be written.');
        }

        $json = json_decode(file_get_contents($jsonPath), true);
        $mountains = $json['mountains'];
        $total = count($mountains);

        $updated = [];
        $kept = [];

        $this->info("Processing {$total} mountains...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($mountains as $index => $mountain) {
            $name = $mountain['name'];
            $existing = $mountain['images'][0]['image_url'] ?? '';
            $isEmpty = $existing === '';
            $isOffSubject = in_array($name, self::OFF_SUBJECT, true);

            if ($isEmpty || $isOffSubject) {
                // Off-subject: Wikipedia gave us the same bad image, skip directly to fallback.
                // Empty: try Wikipedia first, fall back to Unsplash if nothing found.
                $newUrl = $isOffSubject
                    ? $this->fallback($name)
                    : ($this->fetchWikipediaImage($name) ?? $this->fallback($name));

                $updated[] = [
                    'name' => $name,
                    'reason' => $isEmpty ? 'empty' : 'off-subject',
                    'old' => $existing ?: '(empty)',
                    'new' => $newUrl,
                ];

                if (! $isDryRun) {
                    $json['mountains'][$index]['images'][0]['image_url'] = $newUrl;
                    $this->updateDatabase($name, $newUrl);
                }
            } else {
                $kept[] = $name;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (! $isDryRun && count($updated) > 0) {
            file_put_contents(
                $jsonPath,
                json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
            Cache::forget('mountains.home');
            $this->info('Seeder JSON updated and mountains.home cache cleared.');
        }

        $this->printSummary($updated, $kept, $isDryRun);

        return self::SUCCESS;
    }

    private function fetchWikipediaImage(string $mountainName): ?string
    {
        $slug = str_replace(' ', '_', $mountainName);
        $cleanSlug = preg_replace('/^Gunung_/i', '', $slug);

        $attempts = [
            "https://id.wikipedia.org/api/rest_v1/page/summary/Gunung_{$cleanSlug}",
            "https://en.wikipedia.org/api/rest_v1/page/summary/{$slug}",
            "https://en.wikipedia.org/api/rest_v1/page/summary/Mount_{$cleanSlug}",
            "https://id.wikipedia.org/api/rest_v1/page/summary/{$slug}",
        ];

        foreach ($attempts as $url) {
            $image = $this->fetchFromUrl($url);
            if ($image) {
                return $image;
            }
            usleep(100_000);
        }

        return null;
    }

    private function fetchFromUrl(string $url): ?string
    {
        try {
            $response = Http::withHeaders(['User-Agent' => 'Sumorrow/1.0 (https://github.com/sumorrow-id)'])
                ->timeout(8)
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            return $data['originalimage']['source'] ?? $data['thumbnail']['source'] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    /** Pick a fallback deterministically by mountain name so the same mountain always gets the same photo */
    private function fallback(string $mountainName): string
    {
        $index = abs(crc32($mountainName)) % count(self::FALLBACK_POOL);

        return self::FALLBACK_POOL[$index];
    }

    private function updateDatabase(string $mountainName, string $imageUrl): void
    {
        $mountain = Mountain::where('name', $mountainName)->first();

        if (! $mountain) {
            return;
        }

        MountainImage::updateOrCreate(
            ['mountain_id' => $mountain->id, 'position' => 0],
            ['image_url' => $imageUrl, 'is_cover' => true]
        );
    }

    private function printSummary(array $updated, array $kept, bool $isDryRun): void
    {
        $label = $isDryRun ? 'Would update' : 'Updated';

        $this->info('── Summary ──────────────────────────────────');
        $this->line(sprintf('  %s : %d mountains', $label, count($updated)));
        $this->line(sprintf('  Kept     : %d mountains (image OK)', count($kept)));

        if (count($updated) > 0) {
            $this->newLine();

            $fromWiki = array_filter($updated, fn ($u) => ! str_contains($u['new'], 'unsplash'));
            $fromPool = array_filter($updated, fn ($u) => str_contains($u['new'], 'unsplash'));

            $this->line(sprintf('  ↳ %d got a Wikipedia image', count($fromWiki)));
            $this->line(sprintf('  ↳ %d used Unsplash fallback', count($fromPool)));

            $this->newLine();
            $this->info($isDryRun ? 'Preview of changes:' : 'Changes applied:');
            foreach ($updated as $item) {
                $tag = $item['reason'] === 'empty' ? '<fg=yellow>[empty]</>' : '<fg=red>[off-subject]</>';
                $this->line("  {$tag} <fg=green>{$item['name']}</>");
                $this->line("    was: <fg=gray>{$item['old']}</>");
                $this->line("    now: <fg=cyan>{$item['new']}</>");
            }
        }
    }
}
