<?php
declare(strict_types=1);

$basePath = rtrim((string) config('app.base_path'), '/');

function landingEsc(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<style>
/* ManipurApp public landing page — visual layer only. */
.ma-public{--green:#087d64;--green-dark:#075743;--green-soft:#eef9f5;--ink:#10241f;--muted:#71827c;--line:#e0ebe6;--bg:#f6faf8;--white:#fff;font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:var(--ink);background:var(--bg);min-height:100vh}
.ma-public *{box-sizing:border-box}.ma-public a{text-decoration:none;color:inherit}.ma-public .container{width:min(1380px,calc(100% - 48px));margin:auto}
.ma-public-header{height:76px;background:rgba(255,255,255,.94);border-bottom:1px solid var(--line);display:flex;align-items:center;padding:0 34px;gap:24px;position:sticky;top:0;z-index:100;backdrop-filter:blur(18px)}
.ma-public-brand{display:flex;align-items:center;gap:10px;min-width:245px}.ma-public-logo{width:43px;height:43px;border-radius:14px;background:linear-gradient(145deg,#0c9677,#075743);display:grid;place-items:center;color:#fff;font-size:23px;box-shadow:0 8px 20px rgba(8,125,100,.18)}.ma-public-brand strong{display:block;font-size:20px;letter-spacing:-.6px}.ma-public-brand small{display:block;margin-top:2px;color:#7a8b85;font-size:9px;font-weight:800}
.ma-public-nav{display:flex;align-items:stretch;justify-content:center;gap:6px;flex:1;height:100%}.ma-public-nav a{display:flex;align-items:center;padding:0 14px;color:#61736d;font-size:11px;font-weight:800}.ma-public-nav a:hover{color:var(--green)}
.ma-public-actions{display:flex;align-items:center;gap:9px;min-width:250px;justify-content:flex-end}.ma-public-actions .login{font-size:11px;font-weight:900;color:#087d64;padding:10px 13px}.ma-public-actions .register{padding:11px 16px;border-radius:12px;background:#087d64;color:#fff;font-size:10px;font-weight:900;box-shadow:0 7px 18px rgba(8,125,100,.18)}
.ma-public-menu{display:none}
.ma-public-hero{position:relative;min-height:480px;overflow:hidden;background:linear-gradient(180deg,#e9b985 0%,#f0ca91 30%,#6f8e94 60%,#173f4a 100%);color:#fff}
.ma-public-hero:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(8,39,48,.84),rgba(8,39,48,.52) 40%,rgba(8,39,48,.08) 78%),linear-gradient(180deg,transparent 45%,rgba(8,38,46,.42))}
.ma-scene{position:absolute;inset:0;overflow:hidden}.ma-sun{position:absolute;width:130px;height:130px;border-radius:50%;right:17%;top:55px;background:rgba(255,229,176,.72);box-shadow:0 0 75px rgba(255,216,143,.5)}.ma-mountain{position:absolute;bottom:62px;width:58%;height:240px;background:linear-gradient(155deg,#35535e,#173b47);clip-path:polygon(0 100%,12% 56%,20% 72%,33% 24%,43% 54%,55% 8%,68% 50%,82% 29%,100% 76%,100% 100%)}.ma-mountain.one{left:-5%}.ma-mountain.two{right:-9%;transform:scaleX(-1);opacity:.7;background:linear-gradient(155deg,#547078,#1d4650)}.ma-city{position:absolute;left:0;right:0;bottom:0;height:115px;background:rgba(21,50,55,.78);clip-path:polygon(0 66%,7% 44%,12% 56%,18% 38%,24% 57%,31% 31%,38% 55%,45% 42%,52% 58%,60% 34%,69% 55%,76% 41%,83% 59%,90% 38%,100% 54%,100% 100%,0 100%)}
.ma-hero-inner{position:relative;z-index:3;max-width:1380px;height:480px;margin:auto;padding:68px 54px}.ma-hero-kicker{display:inline-flex;padding:7px 12px;border-radius:999px;background:rgba(255,255,255,.15);font-size:9px;font-weight:900;letter-spacing:1px}.ma-hero h1{font-size:52px;line-height:.98;letter-spacing:-2.7px;max-width:680px;margin:18px 0 0}.ma-hero h1 span{color:#bdf0dc}.ma-hero p{max-width:650px;margin:15px 0 0;color:rgba(255,255,255,.9);font-size:14px;line-height:1.65}.ma-hero-actions{display:flex;gap:10px;margin-top:23px}.ma-hero-actions a{padding:13px 18px;border-radius:12px;font-size:10px;font-weight:900}.ma-hero-primary{background:#fff;color:#087d64!important}.ma-hero-secondary{border:1px solid rgba(255,255,255,.35);background:rgba(255,255,255,.1);color:#fff!important}.ma-hero-note{position:absolute;right:90px;top:100px;font-family:cursive;font-size:25px;transform:rotate(-5deg);color:rgba(255,255,255,.9)}
.ma-search{position:absolute;z-index:5;left:50%;transform:translateX(-50%);bottom:43px;width:min(880px,calc(100% - 120px));height:58px;background:#fff;border-radius:19px;display:flex;align-items:center;padding:6px 7px 6px 18px;box-shadow:0 15px 35px rgba(4,36,42,.28);color:#82928d}.ma-search span{font-size:23px;margin-right:8px}.ma-search input{border:0;outline:0;background:transparent;flex:1;font-size:13px;color:#203b34}.ma-search button{height:46px;border:0;border-radius:14px;padding:0 24px;background:#087d64;color:#fff;font-size:10px;font-weight:900;cursor:pointer}
.ma-content{max-width:1440px;margin:-1px auto 0;padding:0 24px 75px}.ma-services{position:relative;z-index:7;margin-top:-1px;background:#fff;border:1px solid var(--line);border-radius:24px;display:grid;grid-template-columns:repeat(8,minmax(0,1fr));padding:18px 10px;box-shadow:0 13px 30px rgba(31,70,58,.08)}.ma-service{min-height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:7px;border-right:1px solid #edf2ef}.ma-service:last-child{border-right:0}.ma-service-icon{width:58px;height:58px;border-radius:18px;display:grid;place-items:center;font-size:27px}.ma-service strong{font-size:11px}.ma-service small{font-size:8px;color:#7b8c86}.s1 .ma-service-icon{background:#ffe2d5}.s2 .ma-service-icon{background:#dcf6e9}.s3 .ma-service-icon{background:#fff1c7}.s4 .ma-service-icon{background:#e1efff}.s5 .ma-service-icon{background:#eee2ff}.s6 .ma-service-icon{background:#dff7ef}.s7 .ma-service-icon{background:#ffe0e4}.s8 .ma-service-icon{background:#e4edff}
.ma-ai{margin:22px auto 0;max-width:1180px;padding:16px 19px;border:1px solid #bfe1d5;border-radius:19px;background:linear-gradient(135deg,#eef9f4,#fff);display:flex;align-items:center;gap:14px;box-shadow:0 9px 24px rgba(8,82,64,.06)}.ma-ai-icon{width:43px;height:43px;border-radius:14px;background:#087d64;color:#fff;display:grid;place-items:center;font-size:20px}.ma-ai-copy{flex:1}.ma-ai-copy small{color:#087d64;font-size:7px;font-weight:950;letter-spacing:1.1px}.ma-ai-copy strong{display:block;font-size:17px;margin-top:2px}.ma-ai-copy span{display:block;color:#71817a;font-size:10px;margin-top:2px}.ma-ai b{padding:10px 15px;border-radius:11px;background:#087d64;color:#fff;font-size:9px}
.ma-section{margin-top:28px}.ma-section-head{display:flex;justify-content:space-between;align-items:end;margin-bottom:13px}.ma-section-head h2{margin:0;font-size:20px;letter-spacing:-.5px}.ma-section-head p{margin:4px 0 0;color:#7b8c86;font-size:10px}.ma-section-head a{font-size:10px;font-weight:900;color:#087d64}.ma-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px}.ma-card{background:#fff;border:1px solid #e1ebe6;border-radius:15px;overflow:hidden;box-shadow:0 6px 18px rgba(30,67,56,.045)}.ma-photo{height:135px;display:grid;place-items:center;font-size:50px;background:linear-gradient(135deg,#e2c09c,#73513f)}.ma-photo.f2{background:linear-gradient(135deg,#b8d0ca,#54716d)}.ma-photo.f3{background:linear-gradient(135deg,#d1a06f,#704033)}.ma-photo.f4{background:linear-gradient(135deg,#e8d0aa,#9b6a46)}.ma-photo.d1{background:linear-gradient(135deg,#91cbe0,#4d7282)}.ma-photo.d2{background:linear-gradient(135deg,#c9a46e,#73553d)}.ma-photo.d3{background:linear-gradient(135deg,#91c9dc,#4d8b6d)}.ma-photo.d4{background:linear-gradient(135deg,#a7c778,#55783d)}.ma-card-body{padding:12px}.ma-card-body strong{display:block;font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ma-card-body b{display:block;color:#a46e08;font-size:9px;margin-top:5px}.ma-card-body small{display:block;color:#7b8c86;font-size:8.5px;margin-top:5px}.ma-tag{display:inline-flex;margin-top:7px;padding:4px 7px;border-radius:999px;background:#eff7f4;color:#087d64;font-size:7px;font-weight:900}
.ma-promos{display:grid;grid-template-columns:1.1fr 1fr 1fr;gap:15px}.ma-promo{min-height:140px;border-radius:18px;padding:20px;display:flex;justify-content:space-between;overflow:hidden}.ma-promo h3{margin:0;font-size:22px;line-height:1}.ma-promo p{margin:8px 0 12px;color:inherit;opacity:.75;font-size:9px;max-width:250px}.ma-promo a{display:inline-flex;padding:8px 11px;border-radius:10px;background:#087d64;color:#fff;font-size:8px;font-weight:900}.ma-promo>span{font-size:72px;align-self:center}.promo-food{background:linear-gradient(110deg,#ffe0ce,#ffd0b0)}.promo-travel{background:linear-gradient(110deg,#cceaf1,#b5d6e7)}.promo-rental{background:linear-gradient(110deg,#dcf5e7,#c7ebd8)}
.ma-footer{padding:30px 0;border-top:1px solid var(--line);background:#fff}.ma-footer-inner{display:flex;justify-content:space-between;gap:20px;align-items:center}.ma-footer strong{font-size:15px}.ma-footer small{color:#7c8d87;font-size:9px}.ma-footer-links{display:flex;gap:18px;color:#667a73;font-size:9px;font-weight:800}
@media(max-width:1100px){.ma-public-nav{display:none}.ma-public-actions{min-width:0;margin-left:auto}.ma-public-header{padding:0 20px}.ma-services{grid-template-columns:repeat(4,1fr)}.ma-service:nth-child(4){border-right:0}.ma-grid{grid-template-columns:repeat(2,1fr)}.ma-hero h1{font-size:44px}.ma-hero-note{display:none}}
@media(max-width:700px){.ma-public-header{height:68px;padding:0 15px}.ma-public-brand{min-width:0}.ma-public-brand small{display:none}.ma-public-brand strong{font-size:18px}.ma-public-logo{width:39px;height:39px}.ma-public-actions .login{display:none}.ma-public-actions .register{padding:10px 12px}.ma-public-hero{min-height:510px}.ma-hero-inner{height:510px;padding:46px 20px}.ma-hero h1{font-size:38px;letter-spacing:-2px}.ma-hero p{font-size:12px}.ma-hero-actions{margin-top:18px}.ma-search{width:calc(100% - 30px);bottom:28px;height:54px}.ma-search button{padding:0 15px}.ma-content{padding:0 14px 55px}.ma-services{grid-template-columns:repeat(2,1fr);border-radius:20px;padding:8px}.ma-service{border-right:0;border-bottom:1px solid #edf2ef;min-height:105px}.ma-service:nth-last-child(-n+2){border-bottom:0}.ma-grid{grid-template-columns:1fr 1fr;gap:9px}.ma-photo{height:105px;font-size:40px}.ma-promos{grid-template-columns:1fr}.ma-promo{min-height:125px}.ma-ai{align-items:flex-start}.ma-ai b{display:none}.ma-footer-inner{flex-direction:column;align-items:flex-start}.ma-footer-links{flex-wrap:wrap}}
</style>

<div class="ma-public">
<header class="ma-public-header">
  <a class="ma-public-brand" href="<?= landingEsc($basePath) ?>/">
    <span class="ma-public-logo">🌿</span>
    <span><strong>ManipurApp</strong><small>People · Places · Possibilities</small></span>
  </a>
  <nav class="ma-public-nav" aria-label="Main navigation">
    <a href="#services">Services</a><a href="#discover">Discover Manipur</a><a href="#plan">Plan with AI</a><a href="#about">About</a>
  </nav>
  <div class="ma-public-actions">
    <a class="login" href="<?= landingEsc($basePath) ?>/login">Business Login</a>
    <a class="register" href="<?= landingEsc($basePath) ?>/register">Register as Business Owner</a>
  </div>
</header>

<section class="ma-public-hero">
  <div class="ma-scene"><div class="ma-sun"></div><div class="ma-mountain one"></div><div class="ma-mountain two"></div><div class="ma-city"></div></div>
  <div class="ma-hero-inner">
    <span class="ma-hero-kicker">YOUR DIGITAL GATEWAY TO MANIPUR</span>
    <h1>Discover Manipur.<br><span>One app. Many possibilities.</span></h1>
    <p>Explore places, find food, book local services and plan your journey across Manipur from one connected platform built for local communities and visitors.</p>
    <div class="ma-hero-actions">
      <a class="ma-hero-primary" href="<?= landingEsc($basePath) ?>/register">Register Your Business</a>
      <a class="ma-hero-secondary" href="<?= landingEsc($basePath) ?>/login">Business Login</a>
    </div>
    <div class="ma-hero-note">Explore • Eat • Travel • Experience</div>
  </div>
  <form class="ma-search" action="<?= landingEsc($basePath) ?>/member/search" method="get">
    <span>⌕</span><input name="church" aria-label="Search" placeholder="Search services, places and experiences in Manipur…"><button type="submit">Explore</button>
  </form>
</section>

<main class="ma-content">
  <section class="ma-services" id="services">
    <a class="ma-service s1" href="<?= landingEsc($basePath) ?>/member/restaurants"><span class="ma-service-icon">🍽️</span><strong>Restaurants</strong><small>Food & dining</small></a>
    <a class="ma-service s2" href="<?= landingEsc($basePath) ?>/member/fresh-food"><span class="ma-service-icon">🥬</span><strong>Fresh Food</strong><small>Local produce</small></a>
    <a class="ma-service s3" href="<?= landingEsc($basePath) ?>/member/taxi"><span class="ma-service-icon">🚕</span><strong>Taxi</strong><small>Local rides</small></a>
    <a class="ma-service s4" href="<?= landingEsc($basePath) ?>/member/commercial-rental"><span class="ma-service-icon">🚐</span><strong>Vehicle Rental</strong><small>Travel & work</small></a>
    <a class="ma-service s5" href="<?= landingEsc($basePath) ?>/member/tourism"><span class="ma-service-icon">🏔️</span><strong>Tourism</strong><small>Places to explore</small></a>
    <a class="ma-service s6" href="<?= landingEsc($basePath) ?>/member/tourism"><span class="ma-service-icon">🏡</span><strong>Stays</strong><small>Hotels & homestays</small></a>
    <a class="ma-service s7" href="<?= landingEsc($basePath) ?>/member/tourism"><span class="ma-service-icon">🧭</span><strong>Experiences</strong><small>Local activities</small></a>
    <a class="ma-service s8" href="<?= landingEsc($basePath) ?>/member/tourism"><span class="ma-service-icon">📅</span><strong>Events</strong><small>What's happening</small></a>
  </section>

  <section class="ma-ai" id="plan">
    <span class="ma-ai-icon">✦</span><div class="ma-ai-copy"><small>SMART TRAVEL PLANNING</small><strong>Visiting Manipur for the first time?</strong><span>Ask ManipurApp AI for help discovering destinations, stays, restaurants and experiences.</span></div><b>Plan with AI</b>
  </section>

  <section class="ma-section" id="discover">
    <div class="ma-section-head"><div><h2>Popular around Manipur</h2><p>Discover places and local food through ManipurApp.</p></div><a href="<?= landingEsc($basePath) ?>/member/tourism">Explore all →</a></div>
    <div class="ma-grid">
      <article class="ma-card"><div class="ma-photo f1">🍛</div><div class="ma-card-body"><strong>Ema Kitchen</strong><b>★ Restaurant</b><small>Manipuri · Imphal West</small><em class="ma-tag">Local favourite</em></div></article>
      <article class="ma-card"><div class="ma-photo f2">☕</div><div class="ma-card-body"><strong>Hillside Café</strong><b>★ Café</b><small>Imphal West</small><em class="ma-tag">Café & food</em></div></article>
      <article class="ma-card"><div class="ma-photo f3">🍲</div><div class="ma-card-body"><strong>Tribal Foods</strong><b>★ Restaurant</b><small>Imphal East</small><em class="ma-tag">Northeast cuisine</em></div></article>
      <article class="ma-card"><div class="ma-photo f4">🍱</div><div class="ma-card-body"><strong>Yaiphaba Kitchen</strong><b>★ Restaurant</b><small>Imphal West</small><em class="ma-tag">Manipuri food</em></div></article>
    </div>
  </section>

  <section class="ma-section">
    <div class="ma-section-head"><div><h2>Places worth discovering</h2><p>Start exploring the destinations already available on the platform.</p></div><a href="<?= landingEsc($basePath) ?>/member/tourism">View destinations →</a></div>
    <div class="ma-grid">
      <article class="ma-card"><div class="ma-photo d1">🌊</div><div class="ma-card-body"><strong>Loktak Lake</strong><small>Moirang · Bishnupur</small><em class="ma-tag">Nature</em></div></article>
      <article class="ma-card"><div class="ma-photo d2">🏯</div><div class="ma-card-body"><strong>Kangla Fort</strong><small>Imphal · Imphal West</small><em class="ma-tag">Heritage</em></div></article>
      <article class="ma-card"><div class="ma-photo d3">🌿</div><div class="ma-card-body"><strong>Ukhrul</strong><small>Ukhrul District</small><em class="ma-tag">Hills & nature</em></div></article>
      <article class="ma-card"><div class="ma-photo d4">🏞️</div><div class="ma-card-body"><strong>Keibul Lamjao</strong><small>Bishnupur District</small><em class="ma-tag">Wildlife</em></div></article>
    </div>
  </section>

  <section class="ma-section" id="about">
    <div class="ma-promos">
      <div class="ma-promo promo-food"><div><h3>Eat local.</h3><p>Find restaurants and local food from businesses across Manipur.</p><a href="<?= landingEsc($basePath) ?>/member/restaurants">Find food</a></div><span>🍜</span></div>
      <div class="ma-promo promo-travel"><div><h3>See more.</h3><p>Discover destinations, stays, guides and experiences.</p><a href="<?= landingEsc($basePath) ?>/member/tourism">Explore tourism</a></div><span>🏔️</span></div>
      <div class="ma-promo promo-rental"><div><h3>Move local.</h3><p>Book taxi and commercial vehicle services when you need them.</p><a href="<?= landingEsc($basePath) ?>/member/taxi">Book a ride</a></div><span>🚕</span></div>
    </div>
  </section>
</main>

<footer class="ma-footer">
  <div class="container ma-footer-inner">
    <div><strong>ManipurApp</strong><br><small>People · Places · Possibilities</small></div>
    <div class="ma-footer-links"><a href="<?= landingEsc($basePath) ?>/login">Business Login</a><a href="<?= landingEsc($basePath) ?>/register">Register</a><a href="<?= landingEsc($basePath) ?>/about">About</a></div>
  </div>
</footer>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('.ma-public-nav a[href^="#"]').forEach(a=>a.addEventListener('click',e=>{const t=document.querySelector(a.getAttribute('href'));if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth',block:'start'})}}));});
</script>
