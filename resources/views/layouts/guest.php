<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#087d64">
<title><?= htmlspecialchars($title ?? 'ManipurApp') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
.ma-guest{--green:#087d64;--green-dark:#075743;--green-soft:#eef9f5;--ink:#10241f;--muted:#71827c;--line:#dfeae5;min-height:100dvh;background:#f6faf8;color:var(--ink);font-family:"DM Sans",system-ui,sans-serif}
.ma-guest *{box-sizing:border-box}.ma-guest a{text-decoration:none;color:inherit}
.ma-guest-shell{min-height:100dvh;display:grid;grid-template-columns:minmax(420px,1fr) minmax(440px,620px)}
.ma-guest-brand{position:relative;overflow:hidden;color:#fff;background:linear-gradient(180deg,#e9b985 0%,#f0ca91 29%,#6f8e94 59%,#173f4a 100%);min-height:100dvh}
.ma-guest-brand:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(8,39,48,.88),rgba(8,39,48,.54) 48%,rgba(8,39,48,.18)),linear-gradient(180deg,transparent 43%,rgba(8,38,46,.55))}
.gscene{position:absolute;inset:0}.gsun{position:absolute;width:120px;height:120px;border-radius:50%;right:17%;top:80px;background:rgba(255,229,176,.72);box-shadow:0 0 70px rgba(255,216,143,.5)}.gmount{position:absolute;bottom:0;width:75%;height:48%;background:linear-gradient(155deg,#35535e,#173b47);clip-path:polygon(0 100%,12% 55%,20% 72%,33% 24%,43% 54%,55% 8%,68% 50%,82% 29%,100% 76%,100% 100%)}.gmount.two{right:-18%;transform:scaleX(-1);opacity:.68}.gcity{position:absolute;left:0;right:0;bottom:0;height:23%;background:rgba(21,50,55,.8);clip-path:polygon(0 66%,7% 44%,12% 56%,18% 38%,24% 57%,31% 31%,38% 55%,45% 42%,52% 58%,60% 34%,69% 55%,76% 41%,83% 59%,90% 38%,100% 54%,100% 100%,0 100%)}
.gcontent{position:relative;z-index:2;max-width:700px;padding:44px 58px;min-height:100dvh;display:flex;flex-direction:column}.gbrand{display:flex;align-items:center;gap:10px;color:#fff}.glogo{width:43px;height:43px;border-radius:14px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.22);display:grid;place-items:center;font-size:23px}.gbrand strong{display:block;font-size:20px}.gbrand small{display:block;font-size:9px;font-weight:700;color:rgba(255,255,255,.75);margin-top:2px}
.gcopy{margin-top:auto;margin-bottom:auto;max-width:570px}.gkicker{display:inline-flex;padding:7px 11px;border-radius:999px;background:rgba(255,255,255,.13);font-size:8px;font-weight:900;letter-spacing:1px}.gcopy h1{font-family:"Plus Jakarta Sans",sans-serif;font-size:52px;line-height:.99;letter-spacing:-2.6px;margin:18px 0 13px}.gcopy h1 span{color:#bdf0dc}.gcopy p{margin:0;max-width:530px;color:rgba(255,255,255,.88);font-size:14px;line-height:1.7}.gfeatures{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:25px}.gfeature{padding:13px;border-radius:15px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.14);display:flex;gap:10px;align-items:center}.gfeature i{width:34px;height:34px;border-radius:11px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-style:normal}.gfeature strong{display:block;font-size:10px}.gfeature small{display:block;margin-top:2px;font-size:8px;color:rgba(255,255,255,.68)}
.gbottom{font-size:9px;color:rgba(255,255,255,.68);display:flex;justify-content:space-between;gap:15px}.gsecure{color:#d9f5e9;font-weight:800}
.ma-guest-main{min-width:0;background:#fff;display:flex;align-items:center;justify-content:center;padding:35px}
.gform-wrap{width:min(520px,100%)}.gform-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}.gback{width:40px;height:40px;border-radius:50%;background:#f2f6f4;display:grid;place-items:center;color:#48615a;font-size:18px}.gform-brand{display:none;color:var(--green);font-size:18px;font-weight:900}
.gform-intro{text-align:center;margin-bottom:22px}.gform-badge{display:inline-flex;padding:7px 11px;border-radius:999px;background:var(--green-soft);color:var(--green);font-size:8px;font-weight:900;letter-spacing:.5px}.gform-intro h2{font-family:"Plus Jakarta Sans",sans-serif;margin:13px 0 7px;font-size:31px;letter-spacing:-1.2px}.gform-intro p{margin:0 auto;color:var(--muted);font-size:12px;line-height:1.6;max-width:390px}
.gcard{border:1px solid #e7efeb;border-radius:23px;padding:22px;background:#fff;box-shadow:0 16px 50px rgba(16,54,43,.07)}.gerror{padding:12px 13px;border:1px solid #f2c9c9;background:#fff5f5;color:#a43e3e;border-radius:13px;font-size:11px;line-height:1.45;margin-bottom:15px}.gfield{margin-bottom:15px}.glabel{display:block;margin-bottom:7px;font-size:10px;font-weight:800;color:#30443e}.ginput{width:100%;height:50px;border:1px solid #dce7e2;border-radius:13px;padding:0 14px;outline:0;font:inherit;font-size:12px;color:var(--ink);background:#fbfdfc}.ginput:focus{border-color:#83c9b5;box-shadow:0 0 0 4px rgba(8,125,100,.08)}.gsubmit{width:100%;height:50px;border:0;border-radius:13px;background:linear-gradient(135deg,#0b9677,#075743);color:#fff;font-size:11px;font-weight:900;cursor:pointer;box-shadow:0 10px 22px rgba(8,125,100,.18)}.ghelp{text-align:center;margin:17px 0 0;color:#71827c;font-size:10px}.ghelp a{color:var(--green);font-weight:900}.gresults{margin-top:18px;border-top:1px solid #edf2ef;padding-top:16px}.gresults h3{font-family:"Plus Jakarta Sans",sans-serif;margin:0 0 9px;font-size:13px}.gresult{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:11px 12px;border:1px solid #e4ede9;border-radius:13px;margin-top:8px;background:#fbfdfc}.gresult strong{display:block;font-size:11px}.gresult small{display:block;color:#7c8d87;font-size:8px;margin-top:2px}.gresult span{color:var(--green);font-size:16px}.gfoot{text-align:center;margin-top:18px;color:#8a9893;font-size:8px}
@media(max-width:900px){.ma-guest-shell{grid-template-columns:1fr}.ma-guest-brand{display:none}.ma-guest-main{min-height:100dvh;padding:22px 17px}.gform-brand{display:block}.gform-top{margin-bottom:18px}.gform-wrap{width:min(520px,100%)}.gform-intro h2{font-size:28px}}
@media(min-width:901px) and (max-width:1100px){.ma-guest-shell{grid-template-columns:minmax(360px,1fr) minmax(410px,540px)}.gcontent{padding:38px}.gcopy h1{font-size:44px}.ma-guest-main{padding:25px}}
</style>
<div class="ma-guest">
<div class="ma-guest-shell">
  <aside class="ma-guest-brand">
    <div class="gscene"><div class="gsun"></div><div class="gmount"></div><div class="gmount two"></div><div class="gcity"></div></div>
    <div class="gcontent">
      <a class="gbrand" href="<?= config('app.base_path') ?>/"><span class="glogo">🌿</span><span><strong>ManipurApp</strong><small>People · Places · Possibilities</small></span></a>
      <div class="gcopy">
        <span class="gkicker">MANIPURAPP BUSINESS PLATFORM</span>
        <h1>Run your <span>businesses in one place.</span></h1>
        <p>Register once as a Business Owner / Vendor and manage multiple businesses, services and local operations from one connected ManipurApp workspace.</p>
        <div class="gfeatures">
          <div class="gfeature"><i>🏪</i><div><strong>Multiple Businesses</strong><small>Manage more than one business.</small></div></div>
          <div class="gfeature"><i>📊</i><div><strong>One Workspace</strong><small>Run your operations together.</small></div></div>
          <div class="gfeature"><i>🛍️</i><div><strong>Local Services</strong><small>Food, tourism, transport and more.</small></div></div>
          <div class="gfeature"><i>🚀</i><div><strong>Built for Growth</strong><small>Designed for Manipur entrepreneurs.</small></div></div>
        </div>
      </div>
      <div class="gbottom"><span class="gsecure">✓ Built for Manipur</span><span>Local · Connected · Community-focused</span></div>
    </div>
  </aside>
  <main class="ma-guest-main">
    <div class="gform-wrap">
      <div class="gform-top"><a class="gback" href="<?= config('app.base_path') ?>/">←</a><span class="gform-brand">ManipurApp</span><span></span></div>
      <?= $content ?>
      <div class="gfoot">ManipurApp · <?= date('Y') ?></div>
    </div>
  </main>
</div>
</div>
</body>
</html>
