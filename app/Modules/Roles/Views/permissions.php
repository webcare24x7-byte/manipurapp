<?php

declare(strict_types=1);

$assigned = array_map(
    'intval',
    $assigned_permission_ids ?? []
);
?>

<div class="page-header">

    <div>

        <a
            href="<?= config('app.base_path') ?>/roles"
            class="back-link"
        >
            ← Roles & Permissions
        </a>

        <h1>
            <?= htmlspecialchars(
                (string) $role['name']
            ) ?>
        </h1>

        <p class="page-description">
            Configure permissions for this role.
        </p>

    </div>

</div>

<form
    method="POST"
    action="<?= config('app.base_path') ?>/roles/<?= (int) $role['id'] ?>/permissions"
>

    <div class="permission-layout">

        <?php foreach (
            $permissions as $module => $modulePermissions
        ): ?>

            <div class="card permission-card">

                <div class="card-header">

                    <div>

                        <h2>
                            <?= htmlspecialchars(
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        (string) $module
                                    )
                                )
                            ) ?>
                        </h2>

                    </div>

                </div>

                <div class="permission-list">

                    <?php foreach (
                        $modulePermissions as $permission
                    ): ?>

                        <?php
                        $permissionId =
                            (int) $permission['id'];

                        $checked =
                            in_array(
                                $permissionId,
                                $assigned,
                                true
                            );
                        ?>

                        <label class="permission-row">

                            <input
                                type="checkbox"
                                name="permissions[]"
                                value="<?= $permissionId ?>"
                                <?= $checked ? 'checked' : '' ?>
                            >

                            <span class="permission-content">

                                <strong>
                                    <?= htmlspecialchars(
                                        (string) $permission['name']
                                    ) ?>
                                </strong>

                                <small>
                                    <?= htmlspecialchars(
                                        (string) $permission['slug']
                                    ) ?>
                                </small>

                            </span>

                        </label>

                    <?php endforeach; ?>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <div class="page-actions">

        <a
            href="<?= config('app.base_path') ?>/roles"
            class="btn btn-secondary"
        >
            Cancel
        </a>

        <button
            type="submit"
            class="btn btn-primary"
        >
            Save Permissions
        </button>

    </div>

</form>