<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Backup extends BaseController
{
    private string $backupDir;

    public function __construct()
    {
        $this->backupDir = WRITEPATH . 'backups';

        if (! is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0775, true);
        }
    }

    public function index()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $files = [];

        foreach (glob($this->backupDir . DIRECTORY_SEPARATOR . '*') ?: [] as $path) {
            if (! is_file($path)) {
                continue;
            }

            $name = basename($path);
            $files[] = [
                'name'     => $name,
                'type'     => str_starts_with($name, 'files_') ? 'files' : 'database',
                'size'     => filesize($path),
                'modified' => filemtime($path),
            ];
        }

        usort($files, static fn ($a, $b) => $b['modified'] <=> $a['modified']);

        $pagination = $this->paginate($files, 10);

        return $this->render('admin/backup', [
            'title'     => 'Backup & Restore - ' . app_name(),
            'pageTitle' => 'Backup & Restore',
            'files'     => $pagination['items'],
            'page'      => $pagination['page'],
            'perPage'   => $pagination['perPage'],
            'total'     => $pagination['total'],
        ]);
    }

    public function backup_database()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $name = 'db_' . date('Ymd_His') . '.sql';
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $name;
        $sql  = $this->generateDatabaseSql();

        if ($sql === '' || file_put_contents($path, $sql) === false) {
            return redirect()->back()->with('error', 'Gagal membuat backup database.');
        }

        log_activity('backup_database', 'Membuat backup database: ' . $name);

        return redirect()->back()->with('success', 'Backup database berhasil dibuat: ' . $name);
    }

    public function backup_files()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        if (! class_exists('ZipArchive')) {
            return redirect()->back()->with('error', 'Ekstensi ZipArchive tidak tersedia di server.');
        }

        $name = 'files_' . date('Ymd_His') . '.zip';
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $name;
        $zip  = new \ZipArchive();

        if ($zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'Gagal membuat backup file.');
        }

        $base = upload_path();
        $iter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS));

        foreach ($iter as $file) {
            if ($file->isFile()) {
                $relative = str_replace($base . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $zip->addFile($file->getPathname(), $relative);
            }
        }

        $zip->close();
        log_activity('backup_files', 'Membuat backup file uploads: ' . $name);

        return redirect()->back()->with('success', 'Backup file berhasil dibuat: ' . $name);
    }

    public function download_backup(string $type, string $filename)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $filename = basename($filename);
        $path     = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        if (! is_file($path)) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan.');
        }

        log_activity('download_backup', 'Mengunduh backup: ' . $filename);

        return $this->response->download($path, null)->setFileName($filename);
    }

    public function delete_backup()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $filename = basename((string) $this->request->getPost('filename'));
        $path     = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        if ($filename === '' || ! is_file($path)) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan.');
        }

        unlink($path);
        log_activity('delete_backup', 'Menghapus backup: ' . $filename);

        return redirect()->back()->with('success', 'File backup berhasil dihapus.');
    }

    public function restore_database()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $file = $this->request->getFile('sql_file');

        if ($file === null || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Pilih file SQL untuk restore.');
        }

        $sql = (string) file_get_contents($file->getTempName());
        $db  = db_connect();

        $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
        $executed   = 0;

        foreach ($statements as $statement) {
            if ($statement === '' || str_starts_with($statement, '--')) {
                continue;
            }

            try {
                $db->query($statement);
                $executed++;
            } catch (\Throwable $e) {
                log_message('error', 'Restore statement gagal: ' . $e->getMessage());
            }
        }

        log_activity('restore_database', 'Restore database, ' . $executed . ' statement dieksekusi');

        return redirect()->back()->with('success', 'Restore database selesai. ' . $executed . ' statement dieksekusi.');
    }

    public function restore_files()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        if (! class_exists('ZipArchive')) {
            return redirect()->back()->with('error', 'Ekstensi ZipArchive tidak tersedia di server.');
        }

        $file = $this->request->getFile('zip_file');

        if ($file === null || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Pilih file ZIP untuk restore.');
        }

        $zip = new \ZipArchive();

        if ($zip->open($file->getTempName()) === true) {
            $zip->extractTo(upload_path());
            $zip->close();
            log_activity('restore_files', 'Restore file uploads dari ZIP');

            return redirect()->back()->with('success', 'Restore file selesai.');
        }

        return redirect()->back()->with('error', 'File ZIP tidak valid.');
    }

    public function delete_temp_file()
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $filename = basename((string) $this->request->getPost('filename'));
        $path     = WRITEPATH . 'temp' . DIRECTORY_SEPARATOR . $filename;

        if ($filename !== '' && is_file($path)) {
            unlink($path);
        }

        return $this->response->setJSON(['status' => true]);
    }

    private function generateDatabaseSql(): string
    {
        $db     = db_connect();
        $tables = $db->listTables();
        $out    = "-- Backup database " . date('Y-m-d H:i:s') . "\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $create = $db->query('SHOW CREATE TABLE `' . $table . '`')->getRowArray();
            $ddl    = $create['Create Table'] ?? array_values($create)[1] ?? '';

            $out .= "DROP TABLE IF EXISTS `{$table}`;\n" . $ddl . ";\n\n";

            $rows = $db->table($table)->get()->getResultArray();

            foreach ($rows as $row) {
                $columns = array_map(static fn ($col) => '`' . $col . '`', array_keys($row));
                $values  = array_map(static function ($value) use ($db) {
                    if ($value === null) {
                        return 'NULL';
                    }

                    return "'" . $db->escapeString((string) $value) . "'";
                }, array_values($row));

                $out .= 'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ");\n";
            }

            $out .= "\n";
        }

        $out .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $out;
    }
}
