<?php
/**
 * Render markdown sederhana (heading, list, bold, code) tanpa dependency tambahan.
 */
$renderMarkdown = static function (string $markdown): string {
    $lines = preg_split('/\r\n|\r|\n/', $markdown) ?: [];
    $html  = '';
    $inList = false;

    $inline = static function (string $text): string {
        $text = esc($text, 'html');
        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/`([^`]+)`/s', '<code class="rounded bg-stone-100 px-1.5 py-0.5 font-mono text-[0.85em] text-stone-700">$1</code>', $text) ?? $text;
        $text = preg_replace('/\[(.+?)\]\((https?:\/\/[^\s)]+)\)/s', '<a href="$2" target="_blank" rel="noopener" class="text-emerald-700 hover:underline">$1</a>', $text) ?? $text;

        return $text;
    };

    foreach ($lines as $line) {
        $trim = trim($line);

        if ($trim === '') {
            if ($inList) {
                $html .= '</ul>';
                $inList = false;
            }
            continue;
        }

        if (preg_match('/^(#{1,4})\s+(.*)$/', $trim, $m)) {
            if ($inList) {
                $html .= '</ul>';
                $inList = false;
            }
            $level = strlen($m[1]);
            $size  = [1 => 'text-2xl', 2 => 'text-xl', 3 => 'text-lg', 4 => 'text-base'][$level] ?? 'text-base';
            $html .= '<h' . $level . ' class="' . $size . ' font-extrabold text-stone-900' . ($level > 1 ? ' mt-6' : '') . '">' . $inline($m[2]) . '</h' . $level . '>';
            continue;
        }

        if (preg_match('/^[-*]\s+(.*)$/', $trim, $m)) {
            if (! $inList) {
                $html .= '<ul class="mt-3 space-y-2">';
                $inList = true;
            }
            $html .= '<li class="flex items-start gap-2 text-sm text-stone-600"><i class="fa-solid fa-circle text-[5px] mt-2 text-emerald-400"></i><span>' . $inline($m[1]) . '</span></li>';
            continue;
        }

        if ($inList) {
            $html .= '</ul>';
            $inList = false;
        }

        $html .= '<p class="mt-3 text-sm leading-relaxed text-stone-600">' . $inline($trim) . '</p>';
    }

    if ($inList) {
        $html .= '</ul>';
    }

    return $html;
};
?>

<div class="space-y-6">
    <div class="page-header">
        <div>
            <h2 class="page-title">Changelog</h2>
            <p class="page-subtitle">Catatan perubahan dan riwayat pengembangan aplikasi.</p>
        </div>
        <span class="inline-flex items-center gap-2 self-start rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
            <i class="fa-solid fa-code-branch"></i> SAHABAT-NG
        </span>
    </div>

    <div class="card p-6 sm:p-8">
        <?php if (trim($changelog) === '' || $changelog === 'Belum ada changelog.'): ?>
            <div class="py-10 text-center">
                <i class="fa-solid fa-code-branch mb-3 text-3xl text-stone-300"></i>
                <p class="page-subtitle">Belum ada changelog yang tersedia.</p>
            </div>
        <?php else: ?>
            <div class="changelog-content">
                <?= $renderMarkdown($changelog) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
