<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';
$basePath = rtrim((string)config('app.base_path'), '/');
$esc = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$post = is_array($post ?? null) ? $post : [];
?>
<div class="member-blog-post-page">
    <main class="member-blog-post-main">
        <a class="member-blog-back" href="<?= $esc($basePath) ?>/member/blog">← Back to Blog</a>
        <article class="member-blog-post">
            <header class="member-blog-post-head">
                <span class="member-blog-eyebrow"><?= $esc($post['category'] ?? 'BLOG') ?></span>
                <h1><?= $esc($post['title'] ?? '') ?></h1>
                <p class="member-blog-post-excerpt"><?= $esc($post['excerpt'] ?? '') ?></p>
                <div class="member-blog-post-meta"><?= $esc($post['author'] ?? 'ManipurApp Team') ?> · <?= $esc($post['published_at'] ?? '') ?> · <?= $esc($post['reading_time'] ?? '') ?></div>
                <div class="member-blog-tags">
                    <?php foreach (($post['tags'] ?? []) as $tag): ?><span>#<?= $esc($tag) ?></span><?php endforeach; ?>
                </div>
            </header>

            <?php if (!empty($post['cover_image'])): ?>
                <figure class="member-blog-post-cover"><img src="<?= $esc($post['cover_image']) ?>" alt="<?= $esc($post['title'] ?? '') ?>"></figure>
            <?php endif; ?>

            <div class="member-blog-post-layout">
                <div class="member-blog-post-content">
                    <?php foreach (($post['content'] ?? []) as $block): ?>
                        <?php if (($block['type'] ?? '') === 'heading'): ?>
                            <h2><?= $esc($block['text'] ?? '') ?></h2>
                        <?php else: ?>
                            <p><?= $esc($block['text'] ?? '') ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if (!empty($post['photos'])): ?>
                        <section class="member-blog-attachment-section">
                            <h2>Photo story</h2>
                            <div class="member-blog-photo-grid">
                                <?php foreach ($post['photos'] as $photo): ?>
                                    <img src="<?= $esc($photo) ?>" alt="Photo from <?= $esc($post['title'] ?? 'article') ?>">
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <?php if (!empty($post['video_url'])): ?>
                        <section class="member-blog-attachment-section">
                            <h2>Video</h2>
                            <div class="member-blog-video"><iframe src="<?= $esc($post['video_url']) ?>" title="<?= $esc($post['title'] ?? 'Blog video') ?>" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>
                        </section>
                    <?php endif; ?>
                </div>

                <aside class="member-blog-post-side">
                    <div class="member-blog-side-card">
                        <strong>Article details</strong>
                        <span>Category</span><b><?= $esc($post['category'] ?? '') ?></b>
                        <span>Published</span><b><?= $esc($post['published_at'] ?? '') ?></b>
                        <span>Author</span><b><?= $esc($post['author'] ?? '') ?></b>
                    </div>
                    <?php if (!empty($post['links'])): ?>
                        <div class="member-blog-side-card">
                            <strong>Useful links</strong>
                            <?php foreach ($post['links'] as $link): ?>
                                <a href="<?= $esc(str_starts_with((string)($link['url'] ?? ''), '/') ? $basePath . $link['url'] : $link['url']) ?>"><?= $esc($link['label'] ?? 'Open link') ?> →</a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </article>
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
.member-blog-post-page{min-height:70vh;background:#f6faf8;color:#10241f}.member-blog-post-main{max-width:1180px;margin:0 auto;padding:30px 24px 72px}.member-blog-back{display:inline-block;color:#087d64;text-decoration:none;font-size:11px;font-weight:900;margin-bottom:18px}.member-blog-post{background:#fff;border:1px solid #dfeae5;border-radius:28px;overflow:hidden;box-shadow:0 10px 30px rgba(24,62,51,.045)}.member-blog-post-head{max-width:900px;margin:0 auto;padding:44px 45px 30px;text-align:center}.member-blog-eyebrow{color:#087d64;font-size:10px;font-weight:950;letter-spacing:1.6px}.member-blog-post-head h1{margin:10px 0 13px;font-size:44px;line-height:1.04;letter-spacing:-1.5px}.member-blog-post-excerpt{margin:0 auto;color:#687b74;font-size:14px;line-height:1.7;max-width:780px}.member-blog-post-meta{margin-top:16px;color:#7b8b85;font-size:10px;font-weight:800}.member-blog-post-head .member-blog-tags{justify-content:center}.member-blog-post-cover{margin:0;height:420px;background:#dcebe5;overflow:hidden}.member-blog-post-cover img{width:100%;height:100%;object-fit:cover;display:block}.member-blog-post-layout{max-width:1020px;margin:0 auto;padding:40px 42px 55px;display:grid;grid-template-columns:minmax(0,1fr) 260px;gap:45px}.member-blog-post-content{font-size:14px;line-height:1.85;color:#4f625b}.member-blog-post-content p{margin:0 0 20px}.member-blog-post-content h2{margin:32px 0 11px;color:#10241f;font-size:24px;line-height:1.15;letter-spacing:-.5px}.member-blog-attachment-section{margin-top:34px}.member-blog-photo-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.member-blog-photo-grid img{width:100%;height:210px;object-fit:cover;border-radius:15px;display:block}.member-blog-video{position:relative;padding-top:56.25%;border-radius:16px;overflow:hidden;background:#10241f}.member-blog-video iframe{position:absolute;inset:0;width:100%;height:100%;border:0}.member-blog-post-side{display:flex;flex-direction:column;gap:15px}.member-blog-side-card{padding:19px;border:1px solid #dfeae5;border-radius:17px;background:#f8fbfa}.member-blog-side-card strong{display:block;margin-bottom:15px;color:#10241f;font-size:12px}.member-blog-side-card span{display:block;margin-top:11px;color:#84938e;font-size:9px;text-transform:uppercase;letter-spacing:.8px;font-weight:900}.member-blog-side-card b{display:block;margin-top:3px;color:#334d44;font-size:11px}.member-blog-side-card a{display:block;padding:9px 0;color:#087d64;text-decoration:none;font-size:10px;font-weight:900;border-top:1px solid #e4eeea}.member-blog-side-card a:first-of-type{border-top:0}.member-static-mobile-nav{display:none}@media(max-width:899px){.member-blog-post-main{padding:18px 15px 100px}.member-blog-post{border-radius:20px}.member-blog-post-head{padding:30px 21px 23px;text-align:left}.member-blog-post-head h1{font-size:31px}.member-blog-post-excerpt{font-size:12px}.member-blog-post-head .member-blog-tags{justify-content:flex-start}.member-blog-post-cover{height:245px}.member-blog-post-layout{display:block;padding:27px 21px 38px}.member-blog-post-content{font-size:13px}.member-blog-post-content h2{font-size:21px}.member-blog-photo-grid{grid-template-columns:1fr}.member-blog-photo-grid img{height:210px}.member-blog-post-side{margin-top:30px}.member-static-mobile-nav{position:fixed;left:8px;right:8px;bottom:8px;z-index:100;display:grid;grid-template-columns:repeat(6,1fr);background:rgba(255,255,255,.97);border:1px solid #dfeae5;border-radius:20px;box-shadow:0 12px 32px rgba(16,43,36,.16);padding:7px}.member-static-mobile-nav a{text-align:center;text-decoration:none;color:#62746d;font-size:17px;padding:5px 1px}.member-static-mobile-nav a span{display:block;font-size:8px;font-weight:800;margin-top:2px}}
</style>
<?php include __DIR__ . '/../Shared/member-desktop-footer.php'; ?>
