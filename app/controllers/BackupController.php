<?php

declare(strict_types=1);

final class BackupController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('backup.manage');
        $this->view('backup/index', [
            'title'   => 'Backup & Restore',
            'backups' => BackupService::listBackups(),
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('backup.manage');
        $this->requireCsrf();

        try {
            $filename = BackupService::createBackup();
        } catch (Throwable $e) {
            $this->withError('Gagal membuat backup: ' . $e->getMessage(), 'backup/index');
        }

        AuditLogger::log('backup', 'backup', 'Backup database dibuat: ' . $filename);
        $this->withSuccess('Backup database berhasil dibuat: ' . $filename, 'backup/index');
    }

    public function download(string $filename): void
    {
        $this->requirePermission('backup.manage');
        $safeName = Security::safeFilename($filename);
        $path = BackupService::directory() . '/' . $safeName;

        if (!is_file($path)) {
            $this->withError('File backup tidak ditemukan.', 'backup/index');
        }

        AuditLogger::log('backup', 'download', 'Unduh backup: ' . $safeName);

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $safeName . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    public function destroy(string $filename): void
    {
        $this->requirePermission('backup.manage');
        $this->requireCsrf();
        $safeName = Security::safeFilename($filename);
        $path = BackupService::directory() . '/' . $safeName;

        if (is_file($path)) {
            unlink($path);
            AuditLogger::log('backup', 'delete', 'Hapus file backup: ' . $safeName);
        }
        $this->withSuccess('File backup berhasil dihapus.', 'backup/index');
    }

    /** Pulihkan database dari file backup yang tersimpan di server. */
    public function restore(string $filename): void
    {
        $this->requirePermission('backup.manage');
        $this->requireCsrf();
        $safeName = Security::safeFilename($filename);
        $path = BackupService::directory() . '/' . $safeName;

        if (!is_file($path)) {
            $this->withError('File backup tidak ditemukan.', 'backup/index');
        }

        try {
            BackupService::restoreFromFile($path);
        } catch (Throwable $e) {
            $this->withError('Gagal memulihkan database: ' . $e->getMessage(), 'backup/index');
        }

        AuditLogger::log('backup', 'restore', 'Restore database dari: ' . $safeName);
        $this->withSuccess('Database berhasil dipulihkan dari ' . $safeName, 'backup/index');
    }

    /** Pulihkan database dari file .sql yang diunggah pengguna. */
    public function restoreUpload(): void
    {
        $this->requirePermission('backup.manage');
        $this->requireCsrf();

        if (empty($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
            $this->withError('Berkas backup tidak valid atau gagal diunggah.', 'backup/index');
        }

        $tmpPath = $_FILES['backup_file']['tmp_name'];
        $originalName = (string) $_FILES['backup_file']['name'];
        if (strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION)) !== 'sql') {
            $this->withError('Hanya file berekstensi .sql yang diperbolehkan.', 'backup/index');
        }
        if (!is_uploaded_file($tmpPath)) {
            $this->withError('Berkas tidak valid.', 'backup/index');
        }

        try {
            BackupService::restoreFromFile($tmpPath);
        } catch (Throwable $e) {
            $this->withError('Gagal memulihkan database: ' . $e->getMessage(), 'backup/index');
        }

        AuditLogger::log('backup', 'restore', 'Restore database dari file unggahan: ' . Security::safeFilename($originalName));
        $this->withSuccess('Database berhasil dipulihkan dari file unggahan.', 'backup/index');
    }
}
