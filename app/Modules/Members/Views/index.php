<?php

declare(strict_types=1);

?>

<div class="page-header">

    <div>

        <h1>

            Members

        </h1>

        <p>

            Manage your church members.

        </p>

    </div>

    <a
        href="<?= config('app.base_path') ?>/members/create"
        class="btn btn-primary"
    >

        + Add Member

    </a>

</div>

<div class="card">

    <div class="card-header">

        <input
            type="text"
            placeholder="Search members..."
            class="table-search"
        >

    </div>

    <table class="table">

        <thead>

            <tr>

                <th>Member No</th>

                <th>Name</th>

                <th>Phone</th>

                <th>Email</th>

                <th>Status</th>

                <th width="220">

                    Actions

                </th>

            </tr>

        </thead>

        <tbody>

        <?php if (empty($members)): ?>

            <tr>

                <td colspan="6">

                    <div class="empty-state">

                        No members found.

                    </div>

                </td>

            </tr>

        <?php else: ?>

            <?php foreach ($members as $member): ?>

                <tr>

                    <td>

                        <?= htmlspecialchars($member['member_no']) ?>

                    </td>

                    <td>

                        <?= htmlspecialchars(

                            trim(

                                $member['first_name']

                                . ' '

                                . ($member['middle_name'] ?? '')

                                . ' '

                                . $member['last_name']

                            )

                        ) ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($member['phone'] ?? '-') ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($member['email'] ?? '-') ?>

                    </td>

                    <td>

                        <span class="badge badge-success">

                            <?= htmlspecialchars($member['status']) ?>

                        </span>

                    </td>

                    <td>

                        <div class="table-actions">

                            <a
                                href="<?= config('app.base_path') ?>/members/<?= $member['id'] ?>"
                                class="btn btn-sm"
                            >
                                View
                            </a>

                            <a
                                href="<?= config('app.base_path') ?>/members/<?= $member['id'] ?>/edit"
                                class="btn btn-sm"
                            >
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="<?= config('app.base_path') ?>/members/<?= $member['id'] ?>/delete"
                                style="display:inline"
                            >

                            <button
                                class="btn btn-warning btn-sm"
                                onclick="return confirm(
                                    'Archive this member?\n\nThis member can be restored later.'
                                );"
                            >

                                Archive

                            </button>

                            </form>

                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

</div>

<div class="table-footer">

    Total Members:

    <strong>

        <?= count($members) ?>

    </strong>

</div>