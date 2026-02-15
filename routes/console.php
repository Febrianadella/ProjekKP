<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command(
    'surat:migrate-to-r2
    {--source=public : Disk sumber file lama}
    {--target=r2 : Disk tujuan (R2)}
    {--paths=surat-masuk,surat-keluar : Direktori yang dipindahkan, pisahkan dengan koma}
    {--overwrite : Timpa file jika sudah ada di disk target}
    {--dry-run : Simulasi tanpa upload file}
    {--clear-proxy : Hapus env proxy pada proses command ini}',
    function () {
        $source = (string) $this->option('source');
        $target = (string) $this->option('target');
        $overwrite = (bool) $this->option('overwrite');
        $dryRun = (bool) $this->option('dry-run');
        $clearProxy = (bool) $this->option('clear-proxy');

        if ($clearProxy) {
            foreach (['HTTP_PROXY', 'HTTPS_PROXY', 'ALL_PROXY', 'GIT_HTTP_PROXY', 'GIT_HTTPS_PROXY'] as $key) {
                putenv($key . '=');
                unset($_ENV[$key], $_SERVER[$key]);
            }
        }

        $paths = collect(explode(',', (string) $this->option('paths')))
            ->map(fn (string $path) => trim($path))
            ->filter()
            ->map(fn (string $path) => ltrim(str_replace('\\', '/', $path), '/'))
            ->values()
            ->all();

        if (empty($paths)) {
            $this->error('Opsi --paths kosong. Contoh: --paths=surat-masuk,surat-keluar');
            return self::FAILURE;
        }

        try {
            $sourceDisk = Storage::disk($source);
            $targetDisk = Storage::disk($target);
        } catch (\Throwable $e) {
            $this->error('Gagal inisialisasi disk storage: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Migrasi file surat dimulai...');
        $this->line('Sumber : ' . $source);
        $this->line('Target : ' . $target);
        $this->line('Paths  : ' . implode(', ', $paths));

        if ($dryRun) {
            $this->comment('Mode dry-run aktif. Tidak ada file yang benar-benar dipindahkan.');
        }

        $files = [];

        foreach ($paths as $path) {
            try {
                $pathFiles = $sourceDisk->allFiles($path);
            } catch (\Throwable $e) {
                $this->warn("Gagal membaca direktori '{$path}': {$e->getMessage()}");
                continue;
            }

            foreach ($pathFiles as $file) {
                $files[] = ltrim(str_replace('\\', '/', $file), '/');
            }
        }

        $files = array_values(array_unique($files));
        sort($files);

        if (empty($files)) {
            $this->warn('Tidak ada file ditemukan pada path yang dipilih.');
            return self::SUCCESS;
        }

        $copied = 0;
        $skippedExists = 0;
        $wouldCopy = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $file) {
            try {
                $targetExists = !$dryRun && $targetDisk->exists($file);

                if ($targetExists && !$overwrite) {
                    $skippedExists++;
                    $bar->advance();
                    continue;
                }

                if ($dryRun) {
                    $wouldCopy++;
                    $bar->advance();
                    continue;
                }

                $stream = $sourceDisk->readStream($file);
                if ($stream === false) {
                    $failed++;
                    $bar->advance();
                    continue;
                }

                $writeOk = (bool) $targetDisk->writeStream($file, $stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }

                if ($writeOk) {
                    $copied++;
                } else {
                    $failed++;
                }
            } catch (\Throwable $e) {
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Status', 'Jumlah'],
            [
                ['Total file terdeteksi', (string) count($files)],
                ['Berhasil dipindahkan', (string) $copied],
                ['Akan dipindahkan (dry-run)', (string) $wouldCopy],
                ['Dilewati (sudah ada)', (string) $skippedExists],
                ['Gagal', (string) $failed],
            ]
        );

        if ($failed > 0) {
            $this->warn('Selesai dengan sebagian kegagalan. Cek konfigurasi disk/proxy lalu jalankan ulang.');
            return self::FAILURE;
        }

        $this->info('Migrasi selesai.');
        return self::SUCCESS;
    }
)->purpose('Migrasi file surat dari disk lokal ke Cloudflare R2');
