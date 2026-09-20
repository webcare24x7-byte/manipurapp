<h1>Add Member</h1>

<p class="page-subtitle">

Register a new church member.

</p>

<div class="card">

    <form
        method="POST"
        enctype="multipart/form-data"
        action="<?= config('app.base_path') ?>/members"
    >

        <?php require __DIR__ . '/form.php'; ?>

    </form>

</div>