<div class="page-header">

    <div style="display:flex;align-items:center;gap:16px;">

        <?php if (!empty($member['photo'])): ?>
            <img
                src="<?= rtrim(config('app.base_path'), '/') . '/' . ltrim(htmlspecialchars($member['photo']), '/') ?>"
                alt="Member photo"
                style="width:88px;height:88px;border-radius:50%;object-fit:cover;"
            >
        <?php else: ?>
            <div
                aria-hidden="true"
                style="width:88px;height:88px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:30px;background:#f1f5f9;"
            >
                <?= htmlspecialchars(strtoupper(substr((string)($member['first_name'] ?? 'M'), 0, 1))) ?>
            </div>
        <?php endif; ?>

        <div>
            <h1>
                <?= htmlspecialchars(
                    trim(
                        $member['first_name']
                        . ' '
                        . ($member['middle_name'] ?? '')
                        . ' '
                        . $member['last_name']
                    )
                ) ?>
            </h1>

            <p>
                <?= htmlspecialchars($member['member_no']) ?>
                &nbsp;·&nbsp;
                <span class="badge badge-success">
                    <?= htmlspecialchars($member['status'] ?? '-') ?>
                </span>
            </p>

            <?php if (!empty($member['preferred_name'])): ?>
                <p>
                    Preferred name:
                    <strong><?= htmlspecialchars($member['preferred_name']) ?></strong>
                </p>
            <?php endif; ?>
        </div>

    </div>

    <div>
        <a
            href="<?= config('app.base_path') ?>/members/<?= (int) $member['id'] ?>/edit"
            class="btn btn-primary"
        >
            Edit Member
        </a>
    </div>

</div>


<div class="profile-grid">

    <div class="card">

        <h2>Personal Information</h2>

        <table class="profile-table">

            <tr>
                <th>First Name</th>
                <td><?= htmlspecialchars($member['first_name'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Middle Name</th>
                <td><?= htmlspecialchars($member['middle_name'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Last Name</th>
                <td><?= htmlspecialchars($member['last_name'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Preferred Name</th>
                <td><?= htmlspecialchars($member['preferred_name'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Gender</th>
                <td><?= htmlspecialchars($member['gender'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Date of Birth</th>
                <td><?= htmlspecialchars($member['dob'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Occupation</th>
                <td><?= htmlspecialchars($member['occupation'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Marital Status</th>
                <td><?= htmlspecialchars($member['marital_status'] ?? '-') ?></td>
            </tr>

        </table>

    </div>


    <div class="card">

        <h2>Contact Information</h2>

        <table class="profile-table">

            <tr>
                <th>Phone</th>
                <td><?= htmlspecialchars($member['phone'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Alternate Phone</th>
                <td><?= htmlspecialchars($member['alternate_phone'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($member['email'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Address</th>
                <td>
                    <?= nl2br(htmlspecialchars($member['address'] ?? '-')) ?>
                </td>
            </tr>

            <tr>
                <th>City</th>
                <td><?= htmlspecialchars($member['city'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>State</th>
                <td><?= htmlspecialchars($member['state'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Country</th>
                <td><?= htmlspecialchars($member['country'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Postal Code</th>
                <td><?= htmlspecialchars($member['postal_code'] ?? '-') ?></td>
            </tr>

        </table>

    </div>

</div>


<div class="profile-grid">

    <div class="card">

        <h2>Membership</h2>

        <table class="profile-table">

            <tr>
                <th>Member Number</th>
                <td><?= htmlspecialchars($member['member_no'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    <span class="badge badge-success">
                        <?= htmlspecialchars($member['status'] ?? '-') ?>
                    </span>
                </td>
            </tr>

            <tr>
                <th>Member Since</th>
                <td><?= htmlspecialchars($member['membership_date'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Baptism Date</th>
                <td><?= htmlspecialchars($member['baptism_date'] ?? '-') ?></td>
            </tr>

        </table>

    </div>


    <div class="card">

        <h2>Emergency Contact</h2>

        <table class="profile-table">

            <tr>
                <th>Name</th>
                <td><?= htmlspecialchars($member['emergency_name'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Phone</th>
                <td><?= htmlspecialchars($member['emergency_phone'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Relationship</th>
                <td><?= htmlspecialchars($member['emergency_relationship'] ?? '-') ?></td>
            </tr>

        </table>

    </div>

</div>

<div class="card">

    <div class="card-header">
        <h2>Member Login</h2>
    </div>

    <?php if (!empty($memberLogin)): ?>

        <div class="detail-grid">

            <div>
                <strong>Login ID</strong>
                <div>
                    <?= htmlspecialchars(
                        $memberLogin['email'] ?? ''
                    ) ?>
                </div>
            </div>

            <div>
                <strong>Status</strong>
                <div>
                    <?= htmlspecialchars(
                        $memberLogin['status'] ?? ''
                    ) ?>
                </div>
            </div>

            <div>
                <strong>Last Login</strong>
                <div>
                    <?php if (!empty($memberLogin['last_login_at'])): ?>

                        <?= htmlspecialchars(
                            $memberLogin['last_login_at']
                        ) ?>

                    <?php else: ?>

                        Never

                    <?php endif; ?>
                </div>
            </div>

        </div>

        <div class="form-actions">

            <a
                href="<?= config('app.base_path') ?>/members/<?= (int) $member['id'] ?>/edit"
                class="btn"
            >
                Manage Login
            </a>

        </div>

    <?php else: ?>

        <p>
            This member does not currently have a Member login account.
        </p>

        <div class="form-actions">

            <a
                href="<?= config('app.base_path') ?>/members/<?= (int) $member['id'] ?>/edit"
                class="btn btn-primary"
            >
                Enable Member Login
            </a>

        </div>

    <?php endif; ?>

</div>


<div class="card">

    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">

        <div>
            <h2>Family</h2>
        </div>

        <?php if ($family !== null): ?>
            <a
                href="<?= config('app.base_path') ?>/families/<?= (int) $family['id'] ?>"
                class="btn btn-sm"
            >
                View Family
            </a>
        <?php endif; ?>

    </div>

    <?php if ($family === null): ?>

        <div class="empty-state">
            <p>This member is not currently assigned to a family.</p>
        </div>

    <?php else: ?>

        <table class="profile-table">

            <tr>
                <th>Family</th>
                <td><?= htmlspecialchars($family['family_name'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Family Code</th>
                <td><?= htmlspecialchars($family['family_code'] ?? '-') ?></td>
            </tr>

            <tr>
                <th>Family Role</th>
                <td><?= htmlspecialchars($member['family_role'] ?? '-') ?></td>
            </tr>

        </table>

        <?php if (!empty($familyMembers)): ?>

            <h3 style="margin-top:20px;">Household Members</h3>

            <table class="table">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($familyMembers as $familyMember): ?>

                        <tr>

                            <td>
                                <a
                                    href="<?= config('app.base_path') ?>/members/<?= (int) $familyMember['id'] ?>"
                                >
                                    <?= htmlspecialchars(
                                        trim(
                                            ($familyMember['first_name'] ?? '')
                                            . ' '
                                            . ($familyMember['last_name'] ?? '')
                                        )
                                    ) ?>
                                </a>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    (string) ($familyMember['family_role'] ?? '-')
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    (string) ($familyMember['gender'] ?? '-')
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    (string) ($familyMember['phone'] ?? '-')
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    (string) ($familyMember['status'] ?? '-')
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    <?php endif; ?>

</div>


<div class="profile-grid">

    <div class="card">

        <h2>Church Activity</h2>

        <table class="profile-table">

            <tr>
                <th>Groups</th>
                <td>Not available yet.</td>
            </tr>

            <tr>
                <th>Ministries</th>
                <td>Not available yet.</td>
            </tr>

            <tr>
                <th>Attendance</th>
                <td>Not available yet.</td>
            </tr>

            <tr>
                <th>Giving</th>
                <td>Not available yet.</td>
            </tr>

            <tr>
                <th>Volunteering</th>
                <td>Not available yet.</td>
            </tr>

            <tr>
                <th>Leadership</th>
                <td>Not available yet.</td>
            </tr>

        </table>

    </div>


    <div class="card">

        <h2>Notes</h2>

        <p>
            <?= nl2br(
                htmlspecialchars(
                    $member['notes'] ?: 'No notes available.'
                )
            ) ?>
        </p>

    </div>

</div>


<div style="margin-top:25px;">

    <a
        href="<?= config('app.base_path') ?>/members"
        class="btn"
    >
        ← Back to Members
    </a>

</div>
