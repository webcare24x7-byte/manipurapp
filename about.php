<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Learn why ChurchOS exists, our mission to make church technology accessible to every church, and our vision for connected churches, websites, giving and missions.">
    <meta name="theme-color" content="#5146e5">
    <title>About ChurchOS — Technology for Every Church</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>

     /* =========================================================
   CHURCHOS — DESIGN SYSTEM
========================================================= */

:root {

    --primary: #5146e5;
    --primary-dark: #4037c8;
    --primary-light: #edeaff;

    --navy: #111936;
    --navy-2: #182044;

    --text: #182038;
    --text-soft: #556078;
    --muted: #7a849a;

    --background: #ffffff;
    --background-soft: #f8f9fd;
    --background-purple: #f5f4ff;

    --border: #e7e9f1;

    --green: #15a675;
    --green-light: #e7f8f1;

    --pink: #e75c8b;
    --pink-light: #fff0f5;

    --blue: #3985e8;
    --blue-light: #edf5ff;

    --orange: #e68a35;
    --orange-light: #fff4e7;

    --yellow: #e2a72c;
    --yellow-light: #fff8df;

    --shadow-sm:
        0 5px 20px rgba(21, 27, 58, .05);

    --shadow:
        0 20px 50px rgba(30, 35, 80, .09);

    --shadow-lg:
        0 30px 80px rgba(30, 35, 80, .16);

    --radius-sm: 10px;
    --radius: 16px;
    --radius-lg: 24px;

    --container: 1180px;

}


/* =========================================================
   RESET
========================================================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {

    font-family:
        "DM Sans",
        Arial,
        sans-serif;

    color: var(--text);

    background: var(--background);

    line-height: 1.6;

    overflow-x: hidden;

}

button,
input,
textarea,
select {
    font: inherit;
}

button {
    border: 0;
    background: none;
    cursor: pointer;
}

a {
    text-decoration: none;
    color: inherit;
}

img {
    max-width: 100%;
    display: block;
}

.container {

    width: min(
        calc(100% - 48px),
        var(--container)
    );

    margin-inline: auto;

}


/* =========================================================
   TYPOGRAPHY
========================================================= */

h1,
h2,
h3,
h4 {

    font-family:
        "Plus Jakarta Sans",
        sans-serif;

    line-height: 1.15;

    color: var(--navy);

}

h1 {
    font-size: clamp(44px, 5.4vw, 72px);
    letter-spacing: -3px;
}

h2 {

    font-size: clamp(34px, 4vw, 52px);

    letter-spacing: -2px;

}

h3 {
    font-size: 21px;
}

p {
    color: var(--text-soft);
}


/* =========================================================
   BUTTONS
========================================================= */

.button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

    border-radius: 10px;

    font-size: 14px;

    font-weight: 700;

    padding: 13px 19px;

    transition:
        transform .2s ease,
        box-shadow .2s ease,
        background .2s ease;

}

.button:hover {

    transform: translateY(-2px);

}

.button-primary {

    color: white;

    background:
        linear-gradient(
            135deg,
            #5d51ea,
            #493ed0
        );

    box-shadow:
        0 10px 25px
        rgba(81, 70, 229, .22);

}

.button-primary:hover {

    box-shadow:
        0 15px 30px
        rgba(81, 70, 229, .30);

}

.button-secondary {

    color: var(--navy);

    background: white;

    border: 1px solid var(--border);

}

.button-large {

    padding: 16px 23px;

    border-radius: 11px;

}


/* =========================================================
   HEADER
========================================================= */

.site-header {

    position: fixed;

    top: 0;

    left: 0;

    right: 0;

    z-index: 1000;

    background:
        rgba(255,255,255,.84);

    backdrop-filter:
        blur(18px);

    border-bottom:
        1px solid transparent;

    transition:
        background .25s ease,
        border .25s ease,
        box-shadow .25s ease;

}

.site-header.scrolled {

    background:
        rgba(255,255,255,.95);

    border-color:
        var(--border);

    box-shadow:
        0 8px 30px
        rgba(20,25,60,.05);

}

.nav-wrapper {

    height: 78px;

    display: flex;

    align-items: center;

    justify-content: space-between;

}


/* Brand */

.brand {

    display: flex;

    align-items: center;

    gap: 9px;

    flex-shrink: 0;

}

.brand-mark {

    width: 34px;
    height: 34px;

    display: grid;

    place-items: center;

    color: var(--primary);

}

.brand-mark svg {
    width: 100%;
}

.brand-name {

    font-family:
        "Plus Jakarta Sans",
        sans-serif;

    font-size: 21px;

    font-weight: 800;

    letter-spacing: -1px;

    color: var(--navy);

}

.brand-name span {
    color: var(--primary);
}


/* Desktop navigation */

.desktop-nav {

    display: flex;

    align-items: center;

    gap: 28px;

    margin-left: auto;

    margin-right: 38px;

}

.desktop-nav > a,
.dropdown-button {

    font-size: 13px;

    font-weight: 600;

    color: #303951;

    transition:
        color .2s ease;

}

.desktop-nav > a:hover,
.dropdown-button:hover {

    color: var(--primary);

}


.nav-dropdown {
    position: relative;
}

.dropdown-button {

    display: flex;

    gap: 5px;

    align-items: center;

}

.dropdown-menu {

    position: absolute;

    top: calc(100% + 15px);

    left: -18px;

    width: 180px;

    padding: 10px;

    background: white;

    border:
        1px solid var(--border);

    border-radius: 12px;

    box-shadow:
        var(--shadow);

    opacity: 0;

    visibility: hidden;

    transform:
        translateY(-5px);

    transition: all .2s ease;

}

.nav-dropdown:hover .dropdown-menu {

    opacity: 1;

    visibility: visible;

    transform:
        translateY(0);

}

.dropdown-menu a {

    display: block;

    padding: 9px 10px;

    border-radius: 8px;

    font-size: 13px;

}

.dropdown-menu a:hover {

    background:
        var(--background-soft);

    color:
        var(--primary);

}


.nav-actions {

    display: flex;

    align-items: center;

    gap: 22px;

}

.login-link {

    font-size: 13px;

    font-weight: 600;

}

.login-link:hover {
    color: var(--primary);
}


/* Mobile */

.mobile-menu-button {

    display: none;

    width: 40px;
    height: 40px;

    align-items: center;
    justify-content: center;

    flex-direction: column;

    gap: 5px;

}

.mobile-menu-button span {

    width: 22px;
    height: 2px;

    background:
        var(--navy);

    border-radius: 2px;

}

.mobile-menu {

    display: none;

}


/* =========================================================
   HERO
========================================================= */

.hero {

    position: relative;

    padding-top: 145px;

    min-height: 740px;

    overflow: hidden;

    background:
        linear-gradient(
            180deg,
            #f7f7ff 0%,
            #ffffff 100%
        );

}

.hero-background {

    position: absolute;

    inset: 0;

    pointer-events: none;

    background:

        radial-gradient(
            circle at 10% 20%,
            rgba(81,70,229,.10),
            transparent 28%
        ),

        radial-gradient(
            circle at 80% 25%,
            rgba(113,96,242,.13),
            transparent 32%
        );

}

.hero-grid {

    display: grid;

    grid-template-columns:
        .88fr 1.12fr;

    align-items: center;

    gap: 40px;

}

.hero-content {

    position: relative;

    z-index: 2;

}

.eyebrow {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 7px 12px;

    border-radius: 100px;

    background:
        rgba(81,70,229,.08);

    color:
        var(--primary);

    font-size: 12px;

    font-weight: 700;

    margin-bottom: 22px;

}

.eyebrow-dot {

    width: 7px;
    height: 7px;

    background:
        var(--primary);

    border-radius: 50%;

}

.hero h1 {

    max-width: 700px;

}

.hero h1 span {

    display: block;

    color: var(--primary);

}

.hero-description {

    max-width: 580px;

    font-size: 17px;

    margin-top: 24px;

    line-height: 1.75;

}

.hero-buttons {

    display: flex;

    align-items: center;

    gap: 12px;

    margin-top: 32px;

}

.hero-trust {

    display: flex;

    flex-wrap: wrap;

    gap: 18px;

    margin-top: 25px;

}

.hero-trust span {

    display: flex;

    align-items: center;

    gap: 7px;

    font-size: 11px;

    color: #667087;

}

.hero-trust i {

    width: 16px;
    height: 16px;

    display: grid;

    place-items: center;

    border-radius: 50%;

    color:
        var(--primary);

    background:
        #e8e6ff;

    font-style: normal;

    font-size: 10px;

}


/* =========================================================
   DASHBOARD MOCKUP
========================================================= */

.hero-visual {

    position: relative;

    min-height: 550px;

    display: flex;

    align-items: center;

    justify-content: center;

}

.dashboard-glow {

    position: absolute;

    width: 650px;
    height: 480px;

    background:
        radial-gradient(
            ellipse,
            rgba(81,70,229,.20),
            transparent 68%
        );

    filter:
        blur(15px);

}

.dashboard-window {

    position: relative;

    z-index: 2;

    width: 650px;

    border-radius: 18px;

    overflow: hidden;

    background: #fff;

    border:
        1px solid rgba(60,70,120,.10);

    box-shadow:
        var(--shadow-lg);

    transform:
        perspective(1500px)
        rotateY(-4deg)
        rotateX(2deg);

}

.dashboard-topbar {

    height: 43px;

    display: flex;

    align-items: center;

    justify-content: center;

    position: relative;

    border-bottom:
        1px solid #edf0f5;

}

.window-controls {

    position: absolute;

    left: 16px;

    display: flex;

    gap: 5px;

}

.window-controls span {

    width: 7px;
    height: 7px;

    border-radius: 50%;

    background:
        #dfe2ea;

}

.dashboard-title {

    font-size: 9px;

    font-weight: 700;

    color:
        #687187;

}

.dashboard-profile {

    position: absolute;

    right: 15px;

}

.profile-avatar {

    width: 22px;
    height: 22px;

    display: grid;

    place-items: center;

    border-radius: 50%;

    font-size: 7px;

    font-weight: 700;

    color: white;

    background:
        linear-gradient(
            135deg,
            #5146e5,
            #8c81ff
        );

}

.dashboard-body {

    display: flex;

    min-height: 430px;

}

.dashboard-sidebar {

    width: 125px;

    padding: 18px 9px;

    background:
        #fafbfe;

    border-right:
        1px solid #edf0f5;

}

.mini-logo {

    width: 26px;
    height: 26px;

    margin:
        0 auto 18px;

    display: grid;

    place-items: center;

    border-radius: 7px;

    color: white;

    background:
        var(--primary);

    font-weight: 700;

    font-size: 14px;

}

.sidebar-item {

    display: flex;

    align-items: center;

    gap: 7px;

    padding: 7px 8px;

    margin-bottom: 2px;

    border-radius: 6px;

    font-size: 7px;

    color: #788198;

}

.sidebar-item span {

    font-size: 9px;

}

.sidebar-item.active {

    color: var(--primary);

    background:
        #eeecff;

    font-weight: 700;

}

.dashboard-content {

    flex: 1;

    padding: 18px;

    background: white;

}

.dashboard-heading {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 15px;

}

.dashboard-heading small {

    display: block;

    font-size: 7px;

    color: #8c94a5;

}

.dashboard-heading h3 {

    font-size: 15px;

    margin-top: 2px;

}

.church-selector {

    border:
        1px solid #e7e9ef;

    border-radius: 6px;

    padding: 6px 8px;

    color: #596278;

    background: white;

    font-size: 7px;

}

.stat-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 8px;

}

.dashboard-stat {

    display: flex;

    gap: 7px;

    padding: 10px;

    border:
        1px solid #edf0f4;

    border-radius: 8px;

}

.stat-icon {

    width: 24px;
    height: 24px;

    display: grid;

    place-items: center;

    flex-shrink: 0;

    border-radius: 7px;

    font-size: 10px;

}

.stat-icon.purple {
    color: var(--primary);
    background: #efedff;
}

.stat-icon.blue {
    color: var(--blue);
    background: #edf5ff;
}

.stat-icon.green {
    color: var(--green);
    background: #e9f8f2;
}

.stat-icon.pink {
    color: var(--pink);
    background: #fff0f5;
}

.dashboard-stat small {

    display: block;

    font-size: 6px;

    color: #8b93a5;

}

.dashboard-stat strong {

    display: block;

    font-size: 12px;

    margin-top: 1px;

}

.dashboard-stat em {

    display: block;

    font-size: 5px;

    color: var(--green);

    font-style: normal;

}

.dashboard-panels {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 8px;

    margin-top: 9px;

}

.dashboard-card {

    padding: 12px;

    border:
        1px solid #edf0f4;

    border-radius: 9px;

}

.card-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 9px;

}

.card-header small {

    display: block;

    color: #9299aa;

    font-size: 6px;

}

.card-header h4 {

    font-size: 10px;

    margin-top: 2px;

}

.card-header a {

    color: var(--primary);

    font-size: 6px;

}

.event-row {

    display: flex;

    align-items: center;

    gap: 8px;

    padding: 7px 0;

    border-bottom:
        1px solid #f0f1f5;

}

.event-row:last-child {
    border-bottom: 0;
}

.event-date {

    width: 25px;
    height: 29px;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    border-radius: 5px;

    background: #f1efff;

    color: var(--primary);

}

.event-date strong {

    font-size: 9px;

    line-height: 1;

}

.event-date span {

    font-size: 5px;

    font-weight: 700;

}

.purple-date {

    color: #7354d9;
    background: #f3ecff;

}

.green-date {

    color: var(--green);
    background: var(--green-light);

}

.event-row > div:last-child strong {

    display: block;

    font-size: 7px;

}

.event-row > div:last-child small {

    display: block;

    font-size: 5px;

    color: #969dac;

}

.chart-period {

    padding: 4px 6px;

    border-radius: 4px;

    font-size: 5px;

    color: #6e7689;

    background: #f7f8fa;

}

.chart-value {

    font-size: 17px;

    font-weight: 800;

    color: var(--navy);

}

.chart {

    position: relative;

    height: 100px;

    margin-top: 4px;

}

.chart svg {

    width: 100%;

    height: 100%;

}

.chart-area {

    fill:
        url(#chartGradient);

}

.chart-line {

    fill: none;

    stroke: #5b50e8;

    stroke-width: 3;

    vector-effect: non-scaling-stroke;

}

.chart-grid-line {

    position: absolute;

    left: 0;

    right: 0;

    height: 1px;

    background: #f0f1f5;

}

.line-1 {
    top: 20%;
}

.line-2 {
    top: 50%;
}

.line-3 {
    top: 80%;
}

.dashboard-bottom {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 8px;

    margin-top: 9px;

}

.mini-dashboard-card {

    min-height: 90px;

}

.ministry-mini {

    display: flex;

    align-items: center;

    gap: 7px;

    padding: 5px 0;

}

.ministry-dot {

    width: 16px;
    height: 16px;

    border-radius: 50%;

    background: #fff0b9;

}

.purple-dot {
    background: #dedaff;
}

.ministry-mini strong {

    display: block;

    font-size: 7px;

}

.ministry-mini small {

    display: block;

    font-size: 5px;

    color: #959cad;

}

.activity-item {

    display: flex;

    align-items: center;

    gap: 7px;

    margin-top: 6px;

}

.activity-avatar {

    width: 17px;
    height: 17px;

    display: grid;

    place-items: center;

    border-radius: 50%;

    background: #e9e6ff;

    color: var(--primary);

    font-size: 5px;

    font-weight: 700;

}

.green-avatar {

    color: var(--green);

    background: var(--green-light);

}

.activity-item strong {

    display: block;

    font-size: 6px;

}

.activity-item small {

    display: block;

    font-size: 5px;

    color: #999fac;

}


/* Floating mobile dashboard */

.mobile-dashboard-card {

    position: absolute;

    z-index: 4;

    left: -10px;

    bottom: 18px;

    width: 145px;

    padding: 13px;

    border-radius: 15px;

    background: white;

    box-shadow:
        0 20px 50px
        rgba(30,35,80,.18);

    border:
        1px solid #eceef5;

}

.mobile-top {

    display: flex;

    justify-content: space-between;

    font-size: 7px;

    font-weight: 700;

}

.mobile-attendance {

    margin-top: 10px;

    padding: 10px;

    border-radius: 10px;

    color: white;

    background:
        linear-gradient(
            135deg,
            #594de5,
            #786ef5
        );

}

.mobile-attendance small {

    display: block;

    font-size: 6px;

    opacity: .8;

}

.mobile-attendance strong {

    display: block;

    font-size: 22px;

    line-height: 1.1;

}

.mobile-attendance span {

    font-size: 5px;

}

.mobile-events {

    margin-top: 10px;

}

.mobile-events > small {

    font-size: 6px;

    color: #858da0;

}

.mobile-events div {

    display: flex;

    align-items: center;

    gap: 7px;

    margin-top: 7px;

    font-size: 6px;

    font-weight: 600;

}

.mobile-events b {

    width: 22px;
    height: 22px;

    display: grid;

    place-items: center;

    border-radius: 6px;

    background: #f0efff;

    color: var(--primary);

}


/* =========================================================
   TRUST
========================================================= */

.trust-section {

    padding: 35px 0;

    border-bottom:
        1px solid #eff0f4;

}

.trust-label {

    text-align: center;

    font-size: 10px;

    font-weight: 700;

    letter-spacing: 1.3px;

    color: #8991a3;

}

.church-types {

    display: grid;

    grid-template-columns:
        repeat(5, 1fr);

    margin-top: 25px;

    gap: 15px;

}

.church-types > div {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    color: #687188;

    font-size: 12px;

    font-weight: 600;

}

.church-type-icon {

    color: #7c82a2;

    font-size: 20px;

}


/* =========================================================
   SECTIONS
========================================================= */

.section {

    padding: 105px 0;

}

.section-heading {

    max-width: 650px;

}

.centered {

    text-align: center;

    margin-inline: auto;

}

.section-label {

    font-size: 10px;

    font-weight: 800;

    letter-spacing: 1.4px;

    color: var(--primary);

    margin-bottom: 14px;

}

.section-heading h2 span,
.split-content h2 span,
.why-content h2 span,
.giving-content h2 span,
.cta-content h2 span {

    color: var(--primary);

}

.section-heading p {

    margin-top: 18px;

    font-size: 16px;

}


/* =========================================================
   FEATURES
========================================================= */

.feature-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 15px;

    margin-top: 55px;

}

.feature-card {

    padding: 27px;

    border:
        1px solid var(--border);

    border-radius: 16px;

    background: white;

    transition:
        transform .25s ease,
        box-shadow .25s ease,
        border .25s ease;

}

.feature-card:hover {

    transform:
        translateY(-5px);

    box-shadow:
        var(--shadow);

    border-color:
        #dcd9ff;

}

.feature-icon {

    width: 42px;
    height: 42px;

    display: grid;

    place-items: center;

    border-radius: 12px;

    font-size: 18px;

    margin-bottom: 20px;

}

.purple-bg {
    background: #eeecff;
    color: #5146e5;
}

.blue-bg {
    background: #edf5ff;
    color: #3985e8;
}

.pink-bg {
    background: #fff0f5;
    color: #e75c8b;
}

.orange-bg {
    background: #fff4e7;
    color: #e68a35;
}

.green-bg {
    background: #e7f8f1;
    color: #15a675;
}

.violet-bg {
    background: #f1ebff;
    color: #8358dc;
}

.yellow-bg {
    background: #fff8df;
    color: #c79521;
}

.cyan-bg {
    background: #e7f8fa;
    color: #2499a7;
}

.feature-card h3 {

    font-size: 17px;

}

.feature-card p {

    margin-top: 10px;

    font-size: 13px;

    line-height: 1.7;

}

.feature-card a {

    display: inline-block;

    margin-top: 17px;

    color: var(--primary);

    font-size: 11px;

    font-weight: 700;

}


/* =========================================================
   SPLIT SECTIONS
========================================================= */

.light-section {

    background:
        var(--background-soft);

}

.split-grid {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 80px;

    align-items: center;

}

.split-content h2 {

    max-width: 550px;

}

.split-content > p {

    margin-top: 20px;

    max-width: 540px;

    font-size: 16px;

    line-height: 1.8;

}

.check-list {

    list-style: none;

    margin-top: 25px;

}

.check-list li {

    display: flex;

    align-items: center;

    gap: 10px;

    margin-bottom: 12px;

    font-size: 13px;

    color: #4d576e;

}

.check-list span {

    width: 20px;
    height: 20px;

    display: grid;

    place-items: center;

    border-radius: 50%;

    background: #e7e6ff;

    color: var(--primary);

    font-size: 10px;

    font-weight: 800;

}

.text-link {

    display: inline-block;

    margin-top: 15px;

    color: var(--primary);

    font-size: 13px;

    font-weight: 700;

}


/* =========================================================
   MEMBER UI
========================================================= */

.member-ui-wrapper {

    position: relative;

}

.member-ui-card {

    padding: 28px;

    border-radius: 22px;

    background: white;

    border:
        1px solid var(--border);

    box-shadow:
        var(--shadow);

}

.member-ui-header {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

}

.member-ui-header small {

    display: block;

    color: #9098aa;

    font-size: 10px;

}

.member-ui-header h3 {

    font-size: 21px;

    margin-top: 3px;

}

.status-pill {

    padding: 6px 10px;

    border-radius: 100px;

    background:
        var(--green-light);

    color:
        var(--green);

    font-size: 10px;

    font-weight: 700;

}

.member-profile {

    display: flex;

    align-items: center;

    gap: 14px;

    margin-top: 28px;

    padding-bottom: 22px;

    border-bottom:
        1px solid var(--border);

}

.large-avatar {

    width: 58px;
    height: 58px;

    display: grid;

    place-items: center;

    border-radius: 16px;

    background:
        linear-gradient(
            135deg,
            #5146e5,
            #8e85f8
        );

    color: white;

    font-weight: 800;

    font-size: 17px;

}

.member-profile strong {

    font-size: 13px;

}

.member-profile p {

    margin-top: 2px;

    font-size: 11px;

}

.member-info-grid {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 18px;

    padding:
        22px 0;

}

.member-info-grid small {

    display: block;

    color: #969eae;

    font-size: 10px;

}

.member-info-grid strong {

    display: block;

    margin-top: 3px;

    font-size: 11px;

}

.member-tags {

    display: flex;

    flex-wrap: wrap;

    gap: 6px;

}

.member-tags span {

    padding: 6px 9px;

    border-radius: 7px;

    color: var(--primary);

    background: #f0efff;

    font-size: 9px;

    font-weight: 600;

}

.member-history {

    margin-top: 22px;

}

.member-history h4 {

    font-size: 12px;

    margin-bottom: 10px;

}

.member-history > div {

    display: grid;

    grid-template-columns:
        7px 1fr auto;

    gap: 7px;

    align-items: center;

    padding: 7px 0;

    font-size: 10px;

    color: #586177;

}

.member-history > div > span {

    width: 7px;
    height: 7px;

    border-radius: 50%;

    background: var(--primary);

}

.member-history small {

    color: #a0a6b3;

    font-size: 8px;

}


/* =========================================================
   EVENT UI
========================================================= */

.event-ui-wrapper {

    position: relative;

}

.event-ui-card {

    overflow: hidden;

    border:
        1px solid var(--border);

    border-radius: 22px;

    background: white;

    box-shadow:
        var(--shadow);

}

.event-cover {

    height: 210px;

    position: relative;

    background:

        linear-gradient(
            135deg,
            rgba(35,31,100,.75),
            rgba(81,70,229,.20)
        ),

        linear-gradient(
            135deg,
            #4b456b,
            #7b739d
        );

}

.event-cover::before {

    content: "";

    position: absolute;

    inset: 0;

    background:
        radial-gradient(
            circle at 30% 30%,
            rgba(255,255,255,.25),
            transparent 35%
        );

}

.event-cover-overlay {

    position: absolute;

    left: 25px;

    bottom: 24px;

    color: white;

}

.event-cover-overlay span {

    font-size: 9px;

    letter-spacing: 1px;

    font-weight: 800;

    opacity: .8;

}

.event-cover-overlay h3 {

    max-width: 300px;

    margin-top: 7px;

    color: white;

    font-size: 25px;

}

.event-ui-content {

    padding: 25px;

}

.event-meta {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 10px;

}

.event-meta small {

    display: block;

    color: #9aa1b0;

    font-size: 8px;

}

.event-meta strong {

    display: block;

    margin-top: 3px;

    font-size: 11px;

}

.event-ui-content > p {

    margin-top: 20px;

    font-size: 12px;

    line-height: 1.7;

}

.event-registration {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-top: 20px;

    padding-top: 17px;

    border-top:
        1px solid var(--border);

}

.event-registration strong {

    display: block;

    font-size: 11px;

}

.event-registration small {

    font-size: 9px;

    color: #929aaa;

}

.event-registration button {

    padding: 9px 13px;

    border-radius: 8px;

    color: white;

    background: var(--primary);

    font-size: 10px;

    font-weight: 700;

}


/* =========================================================
   GIVING
========================================================= */

.giving-section {

    padding: 105px 0;

    background:
        linear-gradient(
            135deg,
            #121a40,
            #24276a
        );

    position: relative;

    overflow: hidden;

}

.giving-section::before {

    content: "";

    position: absolute;

    width: 500px;
    height: 500px;

    right: -100px;
    top: -150px;

    border-radius: 50%;

    background:
        rgba(100,89,255,.18);

    filter: blur(50px);

}

.giving-grid {

    display: grid;

    grid-template-columns:
        .8fr 1.2fr;

    gap: 80px;

    align-items: center;

    position: relative;

}

.light-label {

    color: #aaa4ff;

}

.giving-content h2 {

    color: white;

}

.giving-content p {

    max-width: 500px;

    margin-top: 20px;

    color: #b7bdd3;

    font-size: 15px;

}

.giving-points {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 13px;

    margin-top: 28px;

}

.giving-points div {

    display: flex;

    gap: 8px;

    align-items: center;

    color: #e3e6f2;

    font-size: 11px;

}

.giving-points span {

    width: 18px;
    height: 18px;

    display: grid;

    place-items: center;

    border-radius: 50%;

    color: #a9a2ff;

    background: rgba(255,255,255,.08);

}


/* Giving dashboard */

.giving-dashboard {

    padding: 28px;

    border-radius: 20px;

    background:
        rgba(255,255,255,.06);

    border:
        1px solid rgba(255,255,255,.10);

    backdrop-filter:
        blur(20px);

}

.giving-dashboard-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

}

.giving-dashboard-header small {

    color: #929ab3;

    font-size: 10px;

}

.giving-dashboard-header h3 {

    color: white;

    font-size: 18px;

    margin-top: 2px;

}

.growth {

    padding: 6px 9px;

    border-radius: 7px;

    color: #55d9ae;

    background: rgba(21,166,117,.12);

    font-size: 9px;

    font-weight: 700;

}

.giving-amount {

    margin-top: 25px;

    color: white;

    font-size: 38px;

    font-weight: 800;

    letter-spacing: -2px;

}

.giving-chart {

    position: relative;

    height: 190px;

    margin-top: 10px;

}

.giving-chart svg {

    width: 100%;
    height: 100%;

}

.giving-chart svg path {

    fill: none;

    stroke: #8178ff;

    stroke-width: 4;

    vector-effect: non-scaling-stroke;

}

.g-line {

    position: absolute;

    left: 0;
    right: 0;

    height: 1px;

    background:
        rgba(255,255,255,.08);

}

.g-line:nth-child(1) {
    top: 20%;
}

.g-line:nth-child(2) {
    top: 50%;
}

.g-line:nth-child(3) {
    top: 80%;
}

.giving-summary {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 10px;

    padding-top: 18px;

    border-top:
        1px solid rgba(255,255,255,.08);

}

.giving-summary small {

    display: block;

    color: #8d95ad;

    font-size: 8px;

}

.giving-summary strong {

    display: block;

    color: white;

    margin-top: 4px;

    font-size: 12px;

}


/* =========================================================
   MINISTRY
========================================================= */

.ministry-section {

    background:
        #fafbfe;

}

.ministry-grid {

    display: grid;

    grid-template-columns:
        1.2fr .8fr;

    gap: 16px;

    margin-top: 55px;

}

.ministry-card {

    padding: 32px;

    border:
        1px solid var(--border);

    border-radius: 18px;

    background: white;

    box-shadow:
        0 8px 25px rgba(30,35,80,.035);

}

.ministry-card.large {

    grid-row:
        span 2;

}

.ministry-card-top {

    display: flex;

    align-items: center;

    justify-content: space-between;

}

.round-icon {

    width: 44px;
    height: 44px;

    display: grid;

    place-items: center;

    border-radius: 13px;

    font-size: 19px;

}

.ministry-card-top > span {

    color: #858da0;

    font-size: 10px;

    font-weight: 600;

}

.ministry-card h3 {

    max-width: 430px;

    margin-top: 20px;

    font-size: 25px;

}

.ministry-card p {

    margin-top: 12px;

    font-size: 12px;

    line-height: 1.8;

}

.fake-ministry-list {

    margin-top: 25px;

}

.fake-ministry-list > div {

    display: flex;

    align-items: center;

    gap: 11px;

    padding: 11px 0;

    border-bottom:
        1px solid #eef0f5;

}

.fake-ministry-list > div:last-child {
    border-bottom: 0;
}

.ministry-avatar {

    width: 34px;
    height: 34px;

    display: grid;

    place-items: center;

    border-radius: 10px;

    font-size: 11px;

    font-weight: 800;

}

.purple-avatar {

    color: var(--primary);

    background: #eeecff;

}

.blue-avatar {

    color: var(--blue);

    background: #edf5ff;

}

.green-avatar {

    color: var(--green);

    background: #e7f8f1;

}

.fake-ministry-list > div > div {

    flex: 1;

}

.fake-ministry-list strong {

    display: block;

    font-size: 11px;

}

.fake-ministry-list small {

    display: block;

    color: #949baa;

    font-size: 8px;

}

.fake-ministry-list > div > span:last-child {

    color: #a0a6b4;

}

.small-label {

    display: block;

    margin-top: 17px;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: 1px;

    color: #9098aa;

}

.ministry-card:not(.large) h3 {

    font-size: 20px;

}

.skill-pills {

    display: flex;

    flex-wrap: wrap;

    gap: 6px;

    margin-top: 20px;

}

.skill-pills span {

    padding: 6px 9px;

    border-radius: 7px;

    background: #f5f5fa;

    color: #606a80;

    font-size: 8px;

}

.leadership-mini {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 7px;

    margin-top: 20px;

}

.leadership-mini span {

    padding: 8px;

    border-radius: 7px;

    background: #faf9f0;

    color: #6e6652;

    font-size: 8px;

}


/* =========================================================
   WHY
========================================================= */

.why-section {

    padding: 110px 0;

    background:
        linear-gradient(
            180deg,
            #f4f3ff,
            #ffffff
        );

    overflow: hidden;

}

.why-grid {

    display: grid;

    grid-template-columns:
        .9fr 1.1fr;

    align-items: center;

    gap: 70px;

}

.why-content > p {

    max-width: 500px;

    margin-top: 20px;

    font-size: 15px;

    line-height: 1.8;

}

.why-list {

    margin-top: 35px;

}

.why-list > div {

    display: grid;

    grid-template-columns:
        38px 1fr;

    gap: 15px;

    padding: 16px 0;

    border-bottom:
        1px solid #e7e6f0;

}

.why-list > div > span {

    color: #a2a6b5;

    font-family:
        "Plus Jakarta Sans",
        sans-serif;

    font-size: 11px;

    font-weight: 800;

}

.why-list strong {

    font-size: 13px;

}

.why-list p {

    margin-top: 4px;

    font-size: 10px;

    line-height: 1.6;

}


/* Why visual */

.why-visual {

    position: relative;

    height: 520px;

    display: flex;

    align-items: center;

    justify-content: center;

}

.central-visual {

    width: 300px;
    height: 300px;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            #ffffff,
            #eeecff
        );

    border:
        1px solid #dfdcff;

    box-shadow:
        0 30px 80px rgba(81,70,229,.12);

}

.central-logo {

    width: 65px;
    height: 65px;

    display: grid;

    place-items: center;

    color: white;

    border-radius: 20px;

    background:
        linear-gradient(
            135deg,
            #5146e5,
            #7e75f4
        );

    box-shadow:
        0 15px 30px
        rgba(81,70,229,.25);

}

.central-logo svg {

    width: 38px;

}

.central-visual strong {

    margin-top: 18px;

    font-family:
        "Plus Jakarta Sans",
        sans-serif;

    font-size: 25px;

}

.central-visual > span {

    margin-top: 5px;

    color: #858da0;

    font-size: 10px;

}

.floating-card {

    position: absolute;

    display: flex;

    align-items: center;

    gap: 9px;

    padding: 12px;

    min-width: 145px;

    border:
        1px solid var(--border);

    border-radius: 12px;

    background: white;

    box-shadow:
        0 18px 45px rgba(30,35,80,.12);

}

.floating-icon {

    width: 31px;
    height: 31px;

    display: grid;

    place-items: center;

    border-radius: 9px;

    font-size: 12px;

}

.floating-card div {

    flex: 1;

}

.floating-card strong {

    display: block;

    font-size: 13px;

}

.floating-card small {

    display: block;

    color: #959dac;

    font-size: 8px;

}

.floating-card em {

    font-style: normal;

    color: var(--green);

    font-size: 8px;

    font-weight: 700;

}

.card-one {

    left: 10px;
    top: 75px;

}

.card-two {

    right: 0;
    top: 130px;

}

.card-three {

    left: 25px;
    bottom: 85px;

}

.card-four {

    right: 20px;
    bottom: 50px;

}


/* =========================================================
   STATS
========================================================= */

.stats-section {

    padding: 60px 0;

    background:
        white;

    border-top:
        1px solid var(--border);

    border-bottom:
        1px solid var(--border);

}

.stats-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

}

.stat-block {

    text-align: center;

    padding:
        10px 20px;

    border-right:
        1px solid var(--border);

}

.stat-block:last-child {
    border-right: 0;
}

.stat-block strong {

    display: block;

    color: var(--primary);

    font-family:
        "Plus Jakarta Sans",
        sans-serif;

    font-size: 39px;

    letter-spacing: -2px;

}

.stat-block > span {

    display: block;

    margin-top: 4px;

    color: #777f91;

    font-size: 10px;

}


/* =========================================================
   PRICING
========================================================= */

.pricing-section {

    background:
        #fafbfe;

}

.pricing-card {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    margin:
        55px auto 0;

    max-width: 850px;

    overflow: hidden;

    border:
        1px solid var(--border);

    border-radius: 22px;

    background: white;

    box-shadow:
        var(--shadow);

}

.pricing-main {

    padding: 45px;

}

.pricing-badge {

    display: inline-block;

    padding: 6px 9px;

    border-radius: 6px;

    color: var(--primary);

    background: #eeecff;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 1px;

}

.pricing-main h3 {

    margin-top: 20px;

    font-size: 28px;

}

.pricing-main > p {

    max-width: 350px;

    margin-top: 10px;

    font-size: 12px;

}

.price {

    display: flex;

    align-items: baseline;

    gap: 7px;

    margin-top: 25px;

}

.price strong {

    font-family:
        "Plus Jakarta Sans",
        sans-serif;

    font-size: 42px;

    letter-spacing: -2px;

}

.price span {

    color: #8991a2;

    font-size: 10px;

}

.pricing-main .button {

    margin-top: 25px;

}

.pricing-features {

    padding: 45px;

    background:
        #f8f7ff;

}

.pricing-features h4 {

    font-size: 13px;

}

.pricing-features ul {

    list-style: none;

    margin-top: 20px;

}

.pricing-features li {

    display: flex;

    align-items: center;

    gap: 9px;

    padding: 8px 0;

    font-size: 11px;

    color: #5d667b;

}

.pricing-features li span {

    color: var(--green);

    font-weight: 800;

}


/* =========================================================
   FAQ
========================================================= */

.faq-grid {

    display: grid;

    grid-template-columns:
        .7fr 1.3fr;

    gap: 100px;

    align-items: start;

}

.faq-list {

    border-top:
        1px solid var(--border);

}

.faq-item {

    border-bottom:
        1px solid var(--border);

}

.faq-question {

    width: 100%;

    display: flex;

    justify-content: space-between;

    align-items: center;

    text-align: left;

    padding: 19px 0;

    color: var(--navy);

    font-family:
        "Plus Jakarta Sans",
        sans-serif;

    font-size: 13px;

    font-weight: 700;

}

.faq-question span {

    width: 23px;
    height: 23px;

    display: grid;

    place-items: center;

    border-radius: 6px;

    background: #f4f4f8;

    color: var(--primary);

    font-size: 17px;

    transition:
        transform .2s ease;

}

.faq-answer {

    max-height: 0;

    overflow: hidden;

    transition:
        max-height .3s ease;

}

.faq-answer p {

    padding:
        0 30px 20px 0;

    font-size: 11px;

    line-height: 1.8;

}

.faq-item.active .faq-answer {

    max-height: 200px;

}

.faq-item.active .faq-question span {

    transform:
        rotate(45deg);

}


/* =========================================================
   CTA
========================================================= */

.cta-section {

    position: relative;

    overflow: hidden;

    padding:
        85px 0;

    background:
        linear-gradient(
            135deg,
            #4d42d7,
            #6257ec
        );

}

.cta-glow {

    position: absolute;

    width: 600px;
    height: 600px;

    border-radius: 50%;

    right: -250px;
    top: -300px;

    background:
        rgba(255,255,255,.08);

    filter:
        blur(20px);

}

.cta-content {

    position: relative;

    text-align: center;

    color: white;

}

.cta-icon {

    width: 54px;
    height: 54px;

    display: grid;

    place-items: center;

    margin:
        0 auto 20px;

    color: white;

    border-radius: 15px;

    background:
        rgba(255,255,255,.13);

}

.cta-icon svg {

    width: 31px;

}

.cta-content h2 {

    color: white;

    max-width: 650px;

    margin:
        0 auto;

}

.cta-content h2 span {

    color: #d8d5ff;

}

.cta-content > p {

    max-width: 550px;

    margin:
        18px auto 0;

    color:
        rgba(255,255,255,.75);

    font-size: 14px;

}

.cta-buttons {

    display: flex;

    justify-content: center;

    gap: 10px;

    margin-top: 27px;

}

.button-white {

    background: white;

    color: var(--primary);

}

.button-outline-white {

    color: white;

    border:
        1px solid rgba(255,255,255,.35);

    background:
        rgba(255,255,255,.05);

}


/* =========================================================
   FOOTER
========================================================= */

.site-footer {

    padding:
        70px 0 25px;

    color: #aeb5c9;

    background:
        #101735;

}

.footer-grid {

    display: grid;

    grid-template-columns:
        2fr repeat(4, 1fr);

    gap: 45px;

}

.footer-brand-logo .brand-name {
    color: white;
}

.footer-brand p {

    max-width: 270px;

    margin-top: 17px;

    color: #929ab2;

    font-size: 11px;

    line-height: 1.8;

}

.social-links {

    display: flex;

    gap: 7px;

    margin-top: 20px;

}

.social-links a {

    width: 29px;
    height: 29px;

    display: grid;

    place-items: center;

    border-radius: 8px;

    background:
        rgba(255,255,255,.07);

    color: #d5d8e4;

    font-size: 11px;

}

.footer-column h4 {

    margin-bottom: 15px;

    color: white;

    font-size: 11px;

}

.footer-column a {

    display: block;

    margin-bottom: 9px;

    color: #9199b1;

    font-size: 10px;

}

.footer-column a:hover {

    color: white;

}

.footer-bottom {

    display: flex;

    justify-content: space-between;

    margin-top: 55px;

    padding-top: 20px;

    border-top:
        1px solid rgba(255,255,255,.08);

    color: #747d98;

    font-size: 9px;

}


/* =========================================================
   REVEAL ANIMATIONS
========================================================= */

.reveal {

    opacity: 0;

    transform:
        translateY(25px);

    transition:
        opacity .7s ease,
        transform .7s ease;

}

.reveal.visible {

    opacity: 1;

    transform:
        translateY(0);

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1050px) {

    .desktop-nav {
        gap: 17px;
        margin-right: 20px;
    }

    .feature-grid {
        grid-template-columns:
            repeat(2, 1fr);
    }

    .hero-grid {
        grid-template-columns:
            1fr;
    }

    .hero-content {
        text-align: center;
        margin-inline: auto;
    }

    .hero-description {
        margin-inline: auto;
    }

    .hero-buttons,
    .hero-trust {
        justify-content: center;
    }

    .hero-visual {
        margin-top: 35px;
    }

    .dashboard-window {
        width: min(650px, 100%);
    }

}


@media (max-width: 850px) {

    .desktop-nav,
    .nav-actions {
        display: none;
    }

    .mobile-menu-button {
        display: flex;
    }

    .mobile-menu {

        position: absolute;

        left: 15px;
        right: 15px;

        top: 70px;

        padding: 16px;

        border-radius: 14px;

        background: white;

        border:
            1px solid var(--border);

        box-shadow:
            var(--shadow);

    }

    .mobile-menu.open {
        display: block;
    }

    .mobile-menu > a {

        display: block;

        padding: 11px;

        border-radius: 8px;

        font-size: 13px;

        font-weight: 600;

    }

    .mobile-menu > a:hover {
        background: #f7f7fb;
    }

    .mobile-menu-actions {

        display: grid;

        gap: 8px;

        margin-top: 10px;

        padding-top: 12px;

        border-top:
            1px solid var(--border);

    }

    .church-types {
        grid-template-columns:
            repeat(3, 1fr);
    }

    .split-grid,
    .giving-grid,
    .why-grid,
    .faq-grid {
        grid-template-columns:
            1fr;
    }

    .split-grid {
        gap: 50px;
    }

    .reverse-mobile .event-ui-wrapper {
        order: 2;
    }

    .giving-grid {
        gap: 45px;
    }

    .why-grid {
        gap: 50px;
    }

    .faq-grid {
        gap: 40px;
    }

    .ministry-grid {
        grid-template-columns: 1fr;
    }

    .ministry-card.large {
        grid-row: auto;
    }

    .stats-grid {
        grid-template-columns:
            repeat(2, 1fr);
        row-gap: 25px;
    }

    .stat-block:nth-child(2) {
        border-right: 0;
    }

    .pricing-card {
        grid-template-columns:
            1fr;
    }

    .footer-grid {
        grid-template-columns:
            repeat(3, 1fr);
    }

    .footer-brand {
        grid-column:
            1 / -1;
    }

}


@media (max-width: 600px) {

    .container {
        width:
            min(
                calc(100% - 30px),
                var(--container)
            );
    }

    .nav-wrapper {
        height: 68px;
    }

    .hero {

        padding-top: 110px;

        min-height: auto;

    }

    h1 {

        font-size: 43px;

        letter-spacing: -2px;

    }

    h2 {

        font-size: 35px;

        letter-spacing: -1.5px;

    }

    .hero-buttons {

        flex-direction: column;

        align-items: stretch;

    }

    .hero-trust {

        flex-direction: column;

        align-items: center;

        gap: 9px;

    }

    .hero-visual {

        min-height: 420px;

        margin-top: 10px;

    }

    .dashboard-window {

        transform: none;

    }

    .dashboard-sidebar {

        display: none;

    }

    .dashboard-body {

        min-height: 360px;

    }

    .dashboard-content {

        padding: 12px;

    }

    .stat-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }

    .dashboard-panels {

        grid-template-columns: 1fr;

    }

    .dashboard-bottom {

        display: none;
    }

    .mobile-dashboard-card {

        left: -5px;

        bottom: 5px;

    }

    .church-types {

        grid-template-columns:
            repeat(2, 1fr);

    }

    .feature-grid {

        grid-template-columns:
            1fr;

        margin-top: 35px;

    }

    .section {

        padding: 75px 0;

    }

    .split-grid {

        gap: 40px;

    }

    .member-ui-card,
    .event-ui-content {

        padding: 20px;

    }

    .member-info-grid {

        grid-template-columns:
            1fr;

        gap: 11px;

    }

    .event-meta {

        grid-template-columns:
            1fr 1fr;

    }

    .event-meta > div:last-child {

        grid-column:
            1 / -1;

    }

    .giving-points {

        grid-template-columns:
            1fr;

    }

    .giving-dashboard {

        padding: 20px;

    }

    .giving-amount {

        font-size: 31px;

    }

    .giving-summary {

        grid-template-columns:
            1fr;

    }

    .why-visual {

        height: 450px;

    }

    .central-visual {

        width: 220px;
        height: 220px;

    }

    .floating-card {

        min-width: 125px;

    }

    .card-one {
        left: 0;
        top: 25px;
    }

    .card-two {
        right: 0;
        top: 75px;
    }

    .card-three {
        left: 0;
        bottom: 70px;
    }

    .card-four {
        right: 0;
        bottom: 25px;
    }

    .stats-grid {

        grid-template-columns:
            1fr 1fr;

    }

    .stat-block {

        border-right: 0;

    }

    .stat-block strong {

        font-size: 31px;

    }

    .pricing-main,
    .pricing-features {

        padding: 30px;

    }

    .cta-section {

        padding: 70px 0;

    }

    .cta-buttons {

        flex-direction: column;

        align-items: stretch;

        max-width: 300px;

        margin-inline: auto;

    }

    .footer-grid {

        grid-template-columns:
            repeat(2, 1fr);

        gap: 30px;

    }

    .footer-brand {

        grid-column:
            1 / -1;

    }

    .footer-bottom {

        flex-direction: column;

        gap: 7px;

    }

}

        /* =========================================================
           ABOUT CHURCHOS — PAGE-SPECIFIC UI
        ========================================================= */
        .about-hero {
            position: relative;
            padding: 155px 0 105px;
            overflow: hidden;
            background:
                radial-gradient(circle at 82% 20%, rgba(81,70,229,.12), transparent 34%),
                linear-gradient(180deg, #fbfbff 0%, #fff 100%);
        }

        .about-hero::before {
            content: "";
            position: absolute;
            width: 520px;
            height: 520px;
            right: -170px;
            top: 40px;
            border-radius: 50%;
            background: rgba(81,70,229,.055);
            filter: blur(2px);
        }

        .about-hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.03fr .97fr;
            gap: 70px;
            align-items: center;
        }

        .about-hero h1 {
            max-width: 760px;
            margin: 18px 0 25px;
        }

        .about-hero h1 span,
        .section-heading h2 span,
        .split-content h2 span,
        .vision-copy h2 span,
        .about-cta h2 span {
            color: var(--primary);
        }

        .about-hero-description {
            max-width: 650px;
            font-size: 18px;
            line-height: 1.8;
        }

        .hero-buttons {
            margin-top: 30px;
        }

        .about-hero-card {
            position: relative;
            min-height: 470px;
            display: grid;
            place-items: center;
        }

        .mission-orbit {
            width: 370px;
            height: 370px;
            border-radius: 50%;
            border: 1px solid rgba(81,70,229,.16);
            background:
                radial-gradient(circle, rgba(81,70,229,.08), rgba(255,255,255,.85) 58%);
            box-shadow: var(--shadow-lg);
            display: grid;
            place-items: center;
            position: relative;
        }

        .mission-orbit::before,
        .mission-orbit::after {
            content: "";
            position: absolute;
            border: 1px dashed rgba(81,70,229,.16);
            border-radius: 50%;
        }

        .mission-orbit::before {
            inset: 35px;
        }

        .mission-orbit::after {
            inset: 75px;
        }

        .mission-center {
            width: 150px;
            height: 150px;
            border-radius: 28px;
            background: white;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .mission-center .brand-mark {
            width: 46px;
            height: 46px;
            margin-bottom: 8px;
        }

        .mission-center strong {
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 20px;
            color: var(--navy);
        }

        .mission-center small {
            color: var(--muted);
            font-size: 11px;
            margin-top: 3px;
        }

        .orbit-item {
            position: absolute;
            z-index: 3;
            min-width: 126px;
            padding: 12px 14px;
            border-radius: 13px;
            background: white;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .orbit-item span {
            width: 29px;
            height: 29px;
            border-radius: 9px;
            display: grid;
            place-items: center;
            color: var(--primary);
            background: var(--primary-light);
        }

        .orbit-one { top: 30px; left: 22px; }
        .orbit-two { top: 105px; right: -3px; }
        .orbit-three { bottom: 82px; right: 8px; }
        .orbit-four { bottom: 22px; left: 40px; }
        .orbit-five { top: 190px; left: -38px; }

        .story-section {
            padding: 105px 0;
            background: white;
        }

        .story-grid {
            display: grid;
            grid-template-columns: .92fr 1.08fr;
            gap: 85px;
            align-items: start;
        }

        .story-copy p {
            font-size: 17px;
            line-height: 1.85;
            margin-bottom: 18px;
        }

        .story-highlight {
            margin-top: 30px;
            padding: 25px 27px;
            border-radius: 18px;
            background: var(--background-purple);
            border: 1px solid #e5e1ff;
        }

        .story-highlight strong {
            display: block;
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 19px;
            color: var(--navy);
            margin-bottom: 8px;
        }

        .story-highlight p { margin: 0; }

        .story-timeline {
            position: relative;
            padding-left: 34px;
        }

        .story-timeline::before {
            content: "";
            position: absolute;
            left: 8px;
            top: 5px;
            bottom: 5px;
            width: 2px;
            background: linear-gradient(var(--primary), #ddd9ff);
        }

        .timeline-item {
            position: relative;
            padding: 0 0 34px;
        }

        .timeline-item:last-child { padding-bottom: 0; }

        .timeline-item::before {
            content: "";
            position: absolute;
            left: -32px;
            top: 4px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: white;
            border: 4px solid var(--primary);
            box-shadow: 0 0 0 5px #efedff;
        }

        .timeline-item small {
            color: var(--primary);
            font-weight: 800;
            letter-spacing: .08em;
            font-size: 11px;
            text-transform: uppercase;
        }

        .timeline-item h3 {
            margin: 6px 0 7px;
        }

        .timeline-item p {
            line-height: 1.7;
        }

        .free-section {
            padding: 100px 0;
            background: var(--background-soft);
        }

        .free-card {
            background: linear-gradient(135deg, #5146e5, #4037c8);
            border-radius: 26px;
            padding: 58px 65px;
            color: white;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .free-card::after {
            content: "";
            position: absolute;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.14);
            right: -90px;
            top: -150px;
        }

        .free-grid {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 55px;
            align-items: center;
        }

        .free-card h2 {
            color: white;
            margin: 10px 0 18px;
        }

        .free-card h2 span { color: #c9c5ff; }

        .free-card p {
            color: rgba(255,255,255,.78);
            max-width: 660px;
            line-height: 1.8;
        }

        .free-badge {
            display: inline-flex;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.17);
            color: white;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
        }

        .contribution-card {
            background: white;
            color: var(--text);
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 18px 50px rgba(15,20,65,.16);
        }

        .contribution-card small {
            color: var(--muted);
            font-weight: 700;
        }

        .contribution-card strong {
            display: block;
            font-family: "Plus Jakarta Sans", sans-serif;
            color: var(--navy);
            font-size: 39px;
            letter-spacing: -2px;
            margin: 6px 0 2px;
        }

        .contribution-card p {
            color: var(--text-soft);
            font-size: 13px;
            line-height: 1.65;
        }

        .ecosystem-section {
            padding: 105px 0;
            background: white;
        }

        .ecosystem-grid {
            margin-top: 55px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .ecosystem-card {
            padding: 28px;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: white;
            box-shadow: var(--shadow-sm);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .ecosystem-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .ecosystem-icon {
            width: 47px;
            height: 47px;
            border-radius: 13px;
            display: grid;
            place-items: center;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .ecosystem-card h3 { margin-bottom: 8px; }
        .ecosystem-card p { line-height: 1.7; font-size: 14px; }

        .northeast-section {
            padding: 105px 0;
            background: var(--background-purple);
            overflow: hidden;
        }

        .northeast-grid {
            display: grid;
            grid-template-columns: .95fr 1.05fr;
            gap: 75px;
            align-items: center;
        }

        .region-visual {
            min-height: 440px;
            border-radius: 25px;
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 50% 40%, rgba(81,70,229,.20), transparent 35%),
                linear-gradient(145deg, #161d42, #2e3a7a);
            box-shadow: var(--shadow-lg);
        }

        .region-grid {
            position: absolute;
            inset: 0;
            opacity: .14;
            background-image:
                linear-gradient(rgba(255,255,255,.5) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.5) 1px, transparent 1px);
            background-size: 38px 38px;
        }

        .region-label {
            position: absolute;
            left: 38px;
            top: 34px;
            color: rgba(255,255,255,.65);
            font-size: 11px;
            letter-spacing: .12em;
            font-weight: 800;
        }

        .region-center {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
        }

        .region-center-card {
            width: 235px;
            height: 235px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.22);
            display: grid;
            place-items: center;
            text-align: center;
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(8px);
        }

        .region-center-card strong {
            display: block;
            color: white;
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 26px;
        }

        .region-center-card span {
            color: rgba(255,255,255,.7);
            font-size: 12px;
        }

        .region-pin {
            position: absolute;
            padding: 9px 12px;
            border-radius: 10px;
            background: rgba(255,255,255,.95);
            color: var(--navy);
            font-size: 11px;
            font-weight: 800;
            box-shadow: 0 10px 25px rgba(0,0,0,.18);
        }

        .pin-1 { top: 78px; left: 44px; }
        .pin-2 { top: 105px; right: 42px; }
        .pin-3 { bottom: 88px; left: 63px; }
        .pin-4 { bottom: 55px; right: 65px; }

        .northeast-copy p {
            line-height: 1.82;
            font-size: 16px;
            margin-bottom: 18px;
        }

        .region-facts {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 13px;
            margin-top: 28px;
        }

        .region-fact {
            background: white;
            border: 1px solid #e5e1ff;
            padding: 16px;
            border-radius: 13px;
        }

        .region-fact strong {
            display: block;
            color: var(--navy);
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .region-fact span {
            color: var(--muted);
            font-size: 12px;
        }

        .vision-section {
            padding: 105px 0;
            background: white;
        }

        .vision-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 70px;
            align-items: center;
        }

        .vision-copy > p {
            font-size: 17px;
            line-height: 1.82;
            margin: 18px 0 25px;
        }

        .vision-list {
            display: grid;
            gap: 14px;
        }

        .vision-list > div {
            display: grid;
            grid-template-columns: 38px 1fr;
            gap: 13px;
            align-items: start;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 13px;
            background: var(--background-soft);
        }

        .vision-number {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 800;
            font-size: 12px;
        }

        .vision-list strong {
            display: block;
            color: var(--navy);
            margin-bottom: 3px;
        }

        .vision-list p {
            font-size: 13px;
            line-height: 1.55;
        }

        .belief-visual {
            min-height: 500px;
            border-radius: 25px;
            background: var(--navy);
            padding: 48px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
        }

        .belief-visual::before {
            content: "";
            position: absolute;
            width: 390px;
            height: 390px;
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 50%;
            top: -190px;
            right: -80px;
        }

        .belief-visual::after {
            content: "";
            position: absolute;
            width: 270px;
            height: 270px;
            border: 1px solid rgba(255,255,255,.07);
            border-radius: 50%;
            bottom: -130px;
            left: -100px;
        }

        .belief-content {
            position: relative;
            z-index: 2;
        }

        .belief-content .section-label { color: #a9a4ff; }

        .belief-content h3 {
            color: white;
            font-size: 29px;
            line-height: 1.35;
            margin: 15px 0 18px;
        }

        .belief-content p {
            color: rgba(255,255,255,.68);
            line-height: 1.8;
            font-size: 14px;
        }

        .office-section {
            padding: 105px 0;
            background: var(--background-soft);
        }

        .office-grid {
            margin-top: 52px;
            display: grid;
            grid-template-columns: 1.3fr .7fr;
            grid-template-rows: 220px 220px;
            gap: 16px;
        }

        .office-photo {
            border-radius: 18px;
            overflow: hidden;
            position: relative;
            background:
                linear-gradient(135deg, rgba(81,70,229,.08), rgba(81,70,229,.02)),
                #eef0f8;
            border: 1px dashed #cfd3e3;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--muted);
        }

        .office-photo:first-child {
            grid-row: 1 / 3;
        }

        .office-photo span {
            padding: 15px 20px;
            border-radius: 13px;
            background: rgba(255,255,255,.82);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            font-size: 12px;
            font-weight: 700;
        }

        .office-photo.has-image {
            background-size: cover;
            background-position: center;
            border-style: solid;
        }

        .office-note {
            margin-top: 18px;
            color: var(--muted);
            font-size: 12px;
        }

        .video-section {
            padding: 105px 0;
            background: white;
        }

        .video-card {
            margin-top: 48px;
            border-radius: 25px;
            overflow: hidden;
            min-height: 430px;
            position: relative;
            background:
                linear-gradient(135deg, rgba(17,25,54,.94), rgba(64,55,200,.82)),
                #111936;
            display: grid;
            place-items: center;
            box-shadow: var(--shadow-lg);
        }

        .video-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 50% 50%, rgba(255,255,255,.12), transparent 28%);
        }

        .play-button {
            position: relative;
            z-index: 2;
            width: 82px;
            height: 82px;
            border-radius: 50%;
            background: white;
            color: var(--primary);
            display: grid;
            place-items: center;
            font-size: 27px;
            padding-left: 4px;
            box-shadow: 0 18px 50px rgba(0,0,0,.22);
            transition: transform .2s ease;
        }

        .play-button:hover { transform: scale(1.07); }

        .video-caption {
            position: absolute;
            left: 35px;
            bottom: 30px;
            color: white;
            z-index: 2;
        }

        .video-caption small {
            color: rgba(255,255,255,.65);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .video-caption h3 {
            color: white;
            margin-top: 5px;
        }

        .video-modal {
            position: fixed;
            inset: 0;
            background: rgba(7,10,28,.82);
            backdrop-filter: blur(8px);
            display: none;
            place-items: center;
            z-index: 2000;
            padding: 24px;
        }

        .video-modal.open { display: grid; }

        .video-modal-inner {
            width: min(1000px, 100%);
            position: relative;
            background: #080b19;
            border-radius: 18px;
            padding: 8px;
            box-shadow: 0 30px 100px rgba(0,0,0,.45);
        }

        .video-modal iframe {
            display: block;
            width: 100%;
            aspect-ratio: 16 / 9;
            border: 0;
            border-radius: 12px;
        }

        .video-close {
            position: absolute;
            right: -12px;
            top: -45px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: white;
            color: var(--navy);
            font-size: 20px;
            z-index: 3;
        }

        .contact-section {
            padding: 95px 0;
            background: var(--background-soft);
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 50px;
        }

        .contact-card {
            padding: 30px;
            border-radius: 18px;
            background: white;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .contact-card h3 { margin-bottom: 17px; }

        .contact-row {
            display: flex;
            gap: 13px;
            padding: 13px 0;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        .contact-row:last-child { border-bottom: 0; }

        .contact-row strong {
            min-width: 80px;
            color: var(--navy);
        }

        .contact-row span,
        .contact-row a { color: var(--text-soft); }

        .social-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin-top: 20px;
        }

        .social-pill {
            padding: 9px 13px;
            border-radius: 9px;
            background: var(--background-purple);
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
        }

        .about-cta {
            padding: 90px 0;
            background: linear-gradient(135deg, #5146e5, #4037c8);
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .about-cta::before,
        .about-cta::after {
            content: "";
            position: absolute;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 50%;
        }

        .about-cta::before {
            width: 400px; height: 400px;
            left: -190px; top: -170px;
        }

        .about-cta::after {
            width: 320px; height: 320px;
            right: -130px; bottom: -180px;
        }

        .about-cta-content {
            position: relative;
            z-index: 2;
        }

        .about-cta h2 { color: white; max-width: 750px; margin: 10px auto 18px; }
        .about-cta h2 span { color: #c9c5ff; }
        .about-cta p { color: rgba(255,255,255,.76); max-width: 650px; margin: 0 auto 30px; }

        .about-cta .button-secondary {
            color: white;
            background: transparent;
            border-color: rgba(255,255,255,.32);
        }

        .about-cta .button-secondary:hover {
            background: rgba(255,255,255,.1);
        }

        @media (max-width: 900px) {
            .about-hero-grid,
            .story-grid,
            .free-grid,
            .northeast-grid,
            .vision-grid,
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .about-hero { padding-top: 125px; }
            .about-hero-card { min-height: 420px; }
            .ecosystem-grid { grid-template-columns: 1fr 1fr; }
            .belief-visual { min-height: 390px; }
            .office-grid {
                grid-template-columns: 1fr 1fr;
                grid-template-rows: 220px 220px;
            }
            .office-photo:first-child { grid-row: auto; grid-column: 1 / -1; }
        }

        @media (max-width: 640px) {
            .about-hero,
            .story-section,
            .free-section,
            .ecosystem-section,
            .northeast-section,
            .vision-section,
            .office-section,
            .video-section,
            .contact-section {
                padding: 75px 0;
            }

            .about-hero h1 { letter-spacing: -2px; }
            .about-hero-description { font-size: 16px; }
            .mission-orbit { width: 300px; height: 300px; }
            .mission-center { width: 125px; height: 125px; }
            .orbit-one { top: 12px; left: 0; }
            .orbit-two { top: 82px; right: -8px; }
            .orbit-three { bottom: 55px; right: -5px; }
            .orbit-four { bottom: 4px; left: 25px; }
            .orbit-five { top: 145px; left: -28px; }
            .free-card { padding: 35px 24px; }
            .ecosystem-grid { grid-template-columns: 1fr; }
            .region-visual { min-height: 370px; }
            .region-facts { grid-template-columns: 1fr; }
            .belief-visual { padding: 30px; }
            .office-grid {
                display: grid;
                grid-template-columns: 1fr;
                grid-template-rows: repeat(3, 190px);
            }
            .office-photo:first-child { grid-column: auto; }
            .video-card { min-height: 300px; }
            .video-close { right: 0; top: -44px; }
            .contact-row { flex-direction: column; gap: 4px; }
        }
    </style>
</head>

<body>

<header class="site-header" id="siteHeader">
    <div class="container nav-wrapper">
        <a href="index.php" class="brand">
            <span class="brand-mark">
                <svg viewBox="0 0 40 40" fill="none">
                    <path d="M20 4L34 14V34H6V14L20 4Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M20 13V25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    <path d="M15 19H25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="brand-name">Church<span>OS</span></span>
        </a>

        <nav class="desktop-nav">
            <a href="index.php#features">Features</a>
            <a href="index.php#modules">Modules</a>
            <a href="index.php#why-churchos">Why ChurchOS</a>
            <a href="index.php#pricing">Pricing</a>
            <div class="nav-dropdown">
                <button class="dropdown-button">Resources <span>⌄</span></button>
                <div class="dropdown-menu">
                    <a href="#">Documentation</a>
                    <a href="#">User Guides</a>
                    <a href="#">Help Center</a>
                    <a href="#">FAQ</a>
                </div>
            </div>
            <a href="#contact">Contact</a>
        </nav>

        <div class="nav-actions">
            <a href="login" class="login-link">Sign in</a>
            <a href="register" class="button button-primary">Get Started Free <span>→</span></a>
        </div>

        <button class="mobile-menu-button" id="mobileMenuButton" aria-label="Open navigation">
            <span></span><span></span><span></span>
        </button>
    </div>

    <div class="mobile-menu" id="mobileMenu">
        <a href="index.php#features">Features</a>
        <a href="index.php#modules">Modules</a>
        <a href="index.php#why-churchos">Why ChurchOS</a>
        <a href="index.php#pricing">Pricing</a>
        <a href="#contact">Contact</a>
        <div class="mobile-menu-actions">
            <a href="login" class="button button-secondary">Sign in</a>
            <a href="register" class="button button-primary">Get Started Free</a>
        </div>
    </div>
</header>

<main>

<section class="about-hero">
    <div class="container about-hero-grid">
        <div class="reveal">
            <div class="eyebrow"><span class="eyebrow-dot"></span> WHY CHURCHOS EXISTS</div>
            <h1>Technology for the Church.<br><span>Built for Every Church.</span></h1>
            <p class="about-hero-description">
                ChurchOS exists because access to good church technology should not depend on the size of a church,
                its location, or its budget. We are building a connected platform that helps churches organize,
                connect, serve and reach their communities.
            </p>
            <div class="hero-buttons">
                <a href="register.php" class="button button-primary button-large">Start with ChurchOS <span>→</span></a>
                <a href="#mission" class="button button-secondary button-large">Our Mission</a>
            </div>
        </div>

        <div class="about-hero-card reveal">
            <div class="mission-orbit">
                <div class="orbit-item orbit-one"><span>♙</span>People</div>
                <div class="orbit-item orbit-two"><span>◈</span>Ministry</div>
                <div class="orbit-item orbit-three"><span>♡</span>Giving</div>
                <div class="orbit-item orbit-four"><span>✦</span>Mission</div>
                <div class="orbit-item orbit-five"><span>▦</span>Events</div>

                <div class="mission-center">
                    <span class="brand-mark">
                        <svg viewBox="0 0 40 40" fill="none">
                            <path d="M20 4L34 14V34H6V14L20 4Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                            <path d="M20 13V25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M15 19H25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <strong>ChurchOS</strong>
                    <small>People · Ministry · Mission</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="story-section" id="mission">
    <div class="container story-grid">
        <div class="story-copy reveal">
            <div class="section-label">OUR STORY</div>
            <h2>Remove the barrier.<br><span>Enable the church.</span></h2>
            <p>
                Churches are doing more than ever. They are caring for people, coordinating ministries,
                organizing events, supporting missionaries, receiving giving, training volunteers and
                communicating with their communities.
            </p>
            <p>
                Yet software can become another financial and administrative burden — especially for smaller
                churches. ChurchOS was created to change that.
            </p>
            <div class="story-highlight">
                <strong>A church should not have fewer digital opportunities because it has fewer resources.</strong>
                <p>Our goal is to make powerful church technology accessible without putting essential features behind expensive plans.</p>
            </div>
        </div>

        <div class="story-timeline reveal">
            <div class="timeline-item">
                <small>01 · Start with the church</small>
                <h3>Understand real church workflows</h3>
                <p>Build around people, families, ministries, leadership, events, giving, volunteers and administration — not generic business processes.</p>
            </div>
            <div class="timeline-item">
                <small>02 · Remove cost barriers</small>
                <h3>Make the platform accessible</h3>
                <p>ChurchOS is free to use. Churches can voluntarily support the project, but contributions do not determine access to features.</p>
            </div>
            <div class="timeline-item">
                <small>03 · Connect the digital church</small>
                <h3>Go beyond the dashboard</h3>
                <p>Connect ChurchOS with a church website, online giving, volunteer registrations, events and other public-facing church activities.</p>
            </div>
            <div class="timeline-item">
                <small>04 · Extend the mission</small>
                <h3>Support missionaries and mission workers</h3>
                <p>Provide a dedicated Missionary App so mission workers and their churches can coordinate and stay connected.</p>
            </div>
        </div>
    </div>
</section>

<section class="free-section">
    <div class="container">
        <div class="free-card reveal">
            <div class="free-grid">
                <div>
                    <span class="free-badge">FREE FOR EVERY CHURCH</span>
                    <h2>No feature budgets.<br><span>No church left behind.</span></h2>
                    <p>
                        ChurchOS is built around a simple principle: essential church technology should be accessible
                        whether a church is small, growing or large. We do not want a church to decide which
                        ministry tools it can afford to unlock.
                    </p>
                </div>
                <div class="contribution-card">
                    <small>OPTIONAL SUPPORT</small>
                    <strong>₹500 / month</strong>
                    <p>
                        Churches that want to support the continued development of ChurchOS may voluntarily contribute
                        ₹500 per month. It is completely optional and never a condition for using the platform.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ecosystem-section">
    <div class="container">
        <div class="section-heading centered reveal">
            <div class="section-label">THE CHURCHOS ECOSYSTEM</div>
            <h2>More than software.<br><span>A connected church platform.</span></h2>
            <p>ChurchOS brings the administrative and public-facing parts of church life closer together.</p>
        </div>

        <div class="ecosystem-grid">
            <article class="ecosystem-card reveal">
                <div class="ecosystem-icon purple-bg">▣</div>
                <h3>ChurchOS SaaS</h3>
                <p>Manage members, families, ministries, leadership, events, giving, volunteers and church administration in one connected platform.</p>
            </article>
            <article class="ecosystem-card reveal">
                <div class="ecosystem-icon blue-bg">⌂</div>
                <h3>Free Church Website</h3>
                <p>Give your church an online home connected to the same platform, so public information and church operations can work together.</p>
            </article>
            <article class="ecosystem-card reveal">
                <div class="ecosystem-icon pink-bg">♡</div>
                <h3>Giving & Donations</h3>
                <p>Help churches make giving accessible online for general support, missions, projects and other church-approved purposes.</p>
            </article>
            <article class="ecosystem-card reveal">
                <div class="ecosystem-icon green-bg">♧</div>
                <h3>Volunteer Registration</h3>
                <p>Let people register their interest in serving directly from the church website and manage those registrations in ChurchOS.</p>
            </article>
            <article class="ecosystem-card reveal">
                <div class="ecosystem-icon orange-bg">▦</div>
                <h3>Events & Activities</h3>
                <p>Connect public events and registrations with church administration so staff can manage activities conveniently.</p>
            </article>
            <article class="ecosystem-card reveal">
                <div class="ecosystem-icon yellow-bg">✦</div>
                <h3>Missionary App</h3>
                <p>A free app for churches and mission workers to coordinate missionary activities and stay connected with their supporting churches.</p>
            </article>
        </div>
    </div>
</section>

<section class="northeast-section">
    <div class="container northeast-grid">
        <div class="region-visual reveal">
            <div class="region-grid"></div>
            <span class="region-label">WHERE OUR STORY BEGINS</span>
            <div class="region-center">
                <div class="region-center-card">
                    <div>
                        <strong>Northeast India</strong>
                        <span>Built from Imphal, Manipur</span>
                    </div>
                </div>
            </div>
            <span class="region-pin pin-1">Nagaland</span>
            <span class="region-pin pin-2">Manipur</span>
            <span class="region-pin pin-3">Mizoram</span>
            <span class="region-pin pin-4">Meghalaya</span>
        </div>

        <div class="northeast-copy reveal">
            <div class="section-label">OUR ROOTS</div>
            <h2>Starting from the Northeast.<br><span>Building for everywhere.</span></h2>
            <p>
                The Northeast has a strong Christian presence and a rich history of churches, ministries and mission work.
                ChurchOS is being built from Imphal, Manipur with a special desire to serve churches in this region.
            </p>
            <p>
                We believe the region can benefit from technology designed around its churches and communities —
                technology that does not assume a large budget, a large staff or a large technical team.
            </p>
            <p>
                Our starting point is local. Our long-term ambition is global.
            </p>

            <div class="region-facts">
                <div class="region-fact">
                    <strong>Imphal, Manipur</strong>
                    <span>Our current home</span>
                </div>
                <div class="region-fact">
                    <strong>Every church</strong>
                    <span>Our intended reach</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="vision-section">
    <div class="container vision-grid">
        <div class="vision-copy reveal">
            <div class="section-label">OUR LONG-TERM VISION</div>
            <h2>Build the digital foundation<br><span>for the connected church.</span></h2>
            <p>
                ChurchOS will continue to grow beyond church administration. Our long-term goal is to build
                a practical ecosystem that helps churches operate, communicate, serve, give, reach their
                communities and coordinate mission work.
            </p>

            <div class="vision-list">
                <div>
                    <span class="vision-number">01</span>
                    <div><strong>Connected church ecosystem</strong><p>Bring church operations, websites, events, giving, volunteers and communication together.</p></div>
                </div>
                <div>
                    <span class="vision-number">02</span>
                    <div><strong>Stronger mission networks</strong><p>Help churches coordinate missionaries, mission activities, supporters and field updates.</p></div>
                </div>
                <div>
                    <span class="vision-number">03</span>
                    <div><strong>Technology for smaller churches</strong><p>Keep powerful tools accessible to churches that cannot justify expensive software systems.</p></div>
                </div>
                <div>
                    <span class="vision-number">04</span>
                    <div><strong>Mobile-first and accessible</strong><p>Continue building practical mobile, PWA and multilingual experiences for real church environments.</p></div>
                </div>
            </div>
        </div>

        <div class="belief-visual reveal">
            <div class="belief-content">
                <div class="section-label">WHAT WE BELIEVE</div>
                <h3>Technology should make ministry easier — not become another burden for church leaders.</h3>
                <p>
                    We believe small churches matter, mission matters, connection matters, and the people
                    using ChurchOS should help shape what it becomes.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="office-section">
    <div class="container">
        <div class="section-heading centered reveal">
            <div class="section-label">WHERE CHURCHOS IS BUILT</div>
            <h2>People behind the platform.<br><span>A place for the work.</span></h2>
            <p>These are temporary placeholders for our future office and team photographs.</p>
        </div>

        <div class="office-grid">
            <div class="office-photo reveal"><span>Replace with main office photo</span></div>
            <div class="office-photo reveal"><span>Replace with team working photo</span></div>
            <div class="office-photo reveal"><span>Replace with meeting / collaboration photo</span></div>
        </div>
        <p class="office-note">Later, simply replace these placeholder blocks with your own office and team images.</p>
    </div>
</section>

<section class="video-section">
    <div class="container">
        <div class="section-heading centered reveal">
            <div class="section-label">SEE CHURCHOS</div>
            <h2>More than an idea.<br><span>A platform being built.</span></h2>
            <p>For now this opens a sample YouTube video. Replace the video ID in the page JavaScript when the official ChurchOS video is ready.</p>
        </div>

        <div class="video-card reveal">
            <button class="play-button" id="openVideo" aria-label="Play ChurchOS video">▶</button>
            <div class="video-caption">
                <small>ChurchOS video</small>
                <h3>See the story behind ChurchOS</h3>
            </div>
        </div>
    </div>
</section>

<section class="contact-section" id="contact">
    <div class="container">
        <div class="section-heading centered reveal">
            <div class="section-label">GET IN TOUCH</div>
            <h2>We are building this<br><span>with churches.</span></h2>
            <p>Use these temporary contact details for now. They can be replaced when the official ChurchOS channels are ready.</p>
        </div>

        <div class="contact-grid">
            <div class="contact-card reveal">
                <h3>ChurchOS</h3>
                <div class="contact-row"><strong>Office</strong><span>Imphal, Manipur, India</span></div>
                <div class="contact-row"><strong>Email</strong><a href="mailto:hello@churchos.example">hello@churchos.example</a></div>
                <div class="contact-row"><strong>Phone</strong><a href="tel:+919876543210">+91 98765 43210</a></div>
            </div>

            <div class="contact-card reveal">
                <h3>Connect with us</h3>
                <p>Follow the ChurchOS journey as we build tools for churches, ministries and mission work.</p>
                <div class="social-pill-row">
                    <a class="social-pill" href="https://facebook.com/churchos" target="_blank" rel="noopener">Facebook</a>
                    <a class="social-pill" href="https://instagram.com/churchos" target="_blank" rel="noopener">Instagram</a>
                    <a class="social-pill" href="https://youtube.com/@ChurchOS" target="_blank" rel="noopener">YouTube</a>
                    <a class="social-pill" href="https://churchos.example" target="_blank" rel="noopener">churchos.example</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-cta">
    <div class="container about-cta-content reveal">
        <div class="cta-icon">
            <svg viewBox="0 0 40 40" fill="none">
                <path d="M20 4L34 14V34H6V14L20 4Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                <path d="M20 13V25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <path d="M15 19H25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
        </div>
        <h2>Every church deserves the tools<br><span>to serve its people well.</span></h2>
        <p>ChurchOS is free. Our vision is bigger than software — we want to help churches become more connected, organized and digitally enabled.</p>
        <div class="cta-buttons">
            <a href="register.php" class="button button-white button-large">Get Started Free <span>→</span></a>
            <a href="index.php" class="button button-secondary button-large">Explore ChurchOS</a>
        </div>
    </div>
</section>

</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="index.php" class="brand footer-brand-logo">
                <span class="brand-mark">
                    <svg viewBox="0 0 40 40" fill="none">
                        <path d="M20 4L34 14V34H6V14L20 4Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                        <path d="M20 13V25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M15 19H25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="brand-name">Church<span>OS</span></span>
            </a>
            <p>A modern church management platform built to help churches organize people, ministry and mission.</p>
            <div class="social-links">
                <a href="https://facebook.com/churchos" target="_blank" rel="noopener" aria-label="Facebook">f</a>
                <a href="https://instagram.com/churchos" target="_blank" rel="noopener" aria-label="Instagram">◎</a>
                <a href="https://youtube.com/@ChurchOS" target="_blank" rel="noopener" aria-label="YouTube">▶</a>
            </div>
        </div>

        <div class="footer-column">
            <h4>Product</h4>
            <a href="index.php#features">Features</a>
            <a href="index.php#modules">Modules</a>
            <a href="index.php#pricing">Pricing</a>
            <a href="#">Updates</a>
        </div>
        <div class="footer-column">
            <h4>Modules</h4>
            <a href="#">Members</a>
            <a href="#">Families</a>
            <a href="#">Events</a>
            <a href="#">Giving</a>
            <a href="#">Volunteers</a>
        </div>
        <div class="footer-column">
            <h4>Resources</h4>
            <a href="#">Documentation</a>
            <a href="#">User Guides</a>
            <a href="#">Help Center</a>
            <a href="#">FAQ</a>
        </div>
        <div class="footer-column">
            <h4>Company</h4>
            <a href="about.php">About ChurchOS</a>
            <a href="#contact">Contact</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
    </div>

    <div class="container footer-bottom">
        <span>© 2026 ChurchOS. All rights reserved.</span>
        <span>Built for churches.</span>
    </div>
</footer>

<div class="video-modal" id="videoModal" aria-hidden="true">
    <div class="video-modal-inner">
        <button class="video-close" id="closeVideo" aria-label="Close video">×</button>
        <iframe id="videoFrame"
                title="ChurchOS video"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen></iframe>
    </div>
</div>

<script>

     /* =========================================================
   CHURCHOS HOMEPAGE JAVASCRIPT
========================================================= */

document.addEventListener("DOMContentLoaded", () => {


    /* =====================================================
       HEADER SCROLL EFFECT
    ===================================================== */

    const header =
        document.getElementById("siteHeader");

    const updateHeader = () => {

        if (window.scrollY > 20) {

            header.classList.add("scrolled");

        } else {

            header.classList.remove("scrolled");

        }

    };

    window.addEventListener(
        "scroll",
        updateHeader,
        { passive: true }
    );

    updateHeader();


    /* =====================================================
       MOBILE MENU
    ===================================================== */

    const mobileButton =
        document.getElementById(
            "mobileMenuButton"
        );

    const mobileMenu =
        document.getElementById(
            "mobileMenu"
        );


    mobileButton.addEventListener(
        "click",
        () => {

            mobileMenu.classList.toggle(
                "open"
            );

        }
    );


    document
        .querySelectorAll(
            ".mobile-menu a"
        )
        .forEach(link => {

            link.addEventListener(
                "click",
                () => {

                    mobileMenu.classList.remove(
                        "open"
                    );

                }
            );

        });


    /* =====================================================
       SCROLL REVEAL
    ===================================================== */

    const revealElements =
        document.querySelectorAll(
            ".reveal"
        );


    const revealObserver =
        new IntersectionObserver(
            entries => {

                entries.forEach(
                    entry => {

                        if (
                            entry.isIntersecting
                        ) {

                            entry.target.classList.add(
                                "visible"
                            );

                            revealObserver.unobserve(
                                entry.target
                            );

                        }

                    }
                );

            },
            {
                threshold: 0.12
            }
        );


    revealElements.forEach(
        element => {

            revealObserver.observe(
                element
            );

        }
    );


    /* =====================================================
       COUNTERS
    ===================================================== */

    const counters =
        document.querySelectorAll(
            ".counter"
        );


    const animateCounter = counter => {

        const target =
            Number(
                counter.dataset.target
            );

        const duration = 1300;

        const startTime =
            performance.now();


        const update = currentTime => {

            const elapsed =
                currentTime - startTime;

            const progress =
                Math.min(
                    elapsed / duration,
                    1
                );


            /*
             * Smooth easing
             */
            const eased =
                1 -
                Math.pow(
                    1 - progress,
                    3
                );


            const value =
                Math.floor(
                    eased * target
                );


            counter.textContent =
                value.toLocaleString();


            if (progress < 1) {

                requestAnimationFrame(
                    update
                );

            } else {

                counter.textContent =
                    target.toLocaleString();

            }

        };


        requestAnimationFrame(
            update
        );

    };


    const counterObserver =
        new IntersectionObserver(
            entries => {

                entries.forEach(
                    entry => {

                        if (
                            entry.isIntersecting
                        ) {

                            animateCounter(
                                entry.target
                            );

                            counterObserver.unobserve(
                                entry.target
                            );

                        }

                    }
                );

            },
            {
                threshold: .5
            }
        );


    counters.forEach(
        counter => {

            counterObserver.observe(
                counter
            );

        }
    );


    /* =====================================================
       FAQ
    ===================================================== */

    const faqItems =
        document.querySelectorAll(
            ".faq-item"
        );


    faqItems.forEach(item => {

        const button =
            item.querySelector(
                ".faq-question"
            );


        button.addEventListener(
            "click",
            () => {

                const isActive =
                    item.classList.contains(
                        "active"
                    );


                /*
                 * Close all FAQ items
                 */

                faqItems.forEach(
                    faq => {

                        faq.classList.remove(
                            "active"
                        );

                    }
                );


                /*
                 * Reopen clicked item
                 */

                if (!isActive) {

                    item.classList.add(
                        "active"
                    );

                }

            }
        );

    });


    /* =====================================================
       SMOOTH ANCHOR SCROLL
    ===================================================== */

    document
        .querySelectorAll(
            'a[href^="#"]'
        )
        .forEach(anchor => {

            anchor.addEventListener(
                "click",
                event => {

                    const targetId =
                        anchor.getAttribute(
                            "href"
                        );


                    if (
                        targetId === "#"
                    ) {

                        event.preventDefault();

                        window.scrollTo({
                            top: 0,
                            behavior: "smooth"
                        });

                        return;

                    }


                    const target =
                        document.querySelector(
                            targetId
                        );


                    if (!target) {
                        return;
                    }


                    event.preventDefault();


                    const headerHeight =
                        header.offsetHeight;


                    const targetPosition =
                        target.getBoundingClientRect()
                            .top
                        +
                        window.scrollY
                        -
                        headerHeight
                        -
                        15;


                    window.scrollTo({

                        top:
                            targetPosition,

                        behavior:
                            "smooth"

                    });

                }
            );

        });


    /* =====================================================
       DASHBOARD PARALLAX
    ===================================================== */

    const dashboard =
        document.querySelector(
            ".dashboard-window"
        );


    const hero =
        document.querySelector(
            ".hero"
        );


    if (
        dashboard &&
        hero &&
        window.innerWidth > 900
    ) {

        hero.addEventListener(
            "mousemove",
            event => {

                const rect =
                    hero.getBoundingClientRect();


                const x =
                    (
                        event.clientX
                        -
                        rect.left
                    )
                    /
                    rect.width
                    -
                    .5;


                const y =
                    (
                        event.clientY
                        -
                        rect.top
                    )
                    /
                    rect.height
                    -
                    .5;


                dashboard.style.transform =
                    `
                    perspective(1500px)
                    rotateY(${x * -5}deg)
                    rotateX(${y * 3}deg)
                    translateY(${y * -4}px)
                    `;

            }
        );


        hero.addEventListener(
            "mouseleave",
            () => {

                dashboard.style.transform =
                    `
                    perspective(1500px)
                    rotateY(-4deg)
                    rotateX(2deg)
                    `;

            }
        );

    }


    /* =====================================================
       CARD STAGGER
    ===================================================== */

    const grids = [
        ".feature-grid",
        ".ministry-grid",
        ".stats-grid"
    ];


    grids.forEach(selector => {

        document
            .querySelectorAll(
                `${selector} .reveal`
            )
            .forEach(
                (element, index) => {

                    element.style.transitionDelay =
                        `${index * 70}ms`;

                }
            );

    });


});


    /* =========================================================
       ABOUT PAGE — VIDEO POPUP
       Replace VIDEO_ID with the official ChurchOS YouTube video.
    ========================================================= */
    document.addEventListener("DOMContentLoaded", () => {
        const videoId = "ScMzIvxBSi4"; // temporary sample YouTube video
        const modal = document.getElementById("videoModal");
        const frame = document.getElementById("videoFrame");
        const open = document.getElementById("openVideo");
        const close = document.getElementById("closeVideo");

        if (!modal || !frame || !open || !close) return;

        const openVideo = () => {
            frame.src = "https://www.youtube.com/embed/" + videoId + "?autoplay=1&rel=0";
            modal.classList.add("open");
            modal.setAttribute("aria-hidden", "false");
            document.body.style.overflow = "hidden";
        };

        const closeVideo = () => {
            modal.classList.remove("open");
            modal.setAttribute("aria-hidden", "true");
            frame.src = "";
            document.body.style.overflow = "";
        };

        open.addEventListener("click", openVideo);
        close.addEventListener("click", closeVideo);

        modal.addEventListener("click", e => {
            if (e.target === modal) closeVideo();
        });

        document.addEventListener("keydown", e => {
            if (e.key === "Escape" && modal.classList.contains("open")) {
                closeVideo();
            }
        });
    });
</script>

</body>
</html>
