<?php

declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';


$basePath = (string) config('app.base_path');

$old = is_array($old ?? null)
    ? $old
    : [];

$escapedBasePath = htmlspecialchars(
    $basePath,
    ENT_QUOTES,
    'UTF-8'
);
?>

<style>
/* =========================================================
   MANIPURAPP AUTH — REGISTER
   Scoped to .ma-register-page
========================================================= */

.ma-register-page {
    --ma-green: #087f5b;
    --ma-green-dark: #056247;
    --ma-green-soft: #e8f7f1;

    min-height: 100dvh;

    width: 100%;

    background:
        radial-gradient(
            circle at 85% 8%,
            rgba(8, 127, 91, 0.07),
            transparent 30%
        ),
        #ffffff;

    color: #10213b;

    font-family:
        Inter,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        Roboto,
        Helvetica,
        Arial,
        sans-serif;

    box-sizing: border-box;
}

.ma-register-page *,
.ma-register-page *::before,
.ma-register-page *::after {
    box-sizing: border-box;
}

/* ---------------------------------------------------------
   Shell
--------------------------------------------------------- */

.ma-register-shell {
    width: 100%;
    min-height: 100dvh;

    padding:
        max(20px, env(safe-area-inset-top))
        18px
        max(24px, env(safe-area-inset-bottom));
}

/* ---------------------------------------------------------
   Header
--------------------------------------------------------- */

.ma-register-header {
    width: 100%;
    max-width: 520px;

    margin: 0 auto;

    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ma-register-back {
    width: 42px;
    height: 42px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #f5f7f8;

    color: #26364b;

    text-decoration: none;
}

.ma-register-back svg {
    width: 20px;
    height: 20px;

    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.ma-register-brand {
    display: inline-flex;
    align-items: center;
    gap: 9px;

    color: var(--ma-green);

    text-decoration: none;

    font-size: 20px;
    font-weight: 800;

    letter-spacing: -.5px;
}

.ma-register-brand-mark {
    width: 34px;
    height: 34px;
}

.ma-register-brand-mark svg {
    width: 34px;
    height: 34px;

    fill: none;
    stroke: currentColor;
    stroke-width: 1.9;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* ---------------------------------------------------------
   Main
--------------------------------------------------------- */

.ma-register-main {
    width: 100%;
    max-width: 520px;

    margin: 0 auto;

    padding-top: 24px;
}

/* ---------------------------------------------------------
   Intro
--------------------------------------------------------- */

.ma-register-intro {
    text-align: center;

    padding:
        14px
        0
        22px;
}

.ma-register-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    margin-bottom: 14px;

    padding: 7px 12px;

    border-radius: 999px;

    background: var(--ma-green-soft);

    color: var(--ma-green);

    font-size: 12px;
    font-weight: 800;

    letter-spacing: .15px;
}

.ma-register-badge svg {
    width: 15px;
    height: 15px;

    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.ma-register-title {
    margin: 0;

    color: #10213b;

    font-size: clamp(
        28px,
        7vw,
        34px
    );

    line-height: 1.12;

    font-weight: 800;

    letter-spacing: -1px;
}

.ma-register-subtitle {
    max-width: 390px;

    margin:
        10px
        auto
        0;

    color: #718096;

    font-size: 15px;
    line-height: 1.55;
}

/* ---------------------------------------------------------
   Form card
--------------------------------------------------------- */

.ma-register-card {
    width: 100%;

    padding: 21px;

    border: 1px solid #edf1ef;

    border-radius: 26px;

    background: #ffffff;

    box-shadow:
        0 16px 50px rgba(16, 33, 59, 0.07);
}

/* ---------------------------------------------------------
   Error
--------------------------------------------------------- */

.ma-register-error {
    display: flex;
    align-items: flex-start;
    gap: 10px;

    margin-bottom: 18px;

    padding: 13px 14px;

    border: 1px solid #f5cccc;

    border-radius: 14px;

    background: #fff5f5;

    color: #a33a3a;

    font-size: 13px;
    line-height: 1.45;
}

.ma-register-error svg {
    flex: 0 0 auto;

    width: 18px;
    height: 18px;

    margin-top: 1px;

    fill: none;
    stroke: currentColor;
    stroke-width: 2;
}

/* ---------------------------------------------------------
   Grid
--------------------------------------------------------- */

.ma-register-name-grid {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr);

    gap: 12px;
}

/* ---------------------------------------------------------
   Field
--------------------------------------------------------- */

.ma-register-field {
    margin-bottom: 16px;
}

.ma-register-label {
    display: block;

    margin-bottom: 8px;

    color: #26364b;

    font-size: 13px;
    font-weight: 700;
}

.ma-register-input-wrap {
    position: relative;
}

.ma-register-input-icon {
    position: absolute;

    left: 15px;
    top: 50%;

    width: 19px;
    height: 19px;

    transform: translateY(-50%);

    color: #8a98a8;

    pointer-events: none;
}

.ma-register-input-icon svg {
    width: 100%;
    height: 100%;

    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.ma-register-input {
    width: 100%;
    height: 53px;

    padding:
        0
        45px
        0
        46px;

    border: 1.5px solid #e3e9e6;

    border-radius: 16px;

    outline: none;

    background: #fafcfb;

    color: #10213b;

    font-family: inherit;

    font-size: 15px;

    transition:
        border-color .18s ease,
        background .18s ease,
        box-shadow .18s ease;
}

.ma-register-input::placeholder {
    color: #a2acb8;
}

.ma-register-input:focus {
    border-color: var(--ma-green);

    background: #ffffff;

    box-shadow:
        0 0 0 4px rgba(8, 127, 91, .08);
}

.ma-register-input-hint {
    display: block;

    margin:
        6px
        2px
        0;

    color: #8b97a4;

    font-size: 11.5px;

    line-height: 1.4;
}

/* ---------------------------------------------------------
   Password toggle
--------------------------------------------------------- */

.ma-register-password-toggle {
    position: absolute;

    right: 8px;
    top: 50%;

    width: 38px;
    height: 38px;

    transform: translateY(-50%);

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border: 0;
    border-radius: 11px;

    background: transparent;

    color: #7c8997;

    cursor: pointer;
}

.ma-register-password-toggle:hover {
    background: #edf5f1;

    color: var(--ma-green);
}

.ma-register-password-toggle svg {
    width: 19px;
    height: 19px;

    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* ---------------------------------------------------------
   Password strength
--------------------------------------------------------- */

.ma-register-strength {
    display: none;

    margin-top: 9px;
}

.ma-register-strength.is-visible {
    display: block;
}

.ma-register-strength-bars {
    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 5px;
}

.ma-register-strength-bar {
    height: 4px;

    border-radius: 99px;

    background: #e8eeeb;

    transition:
        background .18s ease;
}

.ma-register-strength-bar.is-filled {
    background: var(--ma-green);
}

.ma-register-strength-text {
    margin-top: 6px;

    color: #8995a2;

    font-size: 11.5px;
}

/* ---------------------------------------------------------
   Terms
--------------------------------------------------------- */

.ma-register-terms {
    margin:
        4px
        2px
        17px;

    color: #8a96a3;

    font-size: 11.5px;
    line-height: 1.55;

    text-align: center;
}

.ma-register-terms a {
    color: var(--ma-green);

    font-weight: 700;

    text-decoration: none;
}

/* ---------------------------------------------------------
   Submit
--------------------------------------------------------- */

.ma-register-submit {
    width: 100%;
    height: 56px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 9px;

    border: 0;
    border-radius: 17px;

    background:
        linear-gradient(
            135deg,
            #07845e,
            #087453
        );

    color: #ffffff;

    font-family: inherit;

    font-size: 15px;
    font-weight: 800;

    cursor: pointer;

    box-shadow:
        0 10px 24px rgba(8, 127, 91, .20);

    transition:
        transform .18s ease,
        box-shadow .18s ease;
}

.ma-register-submit:hover {
    transform: translateY(-1px);

    box-shadow:
        0 13px 28px rgba(8, 127, 91, .25);
}

.ma-register-submit:active {
    transform: translateY(0);
}

.ma-register-submit svg {
    width: 18px;
    height: 18px;

    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* ---------------------------------------------------------
   Login link
--------------------------------------------------------- */

.ma-register-login {
    margin:
        21px
        0
        0;

    text-align: center;

    color: #7a8795;

    font-size: 14px;
}

.ma-register-login a {
    color: var(--ma-green);

    font-weight: 800;

    text-decoration: none;
}

.ma-register-login a:hover {
    text-decoration: underline;
}

/* ---------------------------------------------------------
   Bottom reassurance
--------------------------------------------------------- */

.ma-register-footer {
    display: flex;

    align-items: center;
    justify-content: center;

    gap: 8px;

    margin:
        18px
        auto
        0;

    color: #8b97a4;

    font-size: 11.5px;
}

.ma-register-footer svg {
    width: 15px;
    height: 15px;

    fill: none;
    stroke: #087f5b;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* ---------------------------------------------------------
   Mobile
--------------------------------------------------------- */

@media (max-width: 420px) {

    .ma-register-shell {
        padding-left: 15px;
        padding-right: 15px;
    }

    .ma-register-card {
        padding: 18px;

        border-radius: 22px;
    }

    .ma-register-title {
        font-size: 29px;
    }

    .ma-register-main {
        padding-top: 19px;
    }
}

@media (max-width: 350px) {

    .ma-register-name-grid {
        grid-template-columns: 1fr;

        gap: 0;
    }
}

@media (min-width: 700px) {

    .ma-register-main {
        max-width: 470px;
    }
}
</style>


<div class="ma-register-page">

    <div class="ma-register-shell">

        <!-- =================================================
             HEADER
        ================================================== -->

        <header class="ma-register-header">

            <a
                class="ma-register-back"
                href="<?= $escapedBasePath ?>/member"
                aria-label="Back to ManipurApp"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M15 5 8 12l7 7"></path>
                </svg>
            </a>


            <a
                class="ma-register-brand"
                href="<?= $escapedBasePath ?>/member"
                aria-label="ManipurApp home"
            >

                <span class="ma-register-brand-mark">

                    <svg
                        viewBox="0 0 40 40"
                        aria-hidden="true"
                    >

                        <path d="M20 35V18"></path>

                        <path d="M20 20C13 18 8 13 9 6c7 1 11 6 11 14"></path>

                        <path d="M20 20C27 18 32 13 31 6c-7 1-11 6-11 14"></path>

                        <path d="M20 26C14 25 10 21 10 16c6 0 10 3 10 10"></path>

                        <path d="M20 26C26 25 30 21 30 16c-6 0-10 3-10 10"></path>

                        <path d="M12 34h16"></path>

                    </svg>

                </span>

                <span>
                    ManipurApp
                </span>

            </a>


            <span
                style="
                    width:42px;
                    height:42px;
                "
            ></span>

        </header>


        <!-- =================================================
             MAIN
        ================================================== -->

        <main class="ma-register-main">

            <section class="ma-register-intro">

                <span class="ma-register-badge">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M12 3v18"></path>
                        <path d="M3 12h18"></path>
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                        ></circle>
                    </svg>

                    One account for ManipurApp

                </span>


                <h1 class="ma-register-title">
                    Create your account
                </h1>


                <p class="ma-register-subtitle">
                    Join ManipurApp and make it easier to
                    discover, book and order local services.
                </p>

            </section>


            <!-- =================================================
                 FORM CARD
            ================================================== -->

            <section class="ma-register-card">

                <?php if (!empty($error)): ?>

                    <div
                        class="ma-register-error"
                        role="alert"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                            ></circle>

                            <path d="M12 8v5"></path>
                            <path d="M12 16h.01"></path>
                        </svg>

                        <span>
                            <?= htmlspecialchars(
                                (string) $error,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </div>

                <?php endif; ?>


                <form
                    method="post"
                    action="<?= $escapedBasePath ?>/member/register"
                    novalidate
                    data-member-register-form
                >

                    <input
                        type="hidden"
                        name="_token"
                        value="<?= htmlspecialchars(
                            (string) $csrf,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >


                    <!-- =================================================
                         NAME
                    ================================================== -->

                    <div class="ma-register-name-grid">

                        <div class="ma-register-field">

                            <label
                                class="ma-register-label"
                                for="register_first_name"
                            >
                                First name
                            </label>

                            <div class="ma-register-input-wrap">

                                <span
                                    class="ma-register-input-icon"
                                    aria-hidden="true"
                                >
                                    <svg viewBox="0 0 24 24">

                                        <circle
                                            cx="12"
                                            cy="8"
                                            r="4"
                                        ></circle>

                                        <path
                                            d="M4 21c.8-4.2 3.4-6 8-6s7.2 1.8 8 6"
                                        ></path>

                                    </svg>
                                </span>

                                <input
                                    class="ma-register-input"
                                    id="register_first_name"
                                    name="first_name"
                                    type="text"
                                    autocomplete="given-name"
                                    value="<?= htmlspecialchars(
                                        (string) (
                                            $old['first_name']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    placeholder="First name"
                                    required
                                    autofocus
                                >

                            </div>

                        </div>


                        <div class="ma-register-field">

                            <label
                                class="ma-register-label"
                                for="register_last_name"
                            >
                                Last name
                            </label>

                            <div class="ma-register-input-wrap">

                                <span
                                    class="ma-register-input-icon"
                                    aria-hidden="true"
                                >
                                    <svg viewBox="0 0 24 24">

                                        <circle
                                            cx="12"
                                            cy="8"
                                            r="4"
                                        ></circle>

                                        <path
                                            d="M4 21c.8-4.2 3.4-6 8-6s7.2 1.8 8 6"
                                        ></path>

                                    </svg>
                                </span>

                                <input
                                    class="ma-register-input"
                                    id="register_last_name"
                                    name="last_name"
                                    type="text"
                                    autocomplete="family-name"
                                    value="<?= htmlspecialchars(
                                        (string) (
                                            $old['last_name']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    placeholder="Last name"
                                    required
                                >

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         EMAIL
                    ================================================== -->

                    <div class="ma-register-field">

                        <label
                            class="ma-register-label"
                            for="register_email"
                        >
                            Email address
                        </label>

                        <div class="ma-register-input-wrap">

                            <span
                                class="ma-register-input-icon"
                                aria-hidden="true"
                            >
                                <svg viewBox="0 0 24 24">

                                    <rect
                                        x="3"
                                        y="5"
                                        width="18"
                                        height="14"
                                        rx="2"
                                    ></rect>

                                    <path d="m4 7 8 6 8-6"></path>

                                </svg>
                            </span>

                            <input
                                class="ma-register-input"
                                id="register_email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                inputmode="email"
                                value="<?= htmlspecialchars(
                                    (string) (
                                        $old['email']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                placeholder="you@example.com"
                                required
                            >

                        </div>

                        <small class="ma-register-input-hint">
                            This will be used to sign in to ManipurApp.
                        </small>

                    </div>


                    <!-- =================================================
                         PHONE
                    ================================================== -->

                    <div class="ma-register-field">

                        <label
                            class="ma-register-label"
                            for="register_phone"
                        >
                            Phone number
                        </label>

                        <div class="ma-register-input-wrap">

                            <span
                                class="ma-register-input-icon"
                                aria-hidden="true"
                            >
                                <svg viewBox="0 0 24 24">

                                    <path
                                        d="M6 3h3l2 5-2 2c1 3 2 4 5 5l2-2 5 2v3c0 1-1 2-2 2C10 19 5 14 4 5c0-1 1-2 2-2Z"
                                    ></path>

                                </svg>
                            </span>

                            <input
                                class="ma-register-input"
                                id="register_phone"
                                name="phone"
                                type="tel"
                                autocomplete="tel"
                                inputmode="tel"
                                value="<?= htmlspecialchars(
                                    (string) (
                                        $old['phone']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                placeholder="Your phone number"
                                required
                            >

                        </div>

                    </div>


                    <!-- =================================================
                         PASSWORD
                    ================================================== -->

                    <div class="ma-register-field">

                        <label
                            class="ma-register-label"
                            for="register_password"
                        >
                            Password
                        </label>

                        <div class="ma-register-input-wrap">

                            <span
                                class="ma-register-input-icon"
                                aria-hidden="true"
                            >
                                <svg viewBox="0 0 24 24">

                                    <rect
                                        x="5"
                                        y="10"
                                        width="14"
                                        height="10"
                                        rx="2"
                                    ></rect>

                                    <path
                                        d="M8 10V7a4 4 0 0 1 8 0v3"
                                    ></path>

                                </svg>
                            </span>

                            <input
                                class="ma-register-input"
                                id="register_password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Create a password"
                                required
                                minlength="8"
                            >

                            <button
                                class="ma-register-password-toggle"
                                type="button"
                                data-password-toggle="register_password"
                                aria-label="Show password"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                                    ></path>

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="2.5"
                                    ></circle>
                                </svg>
                            </button>

                        </div>


                        <div
                            class="ma-register-strength"
                            id="ma-register-strength"
                        >

                            <div class="ma-register-strength-bars">

                                <span class="ma-register-strength-bar"></span>
                                <span class="ma-register-strength-bar"></span>
                                <span class="ma-register-strength-bar"></span>
                                <span class="ma-register-strength-bar"></span>

                            </div>

                            <div
                                class="ma-register-strength-text"
                                id="ma-register-strength-text"
                            >
                                Use 8 or more characters.
                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         CONFIRM PASSWORD
                    ================================================== -->

                    <div class="ma-register-field">

                        <label
                            class="ma-register-label"
                            for="register_password_confirmation"
                        >
                            Confirm password
                        </label>

                        <div class="ma-register-input-wrap">

                            <span
                                class="ma-register-input-icon"
                                aria-hidden="true"
                            >
                                <svg viewBox="0 0 24 24">

                                    <rect
                                        x="5"
                                        y="10"
                                        width="14"
                                        height="10"
                                        rx="2"
                                    ></rect>

                                    <path
                                        d="M8 10V7a4 4 0 0 1 8 0v3"
                                    ></path>

                                </svg>
                            </span>

                            <input
                                class="ma-register-input"
                                id="register_password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Enter it again"
                                required
                            >

                            <button
                                class="ma-register-password-toggle"
                                type="button"
                                data-password-toggle="register_password_confirmation"
                                aria-label="Show password"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                                    ></path>

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="2.5"
                                    ></circle>
                                </svg>
                            </button>

                        </div>

                    </div>


                    <!-- =================================================
                         TERMS
                    ================================================== -->

                    <p class="ma-register-terms">

                        By creating an account, you agree to our
                        <a href="/terms">
                            Terms of Service
                        </a>
                        and
                        <a href="/privacy">
                            Privacy Policy
                        </a>.

                    </p>


                    <!-- =================================================
                         SUBMIT
                    ================================================== -->

                    <button
                        class="ma-register-submit"
                        type="submit"
                    >

                        <span>
                            Create my account
                        </span>

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M5 12h13"></path>
                            <path d="m13 6 6 6-6 6"></path>
                        </svg>

                    </button>

                </form>

            </section>


            <!-- =================================================
                 LOGIN
            ================================================== -->

            <p class="ma-register-login">

                Already have an account?

                <a
                    href="<?= $escapedBasePath ?>/member/login"
                >
                    Sign in
                </a>

            </p>


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <div class="ma-register-footer">

                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        d="M12 3 5 6v5c0 4.5 2.8 8.2 7 10 4.2-1.8 7-5.5 7-10V6l-7-3Z"
                    ></path>

                    <path d="m9 12 2 2 4-4"></path>
                </svg>

                <span>
                    Your account is protected with secure login.
                </span>

            </div>

        </main>

    </div>

</div>


<script>
(function () {

    "use strict";


    /* =====================================================
       PASSWORD VISIBILITY
    ===================================================== */

    document
        .querySelectorAll(
            ".ma-register-password-toggle"
        )
        .forEach(function (button) {

            button.addEventListener(
                "click",
                function () {

                    const inputId =
                        button.getAttribute(
                            "data-password-toggle"
                        );

                    const input =
                        document.getElementById(
                            inputId
                        );

                    if (!input) {
                        return;
                    }

                    const isPassword =
                        input.type === "password";

                    input.type =
                        isPassword
                            ? "text"
                            : "password";

                    button.setAttribute(
                        "aria-label",
                        isPassword
                            ? "Hide password"
                            : "Show password"
                    );

                }
            );

        });


    /* =====================================================
       PASSWORD STRENGTH
    ===================================================== */

    const password =
        document.getElementById(
            "register_password"
        );

    const strength =
        document.getElementById(
            "ma-register-strength"
        );

    const strengthText =
        document.getElementById(
            "ma-register-strength-text"
        );

    const bars =
        document.querySelectorAll(
            ".ma-register-strength-bar"
        );


    if (
        password &&
        strength &&
        strengthText &&
        bars.length
    ) {

        password.addEventListener(
            "input",
            function () {

                const value =
                    password.value;

                if (!value.length) {

                    strength.classList.remove(
                        "is-visible"
                    );

                    bars.forEach(
                        function (bar) {
                            bar.classList.remove(
                                "is-filled"
                            );
                        }
                    );

                    return;
                }


                strength.classList.add(
                    "is-visible"
                );


                let score = 0;


                if (value.length >= 8) {
                    score++;
                }

                if (/[A-Z]/.test(value)) {
                    score++;
                }

                if (/[0-9]/.test(value)) {
                    score++;
                }

                if (/[^A-Za-z0-9]/.test(value)) {
                    score++;
                }


                bars.forEach(
                    function (bar, index) {

                        bar.classList.toggle(
                            "is-filled",
                            index < score
                        );

                    }
                );


                const labels = [
                    "Very weak password",
                    "Weak password",
                    "Good password",
                    "Strong password",
                    "Very strong password"
                ];


                strengthText.textContent =
                    labels[score];

            }
        );

    }


    /* =====================================================
       PASSWORD MATCH FEEDBACK
    ===================================================== */

    const confirmation =
        document.getElementById(
            "register_password_confirmation"
        );


    if (
        password &&
        confirmation
    ) {

        confirmation.addEventListener(
            "input",
            function () {

                if (
                    confirmation.value.length &&
                    password.value !==
                    confirmation.value
                ) {

                    confirmation.setCustomValidity(
                        "Passwords do not match."
                    );

                } else {

                    confirmation.setCustomValidity(
                        ""
                    );

                }

            }
        );

    }


})();
</script>