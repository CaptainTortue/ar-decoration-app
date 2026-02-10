<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UploadStorageToR2 extends Command
{
    protected $signature = 'storage:upload-to-r2';
    protected $description = 'Upload local storage files to R2';

    public function handle()
    {
        $this->info('🔧 R2 Configuration:');
        $this->info('Bucket: ' . config('filesystems.disks.s3.bucket'));
        $this->info('Endpoint: ' . config('filesystems.disks.s3.endpoint'));
        $this->newLine();

        // Test de connexion avec fichier visible
        $this->info('🔌 Testing R2 connection...');
        try {
            Storage::disk('s3')->put('test-upload.txt', 'Upload test at ' . now());
            $this->info('✅ Test file created!');

            // Vérifier si le fichier existe
            if (Storage::disk('s3')->exists('test-upload.txt')) {
                $this->info('✅ Test file confirmed in R2!');
                $url = Storage::disk('s3')->url('test-upload.txt');
                $this->info('📍 Public URL: ' . $url);
                Storage::disk('s3')->delete('test-upload.txt');
            } else {
                $this->error('❌ Test file not found in R2');
            }

            $this->newLine();
        } catch (\Exception $e) {
            $this->error('❌ Connection failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Lister les fichiers locaux
        $localDisk = Storage::disk('public');
        $files = $localDisk->allFiles();

        if (count($files) === 0) {
            $this->warn('⚠️  No files found in storage/app/public');
            $this->info('Looking in: ' . storage_path('app/public'));
            return Command::SUCCESS;
        }

        $this->info("📦 Found " . count($files) . " files:");
        foreach ($files as $file) {
            $this->line('  - ' . $file);
        }
        $this->newLine();

        // Confirmation
        if (!$this->confirm('Upload these files to R2?', true)) {
            $this->info('Upload cancelled.');
            return Command::SUCCESS;
        }

        // Upload avec détails
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $uploaded = [];
        $failed = [];

        foreach ($files as $file) {
            try {
                $contents = $localDisk->get($file);

                // Upload avec visibilité publique
                Storage::disk('s3')->put($file, $contents, [
                    'visibility' => 'public',
                    'CacheControl' => 'max-age=31536000',
                ]);

                // Vérifier que le fichier existe après upload
                if (Storage::disk('s3')->exists($file)) {
                    $uploaded[] = $file;
                } else {
                    $failed[] = $file . ' (not found after upload)';
                }

                $bar->advance();
            } catch (\Exception $e) {
                $failed[] = $file . ' (' . $e->getMessage() . ')';
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Résultats détaillés
        $this->info('📊 Upload Summary:');
        $this->info('   ✅ Uploaded: ' . count($uploaded));

        if (count($failed) > 0) {
            $this->warn('   ❌ Failed: ' . count($failed));
            $this->newLine();
            $this->error('Failed files:');
            foreach ($failed as $fail) {
                $this->line('  - ' . $fail);
            }
        }

        $this->newLine();
        $this->info('🔍 Listing R2 bucket contents:');

        try {
            $r2Files = Storage::disk('s3')->allFiles();
            if (count($r2Files) > 0) {
                foreach ($r2Files as $r2File) {
                    $url = Storage::disk('s3')->url($r2File);
                    $this->line('  📄 ' . $r2File);
                    $this->line('     URL: ' . $url);
                }
            } else {
                $this->warn('  No files found in R2 bucket (might be a listing issue)');
            }
        } catch (\Exception $e) {
            $this->warn('  Could not list R2 contents: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
