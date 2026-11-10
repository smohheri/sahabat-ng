<?php

use App\Models\PengaturanModel;
use App\Models\UserLogModel;

if (! function_exists('setting')) {
    /**
     * Ambil baris pengaturan aplikasi (single row, id = 1).
     * Hasil di-cache per request agar tidak query berulang.
     */
    function setting(?string $key = null, mixed $default = null): mixed
    {
        static $settings = null;

        if ($settings === null) {
            try {
                $settings = (new PengaturanModel())->find(1);
            } catch (Throwable) {
                $settings = null;
            }
        }

        if ($key === null) {
            return $settings;
        }

        if ($settings === null) {
            return $default;
        }

        $value = $settings->{$key} ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }
}

if (! function_exists('app_name')) {
    function app_name(): string
    {
        return (string) setting('nama_lksa', 'LKSA Harapan Bangsa');
    }
}

if (! function_exists('log_activity')) {
    /**
     * Simpan log aktivitas user. Aman dipanggil walau tabel belum ada.
     */
    function log_activity(string $activity, string $description = ''): void
    {
        $session = session();
        $idUser  = (int) ($session->get('id_user') ?? 0);

        if ($idUser <= 0) {
            return;
        }

        try {
            (new UserLogModel())->insert([
                'id_user'     => $idUser,
                'activity'    => $activity,
                'description' => $description,
                'ip_address'  => get_real_ip(),
                'user_agent'  => service('request')->getUserAgent()->getAgentString(),
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Gagal menyimpan activity log: ' . $e->getMessage());
        }
    }
}

if (! function_exists('get_real_ip')) {
    function get_real_ip(): string
    {
        $request = service('request');
        $ip      = $request->getIPAddress();

        return $ip !== '' ? $ip : '0.0.0.0';
    }
}

if (! function_exists('activity_color')) {
    function activity_color(string $activity): string
    {
        return match ($activity) {
            'login', 'export_pdf', 'export_excel' => 'success',
            'logout', 'backup_database', 'backup_files' => 'warning',
            'delete_user', 'delete_anak', 'delete_pengurus', 'delete_facility', 'delete_carousel_image', 'delete_hero_image', 'restore_database', 'restore_files', 'delete_backup' => 'danger',
            'add_user', 'add_anak', 'add_pengurus', 'add_facility', 'upload_logo', 'upload_dokumen', 'upload_kop', 'upload_hero_image', 'upload_about_image', 'upload_carousel_image', 'upload_foto', 'upload_kk', 'upload_akta', 'upload_pendukung' => 'primary',
            default => 'info',
        };
    }
}

if (! function_exists('activity_icon')) {
    function activity_icon(string $activity): string
    {
        return match ($activity) {
            'login' => 'right-to-bracket',
            'logout' => 'right-from-bracket',
            'add_user', 'add_anak', 'add_pengurus' => 'plus',
            'edit_user', 'edit_anak', 'edit_pengurus', 'update_settings' => 'pencil',
            'delete_user', 'delete_anak', 'delete_pengurus', 'delete_facility', 'delete_carousel_image', 'delete_hero_image' => 'trash',
            'upload_logo', 'upload_dokumen', 'upload_kop', 'upload_hero_image', 'upload_about_image', 'upload_carousel_image', 'upload_foto' => 'upload',
            'export_pdf', 'export_pdf_pengurus', 'export_pdf_dokumen', 'export_pdf_statistik', 'export_pdf_karakter', 'export_pdf_eksternal' => 'file-pdf',
            'export_excel', 'export_excel_pengurus', 'export_excel_dokumen', 'export_excel_eksternal' => 'file-excel',
            default => 'clock-rotate-left',
        };
    }
}

if (! function_exists('rupiah')) {
    function rupiah(mixed $value): string
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }
}

if (! function_exists('nomor_wa')) {
    /**
     * Normalisasi nomor telepon/WhatsApp Indonesia menjadi format internasional (62...)
     * agar bisa dipakai pada tautan wa.me. Contoh: 08123456789 -> 628123456789.
     */
    function nomor_wa(?string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', (string) $number);

        if ($number === '') {
            return '';
        }

        // Sudah format internasional.
        if (str_starts_with($number, '62')) {
            return $number;
        }

        // 0812... -> 62812...
        if (str_starts_with($number, '0')) {
            return '62' . substr($number, 1);
        }

        // 812... -> 62812...
        return '62' . $number;
    }
}

if (! function_exists('tanggal_id')) {
    function tanggal_id(?string $date, bool $withTime = false): string
    {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return '-';
        }

        $ts = strtotime($date);

        if ($ts === false) {
            return (string) $date;
        }

        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $out = date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);

        if ($withTime) {
            $out .= ' ' . date('H:i', $ts);
        }

        return $out;
    }
}

if (! function_exists('umur')) {
    /**
     * Hitung umur (tahun/bulan/hari) dari tanggal lahir.
     */
    function umur(?string $tanggalLahir): string
    {
        if (empty($tanggalLahir) || str_starts_with((string) $tanggalLahir, '0000-00-00')) {
            return '-';
        }

        try {
            $lahir = new \DateTime($tanggalLahir);
        } catch (\Throwable) {
            return '-';
        }

        $diff = $lahir->diff(new \DateTime());

        if ($diff->y > 0) {
            return $diff->y . ' tahun';
        }

        if ($diff->m > 0) {
            return $diff->m . ' bulan';
        }

        return max(0, $diff->d) . ' hari';
    }
}

if (! function_exists('role_home')) {
    function role_home(?string $role): string
    {
        $role = strtolower(trim((string) $role));

        if (in_array($role, ['guru', 'pengajar'], true)) {
            return site_url('guru');
        }

        if ($role === 'anak') {
            return site_url('anak');
        }

        return site_url('admin');
    }
}

if (! function_exists('upload_path')) {
    function upload_path(string $folder = ''): string
    {
        $base = rtrim(FCPATH . 'uploads', '/\\');

        return $folder === '' ? $base : $base . DIRECTORY_SEPARATOR . trim($folder, '/\\');
    }
}

if (! function_exists('upload_url')) {
    function upload_url(string $folder, ?string $filename): string
    {
        if (empty($filename)) {
            return '';
        }

        return base_url('uploads/' . trim($folder, '/') . '/' . $filename);
    }
}

if (! function_exists('store_upload')) {
    /**
     * Simpan file upload ke public/uploads/{folder}. Mengembalikan nama file
     * atau null jika tidak ada file / gagal.
     */
    function store_upload(?object $file, string $folder, string $prefix = 'file', array $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'pdf'], int $maxSize = 2097152): ?string
    {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ext = strtolower($file->getClientExtension() ?: $file->guessExtension() ?: 'bin');

        if (! in_array($ext, $allowedExt, true)) {
            return null;
        }

        if ($file->getSize() > $maxSize) {
            log_message('error', 'Upload ditolak: ukuran melebihi batas (' . $file->getSize() . ' bytes).');

            return null;
        }

        // Tolak tipe MIME yang jelas berbahaya walau ekstensinya lolos.
        $mime = strtolower((string) $file->getMimeType());
        $blockedMimes = ['application/x-httpd-php', 'text/html', 'text/x-php', 'application/javascript', 'application/x-msdownload', 'application/x-sh'];
        if (in_array($mime, $blockedMimes, true)) {
            log_message('error', 'Upload ditolak: tipe MIME tidak diizinkan (' . $mime . ').');

            return null;
        }

        $dir = upload_path($folder);

        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            return null;
        }

        $name = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        try {
            $file->move($dir, $name);
        } catch (Throwable $e) {
            log_message('error', 'Upload gagal: ' . $e->getMessage());

            return null;
        }

        return $name;
    }
}

if (! function_exists('delete_upload')) {
    function delete_upload(string $folder, ?string $filename): void
    {
        if (empty($filename)) {
            return;
        }

        $path = upload_path($folder) . DIRECTORY_SEPARATOR . basename($filename);

        if (is_file($path)) {
            @unlink($path);
        }
    }
}

if (! function_exists('generate_favicon')) {
    /**
     * Buat favicon.png (64x64) dari logo yang terunggah.
     * Mendukung PNG/JPG/GIF/WebP (bila GD mendukung), lalu simpan sebagai PNG
     * agar kompatibel dipakai sebagai favicon browser.
     */
    function generate_favicon(?string $filename, string $folder = 'logos', string $outputName = 'favicon.png'): bool
    {
        if (empty($filename)) {
            return false;
        }

        $path = upload_path($folder) . DIRECTORY_SEPARATOR . basename($filename);

        if (! is_file($path)) {
            return false;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $src = match ($ext) {
            'jpg', 'jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($path) : false,
            'gif'         => function_exists('imagecreatefromgif') ? @imagecreatefromgif($path) : false,
            'webp'        => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default       => function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : false,
        };

        if ($src === false) {
            return false;
        }

        $width  = imagesx($src);
        $height = imagesy($src);
        $side   = min($width, $height);
        $srcX   = (int) floor(($width - $side) / 2);
        $srcY   = (int) floor(($height - $side) / 2);

        $size = 64;
        $dst  = imagecreatetruecolor($size, $size);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);
        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $size, $size, $side, $side);

        $out = FCPATH . $outputName;

        return (bool) imagepng($dst, $out, 9);
    }
}

if (! function_exists('favicon_url')) {
    /**
     * URL favicon: gunakan favicon.png yang digenerate dari logo (termasuk WebP),
     * fallback ke favicon.ico bawaan bila belum ada.
     */
    function favicon_url(): string
    {
        if (is_file(FCPATH . 'favicon.png')) {
            return base_url('favicon.png');
        }

        $logo = (string) setting('logo');

        if ($logo !== '' && generate_favicon($logo)) {
            return base_url('favicon.png');
        }

        return base_url('favicon.ico');
    }
}

if (! function_exists('anak_export_columns')) {
    /**
     * Daftar kolom yang bisa dipilih saat ekspor data anak (key => label).
     */
    function anak_export_columns(): array
    {
        return [
            'no_registrasi'  => 'No. Registrasi',
            'nama_anak'      => 'Nama Anak',
            'jenis_kelamin'  => 'Jenis Kelamin',
            'tempat_lahir'   => 'Tempat Lahir',
            'tanggal_lahir'  => 'Tanggal Lahir',
            'umur'           => 'Umur',
            'kategori'       => 'Kategori',
            'pendidikan'     => 'Pendidikan',
            'status_anak'    => 'Status',
            'nama_sekolah'   => 'Sekolah',
            'biaya_spp'      => 'Biaya Pendidikan',
            'nama_provinsi'  => 'Provinsi',
            'nama_kabupaten' => 'Kabupaten',
        ];
    }
}

if (! function_exists('anak_graphable_columns')) {
    /**
     * Kolom kategorikal yang bisa dijadikan grafik ringkasan.
     */
    function anak_graphable_columns(): array
    {
        return ['jenis_kelamin', 'kategori', 'pendidikan', 'status_anak', 'nama_provinsi', 'nama_kabupaten'];
    }
}

if (! function_exists('bar_chart_file')) {
    /**
     * Buat file PNG bar chart horizontal dari data [label => jumlah] memakai GD.
     * Mengembalikan path file PNG (di writable/cache/charts) atau null bila gagal.
     */
    function bar_chart_file(array $counts, string $label): ?string
    {
        if ($counts === []) {
            return null;
        }

        arsort($counts);

        if (count($counts) > 12) {
            $top          = array_slice($counts, 0, 11, true);
            $rest         = array_slice($counts, 11);
            $top['Lainnya'] = array_sum($rest);
            $counts       = $top;
        }

        $values = array_values($counts);
        $max    = max(1, max($values));

        $fontTitle = 4;
        $font      = 3;
        $barH      = 16;
        $gap       = 5;
        $left      = 150;
        $rightPad  = 55;
        $top       = 30;
        $width     = 560;
        $height    = $top + count($counts) * ($barH + $gap) + 12;

        $img = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($img, 255, 255, 255);
        $ink   = imagecolorallocate($img, 30, 41, 59);
        $barA  = imagecolorallocate($img, 79, 70, 229);
        $barB  = imagecolorallocate($img, 96, 165, 250);
        $muted = imagecolorallocate($img, 100, 116, 139);

        imagefilledrectangle($img, 0, 0, $width, $height, $white);
        imagestring($img, $fontTitle, 12, 12, (string) $label, $ink);
        imageline($img, $left - 8, $top - 10, $left - 8, $height - 10, $ink);

        $y = $top;
        $i = 0;

        foreach ($counts as $key => $value) {
            $labelText = (string) $key;
            if (strlen($labelText) > 28) {
                $labelText = substr($labelText, 0, 27) . '...';
            }

            imagestring($img, $font, 12, $y + 5, $labelText, $ink);

            $barMax = $width - $left - $rightPad;
            $barLen = max(4, (int) round(($value / $max) * $barMax));

            $color = ($i % 2 === 0) ? $barA : $barB;
            imagefilledrectangle($img, $left, $y, $left + $barLen, $y + $barH, $color);
            imagestring($img, $font, $left + $barLen + 8, $y + 5, (string) $value, $muted);

            $y += $barH + $gap;
            $i++;
        }

        $dir = WRITEPATH . 'cache/charts';
        if (! is_dir($dir) && ! mkdir($dir, 0775, true) && ! is_dir($dir)) {
            return null;
        }

        $file = $dir . '/chart_' . bin2hex(random_bytes(5)) . '.png';

        return imagepng($img, $file) ? $file : null;
    }
}

if (! function_exists('status_badge')) {
    function status_badge(?string $status): string
    {
        return match (strtolower(trim((string) $status))) {
            'aktif', 'hadir', 'completed', 'reviewed', 'success' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
            'nonaktif', 'alpha', 'draft', 'danger' => 'bg-rose-100 text-rose-700 ring-rose-600/20',
            'alumni', 'izin', 'warning' => 'bg-amber-100 text-amber-700 ring-amber-600/20',
            'sakit', 'info' => 'bg-sky-100 text-sky-700 ring-sky-600/20',
            default => 'bg-slate-100 text-slate-600 ring-slate-500/20',
        };
    }
}

if (! function_exists('active_class')) {
    function active_class(string $segment, string $class = 'bg-indigo-600 text-white'): string
    {
        $current = trim(service('request')->getPath(), '/');
        $needle  = trim($segment, '/');

        return ($current === $needle || str_starts_with($current, $needle . '/')) ? $class : '';
    }
}
