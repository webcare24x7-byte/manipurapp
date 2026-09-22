<?php
$memberFooterBasePath = rtrim((string)config('app.base_path'), '/');
$memberFooterEsc = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>
<footer class="member-desktop-footer" aria-label="ManipurApp footer">
    <div class="member-desktop-footer-inner">
        <div class="member-footer-brand">
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member" class="member-footer-logo">🌿</a>
            <div>
                <strong>ManipurApp</strong>
                <p>People · Places · Possibilities</p>
                <small>One connected platform for discovering, travelling, eating and doing business in Manipur.</small>
            </div>
        </div>

        <div class="member-footer-column">
            <h3>About</h3>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/about">About Us</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/contact">Contact Us</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/faq">FAQ</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/blog">Blog</a>
        </div>

        <div class="member-footer-column">
            <h3>Services</h3>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/services/taxi">Taxi</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/services/commercial-vehicle-rental">Commercial Vehicle Rental</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/services/tour-packages">Tour Packages</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/services/destinations">Destinations</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/services/ilp-helper">Digital ILP Helper</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/services/trip-planner">Trip Planner</a>
        </div>

        <div class="member-footer-column">
            <h3>More Services</h3>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/services/fresh-food-groceries">Fresh Food &amp; Groceries</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/services/restaurants">Restaurants &amp; Food Ordering</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/services/homestays-hotels">Homestays &amp; Hotels</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/services">All Services</a>
        </div>

        <div class="member-footer-column">
            <h3>Resources</h3>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/vendor-registration">Vendor Registration</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/business-registration">Business Registration</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/terms">Terms of Use</a>
            <a href="<?= $memberFooterEsc($memberFooterBasePath) ?>/member/privacy">Privacy Policy</a>
        </div>
    </div>
    <div class="member-footer-bottom">
        <span>© <?= date('Y') ?> ManipurApp. Built for Manipur.</span>
        <span>People · Places · Possibilities</span>
    </div>
</footer>
