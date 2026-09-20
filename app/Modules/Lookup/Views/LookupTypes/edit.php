<div class="page-header">

    <div>

        <h1>

            Edit Lookup Type

        </h1>

    </div>

</div>

<form
    method="POST"
    action="<?= config('app.base_path') ?>/lookup-types/<?= $lookupType['id'] ?>"
>

    <?php require __DIR__ . '/form.php'; ?>

</form>