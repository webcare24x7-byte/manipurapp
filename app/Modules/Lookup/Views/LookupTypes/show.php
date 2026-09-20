<?php

declare(strict_types=1);

?>

<div class="page-header">

    <div>

        <h1>

            <?= htmlspecialchars(
                $lookupType['name']
            ) ?>

        </h1>

        <p>

            Manage lookup values.

        </p>

    </div>

    <div class="page-actions">

        <a
            href="<?= config('app.base_path') ?>/lookup-types/<?= $lookupType['id'] ?>/values/create"
            class="btn btn-primary"
        >

            + Add Value

        </a>

        <a
            href="<?= config('app.base_path') ?>/lookup-types"
            class="btn btn-outline"
        >

            Back

        </a>

    </div>

</div>

<div class="card">

    <table class="table table-details">

        <tr>

            <th width="220">

                Name

            </th>

            <td>

                <?= htmlspecialchars(
                    $lookupType['name']
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                Slug

            </th>

            <td>

                <code>

                    <?= htmlspecialchars(
                        $lookupType['slug']
                    ) ?>

                </code>

            </td>

        </tr>

        <tr>

            <th>

                Description

            </th>

            <td>

                <?= nl2br(
                    htmlspecialchars(
                        $lookupType['description'] ?: '-'
                    )
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                Status

            </th>

            <td>

                <?= htmlspecialchars(
                    $lookupType['status']
                ) ?>

            </td>

        </tr>

    </table>

</div>

<div class="card mt-4">

    <div class="card-header">

        <h3>

            Lookup Values

        </h3>

    </div>

    <table class="table">

        <thead>

            <tr>

                <th>

                    Name

                </th>

                <th>

                    Slug

                </th>

                <th>

                    Status

                </th>

                <th width="180">

                    Actions

                </th>

            </tr>

        </thead>

        <tbody>

        <?php if (empty($lookupValues)): ?>

            <tr>

                <td colspan="4">

                    No lookup values yet.

                </td>

            </tr>

        <?php else: ?>

            <?php foreach ($lookupValues as $value): ?>

                <tr>

                    <td>

                        <?= htmlspecialchars(
                            $value['name']
                        ) ?>

                    </td>

                    <td>

                        <code>

                            <?= htmlspecialchars(
                                $value['slug']
                            ) ?>

                        </code>

                    </td>

                    <td>

                        <?= htmlspecialchars(
                            $value['status']
                        ) ?>

                    </td>

                    <td>

<a
    href="<?= config('app.base_path') ?>/lookup-values/<?= $value['id'] ?>/edit"
>

    Edit

</a>

|

    <form
        method="POST"
        action="<?= config('app.base_path') ?>/lookup-values/<?= $value['id'] ?>/delete"
        style="display:inline"
    >

        <button
            class="link-button"
            onclick="return confirm('Delete this lookup value?')"
        >

            Delete

        </button>

    </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

</div>