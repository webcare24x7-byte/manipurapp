<?php

declare(strict_types=1);

?>

<div class="page-header">

    <div>

        <h1>

            <?= htmlspecialchars($family['family_name']) ?>

        </h1>

        <p>

            Family Details

        </p>

    </div>

    <div>
    <a
            href="/churchos/public/families"
            class="btn btn-secondary"
        >
            ← Back to Families
        </a>
        <a
            href="<?= config('app.base_path') ?>/families/<?= (int) $family['id'] ?>/edit"
            class="btn btn-primary"
        >

            Edit

        </a>

    </div>

</div>

<div class="card">

    <table class="table table-bordered">

        <tbody>

        <tr>

            <th width="220">

                Family Code

            </th>

            <td>

                <?= htmlspecialchars($family['family_code']) ?>

            </td>

        </tr>

        <tr>

            <th>

                Family Name

            </th>

            <td>

                <?= htmlspecialchars($family['family_name']) ?>

            </td>

        </tr>

        <tr>

            <th>

                Primary Family Head

            </th>

            <td>

                <?= htmlspecialchars(
                    (string) ($family['head_name'] ?? '-')
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                Address

            </th>

            <td>

                <?= nl2br(
                    htmlspecialchars(
                        (string) ($family['address'] ?? '-')
                    )
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                City

            </th>

            <td>

                <?= htmlspecialchars(
                    (string) ($family['city'] ?? '-')
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                State

            </th>

            <td>

                <?= htmlspecialchars(
                    (string) ($family['state'] ?? '-')
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                Country

            </th>

            <td>

                <?= htmlspecialchars(
                    (string) ($family['country'] ?? '-')
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                Postal Code

            </th>

            <td>

                <?= htmlspecialchars(
                    (string) ($family['postal_code'] ?? '-')
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                Phone

            </th>

            <td>

                <?= htmlspecialchars(
                    (string) ($family['phone'] ?? '-')
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                Email

            </th>

            <td>

                <?= htmlspecialchars(
                    (string) ($family['email'] ?? '-')
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                Anniversary

            </th>

            <td>

                <?= htmlspecialchars(
                    (string) ($family['anniversary'] ?? '-')
                ) ?>

            </td>

        </tr>

        <tr>

            <th>

                Status

            </th>

            <td>

                <?= htmlspecialchars($family['status']) ?>

            </td>

        </tr>

        <tr>

            <th>

                Remarks

            </th>

            <td>

                <?= nl2br(
                    htmlspecialchars(
                        (string) ($family['remarks'] ?? '-')
                    )
                ) ?>

            </td>

        </tr>

        </tbody>

    </table>

</div>

<div class="card">

    <div class="card-header">

        <h3>

            Family Members

        </h3>

        <?php if (!empty($availableMembers)): ?>

            <form
                method="post"
                action="<?= config('app.base_path') ?>/families/<?= (int) $family['id'] ?>/members"
                style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-top:12px;"
            >

                <select
                    name="member_id"
                    class="form-control"
                    required
                >
                    <option value="">-- Add Existing Member --</option>

                    <?php foreach ($availableMembers as $availableMember): ?>

                        <option value="<?= (int) $availableMember['id'] ?>">

                            <?= htmlspecialchars(
                                trim(
                                    ($availableMember['first_name'] ?? '')
                                    . ' '
                                    . ($availableMember['last_name'] ?? '')
                                )
                            ) ?>

                            <?php if (!empty($availableMember['member_no'])): ?>

                                — <?= htmlspecialchars($availableMember['member_no']) ?>

                            <?php endif; ?>

                        </option>

                    <?php endforeach; ?>

                </select>

                <select
                    name="family_role"
                    class="form-control"
                    required
                >
                    <option value="">-- Role --</option>
                    <option value="Head">Head</option>
                    <option value="Spouse">Spouse</option>
                    <option value="Parent">Parent</option>
                    <option value="Child">Child</option>
                    <option value="Other Adult">Other Adult</option>
                    <option value="Dependent">Dependent</option>
                    <option value="Guardian">Guardian</option>
                    <option value="Other">Other</option>
                </select>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Member
                </button>

            </form>

        <?php endif; ?>

    </div>

    <?php if (empty($members)): ?>

        <div class="empty-state">

            <p>

                No members have been assigned to this family.

            </p>

        </div>

    <?php else: ?>

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

                        Gender

                    </th>

                    <th>

                        Mobile

                    </th>

                    <th>

                        Status

                    </th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($members as $member): ?>

                    <tr>

                        <td>

                            <a
                                href="<?= config('app.base_path') ?>/members/<?= (int) $member['id'] ?>"
                            >

                                <?= htmlspecialchars(
                                    $member['first_name']
                                    . ' '
                                    . $member['last_name']
                                ) ?>

                            </a>

                        </td>

                        <td>

                            <form
                                method="post"
                                action="<?= config('app.base_path') ?>/families/<?= (int) $family['id'] ?>/members/<?= (int) $member['id'] ?>/role"
                                style="display:flex;gap:6px;align-items:center;"
                            >

                                <select
                                    name="family_role"
                                    class="form-control"
                                >
                                    <?php foreach ([
                                        'Head',
                                        'Spouse',
                                        'Parent',
                                        'Child',
                                        'Other Adult',
                                        'Dependent',
                                        'Guardian',
                                        'Other',
                                    ] as $role): ?>

                                        <option
                                            value="<?= htmlspecialchars($role) ?>"
                                            <?= ($member['family_role'] ?? '') === $role
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= htmlspecialchars($role) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                                <?php if (
                                    (int) ($family['head_member_id'] ?? 0)
                                    === (int) $member['id']
                                ): ?>

                                    <span
                                        style="font-size:12px;color:#64748b;white-space:nowrap;"
                                        title="Primary Family Head"
                                    >
                                        Primary
                                    </span>

                                <?php endif; ?>

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-primary"
                                >
                                    Save
                                </button>

                            </form>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                (string) ($member['gender'] ?? '-')
                            ) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                (string) ($member['mobile'] ?? '-')
                            ) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                (string) ($member['status'] ?? '-')
                            ) ?>

                            <div style="margin-top:6px;">

                                <?php if (!empty($availableMoveFamilies)): ?>

                                    <details style="margin-bottom:6px;">

                                        <summary
                                            style="cursor:pointer;color:#2563eb;font-size:13px;"
                                        >
                                            Move Member
                                        </summary>

                                        <form
                                            method="post"
                                            action="<?= config('app.base_path') ?>/families/<?= (int) $family['id'] ?>/members/<?= (int) $member['id'] ?>/move"
                                            style="margin-top:8px;display:grid;gap:6px;"
                                        >

                                            <select
                                                name="destination_family_id"
                                                class="form-control"
                                                required
                                            >
                                                <option value="">
                                                    -- Destination Family --
                                                </option>

                                                <?php foreach ($availableMoveFamilies as $moveFamily): ?>

                                                    <option
                                                        value="<?= (int) $moveFamily['id'] ?>"
                                                    >
                                                        <?= htmlspecialchars(
                                                            (string) $moveFamily['family_name']
                                                        ) ?>

                                                        <?php if (!empty($moveFamily['family_code'])): ?>

                                                            — <?= htmlspecialchars(
                                                                (string) $moveFamily['family_code']
                                                            ) ?>

                                                        <?php endif; ?>

                                                    </option>

                                                <?php endforeach; ?>

                                            </select>

                                            <select
                                                name="family_role"
                                                class="form-control"
                                                required
                                            >
                                                <?php foreach ([
                                                    'Head',
                                                    'Spouse',
                                                    'Parent',
                                                    'Child',
                                                    'Other Adult',
                                                    'Dependent',
                                                    'Guardian',
                                                    'Other',
                                                ] as $moveRole): ?>

                                                    <option
                                                        value="<?= htmlspecialchars($moveRole) ?>"
                                                        <?= ($member['family_role'] ?? '') === $moveRole
                                                            ? 'selected'
                                                            : '' ?>
                                                    >
                                                        <?= htmlspecialchars($moveRole) ?>
                                                    </option>

                                                <?php endforeach; ?>

                                            </select>

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-primary"
                                                onclick="return confirm('Move this member to the selected family? If Head is selected, existing Heads in the destination family will remain Heads.');"
                                            >
                                                Move
                                            </button>

                                        </form>

                                    </details>

                                <?php endif; ?>

                                <form
                                    method="post"
                                    action="<?= config('app.base_path') ?>/families/<?= (int) $family['id'] ?>/members/<?= (int) $member['id'] ?>/remove"
                                    onsubmit="return confirm('Remove this member from the family? The member record will not be deleted.');"
                                >
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                    >
                                        Remove
                                    </button>
                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</div>