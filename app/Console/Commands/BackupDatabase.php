<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--compress : Compress the backup file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$timestamp}.sql";
        
        if ($this->option('compress')) {
            $filename .= '.gz';
        }

        $backupPath = storage_path("app/backups/{$filename}");

        // Ensure backup directory exists
        if (!file_exists(dirname($backupPath))) {
            mkdir(dirname($backupPath), 0755, true);
        }

        // Get database configuration
        $database = config('database.connections.' . config('database.default'));
        
        if ($database['driver'] === 'sqlite') {
            $this->backupSqlite($database['database'], $backupPath);
        } else {
            $this->backupMysql($database, $backupPath);
        }

        // Compress if requested
        if ($this->option('compress')) {
            $this->compressBackup($backupPath);
        }

        $this->info("Database backup created: {$filename}");
        
        // Clean old backups (keep last 7 days)
        $this->cleanOldBackups();

        return Command::SUCCESS;
    }

    private function backupSqlite($databasePath, $backupPath)
    {
        if (!file_exists($databasePath)) {
            $this->error('SQLite database file not found!');
            return;
        }

        copy($databasePath, $backupPath);
    }

    private function backupMysql($database, $backupPath)
    {
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            $database['host'],
            $database['port'],
            $database['username'],
            $database['password'],
            $database['database'],
            $backupPath
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Database backup failed!');
            return;
        }
    }

    private function compressBackup($backupPath)
    {
        $compressedPath = $backupPath . '.gz';
        
        $fp_out = gzopen($compressedPath, 'wb9');
        $fp_in = fopen($backupPath, 'rb');
        
        while (!feof($fp_in)) {
            gzwrite($fp_out, fread($fp_in, 1024 * 512));
        }
        
        fclose($fp_in);
        gzclose($fp_out);
        
        unlink($backupPath);
    }

    private function cleanOldBackups()
    {
        $backupDir = storage_path('app/backups');
        $files = glob($backupDir . '/backup_*.sql*');
        $cutoff = Carbon::now()->subDays(7);

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff->timestamp) {
                unlink($file);
                $this->info("Deleted old backup: " . basename($file));
            }
        }
    }
}
