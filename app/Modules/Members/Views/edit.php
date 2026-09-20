<h1>Edit Member</h1>

<p class="page-subtitle">

Update member information.

</p>

<div class="card">

    <form
        method="POST"
        enctype="multipart/form-data"
        action="<?= config('app.base_path') ?>/members/<?= $member['id'] ?>"
    >

        <?php require __DIR__ . '/form.php'; ?>

    </form>

</div>