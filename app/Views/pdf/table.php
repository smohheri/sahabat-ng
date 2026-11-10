<?php
$headers = $headers ?? [];
$rows    = $rows ?? [];
?>

<?php if ($rows === []): ?>
    <p class="muted" style="margin-top:12px">Tidak ada data untuk ditampilkan.</p>
<?php else: ?>
    <table class="data">
        <thead>
            <tr>
                <?php foreach ($headers as $header): ?>
                    <th><?= esc((string) $header) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <?php foreach ((array) $row as $cell): ?>
                        <td><?= esc((string) $cell) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
