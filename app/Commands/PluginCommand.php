<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class PluginCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'in';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pluginPath = getcwd();
        $pluginName = basename($pluginPath);

        $siteOptions = $this->getSiteOptions();
        $sitePath = $this->menu("Install \"$pluginName\" for which site?", $siteOptions)->open();

        if( ! $sitePath ) {
            return;
        }

        $this->info("You have chosen \"{$siteOptions[$sitePath]}\" for $pluginName");

        symlink( getcwd(), "$sitePath/$pluginName");
    }

    /**
     * @return array $siteOptions
     */
    protected function getSiteOptions(): array
    {
        $sites = [];
        $config = json_decode(file_get_contents($_SERVER['HOME'] . '/.valet/config.json' ));
        foreach( $config->paths as $path ) {
            foreach( array_diff( scandir($path), ['.', '..']) as $site ) {
                if( is_dir("$path/$site/wp-content/plugins") ) {
                    $sites[$site] = "$path/$site/wp-content/plugins";
                }
            }
        }

        return array_flip($sites);
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
