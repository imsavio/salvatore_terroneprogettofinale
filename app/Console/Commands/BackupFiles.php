<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:files {--compress : Compress the backup files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of uploaded files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting files backup...');

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $backupDir = storage_path("app/backups/files_{$timestamp}");

        // Ensure backup directory exists
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Backup storage/app/public
        $publicPath = storage_path('app/public');
        if (is_dir($publicPath)) {
            $this->backupDirectory($publicPath, $backupDir . '/public');
        }

        // Backup .env file
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            copy($envPath, $backupDir . '/.env');
        }

        // Compress if requested
        if ($this->option('compress')) {
            $this->compressBackup($backupDir);
        }

        $this->info("Files backup created: {$backupDir}");
        
        // Clean old backups (keep last 7 days)
        $this->cleanOldBackups();

        return Command::SUCCESS;
    }

    private function backupDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                mkdir($target, 0755, true);
            } else {
                copy($item, $target);
            }
        }
    }

    private function compressBackup($backupDir)
    {
        $compressedPath = $backupDir . '.tar.gz';
        
        $command = "tar -czf " . escapeshellarg($compressedPath) . " -C " . escapeshellarg(dirname($backupDir)) . " " . escapeshellarg(basename($backupDir));
        
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            $this->removeDirectory($backupDir);
            $this->info("Compressed backup created: " . basename($compressedPath));
        } else {
            $this->error('Failed to compress backup!');
        }
    }

    private function removeDirectory($dir)
    {
        if (is_dir($dir)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $item) {
                if ($item->isDir()) {
                    rmdir($item->getRealPath());
                } else {
                    unlink($item->getRealPath());
                }
            }
            rmdir($dir);
        }
    }

    private function cleanOldBackups()
    {
        $backupDir = storage_path('app/backups');
        $files = glob($backupDir . '/files_*');
        $cutoff = Carbon::now()->subDays(7);

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff->timestamp) {
                if (is_dir($file)) {
                    $this->removeDirectory($file);
                } else {
                    unlink($file);
                }
                $this->info("Deleted old backup: " . basename($file));
            }
        }
    }
}
