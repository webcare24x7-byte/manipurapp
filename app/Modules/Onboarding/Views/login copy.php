<h1><?= $title ?></h1>

<?php if (!empty($error)): ?>

<p style="color:red">

    <?= htmlspecialchars($error) ?>

</p>

<?php endif; ?>

<form method="POST" action="<?= config('app.base_path') ?>/login">

    <p>

        <label>Emails</label><br>

        <input
            type="email"
            name="email"
            required
        >

    </p>

    <p>

        <label>Passwordxx</label><br>

        <input
            type="password"
            name="password"
            required
        >

    </p>

    <button type="submit">

        Login

    </button>

</form>

<hr>

<p>

<a href="<?= config('app.base_path') ?>/register">

Register Business

</a>

</p>