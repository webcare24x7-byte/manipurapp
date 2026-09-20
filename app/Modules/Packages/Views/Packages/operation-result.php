<?php

declare(strict_types=1);

$result = $result;

?>

<div class="page-header">

    <div>

        <h1>

            Package Installed Successfully

        </h1>

        <p>

            Package Code:
            <strong>

                <?= htmlspecialchars($packageCode) ?>

            </strong>

        </p>

    </div>

</div>

<div class="card">

    <h3>

        Lookup Types

    </h3>

    <table class="table">

        <tr>

            <td>Created</td>

            <td>

                <?= $result->lookupTypesCreated ?>

            </td>

        </tr>

        <tr>

            <td>Existing</td>

            <td>

                <?= $result->lookupTypesExisting ?>

            </td>

        </tr>

        <tr>

            <td>Restored</td>

            <td>

                <?= $result->lookupTypesRestored ?>

            </td>

        </tr>

    </table>

    <br>

    <h3>

        Lookup Values

    </h3>

    <table class="table">

        <tr>

            <td>Created</td>

            <td>

                <?= $result->lookupValuesCreated ?>

            </td>

        </tr>

        <tr>

            <td>Existing</td>

            <td>

                <?= $result->lookupValuesExisting ?>

            </td>

        </tr>

        <tr>

            <td>Restored</td>

            <td>

                <?= $result->lookupValuesRestored ?>

            </td>

        </tr>

    </table>

    <?php if (!empty($result->messages)): ?>

        <br>

        <h3>

            Messages

        </h3>

        <ul>

            <?php foreach ($result->messages as $message): ?>

                <li>

                    <?= htmlspecialchars($message) ?>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>

    <br>

    <a
        href="<?= config('app.base_path') ?>/packages"
        class="btn btn-primary"
    >

        Back to Packages

    </a>

</div>