<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';
$basePath = rtrim((string)config('app.base_path'), '/');
$esc = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$posts = is_array($posts ?? null) ? $posts : [];
?>
<div class="member-blog-page">
    <main class="member-blog-main">
        <section class="member-blog-hero">
            <div>
                <span class="member-blog-eyebrow">MANIPURAPP BLOG</span>
                <h1>Stories, ideas and possibilities from Manipur.</h1>
                <p>Discover local places, businesses, travel ideas, community stories and updates from the ManipurApp ecosystem.</p>
            </div>
            <div class="member-blog-hero-mark">✦</div>
        </section>

        <div class="member-blog-toolbar">
            <div><strong>Latest articles</strong><span>Explore what is happening across the platform.</span></div>
            <div class="member-blog-category">All posts</div>
        </div>

        <section class="member-blog-grid" aria-label="Blog posts">
            <?php foreach ($posts as $post): ?>
                <article class="member-blog-card">
                    <a href="<?= $esc($basePath) ?>/member/blog/<?= $esc($post['slug']) ?>" class="member-blog-card-image">
                        <img src="<?= $esc($post['cover_image']) ?>" alt="<?= $esc($post['title']) ?>">
                        <span><?= $esc($post['category']) ?></span>
                    </a>
                    <div class="member-blog-card-body">
                        <div class="member-blog-meta"><?= $esc($post['published_at']) ?> · <?= $esc($post['reading_time']) ?></div>
                        <h2><a href="<?= $esc($basePath) ?>/member/blog/<?= $esc($post['slug']) ?>"><?= $esc($post['title']) ?></a></h2>
                        <p><?= $esc($post['excerpt']) ?></p>
                        <div class="member-blog-tags">
                            <?php foreach ($post['tags'] as $tag): ?><span>#<?= $esc($tag) ?></span><?php endforeach; ?>
                        </div>
                        <a class="member-blog-read" href="<?= $esc($basePath) ?>/member/blog/<?= $esc($post['slug']) ?>">Read article →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <nav class="member-static-mobile-nav member-blog-mobile-nav">
        <a href="<?= $esc($basePath) ?>/member">⌂<span>Home</span></a>
        <a href="<?= $esc($basePath) ?>/member/tourism">⌖<span>Explore</span></a>
        <a href="<?= $esc($basePath) ?>/member/bookings">▣<span>Bookings</span></a>
        <a href="<?= $esc($basePath) ?>/member/ilp">▤<span>ILP</span></a>
        <a href="<?= $esc($basePath) ?>/member#ask-ai" data-ai-open>✦<span>Ask AI</span></a>
        <a href="<?= $esc($basePath) ?>/member/profile">●<span>Profile</span></a>
    </nav>
</div>

<style>
.member-blog-page{min-height:70vh;background:#f6faf8;color:#10241f}.member-blog-main{max-width:1180px;margin:0 auto;padding:38px 24px 72px}.member-blog-hero{display:flex;justify-content:space-between;align-items:center;gap:30px;padding:38px 42px;border:1px solid #cfe5dc;border-radius:28px;background:linear-gradient(135deg,#eaf8f3,#fff);overflow:hidden}.member-blog-eyebrow{color:#087d64;font-size:10px;font-weight:950;letter-spacing:1.6px}.member-blog-hero h1{max-width:760px;margin:9px 0 10px;font-size:42px;line-height:1.03;letter-spacing:-1.5px}.member-blog-hero p{max-width:720px;margin:0;color:#687b74;font-size:13px;line-height:1.65}.member-blog-hero-mark{width:110px;height:110px;border-radius:32px;display:grid;place-items:center;background:#087d64;color:#fff;font-size:55px;transform:rotate(5deg);box-shadow:0 18px 35px rgba(8,125,100,.16)}.member-blog-toolbar{display:flex;justify-content:space-between;align-items:center;margin:30px 0 14px}.member-blog-toolbar strong{display:block;font-size:22px}.member-blog-toolbar span{display:block;margin-top:4px;color:#7a8b85;font-size:11px}.member-blog-category{padding:9px 13px;border-radius:999px;background:#eaf5f1;color:#087d64;font-size:10px;font-weight:900}.member-blog-grid{display:grid;grid-template-columns:minmax(0,1fr);gap:18px}.member-blog-card{display:grid;grid-template-columns:360px minmax(0,1fr);background:#fff;border:1px solid #dfeae5;border-radius:22px;overflow:hidden;box-shadow:0 9px 24px rgba(24,62,51,.045)}.member-blog-card-image{display:block;position:relative;min-height:270px;overflow:hidden;background:#dcebe5}.member-blog-card-image img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s ease}.member-blog-card:hover .member-blog-card-image img{transform:scale(1.035)}.member-blog-card-image span{position:absolute;left:15px;top:15px;padding:7px 9px;border-radius:999px;background:rgba(255,255,255,.92);color:#087d64;font-size:9px;font-weight:900}.member-blog-card-body{padding:27px 29px}.member-blog-meta{color:#7b8b85;font-size:10px;font-weight:700}.member-blog-card h2{margin:9px 0 9px;font-size:27px;line-height:1.08;letter-spacing:-.7px}.member-blog-card h2 a{color:#10241f;text-decoration:none}.member-blog-card p{margin:0;color:#697b75;font-size:12px;line-height:1.7;max-width:700px}.member-blog-tags{display:flex;flex-wrap:wrap;gap:7px;margin-top:16px}.member-blog-tags span{padding:6px 9px;border-radius:999px;background:#f1f7f4;color:#087d64;font-size:9px;font-weight:800}.member-blog-read{display:inline-block;margin-top:19px;color:#087d64;text-decoration:none;font-size:10px;font-weight:950}.member-static-mobile-nav{display:none}@media(max-width:899px){.member-blog-main{padding:18px 15px 100px}.member-blog-hero{padding:25px 22px;border-radius:22px}.member-blog-hero h1{font-size:30px}.member-blog-hero p{font-size:12px}.member-blog-hero-mark{display:none}.member-blog-toolbar{margin:22px 2px 12px}.member-blog-toolbar strong{font-size:18px}.member-blog-card{display:block;border-radius:20px}.member-blog-card-image{height:205px;min-height:0}.member-blog-card-body{padding:21px}.member-blog-card h2{font-size:23px}.member-static-mobile-nav{position:fixed;left:8px;right:8px;bottom:8px;z-index:100;display:grid;grid-template-columns:repeat(6,1fr);background:rgba(255,255,255,.97);border:1px solid #dfeae5;border-radius:20px;box-shadow:0 12px 32px rgba(16,43,36,.16);padding:7px}.member-static-mobile-nav a{text-align:center;text-decoration:none;color:#62746d;font-size:17px;padding:5px 1px}.member-static-mobile-nav a span{display:block;font-size:8px;font-weight:800;margin-top:2px}}
</style>
<?php include __DIR__ . '/../Shared/member-desktop-footer.php'; ?>
