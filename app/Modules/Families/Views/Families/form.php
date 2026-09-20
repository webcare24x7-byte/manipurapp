<?php

declare(strict_types=1);

$isEdit = !empty($family['id']);

$action = $isEdit
    ? config('app.base_path') . '/families/' . (int) $family['id'] . '/update'
    : config('app.base_path') . '/families';

?>

<div class="card">

    <form
        method="post"
        action="<?= $action ?>"
    >

        <div class="form-grid">

            <div class="form-group">

                <label>

                    Family Name <span class="required">*</span>

                </label>

                <input
                    type="text"
                    name="family_name"
                    class="form-control"
                    required
                    value="<?= htmlspecialchars(
                        (string) ($family['family_name'] ?? '')
                    ) ?>"
                >

            </div>

            <div class="form-group">

                <label>

                    Family Head

                </label>

                <select
                    name="head_member_id"
                    class="form-control"
                >

                    <option value="">

                        -- Select Member --

                    </option>

                    <?php foreach ($members as $member): ?>

                        <option
                            value="<?= (int) $member['id'] ?>"
                            <?= (int) ($family['head_member_id'] ?? 0) === (int) $member['id']
                                ? 'selected'
                                : '' ?>
                        >

                            <?= htmlspecialchars(
                                $member['first_name']
                                . ' '
                                . $member['last_name']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group form-group-full">

                <label>

                    Address

                </label>

                <textarea
                    name="address"
                    class="form-control"
                    rows="3"
                ><?= htmlspecialchars(
                    (string) ($family['address'] ?? '')
                ) ?></textarea>

            </div>

            <div class="form-group">

                <label>

                    City

                </label>

                <input
                    type="text"
                    name="city"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        (string) ($family['city'] ?? '')
                    ) ?>"
                >

            </div>

            <div class="form-group">

                <label>

                    State

                </label>

                <input
                    type="text"
                    name="state"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        (string) ($family['state'] ?? '')
                    ) ?>"
                >

            </div>

            <div class="form-group">

                <label>

                    Country

                </label>

                <input
                    type="text"
                    name="country"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        (string) ($family['country'] ?? '')
                    ) ?>"
                >

            </div>

            <div class="form-group">

                <label>

                    Postal Code

                </label>

                <input
                    type="text"
                    name="postal_code"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        (string) ($family['postal_code'] ?? '')
                    ) ?>"
                >

            </div>

            <div class="form-group">

                <label>

                    Phone

                </label>

                <input
                    type="text"
                    name="phone"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        (string) ($family['phone'] ?? '')
                    ) ?>"
                >

            </div>

            <div class="form-group">

                <label>

                    Email

                </label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        (string) ($family['email'] ?? '')
                    ) ?>"
                >

            </div>

            <div class="form-group">

                <label>

                    Anniversary

                </label>

                <input
                    type="date"
                    name="anniversary"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        (string) ($family['anniversary'] ?? '')
                    ) ?>"
                >

            </div>

            <div class="form-group">

                <label>

                    Status

                </label>

                <select
                    name="status"
                    class="form-control"
                >

                    <option
                        value="Active"
                        <?= ($family['status'] ?? 'Active') === 'Active'
                            ? 'selected'
                            : '' ?>
                    >

                        Active

                    </option>

                    <option
                        value="Inactive"
                        <?= ($family['status'] ?? '') === 'Inactive'
                            ? 'selected'
                            : '' ?>
                    >

                        Inactive

                    </option>

                </select>

            </div>

            <div class="form-group form-group-full">

                <label>

                    Remarks

                </label>

                <textarea
                    name="remarks"
                    class="form-control"
                    rows="4"
                ><?= htmlspecialchars(
                    (string) ($family['remarks'] ?? '')
                ) ?></textarea>

            </div>

        </div>

        <div class="page-actions">

            <button
                type="submit"
                class="btn btn-primary"
            >

                <?= $isEdit
                    ? 'Update Family'
                    : 'Save Family' ?>

            </button>

            <a
                href="<?= config('app.base_path') ?>/families"
                class="btn btn-outline"
            >

                Cancel

            </a>

        </div>

    </form>

</div>