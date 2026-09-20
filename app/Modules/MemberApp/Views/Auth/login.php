<?php

declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';


$basePath = (string) config('app.base_path');

$old = is_array($old ?? null)
    ? $old
    : [];

$returnTo = $_GET['return_to']
    ?? $_POST['return_to']
    ?? '';

$escapedBasePath = htmlspecialchars(
    $basePath,
    ENT_QUOTES,
    'UTF-8'
);

$escapedReturnTo = htmlspecialchars(
    (string) $returnTo,
    ENT_QUOTES,
    'UTF-8'
);
?>

<style>
/* =========================================================
   MANIPURAPP AUTH — LOGIN
   Scoped to .ma-auth-page
========================================================= */

.ma-auth-page {
    --ma-green: #087f5b;
    --ma-green-dark: #056247;
    --ma-green-soft: #e8f7f1;
    --ma-green-pale: #f3fbf7;

    min-height: 100dvh;
    width: 100%;
    background:
        radial-gradient(
            circle at 15% 10%,
            rgba(8, 127, 91, 0.06),
            transparent 28%
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

.ma-auth-page *,
.ma-auth-page *::before,
.ma-auth-page *::after {
    box-sizing: border-box;
}

/* ---------------------------------------------------------
   Shell
--------------------------------------------------------- */

.ma-auth-shell {
    width: 100%;
    min-height: 100dvh;

    display: flex;
    flex-direction: column;

    padding:
        max(22px, env(safe-area-inset-top))
        20px
        max(24px, env(safe-area-inset-bottom));
}

/* ---------------------------------------------------------
   Top bar
--------------------------------------------------------- */

.ma-auth-topbar {
    width: 100%;
    max-width: 520px;

    margin: 0 auto;

    display: flex;
    align-items: center;
    justify-content: space-between;

    min-height: 48px;
}

.ma-auth-back {
    width: 42px;
    height: 42px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    color: #26364b;
    background: #f5f7f8;

    text-decoration: none;

    transition:
        background .18s ease,
        transform .18s ease;
}

.ma-auth-back:hover {
    background: #eaf1ee;
}

.ma-auth-back:active {
    transform: scale(.96);
}

.ma-auth-back svg {
    width: 20px;
    height: 20px;

    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* ---------------------------------------------------------
   Brand
--------------------------------------------------------- */

.ma-auth-brand {
    display: inline-flex;
    align-items: center;
    gap: 9px;

    color: var(--ma-green);

    text-decoration: none;

    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.5px;
}

.ma-auth-brand-mark {
    width: 34px;
    height: 34px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    color: var(--ma-green);
}

.ma-auth-brand-mark svg {
    width: 34px;
    height: 34px;

    fill: none;
    stroke: currentColor;
    stroke-width: 1.9;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* ---------------------------------------------------------
   Main content
--------------------------------------------------------- */

.ma-auth-main {
    width: 100%;
    max-width: 520px;

    margin: 0 auto;

    flex: 1;

    display: flex;
    flex-direction: column;

    justify-content: center;
}

/* ---------------------------------------------------------
   Intro
--------------------------------------------------------- */

.ma-auth-intro {
    text-align: center;

    padding:
        26px
        0
        24px;
}

.ma-auth-intro-icon {
    width: 68px;
    height: 68px;

    margin: 0 auto 20px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 22px;

    background:
        linear-gradient(
            145deg,
            #effaf5,
            #dff4eb
        );

    color: var(--ma-green);

    box-shadow:
        0 12px 30px rgba(8, 127, 91, 0.08);
}

.ma-auth-intro-icon svg {
    width: 34px;
    height: 34px;

    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.ma-auth-title {
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

.ma-auth-subtitle {
    max-width: 360px;

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

.ma-auth-form-card {
    width: 100%;

    background: #ffffff;

    border: 1px solid #edf1ef;

    border-radius: 26px;

    padding: 22px;

    box-shadow:
        0 16px 50px rgba(16, 33, 59, 0.07);
}

/* ---------------------------------------------------------
   Error
--------------------------------------------------------- */

.ma-auth-error {
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

.ma-auth-error svg {
    flex: 0 0 auto;

    width: 18px;
    height: 18px;

    margin-top: 1px;

    fill: none;
    stroke: currentColor;
    stroke-width: 2;
}

/* ---------------------------------------------------------
   Fields
--------------------------------------------------------- */

.ma-auth-field {
    margin-bottom: 17px;
}

.ma-auth-label {
    display: block;

    margin-bottom: 8px;

    color: #26364b;

    font-size: 13px;
    font-weight: 700;
}

.ma-auth-input-wrap {
    position: relative;

    width: 100%;
}

.ma-auth-input-icon {
    position: absolute;

    left: 15px;
    top: 50%;

    width: 19px;
    height: 19px;

    transform: translateY(-50%);

    color: #8a98a8;

    pointer-events: none;
}

.ma-auth-input-icon svg {
    width: 100%;
    height: 100%;

    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.ma-auth-input {
    width: 100%;
    height: 54px;

    border: 1.5px solid #e3e9e6;

    border-radius: 16px;

    outline: none;

    background: #fafcfb;

    color: #10213b;

    padding:
        0
        48px
        0
        46px;

    font-family: inherit;

    font-size: 15px;

    transition:
        border-color .18s ease,
        background .18s ease,
        box-shadow .18s ease;
}

.ma-auth-input::placeholder {
    color: #a2acb8;
}

.ma-auth-input:hover {
    border-color: #cbd8d2;
}

.ma-auth-input:focus {
    border-color: var(--ma-green);

    background: #ffffff;

    box-shadow:
        0 0 0 4px rgba(8, 127, 91, 0.08);
}

.ma-auth-password-toggle {
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

    transition:
        background .18s ease,
        color .18s ease;
}

.ma-auth-password-toggle:hover {
    background: #edf5f1;
    color: var(--ma-green);
}

.ma-auth-password-toggle svg {
    width: 19px;
    height: 19px;

    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* ---------------------------------------------------------
   Options
--------------------------------------------------------- */

.ma-auth-options {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 12px;

    margin:
        3px
        1px
        20px;
}

.ma-auth-check {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    color: #667486;

    font-size: 13px;

    cursor: pointer;

    user-select: none;
}

.ma-auth-check input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.ma-auth-check-box {
    width: 18px;
    height: 18px;

    border: 1.5px solid #d3ddd8;

    border-radius: 5px;

    background: #ffffff;

    display: flex;
    align-items: center;
    justify-content: center;

    transition: all .18s ease;
}

.ma-auth-check input:checked + .ma-auth-check-box {
    border-color: var(--ma-green);
    background: var(--ma-green);
}

.ma-auth-check input:checked + .ma-auth-check-box::after {
    content: "";

    width: 8px;
    height: 5px;

    border-left: 2px solid #ffffff;
    border-bottom: 2px solid #ffffff;

    transform:
        rotate(-45deg)
        translate(1px, -1px);
}

.ma-auth-forgot {
    color: var(--ma-green);

    font-size: 13px;
    font-weight: 700;

    text-decoration: none;
}

.ma-auth-forgot:hover {
    text-decoration: underline;
}

/* ---------------------------------------------------------
   Submit
--------------------------------------------------------- */

.ma-auth-submit {
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
        0 10px 24px rgba(8, 127, 91, 0.20);

    transition:
        transform .18s ease,
        box-shadow .18s ease;
}

.ma-auth-submit:hover {
    transform: translateY(-1px);

    box-shadow:
        0 13px 28px rgba(8, 127, 91, 0.25);
}

.ma-auth-submit:active {
    transform: translateY(0);
}

.ma-auth-submit svg {
    width: 18px;
    height: 18px;

    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* ---------------------------------------------------------
   Switch
--------------------------------------------------------- */

.ma-auth-switch {
    margin: 21px 0 0;

    text-align: center;

    color: #7a8795;

    font-size: 14px;
}

.ma-auth-switch a {
    color: var(--ma-green);

    font-weight: 800;

    text-decoration: none;
}

.ma-auth-switch a:hover {
    text-decoration: underline;
}

/* ---------------------------------------------------------
   Bottom illustration
--------------------------------------------------------- */

.ma-auth-landscape {
    position: relative;

    width: 100%;
    height: 88px;

    margin-top: 28px;

    overflow: hidden;

    border-radius: 24px;

    background:
        linear-gradient(
            180deg,
            #f2faf7 0%,
            #e6f5ef 100%
        );
}

.ma-auth-landscape::before,
.ma-auth-landscape::after {
    content: "";

    position: absolute;

    left: -5%;
    bottom: -32px;

    width: 70%;
    height: 105px;

    border-radius: 55% 55% 0 0;

    background: #cce9dc;

    transform: rotate(-7deg);
}

.ma-auth-landscape::after {
    left: 38%;
    bottom: -39px;

    width: 72%;
    height: 118px;

    background: #b5dfce;

    transform: rotate(7deg);
}

.ma-auth-landmark {
    position: absolute;

    z-index: 3;

    left: 50%;
    bottom: 13px;

    width: 3px;
    height: 35px;

    transform: translateX(-50%);

    background: #5c9d82;
}

.ma-auth-landmark::before {
    content: "";

    position: absolute;

    left: 50%;
    top: 0;

    width: 14px;
    height: 14px;

    transform:
        translateX(-50%)
        rotate(45deg);

    border-radius: 3px;

    background: #087f5b;
}

.ma-auth-landscape-text {
    position: absolute;

    z-index: 5;

    left: 18px;
    top: 18px;

    color: #28745c;

    font-size: 12px;
    font-weight: 700;

    letter-spacing: .2px;
}

/* ---------------------------------------------------------
   Responsive
--------------------------------------------------------- */

@media (max-width: 420px) {

    .ma-auth-shell {
        padding-left: 15px;
        padding-right: 15px;
    }

    .ma-auth-form-card {
        padding: 18px;

        border-radius: 22px;
    }

    .ma-auth-title {
        font-size: 29px;
    }

    .ma-auth-intro {
        padding-top: 20px;
    }

    .ma-auth-options {
        align-items: flex-start;
    }
}

@media (min-width: 700px) {

    .ma-auth-shell {
        padding-top: 28px;
    }

    .ma-auth-main {
        max-width: 470px;
    }
}
</style>

<div class="ma-auth-page">

    <div class="ma-auth-shell">

        <!-- =================================================
             TOP BAR
        ================================================== -->

        <header class="ma-auth-topbar">

            <a
                class="ma-auth-back"
                href="<?= $escapedBasePath ?>/member"
                aria-label="Back to ManipurApp"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M15 5 8 12l7 7"></path>
                </svg>
            </a>

            <a
                class="ma-auth-brand"
                href="<?= $escapedBasePath ?>/member"
                aria-label="ManipurApp home"
            >
                <span class="ma-auth-brand-mark">
                    <svg viewBox="0 0 40 40" aria-hidden="true">
                        <path d="M20 35V18"></path>

                        <path d="M20 20C13 18 8 13 9 6c7 1 11 6 11 14"></path>

                        <path d="M20 20C27 18 32 13 31 6c-7 1-11 6-11 14"></path>

                        <path d="M20 26C14 25 10 21 10 16c6 0 10 3 10 10"></path>

                        <path d="M20 26C26 25 30 21 30 16c-6 0-10 3-10 10"></path>

                        <path d="M12 34h16"></path>
                    </svg>
                </span>

                <span>ManipurApp</span>
            </a>

            <span style="width:42px;height:42px;"></span>

        </header>


        <!-- =================================================
             MAIN
        ================================================== -->

        <main class="ma-auth-main">

            <section class="ma-auth-intro">

                <div class="ma-auth-intro-icon">

                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                    </svg>

                </div>

                <h1 class="ma-auth-title">
                    Welcome back
                </h1>

                <p class="ma-auth-subtitle">
                    Sign in to continue exploring
                    Manipur and using local services.
                </p>

            </section>


            <!-- =================================================
                 FORM
            ================================================== -->

            <section class="ma-auth-form-card">

                <?php if (!empty($error)): ?>

                    <div
                        class="ma-auth-error"
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
                    action="<?= $escapedBasePath ?>/member/login"
                    novalidate
                    data-member-login-form
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

                    <?php if ($returnTo !== ''): ?>

                        <input
                            type="hidden"
                            name="return_to"
                            value="<?= $escapedReturnTo ?>"
                        >

                    <?php endif; ?>


                    <!-- EMAIL -->

                    <div class="ma-auth-field">

                        <label
                            class="ma-auth-label"
                            for="login_email"
                        >
                            Email address
                        </label>

                        <div class="ma-auth-input-wrap">

                            <span
                                class="ma-auth-input-icon"
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
                                class="ma-auth-input"
                                id="login_email"
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
                                autofocus
                            >

                        </div>

                    </div>


                    <!-- PASSWORD -->

                    <div class="ma-auth-field">

                        <label
                            class="ma-auth-label"
                            for="login_password"
                        >
                            Password
                        </label>

                        <div class="ma-auth-input-wrap">

                            <span
                                class="ma-auth-input-icon"
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
                                class="ma-auth-input"
                                id="login_password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                required
                            >

                            <button
                                class="ma-auth-password-toggle"
                                type="button"
                                data-password-toggle="login_password"
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


                    <!-- OPTIONS -->

                    <div class="ma-auth-options">

                        <label class="ma-auth-check">

                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                            >

                            <span class="ma-auth-check-box"></span>

                            <span>
                                Remember me
                            </span>

                        </label>


                        <a
                            class="ma-auth-forgot"
                            href="#forgot-password"
                            data-coming-soon
                        >
                            Forgot password?
                        </a>

                    </div>


                    <!-- SUBMIT -->

                    <button
                        class="ma-auth-submit"
                        type="submit"
                    >

                        <span>
                            Sign in
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
                 REGISTER LINK
            ================================================== -->

            <p class="ma-auth-switch">

                New to ManipurApp?

                <a
                    href="<?= $escapedBasePath ?>/member/register"
                >
                    Create an account
                </a>

            </p>


            <!-- =================================================
                 LANDSCAPE
            ================================================== -->

            <div
                class="ma-auth-landscape"
                aria-hidden="true"
            >

                <span class="ma-auth-landscape-text">
                    Eigi Manipur · Local Services · One App
                </span>

                <span class="ma-auth-landmark"></span>

            </div>

        </main>

    </div>

</div>