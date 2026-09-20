<div class="page-header">

    <div>

        <h1>

            Add Lookup Type

        </h1>

        <p>

            Create a new lookup list.

        </p>

    </div>

</div>

<form
    method="POST"
    action="<?= config('app.base_path') ?>/lookup-types"
>

    <?php require __DIR__ . '/form.php'; ?>

</form>