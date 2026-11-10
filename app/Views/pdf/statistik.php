<?php
$stats = $stats ?? [];
$num   = static fn ($value): string => number_format((int) $value, 0, ',', '.');
?>

<h2>Ringkasan Umum</h2>
<table class="data">
    <tbody>
        <tr><th style="width:45%">Total Anak</th><td><?= $num($stats['total'] ?? 0) ?></td></tr>
        <tr><th>Anak Aktif</th><td><?= $num($stats['aktif'] ?? 0) ?></td></tr>
        <tr><th>Alumni</th><td><?= $num($stats['alumni'] ?? 0) ?></td></tr>
        <tr><th>Nonaktif</th><td><?= $num($stats['nonaktif'] ?? 0) ?></td></tr>
        <tr><th>Laki-laki</th><td><?= $num($stats['laki'] ?? 0) ?></td></tr>
        <tr><th>Perempuan</th><td><?= $num($stats['perempuan'] ?? 0) ?></td></tr>
    </tbody>
</table>

<h2>Berdasarkan Pendidikan</h2>
<table class="data">
    <thead>
        <tr><th>Jenjang</th><th>Jumlah</th></tr>
    </thead>
    <tbody>
        <tr><td>TK / PAUD</td><td><?= $num($stats['tk'] ?? 0) ?></td></tr>
        <tr><td>SD / MI</td><td><?= $num($stats['sd'] ?? 0) ?></td></tr>
        <tr><td>SMP / MTs</td><td><?= $num($stats['smp'] ?? 0) ?></td></tr>
        <tr><td>SMA / SMK / MA</td><td><?= $num($stats['sma'] ?? 0) ?></td></tr>
        <tr><td>Perguruan Tinggi</td><td><?= $num($stats['pt'] ?? 0) ?></td></tr>
    </tbody>
</table>

<h2>Berdasarkan Kelompok Usia</h2>
<table class="data">
    <thead>
        <tr><th>Kelompok Usia</th><th>Jumlah</th></tr>
    </thead>
    <tbody>
        <tr><td>0 - 5 tahun</td><td><?= $num($stats['usia_0_5'] ?? 0) ?></td></tr>
        <tr><td>6 - 12 tahun</td><td><?= $num($stats['usia_5_12'] ?? 0) ?></td></tr>
        <tr><td>13 - 17 tahun</td><td><?= $num($stats['usia_13_17'] ?? 0) ?></td></tr>
        <tr><td>Lebih dari 17 tahun</td><td><?= $num($stats['usia_17'] ?? 0) ?></td></tr>
    </tbody>
</table>

<?php if (! empty($stats['kategori'])): ?>
    <h2>Berdasarkan Kategori</h2>
    <table class="data">
        <thead>
            <tr><th>Kategori</th><th>Jumlah</th></tr>
        </thead>
        <tbody>
            <?php foreach ($stats['kategori'] as $label => $value): ?>
                <tr><td><?= esc((string) $label) ?></td><td><?= $num($value) ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if (! empty($stats['tahun_masuk'])): ?>
    <h2>Berdasarkan Tahun Masuk</h2>
    <table class="data">
        <thead>
            <tr><th>Tahun</th><th>Jumlah</th></tr>
        </thead>
        <tbody>
            <?php foreach ($stats['tahun_masuk'] as $label => $value): ?>
                <tr><td><?= esc((string) $label) ?></td><td><?= $num($value) ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
