<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= htmlspecialchars($title ?? 'ManipurApp') ?></title>

    <meta
        name="description"
        content="ManipurApp - Local services, tourism and business platform for Manipur"
    >

    <link
        rel="stylesheet"
        href="<?= config('app.base_path') ?>/assets/css/app.css"
    >

</head>

<body>

<?= $content ?>

</body>

</html>