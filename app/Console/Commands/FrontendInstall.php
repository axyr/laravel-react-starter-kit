<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class FrontendInstall extends Command
{
    protected $signature = 'frontend:install
        {--directory=frontend : The directory to install the frontend into}
        {--repository=https://github.com/axyr/laravel-tractor-react.git : Ther repository to clone from}
    ';

    protected $description = 'Install the React frontend';

    public function handle(): void
    {
        $targetDir = base_path($this->option('directory'));
        $repoUrl = $this->option('repository');

        if (file_exists($targetDir)) {
            $this->error("Directory {$targetDir} already exists.");
            return;
        }

        $this->info("Cloning frontend from {$repoUrl} into {$targetDir}...");

        if ($this->cloneRepository($repoUrl, $targetDir)) {
            $this->installDependencies($targetDir);
            $this->setEnvironmentVariables($targetDir);
        }
    }

    protected function cloneRepository(string $repoUrl, string $targetDir): bool
    {
        $process = new Process(['git', 'clone', $repoUrl, $targetDir]);
        $process->setTimeout(300);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if ($process->isSuccessful()) {
            $this->info('Frontend installed successfully.');
        } else {
            $this->error('Failed to install frontend.');
        }

        return $process->isSuccessful();
    }

    protected function installDependencies(string $targetDir): void
    {
        $process = Process::fromShellCommandline('npm install', $targetDir);
        $process->setTimeout(300);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if ($process->isSuccessful()) {
            $this->info('Frontend dependencies installed successfully.');
        } else {
            $this->error('Failed to install frontend dependencies.');
        }
    }

    protected function setEnvironmentVariables(string $targetDir): void
    {
        $env = 'VITE_APP_NAME=' . config('app.name') . "\n";
        $env .= 'VITE_API_BASE_URL=' . Str::finish(config('app.url'), '/') . "api\n";

        $envPath = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.env';

        try {
            file_put_contents($envPath, $env);
            $this->info('Frontend environment variables set successfully.');
        } catch (\Throwable $e) {
            $this->error('Failed to write frontend environment file: ' . $e->getMessage());
        }
    }

}
