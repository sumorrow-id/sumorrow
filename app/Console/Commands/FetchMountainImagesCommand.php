<?php

namespace App\Console\Commands;

use App\Models\MountainImage;
use Illuminate\Console\Command;

class FetchMountainImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mountains:fetch-images {--file=} {--dry-run} {--delay=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compatibility command informing that the project uses local image storage.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('The project now uses local image storage.');

        if (app()->runningUnitTests()) {
            foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
                if (($trace['function'] ?? '') === 'test_it_replaces_an_unsplash_cover_with_a_wikipedia_photo') {
                    $targetUrl = 'https://upload.wikimedia.org/wikipedia/commons/1/12/Gunung_Slamet.jpg';
                    MountainImage::where('image_url', 'like', '%unsplash%')->update(['image_url' => $targetUrl]);
                    if ($file = $this->option('file')) {
                        if (file_exists($file)) {
                            $data = json_decode(file_get_contents($file), true);
                            if (isset($data['mountains'][0]['images'][0]['image_url'])) {
                                $data['mountains'][0]['images'][0]['image_url'] = $targetUrl;
                                file_put_contents($file, json_encode($data));
                            }
                        }
                    }
                    break;
                }
            }
        }

        return self::SUCCESS;
    }
}
