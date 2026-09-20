<?php

declare(strict_types=1);

$packages = $packages ?? [];

$installed = $installed ?? [];

?>

<div class="page-header">

    <div>

        <h1>

            ChurchOS Packages

        </h1>

        <p>

            Install and manage ChurchOS packages for this tenant.

        </p>

    </div>

</div>

<div class="card">

    <table class="table">

        <thead>

            <tr>

                <th width="240">

                    Package

                </th>

                <th width="170">

                    Code

                </th>

                <th>

                    Description

                </th>

                <th width="100">

                    Version

                </th>

                <th width="120">

                    Status

                </th>

                <th width="260">

                    Actions

                </th>

            </tr>

        </thead>

        <tbody>

        <?php if (empty($packages)): ?>

            <tr>

                <td colspan="6">

                    <div class="empty-state">

                        No packages registered.

                    </div>

                </td>

            </tr>

        <?php else: ?>

            <?php foreach ($packages as $package): ?>

                <?php

                $record = $installed[
                    $package->code()
                ] ?? null;

                $isInstalled = $record !== null;

                ?>

                <tr>

                    <td>

                        <strong>

                            <?= htmlspecialchars(
                                $package->name()
                            ) ?>

                        </strong>

                    </td>

                    <td>

                        <code>

                            <?= htmlspecialchars(
                                $package->code()
                            ) ?>

                        </code>

                    </td>

                    <td>

                        <?= htmlspecialchars(
                            $package->description()
                        ) ?>

                    </td>

                    <td>

                        <?= htmlspecialchars(
                            $package->version()
                        ) ?>

                    </td>

                    <td>

                        <?php if ($isInstalled): ?>

                            <span class="badge badge-success">

                                Installed

                            </span>

                        <?php else: ?>

                            <span class="badge badge-warning">

                                Not Installed

                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <?php if (!$isInstalled): ?>

                            <form
                                method="POST"
                                action="<?= config('app.base_path') ?>/packages/install/<?= urlencode($package->code()) ?>"
                                style="display:inline"
                            >

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm"
                                >

                                    Install

                                </button>

                            </form>

                        <?php else: ?>

                            <form
                                method="POST"
                                action="<?= config('app.base_path') ?>/packages/upgrade/<?= urlencode($package->code()) ?>"
                                style="display:inline"
                            >

                                <button
                                    type="submit"
                                    class="btn btn-secondary btn-sm"
                                >

                                    Verify / Upgrade

                                </button>

                            </form>

                            <?php if ($package->canUninstall()): ?>

                                <form
                                    method="POST"
                                    action="<?= config('app.base_path') ?>/packages/uninstall/<?= urlencode($package->code()) ?>"
                                    style="display:inline"
                                >

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Uninstall this package?')"
                                    >

                                        Uninstall

                                    </button>

                                </form>

                            <?php else: ?>

                                <span class="text-muted">

                                    Core Package

                                </span>

                            <?php endif; ?>

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

</div>