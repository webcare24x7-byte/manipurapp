<div class="page-header">

    <div>

        <h1>

            Edit Lookup Value

        </h1>

        <p>

            <?= htmlspecialchars($lookupType['name']) ?>

        </p>

    </div>

</div>

<form
    method="POST"
    action="<?= config('app.base_path') ?>/lookup-values/<?= $lookupValue['id'] ?>"
>

    <?php require __DIR__ . '/form.php'; ?>

</form>