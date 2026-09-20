<?php

declare(strict_types=1);

?>

<div class="page-header">

    <div>

        <h1>
            Staff
        </h1>

        <p>
            Manage church staff and staff accounts.
        </p>

    </div>


    <?php if (!empty($error)): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <div>

        <a
            href="<?= config('app.base_path') ?>/staff/create"
            class="btn btn-primary"
        >
            Add New Staff
        </a>

    </div>

</div>


<?php if (empty($staff)): ?>

    <div class="empty-state">

        <p>
            No staff accounts have been created yet.
        </p>

        <a
            href="<?= config('app.base_path') ?>/staff/create"
            class="btn btn-primary"
        >
            Add First Staff Member
        </a>

    </div>

<?php else: ?>

    <div class="card">

        <table class="table">

            <thead>

                <tr>

                    <th>
                        Name
                    </th>

                    <th>
                        Role
                    </th>

                    <th>
                        Member
                    </th>

                    <th>
                        Email
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

                <?php foreach ($staff as $person): ?>

                    <?php

                    $memberName = trim(
                        implode(
                            ' ',
                            array_filter([
                                $person['member_first_name'] ?? '',
                                $person['member_middle_name'] ?? '',
                                $person['member_last_name'] ?? '',
                            ])
                        )
                    );

                    ?>

                    <tr>

                        <td>

                            <a
                                href="<?= config('app.base_path') ?>/staff/<?= (int) $person['id'] ?>"
                            >

                                <?= htmlspecialchars(
                                    (string) ($person['name'] ?? '-')
                                ) ?>

                            </a>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                (string) ($person['role_name'] ?? '-')
                            ) ?>

                        </td>


                        <td>

                            <?php if ($memberName !== ''): ?>

                                <a
                                    href="<?= config('app.base_path') ?>/members/<?= (int) $person['member_id'] ?>"
                                >

                                    <?= htmlspecialchars(
                                        $memberName
                                    ) ?>

                                </a>

                            <?php else: ?>

                                <span>
                                    Non-member
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                (string) ($person['email'] ?? '-')
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                (string) ($person['status'] ?? '-')
                            ) ?>

                        </td>


                        <td>

                            <div class="table-actions">

                                <a
                                    href="<?= config('app.base_path') ?>/staff/<?= (int) $person['id'] ?>"
                                    class="btn btn-sm btn-outline"
                                >
                                    View
                                </a>


                                <a
                                    href="<?= config('app.base_path') ?>/staff/<?= (int) $person['id'] ?>/edit"
                                    class="btn btn-sm btn-primary"
                                >
                                    Edit
                                </a>


                                <?php if (
                                    ($person['status'] ?? '') === 'Active'
                                ): ?>

                                    <form
                                        method="post"
                                        action="<?= config('app.base_path') ?>/staff/<?= (int) $person['id'] ?>/delete"
                                        style="display:inline;"
                                        onsubmit="return confirm('Deactivate this staff account?');"
                                    >

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                        >
                                            Deactivate
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

<?php endif; ?>