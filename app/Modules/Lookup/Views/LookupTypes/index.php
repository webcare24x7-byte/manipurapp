<?php

declare(strict_types=1);

$lookupTypes = $lookupTypes ?? [];

?>

<div class="page-header">

    <div>

        <h1>

            Lookup Types

        </h1>

        <p>

            Configure reusable lookup lists used throughout ChurchOS.

        </p>

    </div>

    <div class="page-actions">

        <a
            href="<?= config('app.base_path') ?>/lookup-types/create"
            class="btn btn-primary"
        >

            + New Lookup Type

        </a>

    </div>

</div>

<div class="card">

    <table class="table">

        <thead>

            <tr>

                <th>

                    Name

                </th>

                <th>

                    Slug

                </th>

                <th width="90">

                    Values

                </th>

                <th width="120">

                    Status

                </th>

                <th width="220">

                    Actions

                </th>

            </tr>

        </thead>

        <tbody>

        <?php if (empty($lookupTypes)): ?>

            <tr>

                <td colspan="5">

                    <div class="empty-state">

                        No Lookup Types Found.

                    </div>

                </td>

            </tr>

        <?php else: ?>

            <?php foreach ($lookupTypes as $lookupType): ?>

                <tr>

                    <td>

                        <strong>

                            <?= htmlspecialchars(
                                $lookupType['name']
                            ) ?>

                        </strong>

                    </td>

                    <td>

                        <code>

                            <?= htmlspecialchars(
                                $lookupType['slug']
                            ) ?>

                        </code>

                    </td>

                    <td>

                        <span class="badge">

                            <?= (int) $lookupType['value_count'] ?>

                        </span>

                    </td>

                    <td>

                        <?php if ($lookupType['status'] === 'Active'): ?>

                            <span class="badge badge-success">

                                Active

                            </span>

                        <?php else: ?>

                            <span class="badge badge-danger">

                                Inactive

                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <a
                            href="<?= config('app.base_path') ?>/lookup-types/<?= $lookupType['id'] ?>"
                        >

                            Manage

                        </a>

                        |

                        <a
                            href="<?= config('app.base_path') ?>/lookup-types/<?= $lookupType['id'] ?>/edit"
                        >

                            Edit

                        </a>

                        |

                        <form
                            method="POST"
                            action="<?= config('app.base_path') ?>/lookup-types/<?= $lookupType['id'] ?>/delete"
                            style="display:inline"
                        >

                            <button
                                class="link-button"
                                onclick="return confirm('Delete this lookup type?')"
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