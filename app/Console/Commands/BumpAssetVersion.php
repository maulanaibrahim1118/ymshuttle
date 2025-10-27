<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BumpAssetVersion extends Command
{
    protected $signature = 'asset:refresh';
    protected $description = 'Refresh asset version for cache busting';

    public function handle()
    {
        $version = time();

        $configPath = config_path('asset.php');
        $content = "<?php\n\nreturn [\n    'version' => '$version',\n];\n";
        File::put($configPath, $content);

        $this->info("✅ Asset version updated to $version");
    }
}