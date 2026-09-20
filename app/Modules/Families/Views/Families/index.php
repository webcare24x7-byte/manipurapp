<?php

declare(strict_types=1);

?>

<div class="page-header">

    <div>

        <h1>

            Families

        </h1>

        <p>

            Manage church families.

        </p>

    </div>
    <?php if (!empty($error)): ?>

<div class="alert alert-danger">

    <?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>

    <div>

        <a
            href="<?= config('app.base_path') ?>/families/create"
            class="btn btn-primary"
        >

            Add New Family

        </a>

    </div>

</div>

<?php if (empty($families)): ?>

    <div class="empty-state">

        <p>

            No families have been created yet.

        </p>

        <a
            href="<?= config('app.base_path') ?>/families/create"
            class="btn btn-primary"
        >

            Create First Family

        </a>

    </div>

<?php else: ?>

    <div class="card">

        <table class="table">

            <thead>

                <tr>

                    <th width="120">

                        Code

                    </th>

                    <th>

                        Family Name

                    </th>

                    <th>

                        Head

                    </th>

                    <th width="120">

                        Members

                    </th>

                    <th>

                        Phone

                    </th>

                    <th width="120">

                        Status

                    </th>

                    <th width="180">

                        Actions

                    </th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($families as $family): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                $family['family_code']
                            ) ?>

                        </td>

                        <td>

                            <a
                                href="<?= config('app.base_path') ?>/families/<?= (int) $family['id'] ?>"
                            >

                                <?= htmlspecialchars(
                                    $family['family_name']
                                ) ?>

                            </a>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                $family['head_name'] ?? '-'
                            ) ?>

                        </td>

                        <td>

                            <?= (int) ($family['member_count'] ?? 0) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                $family['phone'] ?: '-'
                            ) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                $family['status']
                            ) ?>

                        </td>

                        <td>

                            <div class="table-actions">

                                <a
                                    href="<?= config('app.base_path') ?>/families/<?= (int) $family['id'] ?>"
                                    class="btn btn-sm btn-outline"
                                >

                                    View

                                </a>

                                <a
                                    href="<?= config('app.base_path') ?>/families/<?= (int) $family['id'] ?>/edit"
                                    class="btn btn-sm btn-primary"
                                >

                                    Edit

                                </a>

                                <form
                                    method="post"
                                    action="<?= config('app.base_path') ?>/families/<?= (int) $family['id'] ?>/delete"
                                    style="display:inline;"
                                    onsubmit="return confirm('Delete this family?');"
                                >

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                    >

                                        Delete

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

<?php endif; ?>