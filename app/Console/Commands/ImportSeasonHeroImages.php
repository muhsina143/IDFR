<?php

namespace App\Console\Commands;

use Database\Seeders\SeasonHeroImagesSeeder;
use Illuminate\Console\Command;

class ImportSeasonHeroImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idfr:import-hero-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all existing homepage hero images into the season_images table for Season 8';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting import of existing Season 8 hero images...');

        $seeder = new SeasonHeroImagesSeeder;
        $seeder->setCommand($this);
        $seeder->run();

        $this->info('Hero image import process completed successfully.');

        return Command::SUCCESS;
    }
}
