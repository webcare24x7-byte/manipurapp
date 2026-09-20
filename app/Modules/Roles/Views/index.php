<?php

declare(strict_types=1);
?>

<div class="page-header">

    <div>
        <h1>Roles & Permissions</h1>

        <p class="page-description">
            Configure what each church role can access.
        </p>
    </div>

</div>

<div class="card">

    <div class="card-header">

        <div>
            <h2>Roles</h2>

            <p>
                Select a role to manage its permissions.
            </p>
        </div>

    </div>

    <div class="table-wrapper">

        <table class="data-table">

            <thead>

                <tr>
                    <th>Role</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th></th>
                </tr>

            </thead>

            <tbody>

            <?php if (empty($roles)): ?>

                <tr>
                    <td colspan="4">
                        No roles found.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($roles as $role): ?>

                    <tr>

                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    (string) $role['name']
                                ) ?>
                            </strong>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                (string) (
                                    $role['description'] ?? ''
                                )
                            ) ?>

                        </td>

                        <td>

                            <?php if (
                                (int) $role['is_system'] === 1
                            ): ?>

                                <span class="badge">
                                    System Role
                                </span>

                            <?php else: ?>

                                <span class="badge">
                                    Custom Role
                                </span>

                            <?php endif; ?>

                        </td>

                        <td class="text-right">

                            <a
                                href="<?= config('app.base_path') ?>/roles/<?= (int) $role['id'] ?>/permissions"
                                class="btn btn-primary"
                            >
                                Manage Permissions
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>