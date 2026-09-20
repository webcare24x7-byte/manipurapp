<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>

        <?= htmlspecialchars($title ?? 'ManipurApp') ?>

    </title>

    <link
        rel="stylesheet"
        href="<?= config('app.base_path') ?>/assets/css/app.css"
    >

    <?php foreach ($stylesheets ?? [] as $stylesheet): ?>

    <link
        rel="stylesheet"
        href="<?= config('app.base_path') ?>/assets/css/modules/<?= htmlspecialchars($stylesheet) ?>"
    >

<?php endforeach; ?>

</head>

<body>

<div class="app-layout">

    <?php require base_path('resources/views/partials/sidebar.php'); ?>

    <div class="app-main">

        <?php require base_path('resources/views/partials/topbar.php'); ?>

        <main class="page-content">
            <?php foreach (get_flash() as $flash): ?>

<div class="flash flash-<?= htmlspecialchars($flash['type']) ?>">

    <span class="flash-icon">
        <?php
        echo match ($flash['type']) {
            'success' => '✓',
            'error'   => '✕',
            'warning' => '⚠',
            default   => 'ℹ',
        };
        ?>
    </span>

    <span class="flash-message">
        <?= htmlspecialchars($flash['message']) ?>
    </span>

    <button
        class="flash-close"
        type="button"
        onclick="this.parentElement.remove();">
        &times;
    </button>

</div>

<?php endforeach; ?>

            <?= $content ?>

        </main>

        <?php require base_path('resources/views/partials/footer.php'); ?>
        <script src="<?= config('app.base_path') ?>/assets/js/flash.js"></script>
    </div>

</div>

</body>

</html>