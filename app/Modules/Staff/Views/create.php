<?php

declare(strict_types=1);

$members = $members ?? [];
$roles   = $roles ?? [];
$old     = $old ?? [];

?>

<div class="page-header">

    <div>

        <h1>
            Add Staff
        </h1>

        <p>
            Create a staff account for a member or non-member.
        </p>

    </div>

</div>


<?php if (!empty($error)): ?>

    <div class="alert alert-danger">

        <?= htmlspecialchars($error) ?>

    </div>

<?php endif; ?>


<div class="card">

    <form
        method="POST"
        action="<?= config('app.base_path') ?>/staff"
    >

        <div class="form-grid">

            <!-- Staff Information -->

            <div class="form-group form-group-full">

                <h3>
                    Staff Information
                </h3>

            </div>


            <div class="form-group">

                <label for="name">

                    Name
                    <span class="required">*</span>

                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        (string) ($old['name'] ?? '')
                    ) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="member_id">
                    Link to Member
                </label>

                <select
                    id="member_id"
                    name="member_id"
                    class="form-control"
                >

                    <option value="">
                        Non-member Staff
                    </option>

                    <?php foreach ($members as $member): ?>

                        <?php

                        $memberName = trim(
                            implode(
                                ' ',
                                array_filter([
                                    $member['first_name'] ?? '',
                                    $member['middle_name'] ?? '',
                                    $member['last_name'] ?? '',
                                ])
                            )
                        );

                        ?>

                        <option
                            value="<?= (int) $member['id'] ?>"
                            <?= (
                                (string) ($old['member_id'] ?? '')
                                === (string) $member['id']
                            )
                                ? 'selected'
                                : '' ?>
                        >

                            <?= htmlspecialchars($memberName) ?>

                            <?php if (!empty($member['member_no'])): ?>

                                — <?= htmlspecialchars(
                                    (string) $member['member_no']
                                ) ?>

                            <?php endif; ?>

                        </option>

                    <?php endforeach; ?>

                </select>

                <small>
                    Only active members are available for linking.
                </small>

            </div>


            <div class="form-group">

                <label for="role_id">

                    Staff Role
                    <span class="required">*</span>

                </label>

                <select
                    id="role_id"
                    name="role_id"
                    class="form-control"
                    required
                >

                    <option value="">
                        Select Role
                    </option>

                    <?php foreach ($roles as $role): ?>

                        <option
                            value="<?= (int) $role['id'] ?>"
                            <?= (
                                (string) ($old['role_id'] ?? '')
                                === (string) $role['id']
                            )
                                ? 'selected'
                                : '' ?>
                        >

                            <?= htmlspecialchars(
                                (string) $role['name']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="form-group">

                <label for="status">
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    class="form-control"
                >

                    <option value="Active">
                        Active
                    </option>

                    <option
                        value="Inactive"
                        <?= ($old['status'] ?? '') === 'Inactive'
                            ? 'selected'
                            : '' ?>
                    >
                        Inactive
                    </option>

                </select>

            </div>


            <!-- Login -->

            <div class="form-group form-group-full">

                <h3>
                    Staff Login
                </h3>

            </div>


            <div class="form-group form-group-full">

                <label for="email">

                    Login Email
                    <span class="required">*</span>

                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        (string) ($old['email'] ?? '')
                    ) ?>"
                    required
                    autocomplete="username"
                >

                <small>
                    This email address will be used as the staff login ID.
                </small>

            </div>


            <div class="form-group">

                <label for="password">

                    Password
                    <span class="required">*</span>

                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    required
                    autocomplete="new-password"
                >

            </div>


            <div class="form-group">

                <label for="password_confirmation">

                    Confirm Password
                    <span class="required">*</span>

                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    required
                    autocomplete="new-password"
                >

            </div>

        </div>


        <div class="page-actions">

            <button
                type="submit"
                class="btn btn-primary"
            >
                Create Staff
            </button>

            <a
                href="<?= config('app.base_path') ?>/staff"
                class="btn btn-outline"
            >
                Cancel
            </a>

        </div>

    </form>

</div>