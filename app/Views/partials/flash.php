<?php
$flashSuccess = session()->getFlashdata('success');
$flashError   = session()->getFlashdata('error');
$flashInfo    = session()->getFlashdata('info');
?>

<?php if (! empty($flashSuccess)): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" class="mb-4 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        <i class="fa-solid fa-circle-check mt-0.5 text-emerald-500"></i>
        <p class="flex-1"><?= esc($flashSuccess) ?></p>
        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
    </div>
<?php endif; ?>

<?php if (! empty($flashError)): ?>
    <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-500"></i>
        <p class="flex-1"><?= esc($flashError) ?></p>
        <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-xmark"></i></button>
    </div>
<?php endif; ?>

<?php if (! empty($flashInfo)): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" class="mb-4 flex items-start gap-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
        <i class="fa-solid fa-circle-info mt-0.5 text-sky-500"></i>
        <p class="flex-1"><?= esc($flashInfo) ?></p>
        <button type="button" @click="show = false" class="text-sky-500 hover:text-sky-700"><i class="fa-solid fa-xmark"></i></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        <ul class="list-disc space-y-1 pl-5">
            <?php foreach ((array) session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
