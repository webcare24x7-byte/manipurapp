<?php

declare(strict_types=1);

$title = $title ?? 'ManipurApp';
$basePath = (string) config('app.base_path');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover"
    >
    <meta name="theme-color" content="#ffffff">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="ManipurApp">
    <meta name="description" content="ManipurApp — local services, food, travel and more.">

    <link rel="manifest" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/assets/memberapp/manifest.webmanifest">
    <link rel="icon" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/assets/memberapp/icons/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/assets/memberapp/css/memberapp.css">

    <title><?= htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body class="memberapp-body">
    <?= $content ?>

    <script>
        window.ManipurApp = {
            basePath: <?= json_encode($basePath, JSON_UNESCAPED_SLASHES) ?>,
            memberSessionUrl: <?= json_encode($basePath . '/member/session', JSON_UNESCAPED_SLASHES) ?>
        };
    </script>
    <script src="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/assets/memberapp/js/memberapp.js"></script>
</body>
</html>
