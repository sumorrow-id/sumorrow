<?php

namespace App\Console\Commands;

use App\Models\MountainImage;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Str;

class DownloadMountainImages extends Command
{
    protected $signature = 'mountains:download-images
                            {--disk= : Storage disk to use (defaults to FILESYSTEM_DISK env)}
                            {--delay=500 : Delay in milliseconds between each request}';

    protected $description = 'Download mountain images from external URLs and save to storage disk';

    public function __construct(
        private HttpFactory $http,
        private FilesystemFactory $storage,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $disk = $this->option('disk') ?? config('filesystems.default', 'public');
        $delay = (int) $this->option('delay') * 1000; // convert ms to µs

        $images = MountainImage::with('mountain')
            ->where('image_url', 'like', 'http%')
            ->whereNotNull('source_url')
            ->get();

        if ($images->isEmpty()) {
            $this->info('No external image URLs found. All images are already stored locally.');

            return self::SUCCESS;
        }

        $this->info("Downloading {$images->count()} images to [{$disk}] disk...");
        $bar = $this->output->createProgressBar($images->count());
        $bar->start();

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($images as $image) {
            try {
                $response = $this->http->withoutVerifying()
                    ->withHeaders([
                        'User-Agent' => config('app.name').'/1.0 (mountain-image-downloader)',
                    ])->timeout(30)->get($image->getRawOriginal('image_url'));

                if (! $response->successful()) {
                    $failed++;
                    $errors[] = "{$image->mountain->name}: HTTP {$response->status()}";
                    $bar->advance();

                    continue;
                }

                $ext = $this->guessExtension(
                    $image->getRawOriginal('image_url'),
                    $response->header('Content-Type')
                );

                $slug = Str::slug($image->mountain->name);
                $path = "mountains/{$slug}-{$image->position}.{$ext}";

                $this->storage->disk($disk)->put($path, $response->body());
                $image->updateQuietly(['image_url' => $path]);

                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "{$image->mountain->name}: {$e->getMessage()}";
            }

            $bar->advance();
            usleep($delay);
        }

        $bar->finish();
        $this->newLine(2);

        if (! empty($errors)) {
            $this->warn('Failed downloads:');
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
            $this->newLine();
        }

        $this->info("Done! {$success} succeeded, {$failed} failed.");

        if ($disk === 'public') {
            $this->line('Tip: run <info>php artisan storage:link</info> if you haven\'t already.');
        }

        return self::SUCCESS;
    }

    private function guessExtension(string $url, ?string $contentType): string
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        $cleanType = $contentType ? explode(';', $contentType)[0] : '';
        if (isset($map[$cleanType])) {
            return $map[$cleanType];
        }

        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        $ext = $ext === 'jpeg' ? 'jpg' : $ext;

        return in_array($ext, ['jpg', 'png', 'webp', 'gif']) ? $ext : 'jpg';
    }
}
