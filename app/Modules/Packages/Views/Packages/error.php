<?php

declare(strict_types=1);

$error = $error ?? 'An unexpected package error occurred.';

?>

<div class="page-header">

    <div>

        <h1>

            Package Error

        </h1>

        <p>

            The requested package operation could not be completed.

        </p>

    </div>

</div>

<div class="card">

    <div
        class="alert alert-danger"
        style="margin-bottom:20px;"
    >

        <strong>

            Error

        </strong>

        <br><br>

        <?= htmlspecialchars($error) ?>

    </div>

    <div class="page-actions">

        <a
            href="<?= config('app.base_path') ?>/packages"
            class="btn btn-primary"
        >

            ← Back to Packages

        </a>

    </div>

</div>