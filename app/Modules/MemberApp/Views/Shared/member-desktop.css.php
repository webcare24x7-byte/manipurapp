<style>
/* MemberApp responsive desktop canvas.
   Desktop/tablet expands the app canvas; phone layout remains unchanged. */
@media (min-width: 900px) {
  html, body { min-width: 100%; background: #f7faf8; }

  .ma-home-v13,
  .ma-tour,
  .mr2-page,
  .rs-page,
  .ri-page,
  .ma-profile,
  .ma-taxi,
  .ma-v,
  .ma-book,
  .ma-bshow,
  .ma-cr,
  .ma-crb,
  .ma-crc,
  .co-page,
  .fc-page,
  .fco-page,
  .ff2-page,
  .fi-page,
  .fo-page,
  .fs-page,
  .ma-auth-page,
  .ma-register-page {
    width: 100%;
    max-width: 1440px;
    margin-left: auto;
    margin-right: auto;
  }

  .ma-home-v13 {
    min-height: 100vh;
    padding-left: 32px;
    padding-right: 32px;
    padding-bottom: 125px;
  }
  .ma-home-v13 .home-header { padding-top: 22px; }
  .ma-home-v13 .welcome h1 { font-size: 38px; }
  .ma-home-v13 .welcome p { font-size: 14px; }
  .ma-home-v13 .hero-carousel { min-height: 300px; }
  .ma-home-v13 .hero-slide { padding: 38px; }
  .ma-home-v13 .hero-slide h2 { font-size: 48px; }
  .ma-home-v13 .hero-slide-copy { max-width: 56%; }
  .ma-home-v13 .hero-visual { width: 45%; }
  .ma-home-v13 .hero-food-art { width: 250px; height: 175px; }
  .ma-home-v13 .hero-food-art:before { font-size: 90px; }
  .ma-home-v13 .hero-taxi-art:before { font-size: 125px; }
  .ma-home-v13 .hero-fresh-art:before { font-size: 70px; }
  .ma-home-v13 .services { grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 14px; }
  .ma-home-v13 .service { min-height: 175px; padding: 18px 16px 15px; }
  .ma-home-v13 .service strong { font-size: 14px; }
  .ma-home-v13 .service small { font-size: 10px; }
  .ma-home-v13 .popular-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
  .ma-home-v13 .popular-image { height: 125px; font-size: 52px; }
  .ma-home-v13 .popular-body { padding: 13px; }
  .ma-home-v13 .popular-title { font-size: 12px; }

  .ma-tour {
    min-height: 100vh;
    padding-left: 32px;
    padding-right: 32px;
  }
  .ma-tour-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; }
  .ma-tour-categories { grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 14px; }
  .ma-tour-cat { padding: 18px 10px; font-size: 10px; }
  .ma-tour-media { height: 205px; }
  .ma-tour-card-body { padding: 16px; }
  .ma-tour-card-body h3 { font-size: 16px; }
  .ma-tour-card-body p { font-size: 10px; }

  .mr2-page { max-width: 1440px; padding-left: 32px; padding-right: 32px; padding-bottom: 120px; }
  .mr2-sellers { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
  .mr2-popular { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
  .mr2-near { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
  .mr2-promos { grid-auto-columns: minmax(360px, 1fr); }

  .rs-page, .ri-page { max-width: 1240px; padding-left: 32px; padding-right: 32px; }
  .rs-cover { height: 300px; border-radius: 0 0 28px 28px; }
  .rs-head { padding-left: 0; padding-right: 0; }
  .rs-section { padding-left: 0; padding-right: 0; }
  .rs-stats { padding-left: 0; padding-right: 0; gap: 12px; }
  .rs-mode { padding-left: 0; padding-right: 0; }
  .rs-item { grid-template-columns: 100px 1fr auto; gap: 16px; padding: 14px 0; }
  .rs-thumb { width: 100px; height: 100px; }
  .ri-hero { height: 360px; border-radius: 0 0 28px 28px; }
  .ri-body { padding: 22px 0; }

  .ma-profile { max-width: 1200px; padding-left: 32px; padding-right: 32px; }
  .ma-taxi, .ma-v { max-width: 1200px; padding-left: 32px; padding-right: 32px; }
  .ma-auth-page, .ma-register-page { max-width: 1200px; }

  .ma-cr, .ma-crb, .ma-crc, .ff2-page, .fs-page { max-width: 1400px; padding-left: 32px; padding-right: 32px; }
  .fc-page, .fco-page, .fo-page, .fi-page, .co-page { max-width: 1200px; padding-left: 32px; padding-right: 32px; }

  /* Keep floating mobile navigation compact on desktop. */
  .ma-bottom-nav, .mr2-nav, .ma-nav, .mp-nav, .rs-bottom, .ri-submit, .fo-nav, .fi-nav, .ff2-nav, .fs-nav {
    width: min(620px, calc(100% - 40px));
  }
}
</style>

<style>
/* Desktop Home v2 — built to match the approved full-width illustration. */
.ma-desktop-home{display:none}
@media (min-width:900px){
  .ma-home-v13{display:none!important}
  .ma-desktop-home{display:block;background:#f7faf8;color:#10241f;min-height:100vh}
  .ma-desktop-home *{box-sizing:border-box}
  .ma-desktop-home a{text-decoration:none;color:inherit}
  .mdh-topbar{height:76px;background:#fff;border-bottom:1px solid #e6eeea;display:flex;align-items:center;gap:24px;padding:0 34px;position:relative;z-index:20}
  .mdh-brand{display:flex;align-items:center;gap:11px;min-width:255px}.mdh-logo{width:42px;height:42px;border-radius:14px;background:linear-gradient(145deg,#0c9677,#075743);display:grid;place-items:center;color:#fff}.mdh-logo svg{width:27px;height:27px}.mdh-brand strong{display:block;font-size:20px;letter-spacing:-.6px}.mdh-brand small{display:block;margin-top:3px;color:#7a8b85;font-size:9px;font-weight:700}
  .mdh-mainnav{display:flex;align-self:stretch;align-items:center;justify-content:center;gap:10px;flex:1}.mdh-mainnav a{height:100%;min-width:92px;padding:0 16px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;color:#61736d;font-size:11px;font-weight:700;position:relative}.mdh-mainnav a span{font-size:22px;line-height:1}.mdh-mainnav a.active{color:#087d64;background:#f0f9f5}.mdh-mainnav a.active:after{content:"";position:absolute;left:20px;right:20px;bottom:0;height:3px;border-radius:3px;background:#087d64}
  .mdh-right{display:flex;align-items:center;gap:10px;min-width:390px;justify-content:flex-end}.mdh-location{border:1px solid #d8e9e1;background:#eef9f5;color:#087d64;border-radius:999px;padding:10px 15px;font-size:11px;font-weight:800}.mdh-location b{margin-left:6px}.mdh-icon{position:relative;width:42px;height:42px;border:1px solid #dfe9e5;border-radius:50%;display:grid;place-items:center;font-size:21px;background:#fff}.mdh-badge{position:absolute;right:-1px;top:-2px;min-width:15px;height:15px;border-radius:50%;background:#ef514c;color:#fff;border:2px solid #fff;font-size:8px;text-align:center}.mdh-user{display:flex;align-items:center;gap:9px;padding-left:3px}.mdh-user>span{width:39px;height:39px;border-radius:50%;display:grid;place-items:center;background:#eee7fb;color:#735aa6;font-weight:900}.mdh-user strong{font-size:11px;line-height:1.2}.mdh-user small{display:block;color:#7c8d87;font-size:9px;font-weight:600;margin-top:2px}.mdh-user>b{color:#71827d}
  .mdh-hero{position:relative;min-height:375px;overflow:hidden;background:linear-gradient(180deg,#eab88a 0%,#f2c991 28%,#6b8990 63%,#193e4a 100%);color:#fff}.mdh-hero:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(9,38,47,.82) 0%,rgba(9,38,47,.58) 38%,rgba(9,38,47,.05) 75%),linear-gradient(180deg,transparent 48%,rgba(8,42,50,.38) 100%)}.mdh-hero-scenery{position:absolute;inset:0;overflow:hidden}.mdh-sun{position:absolute;width:120px;height:120px;border-radius:50%;right:17%;top:42px;background:rgba(255,229,176,.72);filter:blur(1px);box-shadow:0 0 70px rgba(255,215,143,.45)}.mdh-mountain{position:absolute;bottom:65px;height:205px;width:55%;background:linear-gradient(155deg,#314f5b,#173b47);clip-path:polygon(0 100%,12% 58%,20% 72%,33% 25%,43% 54%,55% 10%,68% 52%,82% 30%,100% 76%,100% 100%)}.mdh-mountain.m1{left:-4%;opacity:.9}.mdh-mountain.m2{right:-10%;bottom:45px;transform:scaleX(-1);opacity:.68;background:linear-gradient(155deg,#4f6970,#1d4650)}.mdh-city{position:absolute;left:0;right:0;bottom:40px;height:90px;background:linear-gradient(165deg,transparent 0 20%,rgba(24,53,58,.8) 21% 100%);clip-path:polygon(0 65%,7% 45%,12% 56%,18% 38%,24% 57%,31% 31%,38% 55%,45% 42%,52% 58%,60% 34%,69% 55%,76% 41%,83% 59%,90% 38%,100% 54%,100% 100%,0 100%);opacity:.8}
  .mdh-hero-inner{position:relative;z-index:3;max-width:1440px;margin:0 auto;height:375px;padding:35px 54px}.mdh-welcome{font-size:17px;font-weight:800;margin-bottom:7px}.mdh-hero h1{margin:0;font-size:50px;line-height:.99;letter-spacing:-2.3px;max-width:700px}.mdh-hero-copy p{font-size:15px;margin:13px 0 0;color:rgba(255,255,255,.9);max-width:700px}.mdh-hero-note{position:absolute;right:75px;top:105px;font-family:cursive;font-size:26px;line-height:1.12;transform:rotate(-5deg);color:rgba(255,255,255,.92)}.mdh-searchbar{position:absolute;left:50%;transform:translateX(-50%);bottom:70px;width:min(900px,calc(100% - 180px));height:54px;border-radius:18px;background:#fff;display:flex;align-items:center;padding:5px 7px 5px 18px;box-shadow:0 12px 30px rgba(5,35,38,.24);color:#7c8c88}.mdh-searchbar span{font-size:25px;margin-right:9px}.mdh-searchbar input{border:0;outline:0;flex:1;font-size:14px;color:#203b34;background:transparent}.mdh-searchbar button{height:44px;padding:0 26px;border:0;border-radius:14px;background:#087d64;color:#fff;font-weight:900}.mdh-filters{position:absolute;left:50%;transform:translateX(-50%);bottom:22px;display:flex;align-items:center;gap:8px;white-space:nowrap}.mdh-filters>*{border:0;border-radius:999px;padding:8px 15px;background:rgba(255,255,255,.18);color:#fff;font-size:10px;font-weight:800;backdrop-filter:blur(8px)}.mdh-filters button{background:#fff;color:#087d64}.mdh-landmark{position:absolute;right:55px;bottom:25px;font-size:10px;font-weight:800;color:rgba(255,255,255,.92)}
  .mdh-ai-cta{max-width:1180px;margin:18px auto 0;padding:15px 18px;display:flex;align-items:center;gap:13px;border:1px solid #bfe1d5;border-radius:18px;background:linear-gradient(135deg,#eef9f4,#fff);box-shadow:0 10px 24px rgba(8,82,64,.07);cursor:pointer;color:#14342b;position:relative;z-index:6}.mdh-ai-cta:hover{border-color:#83c5b2;transform:translateY(-1px)}.mdh-ai-cta-icon{width:42px;height:42px;border-radius:13px;display:grid;place-items:center;background:#087d64;color:#fff;font-size:19px;box-shadow:0 6px 16px rgba(8,125,100,.18);flex:none}.mdh-ai-cta-copy{min-width:0;flex:1}.mdh-ai-cta-copy small{display:block;color:#087d64;font-size:7px;font-weight:950;letter-spacing:1.2px}.mdh-ai-cta-copy h2{margin:2px 0;font-size:17px;letter-spacing:-.3px}.mdh-ai-cta-copy p{margin:0;color:#71817a;font-size:10px}.mdh-ai-cta-button{flex:none;padding:10px 14px;border-radius:11px;background:#087d64;color:#fff;font-size:10px;font-weight:950}.mdh-content{max-width:1480px;margin:-1px auto 0;padding:0 24px 70px}.mdh-services{margin-top:0;background:#fff;border:1px solid #e3ece8;border-radius:24px;display:grid;grid-template-columns:repeat(8,minmax(0,1fr));padding:20px 12px;box-shadow:0 12px 28px rgba(31,70,58,.07);position:relative;z-index:5}.mdh-service{min-height:122px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;border-right:1px solid #edf2ef;gap:7px;padding:8px 9px;transition:.18s}.mdh-service:last-child{border-right:0}.mdh-service:hover{transform:translateY(-3px)}.mdh-service-icon{width:60px;height:60px;border-radius:18px;display:grid;place-items:center;font-size:28px;box-shadow:0 8px 18px rgba(35,67,57,.09)}.mdh-s1 .mdh-service-icon{background:#ff7047}.mdh-s2 .mdh-service-icon{background:#19b86d}.mdh-s3 .mdh-service-icon{background:#f8c53d}.mdh-s4 .mdh-service-icon{background:#4388df}.mdh-s5 .mdh-service-icon{background:#9b63df}.mdh-s6 .mdh-service-icon{background:#56d2a4}.mdh-s7 .mdh-service-icon{background:#ef646d}.mdh-s8 .mdh-service-icon{background:#dcecff;color:#4388df}.mdh-service strong{font-size:12px;line-height:1.1}.mdh-service small{font-size:8.5px;color:#7c8d87;line-height:1.2}
  .mdh-two-col{display:grid;grid-template-columns:1.25fr 1fr;gap:24px;margin-top:25px}.mdh-panel{min-width:0}.mdh-panel-head{display:flex;justify-content:space-between;align-items:end;margin-bottom:13px}.mdh-panel-head h2{margin:0;font-size:19px;letter-spacing:-.5px}.mdh-panel-head p{margin:4px 0 0;color:#7c8d87;font-size:10px}.mdh-panel-head>a{font-size:10px;color:#087d64;font-weight:900}.mdh-card-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px}.mdh-place{background:#fff;border:1px solid #e1ebe6;border-radius:14px;overflow:hidden;padding-bottom:10px;box-shadow:0 6px 18px rgba(30,67,56,.045);min-width:0}.mdh-photo{height:115px;display:grid;place-items:center;font-size:45px;background:#f1e3d5;position:relative;overflow:hidden}.mdh-photo:after{content:"";position:absolute;inset:45% 0 0;background:linear-gradient(transparent,rgba(0,0,0,.12))}.food1{background:linear-gradient(135deg,#e5b989,#7e5039)}.food2{background:linear-gradient(135deg,#b6c9c0,#4c6d68)}.food3{background:linear-gradient(135deg,#cf9b68,#663b2e)}.food4{background:linear-gradient(135deg,#ead1a7,#9c6944)}.dest1{background:linear-gradient(135deg,#8ec8e1,#4b7183)}.dest2{background:linear-gradient(135deg,#c7a26d,#725339)}.dest3{background:linear-gradient(135deg,#8ec5dc,#4d8a6b)}.dest4{background:linear-gradient(135deg,#9dbf6d,#55763b)}.mdh-place>strong,.mdh-place>b,.mdh-place>small,.mdh-place>em{display:block;margin-left:10px;margin-right:10px}.mdh-place>strong{margin-top:9px;font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.mdh-place>b{margin-top:5px;color:#a66f08;font-size:9px}.mdh-place>b span{color:#7e8d87;font-weight:600}.mdh-place>small{margin-top:5px;color:#7b8c86;font-size:8.5px}.mdh-place>em{display:inline-block;margin-top:7px;margin-right:2px;padding:4px 6px;border-radius:999px;background:#f1f7f4;color:#087d64;font-size:7.5px;font-style:normal;font-weight:800}.mdh-dest-grid .mdh-place{padding-bottom:11px}.mdh-dest-grid .mdh-photo{height:125px}
  .mdh-promos{display:grid;grid-template-columns:1.05fr 1fr 1fr;gap:15px;margin-top:25px}.mdh-promo{min-height:138px;border-radius:17px;padding:20px 20px;display:flex;justify-content:space-between;overflow:hidden;position:relative}.mdh-promo h2{margin:0;font-size:22px;line-height:.98;letter-spacing:-.7px}.mdh-promo p{margin:7px 0 12px;font-size:9px;max-width:260px}.mdh-promo b{display:inline-flex;padding:8px 11px;border-radius:10px;background:#087d64;color:#fff;font-size:9px}.mdh-promo>span{font-size:75px;align-self:center;filter:drop-shadow(0 8px 8px rgba(0,0,0,.12))}.food-promo{background:linear-gradient(110deg,#ffe1cf,#ffd1b3);color:#3b302a}.travel-promo{background:linear-gradient(110deg,#ccebf2,#b6d7e8);color:#124b5b}.rental-promo{background:linear-gradient(110deg,#dcf5e7,#c8ecd9);color:#174c3d}
}
@media (min-width:900px) and (max-width:1200px){.mdh-topbar{padding:0 18px}.mdh-brand{min-width:210px}.mdh-right{min-width:300px}.mdh-mainnav a{min-width:70px;padding:0 8px}.mdh-services{grid-template-columns:repeat(4,1fr)}.mdh-service:nth-child(4){border-right:0}.mdh-two-col{grid-template-columns:1fr}.mdh-hero h1{font-size:43px}.mdh-hero-note{display:none}.mdh-searchbar{width:calc(100% - 100px)}}
</style>

<style>
/* Shared desktop inner-page header. Hidden by default so the mobile/PWA layout
   never renders an unstyled duplicate header while crossing breakpoints. */
.md-global-header{display:none}
@media (min-width:900px){
  .md-global-header{height:76px;background:#fff;border-bottom:1px solid #e5eee9;display:flex!important;align-items:center;gap:22px;padding:0 34px;position:relative;z-index:40;box-shadow:0 2px 12px rgba(18,52,42,.035)}
  .mdgh-brand{display:flex;align-items:center;gap:10px;min-width:245px;text-decoration:none;color:#10241f}.mdgh-logo{width:42px;height:42px;border-radius:14px;background:linear-gradient(145deg,#0c9677,#075743);display:grid;place-items:center;color:#fff;font-size:22px}.mdgh-brand strong{display:block;font-size:20px;letter-spacing:-.6px}.mdgh-brand small{display:block;margin-top:2px;color:#7a8b85;font-size:9px;font-weight:700}
  .mdgh-nav{height:100%;display:flex;align-items:stretch;justify-content:center;gap:6px;flex:1}.mdgh-nav a{min-width:105px;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;text-decoration:none;color:#62746d;font-size:11px;font-weight:800;position:relative}.mdgh-nav a span{font-size:21px;line-height:1}.mdgh-nav a.active{color:#087d64;background:#f0f9f5}.mdgh-nav a.active:after{content:"";position:absolute;left:22px;right:22px;bottom:0;height:3px;border-radius:3px;background:#087d64}
  .mdgh-right{min-width:330px;display:flex;align-items:center;justify-content:flex-end;gap:10px}.mdgh-location{border:1px solid #d8e9e1;background:#eef9f5;color:#087d64;border-radius:999px;padding:10px 14px;font-size:10px;font-weight:900}.mdgh-bell{width:42px;height:42px;border:1px solid #dfe9e5;border-radius:50%;display:grid;place-items:center;text-decoration:none;color:#536761;background:#fff;font-size:20px}.mdgh-user{padding:11px 14px;border-radius:999px;background:#f1edfa;color:#735aa6;text-decoration:none;font-size:10px;font-weight:900}
  .ma-nav,.ma-cr-nav,.ma-crb-nav,.ma-crc-nav,.ma-v + .ma-nav,.mb-nav,.mp-nav,.mn-nav,.mr2-nav,.rs-bottom,.fo-nav,.fi-nav,.ff2-nav,.fs-nav,.fc-nav,.fco-nav,.co-nav,.bs-nav,.offer-nav,.rc-nav,.ro-nav,.ma-bottom-nav{display:none!important}

  /* Taxi desktop experience */
  .ma-taxi{max-width:1440px!important;padding:28px 42px 60px!important}.ma-taxi-head{padding:8px 0 24px!important}.ma-taxi-head h1{font-size:34px!important}.ma-taxi-head p{font-size:13px!important}.ma-taxi .ma-form{max-width:none!important;background:#fff;border:1px solid #e2ece7;border-radius:26px;padding:28px;box-shadow:0 12px 32px rgba(20,57,47,.06)}.ma-taxi .ma-hero{padding:28px 30px!important;min-height:185px!important;border-radius:24px!important}.ma-taxi .ma-pills{display:grid!important;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px!important}.ma-taxi .service-card{min-height:125px!important}.ma-taxi .ma-grid2{grid-template-columns:1fr 1fr!important}.ma-taxi .ma-location-actions{display:flex;gap:10px}.ma-taxi .ma-submit{max-width:360px!important}
  .ma-v{max-width:1440px!important;padding:28px 42px 60px!important}.ma-vhead{padding:8px 0 24px!important}.ma-vhead h1{font-size:34px!important}.ma-v > .ma-card,.ma-v .ma-card{max-width:none!important}.ma-v .ma-card{display:flex;gap:22px;align-items:center;padding:20px!important}.ma-v .ma-card > *{flex:1}.ma-v .ma-select{max-width:250px}

  /* Commercial rental desktop experience */
  .ma-cr{max-width:1440px!important;padding:28px 42px 60px!important}.ma-cr-head{padding:8px 0 24px!important}.ma-cr-head h1{font-size:34px!important}.ma-cr-hero{min-height:210px!important;padding:30px 34px!important;border-radius:28px!important}.ma-cr-hero h2{font-size:42px!important;max-width:720px}.ma-cr-hero p{font-size:14px!important;max-width:720px}.ma-cr-search{padding:22px!important;display:grid;grid-template-columns:1.5fr .75fr .75fr auto;gap:10px;align-items:end}.ma-cr-search .ma-cr-label{grid-column:1/-1}.ma-cr-search .ma-cr-loc{grid-column:1}.ma-cr-search .ma-cr-row{display:contents}.ma-cr-search .ma-cr-row .ma-cr-select{grid-column:auto}.ma-cr-search .ma-cr-check{grid-column:auto;margin:0;align-self:center}.ma-cr-search .ma-cr-submit{grid-column:auto;margin:0;height:46px;white-space:nowrap}.ma-cr-section{margin-top:32px}.ma-cr-section h2{font-size:25px}.ma-cr-section > p{font-size:12px}.ma-cr-section{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}.ma-cr-section h2,.ma-cr-section>p,.ma-cr-empty{grid-column:1/-1}.ma-cr-card{margin:0;padding:14px;display:flex;flex-direction:column}.ma-cr-card-top{grid-template-columns:1fr!important;gap:12px}.ma-cr-photo{height:180px!important}.ma-cr-card h3{font-size:18px}.ma-cr-actions{margin-top:auto;padding-top:14px}.ma-cr-btn{padding:12px 8px}

  /* All other inner pages get breathing room below the shared header. */
  .ma-tour,.mr2-page,.rs-page,.ri-page,.ma-profile,.ma-book,.ma-bshow,.fc-page,.fco-page,.fo-page,.fi-page,.fs-page,.ff2-page,.co-page,.ma-auth-page,.ma-register-page{padding-top:24px!important}
}
</style>
