<?php

declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';


$basePath = rtrim((string) config('app.base_path'), '/');
$isLoggedIn = is_array($member ?? null);
$memberName = trim((string) ($member['name'] ?? ''));
$firstName = $memberName !== '' ? (preg_split('/\s+/', $memberName)[0] ?? '') : '';

function memberAppEsc(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$maHomeCart = $_SESSION['memberapp_restaurant_cart'] ?? [];
$maHomeCartCount = 0;
foreach (is_array($maHomeCart['items'] ?? null) ? $maHomeCart['items'] : [] as $line) {
    if (is_array($line)) {
        $maHomeCartCount += max(0, (int) ($line['quantity'] ?? 0));
    }
}

$maFreshCart = $_SESSION['memberapp_fresh_food_cart'] ?? [];
$maFreshCartCount = 0;
foreach (is_array($maFreshCart['items'] ?? null) ? $maFreshCart['items'] : [] as $line) {
    if (is_array($line)) {
        $maFreshCartCount += max(0, (int) ($line['quantity'] ?? 0));
    }
}
?>

<style>
/* --------------------------------------------------------------------------
   MemberApp Home v1.3.0
   Focus: Food + Fresh Food + Taxi only.
   Presentation-only upgrade. Existing routes/data hooks are preserved.
   -------------------------------------------------------------------------- */
.ma-home-v13{--ink:#10241f;--muted:#70817b;--line:#e2ebe7;--green:#087d64;--green-dark:#075743;--green-soft:#eaf7f2;--orange:#f26d32;--orange-soft:#fff0e8;--blue:#3e83dc;--blue-soft:#edf4ff;--gold:#f5b52e;--bg:#f6faf8;--white:#fff;max-width:980px;margin:0 auto;padding:0 16px 112px;color:var(--ink)}
.ma-home-v13 *{box-sizing:border-box}
.ma-home-v13 a{text-decoration:none}
.ma-home-v13 .home-header{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 0 4px}
.ma-home-v13 .brand{display:flex;align-items:center;gap:10px;min-width:0;color:var(--ink)}
.ma-home-v13 .brand-mark{width:44px;height:44px;flex:0 0 44px;border-radius:15px;display:grid;place-items:center;background:linear-gradient(145deg,#0c9677,#075743);color:#fff;box-shadow:0 9px 22px rgba(8,125,100,.18)}
.ma-home-v13 .brand-mark svg{width:29px;height:29px}.ma-home-v13 .brand-copy{min-width:0}.ma-home-v13 .brand-copy strong{display:block;font-size:19px;line-height:1.05;letter-spacing:-.55px}.ma-home-v13 .brand-copy small{display:block;margin-top:4px;color:var(--muted);font-size:9px;font-weight:700;letter-spacing:.15px}
.ma-home-v13 .header-actions{display:flex;align-items:center;gap:7px;flex:0 0 auto}.ma-home-v13 .fresh-cart-top{position:relative;height:40px;min-width:40px;padding:0 10px;border:1px solid #cfe5dc;border-radius:999px;background:#eaf7f2;color:var(--green);display:flex;align-items:center;justify-content:center;gap:5px;text-decoration:none;box-shadow:0 7px 18px rgba(24,55,46,.05);font-size:15px;font-weight:900}.ma-home-v13 .fresh-cart-top span{font-size:9px;letter-spacing:-.1px}.ma-home-v13 .fresh-cart-top .badge{right:-3px;top:-4px}
.ma-home-v13 .icon-btn{position:relative;width:40px;height:40px;border:1px solid var(--line);border-radius:50%;background:#fff;color:#29443d;display:grid;place-items:center;box-shadow:0 7px 18px rgba(24,55,46,.06)}.ma-home-v13 .icon-btn svg{width:19px;height:19px}.ma-home-v13 .badge{position:absolute;right:-2px;top:-3px;min-width:17px;height:17px;padding:0 4px;border-radius:99px;background:#ef514c;color:#fff;border:2px solid #fff;display:grid;place-items:center;font-size:8px;font-weight:900;line-height:1}
.ma-home-v13 .welcome{padding:22px 0 15px}.ma-home-v13 .welcome h1{margin:0;font-size:31px;line-height:1.06;letter-spacing:-1.25px}.ma-home-v13 .welcome p{margin:7px 0 0;color:var(--muted);font-size:13px;line-height:1.45}
.ma-home-v13 .search{height:52px;display:flex;align-items:center;gap:10px;padding:0 15px;border:1px solid #d8e5e0;border-radius:17px;background:#fff;box-shadow:0 9px 25px rgba(24,65,53,.07)}.ma-home-v13 .search svg{width:20px;flex:none;color:#59736a}.ma-home-v13 .search input{width:100%;border:0;outline:0;background:transparent;color:var(--ink);font:inherit;font-size:13px}.ma-home-v13 .search input::placeholder{color:#91a19c}.ma-home-v13 .search-clear{display:none;border:0;background:none;color:#879994;font-size:20px;cursor:pointer}
.ma-home-v13 .location-row{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:13px 1px 18px}.ma-home-v13 .location{display:flex;align-items:center;gap:7px;border:0;border-radius:999px;background:var(--green-soft);color:var(--green);padding:9px 12px;font-size:11px;font-weight:800;cursor:pointer}.ma-home-v13 .location svg{width:15px;height:15px}.ma-home-v13 .location .chev{width:12px;margin-left:2px}.ma-home-v13 .location-note{color:#899792;font-size:9px;text-align:right}
.ma-home-v13 .hero{position:relative;min-height:225px;overflow:hidden;border-radius:27px;background:linear-gradient(116deg,#075641 0%,#078363 58%,#3db58f 100%);color:#fff;padding:24px 22px;box-shadow:0 18px 38px rgba(5,91,72,.21)}
.ma-home-v13 .hero:before{content:"";position:absolute;width:270px;height:270px;right:-95px;top:-120px;border-radius:50%;background:rgba(255,255,255,.1)}.ma-home-v13 .hero:after{content:"";position:absolute;width:340px;height:145px;right:-90px;bottom:-75px;border-radius:50%;background:rgba(249,198,87,.15)}
.ma-home-v13 .hero-copy{position:relative;z-index:3;max-width:64%}.ma-home-v13 .hero-kicker{display:inline-flex;padding:6px 9px;border-radius:99px;background:rgba(255,255,255,.14);font-size:8px;font-weight:900;letter-spacing:.65px;text-transform:uppercase}.ma-home-v13 .hero h2{margin:13px 0 8px;font-size:32px;line-height:.98;letter-spacing:-1.45px}.ma-home-v13 .hero p{margin:0;color:rgba(255,255,255,.88);font-size:11px;line-height:1.45;max-width:270px}.ma-home-v13 .hero-cta{display:inline-flex;align-items:center;gap:6px;margin-top:16px;padding:10px 13px;border-radius:11px;background:#fff;color:#086b57;font-size:10px;font-weight:900;box-shadow:0 8px 18px rgba(0,0,0,.12)}
.ma-home-v13 .hero-art{position:absolute;right:0;bottom:0;width:42%;height:100%}.ma-home-v13 .sun{position:absolute;right:50px;top:25px;width:44px;height:44px;border-radius:50%;background:#f8c45b;box-shadow:0 0 0 9px rgba(248,196,91,.13)}.ma-home-v13 .hill{position:absolute;right:-25px;bottom:-5px;width:250px;height:110px;background:linear-gradient(145deg,#b6d89b,#4e9673);clip-path:polygon(0 100%,28% 35%,43% 57%,62% 10%,100% 100%)}.ma-home-v13 .house{position:absolute;right:23px;bottom:27px;width:70px;height:50px;background:#f8e7c8;border-radius:3px;box-shadow:0 8px 12px rgba(0,0,0,.12)}.ma-home-v13 .house:before{content:"";position:absolute;left:-9px;top:-18px;border-left:44px solid transparent;border-right:44px solid transparent;border-bottom:23px solid #984b3b}.ma-home-v13 .house:after{content:"";position:absolute;left:27px;bottom:0;width:16px;height:26px;background:#7a5b4a}
.ma-home-v13 .section{margin-top:26px}.ma-home-v13 .section-head{display:flex;align-items:end;justify-content:space-between;gap:10px;margin-bottom:12px}.ma-home-v13 .section-head h2{margin:0;font-size:19px;line-height:1.15;letter-spacing:-.55px}.ma-home-v13 .section-head p{margin:4px 0 0;color:var(--muted);font-size:10px;line-height:1.35}.ma-home-v13 .see{border:0;background:none;color:var(--green);font-size:10px;font-weight:900;white-space:nowrap;cursor:pointer;padding:3px}
.ma-home-v13 .services{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.ma-home-v13 .service{position:relative;min-height:146px;padding:13px 10px 12px;border:1px solid var(--line);border-radius:20px;background:#fff;color:var(--ink);display:flex;flex-direction:column;align-items:flex-start;box-shadow:0 9px 23px rgba(28,63,53,.055);overflow:hidden}.ma-home-v13 .service:after{content:"";position:absolute;width:76px;height:76px;right:-23px;top:-23px;border-radius:50%;background:rgba(255,255,255,.45)}.ma-home-v13 .service-icon{width:50px;height:50px;border-radius:16px;display:grid;place-items:center;font-size:25px;margin-bottom:11px;box-shadow:inset 0 0 0 1px rgba(0,0,0,.025)}.ma-home-v13 .service strong{font-size:11px;line-height:1.18;letter-spacing:-.1px}.ma-home-v13 .service small{margin-top:5px;color:var(--muted);font-size:8.5px;line-height:1.3}.ma-home-v13 .service-arrow{margin-top:auto;color:var(--green);font-size:10px;font-weight:900}.ma-home-v13 .food .service-icon{background:var(--orange-soft);color:var(--orange)}.ma-home-v13 .fresh .service-icon{background:#e8f7ef;color:#078264}.ma-home-v13 .taxi .service-icon{background:var(--blue-soft);color:var(--blue)}
.ma-home-v13 .offer{position:relative;min-height:145px;overflow:hidden;border-radius:22px;padding:19px 18px;background:linear-gradient(110deg,#fff0df,#ffe0c5);border:1px solid #f3dcc9}.ma-home-v13 .offer-copy{position:relative;z-index:2;max-width:62%}.ma-home-v13 .offer-tag{display:inline-block;padding:5px 8px;border-radius:99px;background:#fff;color:#c65324;font-size:7.5px;font-weight:900;letter-spacing:.5px}.ma-home-v13 .offer h3{margin:9px 0 5px;color:#9f3218;font-size:21px;line-height:1.02;letter-spacing:-.65px}.ma-home-v13 .offer p{margin:0;color:#7e5c4d;font-size:9.5px;line-height:1.4}.ma-home-v13 .offer a{display:inline-flex;margin-top:11px;padding:8px 11px;border-radius:10px;background:var(--green);color:#fff;font-size:9px;font-weight:900}.ma-home-v13 .offer-art{position:absolute;right:-2px;bottom:-2px;width:42%;height:130px}.ma-home-v13 .offer-plate{position:absolute;right:3px;bottom:16px;width:135px;height:60px;border-radius:50%;background:#fff;box-shadow:0 9px 16px rgba(111,67,34,.12)}.ma-home-v13 .offer-food{position:absolute;border-radius:50%}.ma-home-v13 .offer-food.one{right:51px;bottom:38px;width:64px;height:42px;background:#d86a35;transform:rotate(-8deg)}.ma-home-v13 .offer-food.two{right:24px;bottom:43px;width:43px;height:30px;background:#e7b43d}.ma-home-v13 .offer-food.three{right:81px;bottom:27px;width:35px;height:28px;background:#6da95a}
.ma-home-v13 .quick-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.ma-home-v13 .quick{min-height:92px;border:1px solid var(--line);border-radius:19px;padding:13px 14px;background:#fff;display:flex;align-items:center;justify-content:space-between;color:var(--ink);box-shadow:0 7px 20px rgba(28,63,53,.045)}.ma-home-v13 .quick-copy strong{display:block;font-size:12px;letter-spacing:-.2px}.ma-home-v13 .quick-copy small{display:block;margin-top:4px;color:var(--muted);font-size:8.5px;line-height:1.35}.ma-home-v13 .quick-badge{width:42px;height:42px;border-radius:14px;display:grid;place-items:center;font-size:21px}.ma-home-v13 .quick.food{background:linear-gradient(145deg,#fff9f5,#fff);}.ma-home-v13 .quick.food .quick-badge{background:var(--orange-soft)}.ma-home-v13 .quick.fresh{background:linear-gradient(145deg,#f2fbf6,#fff)}.ma-home-v13 .quick.fresh .quick-badge{background:#e8f7ef}
.ma-home-v13 .member-banner{margin-top:26px;padding:20px;border-radius:22px;background:linear-gradient(135deg,#083f33,#0b765d);color:#fff;box-shadow:0 14px 30px rgba(6,75,59,.17)}.ma-home-v13 .member-banner h2{margin:0;font-size:20px;letter-spacing:-.5px}.ma-home-v13 .member-banner p{margin:6px 0 0;color:rgba(255,255,255,.78);font-size:10px;line-height:1.45}.ma-home-v13 .member-points{display:flex;flex-wrap:wrap;gap:6px;margin:12px 0}.ma-home-v13 .member-point{padding:6px 8px;border-radius:99px;background:rgba(255,255,255,.1);font-size:8px;font-weight:800}.ma-home-v13 .member-cta{display:inline-flex;padding:10px 13px;border-radius:10px;background:#fff;color:#086b57;font-size:9px;font-weight:900}
.ma-home-v13 .home-foot{padding:25px 0 8px;text-align:center;color:#83918d}.ma-home-v13 .home-foot strong{display:block;font-size:9px}.ma-home-v13 .home-foot small{display:block;margin-top:4px;font-size:8px}
.ma-home-v13 .hero-tourism-art,.ma-home-v13 .hero-experience-art{position:absolute;right:10%;top:16%;font-size:108px;filter:drop-shadow(0 18px 20px rgba(0,0,0,.16));transform:rotate(-4deg)}
.ma-home-v13 .hero-experience-art{font-size:96px;right:13%;top:20%;transform:rotate(8deg)}
@media (max-width:560px){.ma-home-v13{padding-left:14px;padding-right:14px}.ma-home-v13 .welcome h1{font-size:29px}.ma-home-v13 .hero{min-height:214px}.ma-home-v13 .hero h2{font-size:29px}.ma-home-v13 .hero-copy{max-width:67%}.ma-home-v13 .services{gap:8px}.ma-home-v13 .service{min-height:142px;padding:12px 9px}.ma-home-v13 .service-icon{width:46px;height:46px;font-size:23px}}
@media (min-width:700px){.ma-home-v13{padding-left:22px;padding-right:22px}.ma-home-v13 .home-header{padding-top:24px}.ma-home-v13 .welcome{padding-top:25px}.ma-home-v13 .hero{min-height:255px;padding:30px}.ma-home-v13 .hero h2{font-size:38px}.ma-home-v13 .hero p{font-size:12px}.ma-home-v13 .services{gap:13px}.ma-home-v13 .service{min-height:158px;padding:16px}.ma-home-v13 .service-icon{width:54px;height:54px}.ma-home-v13 .offer{min-height:165px;padding:23px}.ma-home-v13 .quick-row{grid-template-columns:repeat(2,1fr)}}

/* --------------------------------------------------------------------------
   Home v1.4.0 carousel presentation
   -------------------------------------------------------------------------- */
.ma-home-v13 .hero-carousel{position:relative;min-height:225px;overflow:hidden;border-radius:27px;box-shadow:0 18px 38px rgba(5,91,72,.18)}
.ma-home-v13 .hero-slide{position:absolute;inset:0;padding:24px 22px;display:flex;align-items:flex-start;opacity:0;visibility:hidden;transform:scale(1.015);transition:opacity .55s ease,transform .7s ease,visibility .55s ease;color:#fff}
.ma-home-v13 .hero-slide.active{opacity:1;visibility:visible;transform:scale(1)}
.ma-home-v13 article.hero-slide:nth-of-type(1){background:linear-gradient(118deg,#075641 0%,#087e63 58%,#35b58d 100%)}
.ma-home-v13 article.hero-slide:nth-of-type(2){background:linear-gradient(118deg,#7c2418 0%,#c74e25 56%,#ef9b48 100%)}
.ma-home-v13 article.hero-slide:nth-of-type(3){background:linear-gradient(118deg,#113e67 0%,#246b9b 55%,#72b6d4 100%)}
.ma-home-v13 article.hero-slide:nth-of-type(4){background:linear-gradient(118deg,#39551c 0%,#5f8d2e 52%,#b5d06d 100%)}
.ma-home-v13 article.hero-slide:nth-of-type(5){background:linear-gradient(118deg,#183a52 0%,#2e6f82 55%,#73c5bf 100%)}
.ma-home-v13 article.hero-slide:nth-of-type(6){background:linear-gradient(118deg,#5a2d4f 0%,#8f4f75 55%,#d49ab8 100%)}
.ma-home-v13 article.hero-slide:nth-of-type(5) .hero-slide-cta{color:#1d6072}.ma-home-v13 article.hero-slide:nth-of-type(6) .hero-slide-cta{color:#754164}
.ma-home-v13 .hero-slide:before{content:"";position:absolute;width:310px;height:310px;right:-125px;top:-145px;border-radius:50%;background:rgba(255,255,255,.105)}
.ma-home-v13 .hero-slide:after{content:"";position:absolute;width:390px;height:160px;right:-120px;bottom:-85px;border-radius:50%;background:rgba(255,255,255,.08)}
.ma-home-v13 .hero-slide-copy{position:relative;z-index:3;max-width:63%}
.ma-home-v13 .hero-slide-kicker{display:inline-flex;padding:6px 9px;border-radius:99px;background:rgba(255,255,255,.15);font-size:8px;font-weight:900;letter-spacing:.6px;text-transform:uppercase}
.ma-home-v13 .hero-slide h2{margin:13px 0 8px;font-size:31px;line-height:.98;letter-spacing:-1.4px}
.ma-home-v13 .hero-slide p{margin:0;max-width:275px;color:rgba(255,255,255,.9);font-size:10.5px;line-height:1.45}
.ma-home-v13 .hero-slide-cta{display:inline-flex;align-items:center;gap:6px;margin-top:15px;padding:10px 13px;border-radius:11px;background:#fff;color:#086b57;font-size:9.5px;font-weight:900;box-shadow:0 8px 18px rgba(0,0,0,.12)}
.ma-home-v13 article.hero-slide:nth-of-type(2) .hero-slide-cta{color:#9f3218}.ma-home-v13 article.hero-slide:nth-of-type(3) .hero-slide-cta{color:#15577f}.ma-home-v13 article.hero-slide:nth-of-type(4) .hero-slide-cta{color:#496f20}
.ma-home-v13 .hero-visual{position:absolute;right:1%;bottom:0;width:43%;height:100%;z-index:2}
.ma-home-v13 .hero-visual .visual-circle{position:absolute;right:26px;top:29px;width:58px;height:58px;border-radius:50%;background:#f8c45b;box-shadow:0 0 0 10px rgba(248,196,91,.12)}
.ma-home-v13 .hero-visual .visual-hill{position:absolute;right:-35px;bottom:-8px;width:270px;height:120px;background:linear-gradient(145deg,#c0dc9e,#4c966f);clip-path:polygon(0 100%,27% 36%,43% 58%,63% 8%,100% 100%)}
.ma-home-v13 .hero-visual .visual-house{position:absolute;right:20px;bottom:30px;width:68px;height:49px;background:#f8e7c8;border-radius:3px;box-shadow:0 8px 12px rgba(0,0,0,.12)}
.ma-home-v13 .hero-visual .visual-house:before{content:"";position:absolute;left:-9px;top:-18px;border-left:43px solid transparent;border-right:43px solid transparent;border-bottom:23px solid #984b3b}
.ma-home-v13 .hero-visual .visual-house:after{content:"";position:absolute;left:26px;bottom:0;width:16px;height:25px;background:#7a5b4a}
.ma-home-v13 .hero-food-art{position:absolute;right:2px;bottom:12px;width:170px;height:125px;border-radius:50%;background:#fff;box-shadow:0 15px 28px rgba(45,19,8,.2)}
.ma-home-v13 .hero-food-art:before{content:"🍜";position:absolute;right:24px;bottom:23px;font-size:70px;filter:drop-shadow(0 8px 6px rgba(0,0,0,.13))}
.ma-home-v13 .hero-food-art:after{content:"";position:absolute;right:17px;bottom:12px;width:140px;height:27px;border-radius:50%;background:rgba(78,38,18,.12)}
.ma-home-v13 .hero-taxi-art{position:absolute;right:-3px;bottom:12px;width:190px;height:118px}
.ma-home-v13 .hero-taxi-art:before{content:"🚕";position:absolute;right:2px;bottom:3px;font-size:94px;filter:drop-shadow(0 12px 7px rgba(0,0,0,.2))}
.ma-home-v13 .hero-fresh-art{position:absolute;right:0;bottom:0;width:195px;height:145px}
.ma-home-v13 .hero-fresh-art:before{content:"🥬🥦🥕";position:absolute;right:2px;bottom:25px;font-size:51px;letter-spacing:-10px;filter:drop-shadow(0 9px 5px rgba(0,0,0,.16))}
.ma-home-v13 .hero-fresh-art:after{content:"";position:absolute;right:14px;bottom:12px;width:170px;height:43px;border-radius:50%;background:#d8a15e;box-shadow:0 10px 15px rgba(51,37,12,.18)}
.ma-home-v13 .hero-dots{position:absolute;z-index:6;right:22px;left:auto;bottom:14px;display:flex;align-items:center;gap:5px}
.ma-home-v13 .hero-dot{width:6px;height:6px;padding:0;border:0;border-radius:50%;background:rgba(255,255,255,.48);cursor:pointer;transition:width .25s ease,background .25s ease}
.ma-home-v13 .hero-dot.active{width:18px;border-radius:99px;background:#fff}
.ma-home-v13 .hero-arrow{position:absolute;z-index:7;top:50%;transform:translateY(-50%);width:28px;height:28px;border:1px solid rgba(255,255,255,.35);border-radius:50%;background:rgba(255,255,255,.9);color:#1b4037;display:grid;place-items:center;font-size:16px;line-height:1;cursor:pointer;box-shadow:0 6px 14px rgba(0,0,0,.12)}
.ma-home-v13 .hero-arrow.prev{left:9px}.ma-home-v13 .hero-arrow.next{right:9px}
 .ma-home-v13 .offer-carousel{position:relative;min-height:150px;overflow:hidden;border-radius:22px}
.ma-home-v13 .offer-slide{position:absolute;inset:0;opacity:0;visibility:hidden;transform:translateX(8px);transition:opacity .45s ease,transform .55s ease,visibility .45s ease}
.ma-home-v13 .offer-slide.active{opacity:1;visibility:visible;transform:translateX(0)}
.ma-home-v13 .offer-pair{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;height:100%;min-height:150px}
.ma-home-v13 .offer-slide .offer{position:relative;height:100%;min-height:150px;padding:16px 13px;border-radius:20px;overflow:hidden}
.ma-home-v13 .offer-slide:nth-child(1) .offer:first-child{background:linear-gradient(110deg,#fff0df,#ffe0c5);border-color:#f3dcc9}
.ma-home-v13 .offer-slide:nth-child(1) .offer:last-child{background:linear-gradient(110deg,#e9f8ee,#d2f0df);border-color:#cce7d7}
.ma-home-v13 .offer-slide:nth-child(2) .offer:first-child{background:linear-gradient(110deg,#eef2ff,#e0e8ff);border-color:#d8e0f6}
.ma-home-v13 .offer-slide:nth-child(2) .offer:last-child{background:linear-gradient(110deg,#fff2d8,#ffe5b2);border-color:#f1dfbc}
.ma-home-v13 .offer-copy{position:relative;z-index:2;max-width:72%}
.ma-home-v13 .offer-tag{display:inline-block;padding:5px 7px;border-radius:99px;background:rgba(255,255,255,.82);color:#a34822;font-size:7px;font-weight:900;letter-spacing:.45px}
.ma-home-v13 .offer h3{margin:8px 0 5px;color:#8e3219;font-size:16px;line-height:1.02;letter-spacing:-.45px}
.ma-home-v13 .offer p{margin:0;color:#73594e;font-size:8.2px;line-height:1.35}
.ma-home-v13 .offer a{display:inline-flex;margin-top:9px;padding:7px 9px;border-radius:9px;background:var(--green);color:#fff;font-size:8px;font-weight:900;white-space:nowrap}
.ma-home-v13 .offer-slide:nth-child(1) .offer:last-child .offer-tag{color:#17704f}.ma-home-v13 .offer-slide:nth-child(1) .offer:last-child h3{color:#176344}.ma-home-v13 .offer-slide:nth-child(1) .offer:last-child p{color:#4c7260}
.ma-home-v13 .offer-slide:nth-child(2) .offer:first-child .offer-tag{color:#315a9c}.ma-home-v13 .offer-slide:nth-child(2) .offer:first-child h3{color:#294d91}.ma-home-v13 .offer-slide:nth-child(2) .offer:first-child p{color:#566984}
.ma-home-v13 .offer-slide:nth-child(2) .offer:last-child .offer-tag{color:#8c6112}.ma-home-v13 .offer-slide:nth-child(2) .offer:last-child h3{color:#80530d}.ma-home-v13 .offer-slide:nth-child(2) .offer:last-child p{color:#786343}
.ma-home-v13 .offer-visual{position:absolute;right:0;bottom:5px;width:39%;height:82px;display:grid;place-items:center;z-index:1}
.ma-home-v13 .offer-visual span{font-size:43px;line-height:1;filter:drop-shadow(0 8px 7px rgba(0,0,0,.12))}
.ma-home-v13 .offer-nav{position:absolute;z-index:6;right:50%;transform:translateX(50%);bottom:6px;display:flex;align-items:center;gap:4px}
.ma-home-v13 .offer-dot{width:5px;height:5px;border:0;padding:0;border-radius:50%;background:#aebbb6;cursor:pointer}.ma-home-v13 .offer-dot.active{width:15px;border-radius:99px;background:var(--green)}
.ma-home-v13 .offer-arrow{position:absolute;z-index:7;top:50%;transform:translateY(-50%);width:26px;height:26px;border:1px solid var(--line);border-radius:50%;background:rgba(255,255,255,.94);color:#34534a;display:grid;place-items:center;font-size:15px;cursor:pointer;box-shadow:0 5px 12px rgba(24,55,46,.08)}
.ma-home-v13 .offer-arrow.prev{left:6px}.ma-home-v13 .offer-arrow.next{right:6px}
.ma-home-v13 .popular-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
.ma-home-v13 .popular-card{display:block;min-width:0;overflow:hidden;border:1px solid var(--line);border-radius:18px;background:#fff;color:var(--ink);box-shadow:0 8px 20px rgba(28,63,53,.045)}
.ma-home-v13 .popular-image{height:78px;display:grid;place-items:center;font-size:39px;background:linear-gradient(145deg,#fff2e9,#f8dfcf)}
.ma-home-v13 .popular-card:nth-child(2) .popular-image{background:linear-gradient(145deg,#eff9f1,#dcefe2)}
.ma-home-v13 .popular-card:nth-child(3) .popular-image{background:linear-gradient(145deg,#eef5ff,#dce8fb)}
.ma-home-v13 .popular-body{padding:9px 9px 10px}.ma-home-v13 .popular-title{font-size:10px;font-weight:900;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ma-home-v13 .popular-meta{margin-top:4px;color:var(--muted);font-size:8px;line-height:1.3}.ma-home-v13 .popular-rating{margin-top:6px;font-size:8.5px;font-weight:800;color:#8b6718}.ma-home-v13 .popular-rating b{color:#f0ae1c;font-size:10px}
@media (max-width:560px){.ma-home-v13 .hero-carousel{min-height:218px}.ma-home-v13 .hero-slide{padding:21px 19px}.ma-home-v13 .hero-slide-copy{max-width:66%}.ma-home-v13 .hero-slide h2{font-size:29px}.ma-home-v13 .hero-visual{width:43%}.ma-home-v13 .hero-food-art{width:145px;height:108px;right:-4px}.ma-home-v13 .hero-food-art:before{font-size:58px;right:19px;bottom:20px}.ma-home-v13 .hero-taxi-art:before{font-size:79px}.ma-home-v13 .hero-fresh-art:before{font-size:43px}.ma-home-v13 .offer-carousel{min-height:148px}.ma-home-v13 .offer-pair{min-height:148px;gap:8px}.ma-home-v13 .offer-slide .offer{min-height:148px;padding:15px 11px}.ma-home-v13 .offer-copy{max-width:76%}.ma-home-v13 .offer h3{font-size:15px}.ma-home-v13 .offer p{font-size:7.8px}.ma-home-v13 .offer-visual{height:76px;width:35%}.ma-home-v13 .offer-visual span{font-size:36px}.ma-home-v13 .popular-grid{gap:8px}.ma-home-v13 .popular-image{height:70px;font-size:33px}.ma-home-v13 .popular-body{padding:8px}.ma-home-v13 .popular-meta{font-size:7.5px}}
@media (min-width:700px){.ma-home-v13 .hero-carousel{min-height:255px}.ma-home-v13 .hero-slide{padding:30px}.ma-home-v13 .hero-slide h2{font-size:38px}.ma-home-v13 .offer-carousel,.ma-home-v13 .offer-slide,.ma-home-v13 .offer-slide .offer{min-height:165px}.ma-home-v13 .hero-dots{right:30px;left:auto}}


/* --------------------------------------------------------------------------
   Service cards refinement — illustration-inspired v1.4.3
   Keep the existing links/data-service hooks; presentation only.
   -------------------------------------------------------------------------- */
.ma-home-v13 .services{grid-template-columns:repeat(3,minmax(0,1fr));gap:11px}
.ma-home-v13 .service{
    min-height:191px;
    padding:15px 10px 13px;
    border:1px solid rgba(214,228,222,.92);
    border-radius:22px;
    align-items:center;
    text-align:center;
    background:#fff;
    box-shadow:0 12px 28px rgba(28,63,53,.065);
    transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease;
}
.ma-home-v13 .service:hover,.ma-home-v13 .service:focus-visible{
    transform:translateY(-2px);
    box-shadow:0 16px 32px rgba(28,63,53,.10);
    border-color:#c9ded6;
}
.ma-home-v13 .service:before{
    content:"";position:absolute;inset:0;z-index:0;opacity:.9;
}
.ma-home-v13 .service.food:before{background:linear-gradient(145deg,#fff7f1 0%,#fff0e7 100%)}
.ma-home-v13 .service.fresh:before{background:linear-gradient(145deg,#f2fbf5 0%,#e8f7ee 100%)}
.ma-home-v13 .service.taxi:before{background:linear-gradient(145deg,#f3f7ff 0%,#eaf2ff 100%)}
.ma-home-v13 .service-glow{
    position:absolute;z-index:1;width:104px;height:104px;top:-38px;right:-34px;border-radius:50%;
    background:rgba(255,255,255,.48);pointer-events:none;
}
.ma-home-v13 .service-icon{
    position:relative;z-index:2;width:66px;height:66px;flex:0 0 66px;margin:0 0 11px;
    border-radius:21px;font-size:32px;line-height:1;
    box-shadow:0 8px 17px rgba(25,70,58,.08),inset 0 0 0 1px rgba(255,255,255,.65);
}
.ma-home-v13 .food .service-icon{background:linear-gradient(145deg,#ff7850,#f15d2e);color:#fff}
.ma-home-v13 .fresh .service-icon{background:linear-gradient(145deg,#32bf79,#079866);color:#fff}
.ma-home-v13 .taxi .service-icon{background:linear-gradient(145deg,#55a5f5,#367bd9);color:#fff}
.ma-home-v13 .service strong{position:relative;z-index:2;font-size:12px;line-height:1.15;letter-spacing:-.15px}
.ma-home-v13 .service small{position:relative;z-index:2;margin-top:6px;color:#667b74;font-size:8.7px;line-height:1.3;min-height:23px}
.ma-home-v13 .service-arrow{position:relative;z-index:2;margin-top:auto;display:inline-flex;align-items:center;gap:4px;padding:7px 10px;border-radius:999px;font-size:9px;font-weight:900;background:rgba(255,255,255,.82);box-shadow:0 3px 9px rgba(20,65,53,.05)}
.ma-home-v13 .service-arrow b{font-size:12px;line-height:1}
.ma-home-v13 .food .service-arrow{color:#d9572a}
.ma-home-v13 .fresh .service-arrow{color:#087d64}
.ma-home-v13 .taxi .service-arrow{color:#367bd9}
@media (max-width:560px){
    .ma-home-v13 .services{gap:8px}
    .ma-home-v13 .service{min-height:181px;padding:13px 8px 12px;border-radius:20px}
    .ma-home-v13 .service-icon{width:58px;height:58px;flex-basis:58px;border-radius:19px;font-size:28px;margin-bottom:10px}
    .ma-home-v13 .service strong{font-size:10.5px}
    .ma-home-v13 .service small{font-size:7.9px;margin-top:5px}
    .ma-home-v13 .service-arrow{font-size:8.3px;padding:6px 8px}
}
@media (min-width:700px){
    .ma-home-v13 .services{gap:14px}
    .ma-home-v13 .service{min-height:205px;padding:18px 14px 15px}
    .ma-home-v13 .service-icon{width:72px;height:72px;flex-basis:72px;font-size:35px}
    .ma-home-v13 .service strong{font-size:13px}
    .ma-home-v13 .service small{font-size:9px}
}



/* Commercial vehicle rental service card — additive; existing service cards unchanged. */
.ma-home-v13 .service.tourism{background:linear-gradient(145deg,#eef9f5,#fff);border-color:#cfe5dc}.ma-home-v13 .service.tourism .service-icon{background:linear-gradient(145deg,#d9f1e7,#b9e2d2)}.ma-home-v13 .service.tourism .service-arrow{color:#087d64}
.ma-home-v13 .service.commercial{background:linear-gradient(145deg,#fff8e9,#fff);border-color:#f0dfb8}
.ma-home-v13 .service.commercial .service-icon{background:#fff0c9}
.ma-home-v13 .service.commercial .service-arrow{color:#9a6a00}
.ma-home-v13 .service.experience{background:linear-gradient(145deg,#fffaf0,#fff);border-color:#f0dfb8}.ma-home-v13 .service.experience .service-icon{background:linear-gradient(145deg,#f8d77a,#e9ad32);color:#fff}.ma-home-v13 .service.experience .service-arrow{color:#a06b00}
.ma-home-ai-promo{width:100%;display:flex;align-items:center;gap:10px;margin:0 0 10px;padding:13px 15px;border:1px solid #bfe1d5;border-radius:18px;background:linear-gradient(135deg,#eef9f4,#fff);box-shadow:0 8px 20px rgba(8,82,64,.06);text-align:left;color:#14342b;cursor:pointer}.ma-home-ai-promo:hover{border-color:#83c5b2;transform:translateY(-1px)}.ma-home-ai-promo-icon{display:grid;place-items:center;width:34px;height:34px;border-radius:11px;background:#087d64;color:#fff;font-weight:900;flex:none}.ma-home-ai-copy{min-width:0;flex:1}.ma-home-ai-copy>span{display:block;font-size:7px;letter-spacing:1.1px;color:#087d64;font-weight:950}.ma-home-ai-copy h2{margin:2px 0;font-size:15px;letter-spacing:-.25px}.ma-home-ai-copy p{margin:0;color:#71817a;font-size:9px;line-height:1.4}.ma-home-ai-promo>b{flex:none;padding:8px 11px;border-radius:10px;background:#087d64;color:#fff;font-size:9px;font-weight:950}.ma-home-ai-promo + .location-row{padding-top:5px}@media(max-width:600px){.ma-home-ai-promo{margin-bottom:8px;padding:11px 12px}.ma-home-ai-copy h2{font-size:13px}.ma-home-ai-copy p{font-size:8px}.ma-home-ai-promo>b{padding:7px 9px;font-size:8px}}
</style>


<style>
/* Digital ILP Helper home entry — additive only. */
.ma-home-ilp-promo{width:100%;display:flex;align-items:center;gap:10px;margin:8px 0 12px;padding:12px 14px;border:1px solid #d9e6e1;border-radius:18px;background:linear-gradient(135deg,#f5fbf8,#fff);box-shadow:0 7px 18px rgba(8,82,64,.045);text-decoration:none;color:#14342b}.ma-home-ilp-icon{display:grid;place-items:center;width:34px;height:34px;border-radius:11px;background:#e7f5ef;color:#087d64;font-weight:950;flex:none}.ma-home-ilp-copy{min-width:0;flex:1}.ma-home-ilp-copy span{display:block;font-size:7px;letter-spacing:1px;color:#087d64;font-weight:950}.ma-home-ilp-copy strong{display:block;margin:2px 0;font-size:14px}.ma-home-ilp-copy small{display:block;color:#71817a;font-size:8.5px;line-height:1.35}.ma-home-ilp-promo>b{flex:none;padding:8px 10px;border-radius:10px;background:#087d64;color:#fff;font-size:8px;font-weight:950}.ma-home-ilp-promo:hover{border-color:#a9cec1;transform:translateY(-1px)}
.mdh-ilp-cta{width:100%;display:flex;align-items:center;gap:14px;margin:12px 0 0;padding:14px 16px;border:1px solid #d6e7e0;border-radius:18px;background:linear-gradient(135deg,#f5fbf8,#fff);box-shadow:0 8px 20px rgba(8,82,64,.05);text-decoration:none;color:#14342b}.mdh-ilp-icon{display:grid;place-items:center;width:40px;height:40px;border-radius:13px;background:#e7f5ef;color:#087d64;font-weight:950;flex:none}.mdh-ilp-cta span:nth-child(2){min-width:0;flex:1}.mdh-ilp-cta small{display:block;color:#087d64;font-size:8px;font-weight:950;letter-spacing:1px}.mdh-ilp-cta strong{display:block;margin:2px 0;font-size:15px}.mdh-ilp-cta em{display:block;color:#71817a;font-style:normal;font-size:10px}.mdh-ilp-cta>b{padding:9px 12px;border-radius:10px;background:#087d64;color:#fff;font-size:9px;white-space:nowrap}
@media(min-width:901px){.ma-home-ilp-promo{display:none}}
@media(max-width:900px){.mdh-ilp-cta{display:none}}
</style>

<!-- Desktop Home v2: illustration-inspired full-width dashboard. Mobile keeps the existing PWA home below. -->
<div class="ma-desktop-home" aria-label="ManipurApp desktop home">
    <header class="mdh-topbar">
        <a class="mdh-brand" href="<?= memberAppEsc($basePath) ?>/member">
            <span class="mdh-logo" aria-hidden="true">
                <svg viewBox="0 0 52 52" fill="none"><path d="M26 46C25.2 34.8 25.5 22.4 27.2 9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><path d="M25.9 29.8C19.1 28.2 13.2 23.6 10.1 17.2C16.8 16.5 23.2 18.9 26.7 24.1" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M27.1 24.7C28.5 17.1 34.1 10.7 41.3 8.4C41.1 16.1 36.9 22.7 30.4 26.1" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M26.2 38.2C18.8 36.8 12.5 32.1 9.3 25.5C17 25.2 23.8 28.3 27.1 34.1" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span><strong>ManipurApp</strong><small>People • Places • Possibilities</small></span>
        </a>
        <nav class="mdh-mainnav" aria-label="Desktop primary navigation">
            <a class="active" href="<?= memberAppEsc($basePath) ?>/member"><span>⌂</span>Home</a>
            <a href="<?= memberAppEsc($basePath) ?>/member/tourism"><span>⌖</span>Explore</a>
            <a href="<?= memberAppEsc($basePath) ?>/member/bookings"><span>▣</span>Bookings</a>
            <a href="<?= memberAppEsc($basePath) ?>/member/ilp"><span>▤</span>ILP Helper</a>
            <a href="<?= memberAppEsc($basePath) ?>/member#ask-ai" data-ai-open><span>✦</span>Ask AI</a>
            <a href="#saved" data-coming-soon><span>♡</span>Saved</a>
            <a href="<?= memberAppEsc($basePath) ?>/member/profile"><span>♙</span>Profile</a>
        </nav>
        <div class="mdh-right">
            <button class="mdh-location" type="button"><span>⌖</span> Imphal, Manipur <b>⌄</b></button>
            <a class="mdh-icon" href="<?= memberAppEsc($basePath) ?>/member/notifications" aria-label="Notifications">♧<b class="mdh-badge" id="desktopHomeNotificationBadge" style="display:none">0</b></a>
            <a class="mdh-user" href="<?= memberAppEsc($basePath) ?>/member/profile"><span><?= memberAppEsc($firstName !== '' ? strtoupper(substr($firstName,0,1)) : 'M') ?></span><strong>Hi, <?= $isLoggedIn && $firstName !== '' ? memberAppEsc($firstName) : 'there' ?> 👋<small>Member</small></strong><b>⌄</b></a>
        </div>
    </header>

    <section class="mdh-hero">
        <div class="mdh-hero-scenery" aria-hidden="true"><i class="mdh-sun"></i><i class="mdh-mountain m1"></i><i class="mdh-mountain m2"></i><i class="mdh-city"></i></div>
        <div class="mdh-hero-inner">
            <div class="mdh-hero-copy">
                <div class="mdh-welcome">Welcome back, <?= $isLoggedIn && $firstName !== '' ? memberAppEsc($firstName) : 'friend' ?> 👋</div>
                <h1>Your Local Favourites,<br>All in One Place.</h1>
                <p>Food, travel, rides, stays, rentals and more — discover the best of Manipur.</p>
            </div>
            <div class="mdh-hero-note">Explore<br>Experience<br>Support Local</div>
            <form class="mdh-searchbar" action="<?= memberAppEsc($basePath) ?>/member/search" method="get">
                <span>⌕</span><input id="desktop-service-search" name="q" type="search" placeholder="Search food, places, rides, stays, services..." autocomplete="off"><button type="submit">Search</button>
            </form>
            <div class="mdh-filters"><button>⌖ Imphal, Manipur</button><a href="<?= memberAppEsc($basePath) ?>/member/restaurants">Food</a><a href="<?= memberAppEsc($basePath) ?>/member/taxi">Taxi</a><a href="<?= memberAppEsc($basePath) ?>/member/tourism">Hotels</a><a href="<?= memberAppEsc($basePath) ?>/member/tourism/experiences">Things to do</a><a href="<?= memberAppEsc($basePath) ?>/member/commercial-rental">Car Rentals</a></div>
            <div class="mdh-landmark">⌖ Loktak Lake, Manipur</div>
        </div>
    </section>

    <section class="mdh-ai-cta" data-ai-open aria-haspopup="dialog" aria-controls="ma-ai-dialog" role="button" tabindex="0">
        <div class="mdh-ai-cta-icon">✦</div>
        <div class="mdh-ai-cta-copy"><small>MANIPURAPP AI</small><h2>Visiting Manipur for the first time?</h2><p>Need help planning your stay, places to visit, food and local travel.</p></div>
        <span class="mdh-ai-cta-button">Plan with AI →</span>
    </section>

    <a class="mdh-ilp-cta" href="<?= memberAppEsc($basePath) ?>/member/ilp">
        <span class="mdh-ilp-icon">▣</span>
        <span><small>TRAVELLING TO MANIPUR?</small><strong>Digital ILP Helper</strong><em>Understand the main ILP categories and open the official application portal.</em></span>
        <b>View guide →</b>
    </a>

    <main class="mdh-content">
        <section class="mdh-services">
            <?php
            $desktopServices = [
                ['🍛','Food & Restaurants','Order from local restaurants',$basePath.'/member/restaurants','food restaurants'],
                ['🥬','Fresh Food','Meat, groceries & local produce',$basePath.'/member/fresh-food','fresh food grocery'],
                ['🚕','Taxi','Book local taxi services',$basePath.'/member/taxi','taxi rides'],
                ['🏨','Stays & Homestays','Hotels, homestays & more',$basePath.'/member/tourism','stays hotels homestays'],
                ['📷','Tourism','Explore Manipur',$basePath.'/member/tourism','tourism destinations'],
                ['🚐','Commercial Rentals','Trucks, commercial vehicles',$basePath.'/member/commercial-rental','commercial rentals vehicles'],
                ['❤','Health & Clinics','Book appointments','#','health clinic'],
                ['▦','More Services','Discover more',$basePath.'/member/tourism','more services'],
            ];
            foreach($desktopServices as $i=>$svc):
            ?>
                <a class="mdh-service mdh-s<?= $i+1 ?>" href="<?= memberAppEsc($svc[3]) ?>" data-mdh-service="<?= memberAppEsc($svc[4]) ?>" <?= $svc[3]==='#' ? 'data-coming-soon' : '' ?>><span class="mdh-service-icon"><?= $svc[0] ?></span><strong><?= memberAppEsc($svc[1]) ?></strong><small><?= memberAppEsc($svc[2]) ?></small></a>
            <?php endforeach; ?>
        </section>

        <section class="mdh-two-col">
            <div class="mdh-panel">
                <div class="mdh-panel-head"><div><h2>🍴 Popular Restaurants</h2><p>Delicious food from your favourite local places</p></div><a href="<?= memberAppEsc($basePath) ?>/member/restaurants">View all →</a></div>
                <div class="mdh-card-grid">
                    <a class="mdh-place" href="<?= memberAppEsc($basePath) ?>/member/restaurants"><div class="mdh-photo food1">🍛</div><strong>Ema Kitchen</strong><b>★ 4.6 <span>(120)</span></b><small>Manipuri · Indian</small><em>Delivery</em><em>Pickup</em></a>
                    <a class="mdh-place" href="<?= memberAppEsc($basePath) ?>/member/restaurants"><div class="mdh-photo food2">☕</div><strong>Hillside Café</strong><b>★ 4.4 <span>(98)</span></b><small>Café · Continental</small><em>Delivery</em><em>Pickup</em></a>
                    <a class="mdh-place" href="<?= memberAppEsc($basePath) ?>/member/restaurants"><div class="mdh-photo food3">🍖</div><strong>Tribal Foods</strong><b>★ 4.5 <span>(86)</span></b><small>Northeast · Manipuri</small><em>Delivery</em><em>Pickup</em></a>
                    <a class="mdh-place" href="<?= memberAppEsc($basePath) ?>/member/restaurants"><div class="mdh-photo food4">🍚</div><strong>Yaiphaba Kitchen</strong><b>★ 4.3 <span>(64)</span></b><small>Manipuri · Thali</small><em>Delivery</em><em>Pickup</em></a>
                </div>
            </div>
            <div class="mdh-panel">
                <div class="mdh-panel-head"><div><h2>🏔️ Top Destinations</h2><p>Explore the natural beauty of Manipur</p></div><a href="<?= memberAppEsc($basePath) ?>/member/tourism">View all →</a></div>
                <div class="mdh-card-grid mdh-dest-grid">
                    <a class="mdh-place" href="<?= memberAppEsc($basePath) ?>/member/tourism"><div class="mdh-photo dest1">🌊</div><strong>Loktak Lake</strong><small>⌖ Imphal West</small></a>
                    <a class="mdh-place" href="<?= memberAppEsc($basePath) ?>/member/tourism"><div class="mdh-photo dest2">🏯</div><strong>Kangla Fort</strong><small>⌖ Imphal West</small></a>
                    <a class="mdh-place" href="<?= memberAppEsc($basePath) ?>/member/tourism"><div class="mdh-photo dest3">🌸</div><strong>Shirui Hills</strong><small>⌖ Ukhrul</small></a>
                    <a class="mdh-place" href="<?= memberAppEsc($basePath) ?>/member/tourism"><div class="mdh-photo dest4">🦌</div><strong>Keibul Lamjao</strong><small>⌖ Bishnupur</small></a>
                </div>
            </div>
        </section>

        <section class="mdh-promos">
            <a class="mdh-promo food-promo" href="<?= memberAppEsc($basePath) ?>/member/restaurants"><div><h2>Good Food.<br>Happy Moments.</h2><p>Order from local restaurants, delivered to your door.</p><b>Order Food →</b></div><span>🍲</span></a>
            <a class="mdh-promo travel-promo" href="<?= memberAppEsc($basePath) ?>/member/tourism"><div><h2>Explore Manipur<br>Like Never Before</h2><p>Discover destinations, experiences and local guides.</p><b>Start Exploring →</b></div><span>🏔️</span></a>
            <a class="mdh-promo rental-promo" href="<?= memberAppEsc($basePath) ?>/member/commercial-rental"><div><h2>Need a Vehicle?</h2><p>Rent commercial vehicles for your business needs.</p><b>View Rentals →</b></div><span>🚐</span></a>
        </section>
    </main>
</div>

<div class="memberapp-shell ma-home-v13">
    <div class="home-header">
        <a class="brand" href="<?= memberAppEsc($basePath) ?>/member" aria-label="ManipurApp home">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 52 52" fill="none">
                    <path d="M26 46C25.2 34.8 25.5 22.4 27.2 9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    <path d="M25.9 29.8C19.1 28.2 13.2 23.6 10.1 17.2C16.8 16.5 23.2 18.9 26.7 24.1" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M27.1 24.7C28.5 17.1 34.1 10.7 41.3 8.4C41.1 16.1 36.9 22.7 30.4 26.1" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M26.2 38.2C18.8 36.8 12.5 32.1 9.3 25.5C17 25.2 23.8 28.3 27.1 34.1" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M27.2 36.9C29.5 30.6 35.2 25.7 42.2 24.9C40.7 32 35.2 37.4 28.6 39.1" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="brand-copy"><strong>ManipurApp</strong><small>People • Places • Possibilities</small></span>
        </a>
        <div class="header-actions">
            <a class="fresh-cart-top" href="<?= memberAppEsc($basePath) ?>/member/fresh-food/cart" aria-label="Fresh Food cart">
                <span aria-hidden="true">🥬</span><span>Fresh</span>
                <b class="badge" id="homeFreshCartBadge" style="<?= $maFreshCartCount > 0 ? '' : 'display:none;' ?>"><?= $maFreshCartCount ?></b>
            </a>
            <a class="icon-btn" href="<?= memberAppEsc($basePath) ?>/member/restaurant/cart" aria-label="Food cart">
                <svg viewBox="0 0 24 24" fill="none"><path d="M4 5h2l1.7 10.1a2 2 0 0 0 2 1.7h6.8a2 2 0 0 0 2-1.5L20 8H7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="20" r="1.2" fill="currentColor"/><circle cx="17" cy="20" r="1.2" fill="currentColor"/></svg>
                <b class="badge" id="homeCartBadge" style="<?= $maHomeCartCount > 0 ? '' : 'display:none;' ?>"><?= $maHomeCartCount ?></b>
            </a>
            <a class="icon-btn" href="<?= memberAppEsc($basePath) ?>/member/notifications" aria-label="Notifications">
                <svg viewBox="0 0 24 24" fill="none"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <b class="badge" id="homeNotificationBadge" style="display:none">0</b>
            </a>
        </div>
    </div>

    <section class="welcome">
        <h1><?= $isLoggedIn && $firstName !== '' ? 'Welcome back, ' . memberAppEsc($firstName) . ' 👋' : 'Discover Manipur, your way.' ?></h1>
        <p><?= $isLoggedIn ? 'Your local favourites, rides and everyday essentials — all in one place.' : 'Food, fresh groceries and rides from local businesses you can count on.' ?></p>
    </section>

    <form class="search" action="<?= memberAppEsc($basePath) ?>/member/search" method="get">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m20 20-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        <input id="service-search" name="q" type="search" placeholder="Search food, groceries, rides..." autocomplete="off" aria-label="Search food, groceries, rides">
        <button type="button" class="search-clear" data-search-clear aria-label="Clear search">×</button>
    </form>

    <button type="button" class="ma-home-ai-promo" data-ai-open aria-haspopup="dialog" aria-controls="ma-ai-dialog">
        <span class="ma-home-ai-promo-icon">✦</span>
        <span class="ma-home-ai-copy"><span>✦ MANIPURAPP AI</span><h2>Visiting Manipur for the first time?</h2><p>Need help planning your stay, places to visit, food and local travel?</p></span>
        <b>Plan with AI →</b>
    </button>

    <a class="ma-home-ilp-promo" href="<?= memberAppEsc($basePath) ?>/member/ilp">
        <span class="ma-home-ilp-icon">▣</span>
        <span class="ma-home-ilp-copy"><span>TRAVELLING TO MANIPUR?</span><strong>Digital ILP Helper</strong><small>Understand the main ILP categories and open the official portal.</small></span>
        <b>View guide →</b>
    </a>

    <div class="location-row">
        <button class="location" type="button" data-location-picker>
            <svg viewBox="0 0 24 24" fill="none"><path d="M20 10c0 5.2-8 11-8 11S4 15.2 4 10a8 8 0 1 1 16 0Z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="2"/></svg>
            <strong>Imphal, Manipur</strong>
            <svg class="chev" viewBox="0 0 24 24" fill="none"><path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <span class="location-note">Showing services near you</span>
    </div>

    <section class="hero-carousel" id="homeHeroCarousel" aria-label="ManipurApp promotions">
        <button class="hero-arrow prev" type="button" data-hero-prev aria-label="Previous banner">‹</button>
        <button class="hero-arrow next" type="button" data-hero-next aria-label="Next banner">›</button>

        <article class="hero-slide active" data-hero-slide>
            <div class="hero-slide-copy">
                <span class="hero-slide-kicker">♡ Built for Manipur</span>
                <h2>Support Local.<br>Live Local.</h2>
                <p>Discover trusted local businesses, everyday essentials and rides — all in one place.</p>
                <a class="hero-slide-cta" href="#services">Explore local →</a>
            </div>
            <div class="hero-visual" aria-hidden="true"><span class="visual-circle"></span><span class="visual-hill"></span><span class="visual-house"></span></div>
        </article>

        <article class="hero-slide" data-hero-slide>
            <div class="hero-slide-copy">
                <span class="hero-slide-kicker">🍛 Taste Manipur</span>
                <h2>Good Food.<br>Happy Moments.</h2>
                <p>Order delicious local favourites from restaurants around you, delivered to your door.</p>
                <a class="hero-slide-cta" href="<?= memberAppEsc($basePath) ?>/member/restaurants">Order food →</a>
            </div>
            <div class="hero-visual" aria-hidden="true"><span class="hero-food-art"></span></div>
        </article>

        <article class="hero-slide" data-hero-slide>
            <div class="hero-slide-copy">
                <span class="hero-slide-kicker">🚕 Move with ease</span>
                <h2>Your Ride.<br>Your Way.</h2>
                <p>Book a local taxi when you need to move around Imphal and beyond.</p>
                <a class="hero-slide-cta" href="<?= memberAppEsc($basePath) ?>/member/taxi">Book a ride →</a>
            </div>
            <div class="hero-visual" aria-hidden="true"><span class="hero-taxi-art"></span></div>
        </article>

        <article class="hero-slide" data-hero-slide>
            <div class="hero-slide-copy">
                <span class="hero-slide-kicker">🏔️ Discover Manipur</span>
                <h2>Places Worth<br>Exploring.</h2>
                <p>Discover beautiful destinations, stays, local guides and unforgettable journeys across Manipur.</p>
                <a class="hero-slide-cta" href="<?= memberAppEsc($basePath) ?>/member/tourism">Explore Manipur →</a>
            </div>
            <div class="hero-visual" aria-hidden="true"><span class="hero-tourism-art">🏔️</span></div>
        </article>

        <article class="hero-slide" data-hero-slide>
            <div class="hero-slide-copy">
                <span class="hero-slide-kicker">✨ Experience Manipur</span>
                <h2>Culture.<br>Nature. Stories.</h2>
                <p>Find local experiences, festivals, food, guides and things to do — then build your own trip.</p>
                <a class="hero-slide-cta" href="<?= memberAppEsc($basePath) ?>/member/tourism/experiences">Find experiences →</a>
            </div>
            <div class="hero-visual" aria-hidden="true"><span class="hero-experience-art">✨</span></div>
        </article>

        <article class="hero-slide" data-hero-slide>
            <div class="hero-slide-copy">
                <span class="hero-slide-kicker">🥬 Fresh from local sellers</span>
                <h2>Fresh Food.<br>Everyday Easy.</h2>
                <p>Shop vegetables, meat, fruits and everyday groceries from local fresh-food stores.</p>
                <a class="hero-slide-cta" href="<?= memberAppEsc($basePath) ?>/member/fresh-food">Shop fresh →</a>
            </div>
            <div class="hero-visual" aria-hidden="true"><span class="hero-fresh-art"></span></div>
        </article>

        <div class="hero-dots" role="tablist" aria-label="Hero banners">
            <button class="hero-dot active" type="button" data-hero-dot="0" aria-label="Banner 1" aria-selected="true"></button>
            <button class="hero-dot" type="button" data-hero-dot="1" aria-label="Banner 2" aria-selected="false"></button>
            <button class="hero-dot" type="button" data-hero-dot="2" aria-label="Banner 3" aria-selected="false"></button>
            <button class="hero-dot" type="button" data-hero-dot="3" aria-label="Banner 4" aria-selected="false"></button>
            <button class="hero-dot" type="button" data-hero-dot="4" aria-label="Banner 5" aria-selected="false"></button>
            <button class="hero-dot" type="button" data-hero-dot="5" aria-label="Banner 6" aria-selected="false"></button>
        </div>
    </section>

    <section class="section" id="services" aria-label="Main services">
        <div class="section-head">
            <div><h2>What do you need today?</h2><p>Three everyday services. One local app.</p></div>
        </div>
        <div class="services" id="service-grid">
            <a class="service food" href="<?= memberAppEsc($basePath) ?>/member/restaurants" data-service="food restaurants meals dining takeaway">
                <span class="service-glow" aria-hidden="true"></span>
                <span class="service-icon" aria-hidden="true">🍛</span>
                <strong>Food &amp;<br>Restaurants</strong>
                <small>Local favourites<br>delivered to you.</small>
                <span class="service-arrow">Order now <b>→</b></span>
            </a>
            <a class="service fresh" href="<?= memberAppEsc($basePath) ?>/member/fresh-food" data-service="fresh food grocery vegetables meat fruits stores">
                <span class="service-glow" aria-hidden="true"></span>
                <span class="service-icon" aria-hidden="true">🥬</span>
                <strong>Fresh Food &amp;<br>Grocery</strong>
                <small>Farm fresh,<br>everyday essentials.</small>
                <span class="service-arrow">Shop fresh <b>→</b></span>
            </a>
            <a class="service taxi" href="<?= memberAppEsc($basePath) ?>/member/taxi" data-service="taxi transport auto cab ride">
                <span class="service-glow" aria-hidden="true"></span>
                <span class="service-icon" aria-hidden="true">🚕</span>
                <strong>Taxi &amp;<br>Transport</strong>
                <small>Reliable rides<br>around Manipur.</small>
                <span class="service-arrow">Book a ride <b>→</b></span>
            </a>
            <a class="service commercial" href="<?= memberAppEsc($basePath) ?>/member/commercial-rental" data-service="commercial vehicle rental truck pickup mini truck jcb excavator tractor tanker heavy vehicle">
                <span class="service-glow" aria-hidden="true"></span>
                <span class="service-icon" aria-hidden="true">🚚</span>
                <strong>Commercial<br>Vehicle Rental</strong>
                <small>Pickups, trucks,<br>JCBs &amp; more.</small>
                <span class="service-arrow">Find a vehicle <b>→</b></span>
            </a>
            <a class="service tourism" href="<?= memberAppEsc($basePath) ?>/member/tourism" data-service="tourism travel tourism destinations stays hotels packages guides experiences events festivals explore manipur">
                <span class="service-glow" aria-hidden="true"></span>
                <span class="service-icon" aria-hidden="true">🏔️</span>
                <strong>Explore<br>Manipur</strong>
                <small>Destinations, stays,<br>experiences &amp; festivals.</small>
                <span class="service-arrow">Start exploring <b>→</b></span>
            </a>
            <a class="service experience" href="<?= memberAppEsc($basePath) ?>/member/tourism/experiences" data-service="experience experiences culture heritage adventure nature food festival things to do activities tourism manipur">
                <span class="service-glow" aria-hidden="true"></span>
                <span class="service-icon" aria-hidden="true">✨</span>
                <strong>Experience<br>Manipur</strong>
                <small>Culture, nature,<br>food and things to do.</small>
                <span class="service-arrow">Find experiences <b>→</b></span>
            </a>

        </div>
    </section>

    <section class="section" aria-label="Promotions">
        <div class="section-head">
            <div><h2>Today's best offers</h2><p>Deals from local services and experiences.</p></div>
            <button class="see" type="button" data-coming-soon>See all →</button>
        </div>

        <div class="offer-carousel" id="homeOfferCarousel" data-promo-slot="home-main">
            <button class="offer-arrow prev" type="button" data-offer-prev aria-label="Previous offers">‹</button>
            <button class="offer-arrow next" type="button" data-offer-next aria-label="Next offers">›</button>

            <article class="offer-slide active" data-offer-slide>
                <div class="offer-pair">
                    <div class="offer">
                        <div class="offer-copy">
                            <span class="offer-tag">FOOD</span>
                            <h3>20% OFF<br>your first order</h3>
                            <p>Use code <strong>FOOD20</strong> on your first meal.</p>
                            <a href="<?= memberAppEsc($basePath) ?>/member/restaurants">Order →</a>
                        </div>
                        <div class="offer-visual" aria-hidden="true"><span>🍔</span></div>
                    </div>
                    <div class="offer">
                        <div class="offer-copy">
                            <span class="offer-tag">FRESH</span>
                            <h3>Free delivery<br>above ₹300</h3>
                            <p>Fresh essentials from local sellers.</p>
                            <a href="<?= memberAppEsc($basePath) ?>/member/fresh-food">Shop →</a>
                        </div>
                        <div class="offer-visual" aria-hidden="true"><span>🥬</span></div>
                    </div>
                </div>
            </article>

            <article class="offer-slide" data-offer-slide>
                <div class="offer-pair">
                    <div class="offer">
                        <div class="offer-copy">
                            <span class="offer-tag">TAXI</span>
                            <h3>First ride<br>50% OFF</h3>
                            <p>Save up to ₹100 on your first ride.</p>
                            <a href="<?= memberAppEsc($basePath) ?>/member/taxi">Ride →</a>
                        </div>
                        <div class="offer-visual" aria-hidden="true"><span>🚕</span></div>
                    </div>
                    <div class="offer">
                        <div class="offer-copy">
                            <span class="offer-tag">COMBO DEAL</span>
                            <h3>Food + drink<br>from ₹199</h3>
                            <p>Selected local meal combos at special prices.</p>
                            <a href="<?= memberAppEsc($basePath) ?>/member/restaurants">View →</a>
                        </div>
                        <div class="offer-visual" aria-hidden="true"><span>🍜🥤</span></div>
                    </div>
                </div>
            </article>

            <div class="offer-nav" role="tablist" aria-label="Offer groups">
                <button class="offer-dot active" type="button" data-offer-dot="0" aria-label="Offers 1 and 2" aria-selected="true"></button>
                <button class="offer-dot" type="button" data-offer-dot="1" aria-label="Offers 3 and 4" aria-selected="false"></button>
            </div>
        </div>
    </section>

    <section class="section" aria-label="Popular near you">
        <div class="section-head">
            <div><h2>Popular near you</h2><p>Local favourites with simulated ratings for now.</p></div>
            <button class="see" type="button" data-coming-soon>See all →</button>
        </div>
        <div class="popular-grid">
            <a class="popular-card" href="<?= memberAppEsc($basePath) ?>/member/restaurants">
                <div class="popular-image" aria-hidden="true">🍛</div>
                <div class="popular-body"><div class="popular-title">Ema Kitchen</div><div class="popular-meta">Restaurant • Local favourites</div><div class="popular-rating"><b>★</b> 4.7 <span>(320)</span></div></div>
            </a>
            <a class="popular-card" href="<?= memberAppEsc($basePath) ?>/member/fresh-food">
                <div class="popular-image" aria-hidden="true">🥦</div>
                <div class="popular-body"><div class="popular-title">Imphal Fresh Mart</div><div class="popular-meta">Fresh food • Grocery</div><div class="popular-rating"><b>★</b> 4.8 <span>(256)</span></div></div>
            </a>
            <a class="popular-card" href="<?= memberAppEsc($basePath) ?>/member/taxi">
                <div class="popular-image" aria-hidden="true">🚕</div>
                <div class="popular-body"><div class="popular-title">Local Taxi</div><div class="popular-meta">Taxi • City rides</div><div class="popular-rating"><b>★</b> 4.6 <span>(189)</span></div></div>
            </a>
        </div>
    </section>

    <?php if (!$isLoggedIn): ?>
        <section class="member-banner" aria-label="Join ManipurApp">
            <h2>Be part of ManipurApp</h2>
            <p>Join once and keep your rides, food orders and fresh grocery activity together.</p>
            <div class="member-points"><span class="member-point">✓ One account</span><span class="member-point">✓ Track orders</span><span class="member-point">✓ Manage bookings</span></div>
            <a class="member-cta" href="<?= memberAppEsc($basePath) ?>/member/register">Create free account →</a>
        </section>
    <?php endif; ?>

    <footer class="home-foot"><strong>Made for Manipur • Built around local businesses</strong><small>People • Places • Possibilities</small></footer>

    <?php include __DIR__ . '/../Shared/restaurant-cart-widget.php'; ?>

    <nav class="ma-bottom-nav" aria-label="Primary navigation">
        <a class="ma-nav-item active" href="<?= memberAppEsc($basePath) ?>/member"><svg viewBox="0 0 24 24" fill="none"><path d="m4 10 8-6 8 6v9H4v-9Z" fill="currentColor"/><path d="M9 19v-5h6v5" stroke="white" stroke-width="1.7" stroke-linejoin="round"/></svg><span>Home</span></a>
        <a class="ma-nav-item" href="<?= memberAppEsc($basePath) ?>/member/tourism"><svg viewBox="0 0 24 24" fill="none"><path d="m4 6 6-2 4 2 6-2v14l-6 2-4-2-6 2V6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M10 4v14m4-12v14" stroke="currentColor" stroke-width="1.5"/></svg><span>Explore</span></a>
        <a class="ma-nav-item" href="<?= memberAppEsc($basePath) ?>/member/bookings"><svg viewBox="0 0 24 24" fill="none"><rect x="5" y="4" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 8h8M8 12h8M8 16h5M9 2v4m6-4v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg><span>Bookings</span></a>
        <a class="ma-nav-item ma-nav-ilp" href="<?= memberAppEsc($basePath) ?>/member/ilp"><span class="ma-nav-symbol">▤</span><span>ILP</span></a>
        <a class="ma-nav-item ma-nav-ai" href="<?= memberAppEsc($basePath) ?>/member#ask-ai" data-ai-open><span class="ma-nav-symbol">✦</span><span>Ask AI</span></a>
        <a class="ma-nav-item" href="#saved" data-coming-soon><svg viewBox="0 0 24 24" fill="none"><path d="M20 8.5c0 5.5-8 10.5-8 10.5S4 14 4 8.5A4.5 4.5 0 0 1 12 6a4.5 4.5 0 0 1 8 2.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg><span>Saved</span></a>
        <?php if ($isLoggedIn): ?>
            <a class="ma-nav-item" href="<?= memberAppEsc($basePath) ?>/member/profile"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/><path d="M5 20c0-4 3-6 7-6s7 6 7 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg><span>Profile</span></a>
        <?php else: ?>
            <a class="ma-nav-item" href="<?= memberAppEsc($basePath) ?>/member/login"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/><path d="M5 20c0-4 3-6 7-6s7 6 7 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg><span>Profile</span></a>
        <?php endif; ?>
    </nav>

    <div class="ma-toast" id="memberapp-toast" role="status" aria-live="polite"></div>
</div>


<style>
@media(max-width:899px){
  .ma-bottom-nav{grid-template-columns:repeat(7,minmax(0,1fr)) !important;width:min(620px,calc(100% - 8px));}
  .ma-bottom-nav .ma-nav-item{padding-left:1px;padding-right:1px;font-size:8px;}
  .ma-bottom-nav .ma-nav-item span:last-child{font-size:8px;}
  .ma-nav-symbol{display:block;font-size:17px !important;line-height:18px;margin-top:0 !important;}
}
</style>

<?php include __DIR__ . '/../AI/freeform.php'; ?>

<script>
(()=>{
    const url=<?=json_encode($basePath.'/member/notifications/status')?>;
    const badge=document.getElementById('homeNotificationBadge');
    if(!badge)return;
    const run=async()=>{try{const r=await fetch(url,{headers:{Accept:'application/json'},cache:'no-store'});const j=await r.json();if(j.success){const n=Number(j.unread_count||0);badge.textContent=n>99?'99+':String(n);badge.style.display=n>0?'grid':'none';}}catch(e){}};
    run();setInterval(run,12000);
})();

(()=>{
    const input=document.getElementById('service-search');
    const clear=document.querySelector('[data-search-clear]');
    if(!input)return;
    const apply=()=>{if(clear)clear.style.display=input.value.trim()?'block':'none';};
    input.addEventListener('input',apply);
    clear?.addEventListener('click',()=>{input.value='';apply();input.focus();});
    apply();
})();

(()=>{
    const setupCarousel=(rootSelector, slideSelector, dotSelector, prevSelector, nextSelector, intervalMs)=>{
        const root=document.querySelector(rootSelector);
        if(!root)return;
        const slides=[...root.querySelectorAll(slideSelector)];
        const dots=[...root.querySelectorAll(dotSelector)];
        const prev=root.querySelector(prevSelector);
        const next=root.querySelector(nextSelector);
        if(slides.length<2)return;
        let index=slides.findIndex(el=>el.classList.contains('active'));
        if(index<0)index=0;
        let timer=null;
        const render=(nextIndex)=>{
            index=(nextIndex+slides.length)%slides.length;
            slides.forEach((slide,i)=>slide.classList.toggle('active',i===index));
            dots.forEach((dot,i)=>{
                dot.classList.toggle('active',i===index);
                dot.setAttribute('aria-selected',i===index?'true':'false');
            });
        };
        const stop=()=>{if(timer){clearInterval(timer);timer=null;}};
        const start=()=>{stop();timer=setInterval(()=>render(index+1),intervalMs);};
        prev?.addEventListener('click',()=>{render(index-1);start();});
        next?.addEventListener('click',()=>{render(index+1);start();});
        dots.forEach((dot,i)=>dot.addEventListener('click',()=>{render(i);start();}));
        root.addEventListener('mouseenter',stop);
        root.addEventListener('mouseleave',start);
        root.addEventListener('focusin',stop);
        root.addEventListener('focusout',()=>{if(!root.contains(document.activeElement))start();});
        document.addEventListener('visibilitychange',()=>{document.hidden?stop():start();});
        render(index);
        start();
    };
    setupCarousel('#homeHeroCarousel','[data-hero-slide]','[data-hero-dot]','[data-hero-prev]','[data-hero-next]',5500);
    setupCarousel('#homeOfferCarousel','[data-offer-slide]','[data-offer-dot]','[data-offer-prev]','[data-offer-next]',4800);
})();
</script>
<?php include __DIR__ . '/../Shared/member-desktop-footer.php'; ?>
