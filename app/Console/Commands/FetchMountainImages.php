<?php

namespace App\Console\Commands;

use App\Models\Mountain;
use App\Models\MountainImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FetchMountainImages extends Command
{
    protected $signature = 'mountains:fetch-images
                            {--dry-run : Preview changes without writing}
                            {--only= : Comma-separated mountain names to process (ignores the placeholder filter)}
                            {--file= : Path to the seeder JSON (defaults to database/data/mountains_seeder.json)}
                            {--delay=150 : Delay in milliseconds between each HTTP request}';

    protected $description = 'Replace generic stock (Unsplash) cover images with real photos sourced from Wikipedia / Wikimedia Commons';

    public function handle(): int
    {
        $jsonPath = $this->option('file') ?: database_path('data/mountains_seeder.json');
        $isDryRun = (bool) $this->option('dry-run');
        $delay = (int) $this->option('delay') * 1000; // ms -> µs

        $only = $this->option('only')
            ? array_map('trim', explode(',', (string) $this->option('only')))
            : null;

        if ($isDryRun) {
            $this->warn('DRY RUN — no changes will be written.');
        }

        $json = json_decode(file_get_contents($jsonPath), true);
        $mountains = $json['mountains'];

        // Target mountains whose cover is a generic Unsplash stock photo or empty.
        $targets = [];
        foreach ($mountains as $index => $mountain) {
            $current = $mountain['images'][0]['image_url'] ?? '';

            $shouldProcess = $only
                ? in_array($mountain['name'], $only, true)
                : ($current === '' || str_contains($current, 'unsplash.com'));

            if ($shouldProcess) {
                $targets[$index] = $mountain;
            }
        }

        if (empty($targets)) {
            $this->info('Nothing to do — no placeholder images found.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Searching real photos for %d mountains...', count($targets)));
        $bar = $this->output->createProgressBar(count($targets));
        $bar->start();

        $found = [];
        $missing = [];

        foreach ($targets as $index => $mountain) {
            $name = $mountain['name'];
            $newUrl = $this->fetchRealImage($name);

            if ($newUrl) {
                $found[] = [
                    'name' => $name,
                    'old' => $mountain['images'][0]['image_url'] ?? '(empty)',
                    'new' => $newUrl,
                ];

                if (! $isDryRun) {
                    $json['mountains'][$index]['images'][0]['image_url'] = $newUrl;
                    $this->updateDatabase($name, $newUrl);
                }
            } else {
                $missing[] = $name;
            }

            $bar->advance();
            usleep($delay);
        }

        $bar->finish();
        $this->newLine(2);

        if (! $isDryRun && count($found) > 0) {
            file_put_contents(
                $jsonPath,
                json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
            Cache::forget('mountains.home');
            $this->info('Seeder JSON updated and mountains.home cache cleared.');
        }

        $this->printSummary($found, $missing, $isDryRun);

        return self::SUCCESS;
    }

    /**
     * Try, in order: the Indonesian/English Wikipedia article lead image (most
     * reliably on-subject), then a Wikimedia Commons keyword search.
     */
    private function fetchRealImage(string $mountainName): ?string
    {
        $cleanName = preg_replace('/^Gunung\s+/i', '', $mountainName);
        $slug = str_replace(' ', '_', $cleanName);

        // Only the "Gunung_"/"Mount_" prefixed titles — a bare slug collides with
        // unrelated disambiguation pages (e.g. "Tapak", "Sagara", "Sanghyang").
        $summaryAttempts = [
            "https://id.wikipedia.org/api/rest_v1/page/summary/Gunung_{$slug}",
            "https://en.wikipedia.org/api/rest_v1/page/summary/Mount_{$slug}",
        ];

        foreach ($summaryAttempts as $url) {
            $image = $this->fetchWikipediaSummaryImage($url);
            if ($image && $this->isLikelyPhoto($image)) {
                return $image;
            }
        }

        // Fall back to Commons search for obscure peaks without a wiki article.
        // Require the mountain name in the filename so keyword search doesn't
        // return on-topic-but-wrong photos (a neighbouring peak, a temple, etc.).
        return $this->searchCommons("Gunung {$cleanName}", $cleanName)
            ?? $this->searchCommons("Mount {$cleanName}", $cleanName);
    }

    /**
     * Reject maps, locator diagrams, flags, lithographs and other non-photo
     * assets that Wikipedia/Commons return for peaks with no real photo.
     */
    private function isLikelyPhoto(string $url): bool
    {
        $lower = strtolower($url);

        if (preg_match('/\.(svg|png|gif|tif|tiff)(\?|$)/', $lower)) {
            return false;
        }

        foreach (['location_map', 'relief', 'topograph', 'atlas', '_map', 'lithograph', 'locator'] as $token) {
            if (str_contains($lower, $token)) {
                return false;
            }
        }

        return true;
    }

    private function fetchWikipediaSummaryImage(string $url): ?string
    {
        $data = $this->getJson($url);

        if (! $data) {
            return null;
        }

        return $data['originalimage']['source'] ?? $data['thumbnail']['source'] ?? null;
    }

    /**
     * Search Wikimedia Commons for a real photo file matching the query.
     * Filters out maps, diagrams, and logos by keeping only raster photo formats.
     */
    private function searchCommons(string $query, string $requiredName): ?string
    {
        $url = 'https://commons.wikimedia.org/w/api.php?'.http_build_query([
            'action' => 'query',
            'format' => 'json',
            'generator' => 'search',
            'gsrsearch' => $query,
            'gsrnamespace' => 6, // File namespace
            'gsrlimit' => 5,
            'prop' => 'imageinfo',
            'iiprop' => 'url|mediatype',
        ]);

        $data = $this->getJson($url);
        $pages = $data['query']['pages'] ?? null;

        if (! $pages) {
            return null;
        }

        foreach ($pages as $page) {
            $info = $page['imageinfo'][0] ?? null;
            $source = $info['url'] ?? null;

            if (! $source || ($info['mediatype'] ?? '') !== 'BITMAP') {
                continue;
            }

            if (! $this->isLikelyPhoto($source)) {
                continue;
            }

            // The file name must mention the peak (collapse spaces — "Kie Besi"
            // appears as "Kie_Besi" or "KieBesi" in Commons file names).
            $filename = strtolower(rawurldecode(basename($source)));
            $needle = strtolower(str_replace(' ', '', $requiredName));
            if (! str_contains(str_replace(['_', '-', ' '], '', $filename), $needle)) {
                continue;
            }

            return $source;
        }

        return null;
    }

    /**
     * @return array<mixed>|null
     */
    private function getJson(string $url): ?array
    {
        try {
            // withoutVerifying() mirrors mountains:download-images — local XAMPP/dev
            // environments often lack a configured CA bundle for HTTPS verification.
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Sumorrow/1.0 (https://github.com/sumorrow-id)',
                ])->timeout(10)->get($url);

            if (! $response->successful()) {
                return null;
            }

            return $response->json();
        } catch (\Throwable) {
            return null;
        }
    }

    private function updateDatabase(string $mountainName, string $imageUrl): void
    {
        $mountain = Mountain::where('name', $mountainName)->first();

        if (! $mountain) {
            return;
        }

        MountainImage::updateOrCreate(
            ['mountain_id' => $mountain->id, 'position' => 0],
            ['image_url' => $imageUrl, 'source_url' => $imageUrl, 'is_cover' => true]
        );
    }

    /**
     * @param  array<int, array{name: string, old: string, new: string}>  $found
     * @param  array<int, string>  $missing
     */
    private function printSummary(array $found, array $missing, bool $isDryRun): void
    {
        $label = $isDryRun ? 'Would update' : 'Updated';

        $this->info('── Summary ──────────────────────────────────');
        $this->line(sprintf('  %s : %d mountains', $label, count($found)));
        $this->line(sprintf('  Not found  : %d mountains', count($missing)));

        if (count($found) > 0) {
            $this->newLine();
            $this->info($isDryRun ? 'Preview of changes:' : 'Changes applied:');
            foreach ($found as $item) {
                $this->line("  <fg=green>{$item['name']}</>");
                $this->line("    was: <fg=gray>{$item['old']}</>");
                $this->line("    now: <fg=cyan>{$item['new']}</>");
            }
        }

        if (count($missing) > 0) {
            $this->newLine();
            $this->warn('No real photo found (left unchanged — needs manual sourcing):');
            $this->line('  '.implode(', ', $missing));
        }
    }
}
