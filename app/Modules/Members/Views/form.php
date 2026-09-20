<?php

$member = $member ?? [];
$memberLogin = $memberLogin ?? null;

$isEdit = !empty($member['id']);

$memberLoginExists = $memberLogin !== null;

$memberLoginEnabled =
    $memberLoginExists
    && (($memberLogin['status'] ?? '') === 'Active');

$memberLoginId =
    $memberLogin['email']
    ?? ($member['email'] ?? '');

$memberLoginStatus =
    $memberLogin['status']
    ?? null;

$memberLoginLast =
    $memberLogin['last_login_at']
    ?? null;

?>

<?php if (!empty($error)): ?>

    <div class="error-box">
        <?= htmlspecialchars($error) ?>
    </div>

<?php endif; ?>


<div class="form-grid">

    <div class="form-group form-group-full">

        <label>Profile Photo</label>

        <input
            type="file"
            name="photo"
            accept="image/jpeg,image/png,image/webp"
        >

        <small class="form-help">
            JPG, PNG, or WebP. Maximum 2 MB.
        </small>

    </div>


    <div class="form-group">

        <label>First Name *</label>

        <input
            type="text"
            name="first_name"
            required
            value="<?= htmlspecialchars($member['first_name'] ?? '') ?>"
        >

    </div>


    <div class="form-group">

        <label>Preferred Name</label>

        <input
            type="text"
            name="preferred_name"
            value="<?= htmlspecialchars($member['preferred_name'] ?? '') ?>"
        >

    </div>


    <div class="form-group">

        <label>Middle Name</label>

        <input
            type="text"
            name="middle_name"
            value="<?= htmlspecialchars($member['middle_name'] ?? '') ?>"
        >

    </div>


    <div class="form-group">

        <label>Last Name *</label>

        <input
            type="text"
            name="last_name"
            required
            value="<?= htmlspecialchars($member['last_name'] ?? '') ?>"
        >

    </div>


    <div class="form-group">

        <label>Gender</label>

        <select name="gender">

            <option value="">Select</option>

            <option
                value="Male"
                <?= (($member['gender'] ?? '') === 'Male') ? 'selected' : '' ?>
            >
                Male
            </option>

            <option
                value="Female"
                <?= (($member['gender'] ?? '') === 'Female') ? 'selected' : '' ?>
            >
                Female
            </option>

        </select>

    </div>


    <div class="form-group">

        <label>Date of Birth</label>

        <input
            type="date"
            name="dob"
            value="<?= htmlspecialchars($member['dob'] ?? '') ?>"
        >

    </div>


    <div class="form-group">

        <label>Occupation</label>

        <input
            type="text"
            name="occupation"
            maxlength="150"
            value="<?= htmlspecialchars($member['occupation'] ?? '') ?>"
        >

    </div>


    <div class="form-group">

        <label>Phone</label>

        <input
            type="text"
            name="phone"
            value="<?= htmlspecialchars($member['phone'] ?? '') ?>"
        >

    </div>


    <div class="form-group">

        <label>Email</label>

        <input
            type="email"
            name="email"
            id="member-email"
            value="<?= htmlspecialchars($member['email'] ?? '') ?>"
        >

    </div>


    <div class="form-group">

        <label>Membership Date</label>

        <input
            type="date"
            name="membership_date"
            value="<?= htmlspecialchars(
                $member['membership_date'] ?? date('Y-m-d')
            ) ?>"
        >

    </div>


    <div class="form-group">

        <label>Status</label>

        <select name="status">

            <option
                value="Active"
                <?= (($member['status'] ?? '') === 'Active') ? 'selected' : '' ?>
            >
                Active
            </option>

            <option
                value="Inactive"
                <?= (($member['status'] ?? '') === 'Inactive') ? 'selected' : '' ?>
            >
                Inactive
            </option>

            <option
                value="Visitor"
                <?= (($member['status'] ?? '') === 'Visitor') ? 'selected' : '' ?>
            >
                Visitor
            </option>

        </select>

    </div>


    <div class="form-group form-group-full">

        <label>Notes</label>

        <textarea
            name="notes"
            rows="5"
        ><?= htmlspecialchars($member['notes'] ?? '') ?></textarea>

    </div>

</div>


<!--
|--------------------------------------------------------------------------
| Member Login
|--------------------------------------------------------------------------
-->

<div class="member-login-section">

    <div class="section-header">

        <h3>Member Login</h3>

        <p class="form-help">
            Allow this member to sign in to the ChurchOS Member PWA.
        </p>

    </div>


    <div class="form-group">

        <label class="checkbox">

          <input
                type="checkbox"
                name="create_user"
                value="1"
                id="enable-member-login"
                <?= $memberLoginEnabled ? 'checked' : '' ?>
            >

            Enable Member Login

        </label>

    </div>


    <div
        id="member-login-fields"
        <?= $memberLoginEnabled ? '' : 'style="display:none;"' ?>
    >

        <?php if ($memberLoginExists): ?>

            <div class="form-grid">

                <div class="form-group">

                    <label>Login ID</label>

                    <input
                        type="email"
                        value="<?= htmlspecialchars($memberLoginId) ?>"
                        readonly
                    >

                    <small class="form-help">
                        The member's login ID.
                    </small>

                </div>


                <div class="form-group">

                    <label>Account Status</label>

                    <input
                        type="text"
                        value="<?= htmlspecialchars(
                            $memberLoginStatus ?? 'Active'
                        ) ?>"
                        readonly
                    >

                </div>


                <?php if ($memberLoginLast): ?>

                    <div class="form-group">

                        <label>Last Login</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $memberLoginLast
                            ) ?>"
                            readonly
                        >

                    </div>

                <?php endif; ?>

            </div>


            <div class="form-group form-group-full">

                <label>
                    Reset Password
                    <small>(leave blank to keep current password)</small>
                </label>

                <div class="password-input-row">

                    <input
                        type="password"
                        name="password"
                        id="member-password"
                        autocomplete="new-password"
                    >

                    <button
                        type="button"
                        class="btn"
                        id="generate-member-password"
                    >
                        Generate Password
                    </button>

                </div>

            </div>


            <div class="form-group">

                <label>Confirm New Password</label>

                <input
                    type="password"
                    name="password_confirmation"
                    id="member-password-confirmation"
                    autocomplete="new-password"
                >

            </div>


        <?php else: ?>

            <div class="form-grid">

                <div class="form-group">

                    <label>Login ID</label>

                    <input
                        type="email"
                        id="member-login-id"
                        value="<?= htmlspecialchars(
                            $memberLoginId
                        ) ?>"
                        readonly
                    >

                    <small class="form-help">
                        The member's email address will be used as the
                        login ID.
                    </small>

                </div>


                <div class="form-group">

                    <label>Password *</label>

                    <div class="password-input-row">

                        <input
                            type="password"
                            name="password"
                            id="member-password"
                            autocomplete="new-password"
                        >

                        <button
                            type="button"
                            class="btn"
                            id="generate-member-password"
                        >
                            Generate Password
                        </button>

                    </div>

                </div>


                <div class="form-group">

                    <label>Confirm Password *</label>

                    <input
                        type="password"
                        name="password_confirmation"
                        id="member-password-confirmation"
                        autocomplete="new-password"
                    >

                </div>

            </div>

        <?php endif; ?>

    </div>

</div>


<div class="form-actions">

    <button
        type="submit"
        class="btn btn-primary"
    >
        Save Member
    </button>


    <a
        href="<?= config('app.base_path') ?>/members"
        class="btn"
    >
        Cancel
    </a>

</div>


<script>
(function () {

    const checkbox = document.getElementById('enable-member-login');
    const fields = document.getElementById('member-login-fields');

    const emailInput = document.getElementById('member-email');
    const loginIdInput = document.getElementById('member-login-id');

    const passwordInput = document.getElementById('member-password');
    const confirmationInput =
        document.getElementById('member-password-confirmation');

    const generateButton =
        document.getElementById('generate-member-password');


    function updateLoginVisibility() {

        if (!checkbox || !fields) {
            return;
        }

        fields.style.display = checkbox.checked
            ? ''
            : 'none';
    }


    function syncLoginId() {

        if (!emailInput || !loginIdInput) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Only automatically populate the login ID.
        |
        | The field is readonly, so the Member email remains the source.
        |--------------------------------------------------------------------------
        */

        loginIdInput.value = emailInput.value.trim();
    }


    function generatePassword(length = 14) {

        const characters =
            'ABCDEFGHJKLMNPQRSTUVWXYZ' +
            'abcdefghijkmnopqrstuvwxyz' +
            '23456789' +
            '!@#$%&*';

        const randomValues =
            window.crypto &&
            window.crypto.getRandomValues
                ? window.crypto.getRandomValues(
                    new Uint32Array(length)
                )
                : null;

        let password = '';

        for (let i = 0; i < length; i++) {

            const randomIndex = randomValues
                ? randomValues[i] % characters.length
                : Math.floor(
                    Math.random() * characters.length
                );

            password += characters[randomIndex];
        }

        return password;
    }


    if (checkbox) {

        checkbox.addEventListener(
            'change',
            updateLoginVisibility
        );
    }


    if (emailInput) {

        emailInput.addEventListener(
            'input',
            syncLoginId
        );

        emailInput.addEventListener(
            'change',
            syncLoginId
        );
    }


    if (generateButton) {

        generateButton.addEventListener(
            'click',
            function () {

                const password = generatePassword();

                if (passwordInput) {
                    passwordInput.value = password;
                    passwordInput.type = 'text';
                }

                if (confirmationInput) {
                    confirmationInput.value = password;
                    confirmationInput.type = 'text';
                }

            }
        );
    }


    updateLoginVisibility();

    syncLoginId();

})();
</script>