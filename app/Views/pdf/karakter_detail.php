<?php
$anak        = $anak ?? [];
$history     = $history ?? [];
$indicators  = $indicators ?? [];
$aspectAvg   = $aspectAvg ?? [];

$monthNames = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$num        = static fn ($value): string => number_format((float) $value, 2);
?>

<h2>Identitas Anak</h2>
<table class="data">
    <tbody>
        <tr><th style="width:32%">Nama</th><td><?= esc($anak['nama_anak'] ?? '-') ?></td></tr>
        <tr><th>No. Registrasi</th><td><?= esc($anak['no_registrasi'] ?: '-') ?></td></tr>
        <tr><th>Jenis Kelamin</th><td><?= ($anak['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan' ?></td></tr>
        <tr><th>Tempat, Tanggal Lahir</th><td><?= esc(trim(($anak['tempat_lahir'] ?? '') . ', ' . ($anak['tanggal_lahir'] ? tanggal_id($anak['tanggal_lahir']) : ''), ', ')) ?></td></tr>
        <tr><th>Pendidikan</th><td><?= esc($anak['pendidikan'] ?: '-') ?></td></tr>
        <tr><th>Sekolah</th><td><?= esc($anak['nama_sekolah'] ?: '-') ?></td></tr>
        <tr><th>Status</th><td><?= esc($anak['status_anak'] ?: '-') ?></td></tr>
    </tbody>
</table>

<?php if ($aspectAvg !== []): ?>
    <h2>Rata-rata per Aspek</h2>
    <table class="data">
        <thead>
            <tr><th>Aspek</th><th>Rata-rata</th></tr>
        </thead>
        <tbody>
            <?php foreach ($aspectAvg as $name => $score): ?>
                <tr><td><?= esc((string) $name) ?></td><td><?= $num($score) ?> / 4</td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
$grouped = [];
foreach ($indicators as $row) {
    $grouped[$row['aspect_name']][] = $row;
}
?>

<?php if ($grouped !== []): ?>
    <h2>Rincian Skor Indikator</h2>
    <?php foreach ($grouped as $aspectName => $rows): ?>
        <h3><?= esc((string) $aspectName) ?></h3>
        <table class="data">
            <thead>
                <tr><th>Indikator</th><th style="width:90px">Rata-rata</th><th style="width:80px">Jumlah</th></tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= esc($row['indicator_name'] ?? '-') ?><?= ! empty($row['indicator_code']) ? ' <span class="muted">(' . esc($row['indicator_code']) . ')</span>' : '' ?></td>
                        <td><?= $num($row['avg_score'] ?? 0) ?></td>
                        <td><?= (int) ($row['total'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
<?php endif; ?>

<h2>Riwayat Penilaian</h2>
<?php if ($history === []): ?>
    <p class="muted">Belum ada riwayat penilaian.</p>
<?php else: ?>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Penilai</th>
                <th>Tipe</th>
                <th style="width:80px">Rata-rata</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $item): ?>
                <tr>
                    <td><?= esc(tanggal_id($item['assessment_date'] ?? null)) ?></td>
                    <td><?= esc($item['assessor_name'] ?: '-') ?></td>
                    <td><?= ($item['assessor_type'] ?? '') === 'anak_asuh' ? 'Anak Asuh' : 'Guru' ?></td>
                    <td><?= $num($item['avg_score'] ?? 0) ?></td>
                    <td><?= esc($item['notes'] ?: '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>