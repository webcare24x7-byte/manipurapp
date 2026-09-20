<?php

declare(strict_types=1);

$staff = $staff ?? [];

$memberName = trim(
    implode(
        ' ',
        array_filter([
            $staff['member_first_name'] ?? '',
            $staff['member_middle_name'] ?? '',
            $staff['member_last_name'] ?? '',
        ])
    )
);

?>

<div class="page-header">

    <div>

        <h1>

            <?= htmlspecialchars(
                (string) ($staff['name'] ?? 'Staff Profile')
            ) ?>

        </h1>

        <p>
            Staff Profile
        </p>

    </div>


    <div>

        <a
            href="<?= config('app.base_path') ?>/staff"
            class="btn btn-outline"
        >
            ← Back to Staff
        </a>

        <a
            href="<?= config('app.base_path') ?>/staff/<?= (int) $staff['id'] ?>/edit"
            class="btn btn-primary"
        >
            Edit Staff
        </a>

    </div>

</div>


<div class="card">

    <div class="card-header">

        <h3>
            Staff Information
        </h3>

    </div>


    <table class="table table-bordered">

        <tbody>

            <tr>

                <th width="220">
                    Name
                </th>

                <td>
                    <?= htmlspecialchars(
                        (string) ($staff['name'] ?? '-')
                    ) ?>
                </td>

            </tr>


            <tr>

                <th>
                    Staff Role
                </th>

                <td>
                    <?= htmlspecialchars(
                        (string) ($staff['role_name'] ?? '-')
                    ) ?>
                </td>

            </tr>


            <tr>

                <th>
                    Linked Member
                </th>

                <td>

                    <?php if (
                        $memberName !== ''
                        && !empty($staff['member_id'])
                    ): ?>

                        <a
                            href="<?= config('app.base_path') ?>/members/<?= (int) $staff['member_id'] ?>"
                        >

                            <?= htmlspecialchars($memberName) ?>

                        </a>

                    <?php else: ?>

                        Non-member Staff

                    <?php endif; ?>

                </td>

            </tr>


            <tr>

                <th>
                    Login Email
                </th>

                <td>
                    <?= htmlspecialchars(
                        (string) ($staff['email'] ?? '-')
                    ) ?>
                </td>

            </tr>


            <tr>

                <th>
                    Status
                </th>

                <td>
                    <?= htmlspecialchars(
                        (string) ($staff['status'] ?? '-')
                    ) ?>
                </td>

            </tr>


            <tr>

                <th>
                    Created
                </th>

                <td>
                    <?= htmlspecialchars(
                        (string) ($staff['created_at'] ?? '-')
                    ) ?>
                </td>

            </tr>


            <tr>

                <th>
                    Last Updated
                </th>

                <td>
                    <?= htmlspecialchars(
                        (string) ($staff['updated_at'] ?? '-')
                    ) ?>
                </td>

            </tr>

        </tbody>

    </table>

</div>


<div class="card">

    <div class="card-header">

        <h3>
            Staff Login
        </h3>

    </div>


    <div style="padding: 16px;">

        <p>

            <strong>
                Login ID:
            </strong>

            <?= htmlspecialchars(
                (string) ($staff['email'] ?? '-')
            ) ?>

        </p>


        <p>

            <strong>
                Password:
            </strong>

            Hidden

        </p>


        <p style="margin-bottom:0;">

            The password is securely stored as a hash and
            cannot be viewed by administrators.

        </p>

    </div>

</div>


<div class="card">

    <div class="card-header">

        <h3>
            Account Status
        </h3>

    </div>


    <div style="padding: 16px;">

        <?php if (($staff['status'] ?? '') === 'Active'): ?>

            <form
                method="POST"
                action="<?= config('app.base_path') ?>/staff/<?= (int) $staff['id'] ?>/delete"
                onsubmit="return confirm('Deactivate this staff account?');"
            >

                <button
                    type="submit"
                    class="btn btn-danger"
                >
                    Deactivate Staff
                </button>

            </form>

        <?php else: ?>

            <form
                method="POST"
                action="<?= config('app.base_path') ?>/staff/<?= (int) $staff['id'] ?>/activate"
            >

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Activate Staff
                </button>

            </form>

        <?php endif; ?>

    </div>

</div>