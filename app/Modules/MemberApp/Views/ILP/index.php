<?php
declare(strict_types=1);
$basePath = rtrim((string) config('app.base_path'), '/');
$esc = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';
$permitTypes = is_array($permitTypes ?? null) ? $permitTypes : [];
$officialUrl = (string)($officialUrl ?? 'https://manipurilponline.mn.gov.in/');
?>

<style>
.ma-ilp{max-width:1120px;margin:0 auto;padding:18px 16px 110px;color:#10241f}.ma-ilp *{box-sizing:border-box}.ma-ilp a{text-decoration:none}
.ma-ilp-hero{position:relative;overflow:hidden;border-radius:26px;padding:30px;background:linear-gradient(120deg,#075641,#087e63 58%,#42b991);color:#fff;box-shadow:0 18px 38px rgba(5,91,72,.18)}.ma-ilp-hero:after{content:"";position:absolute;width:240px;height:240px;right:-75px;top:-105px;border-radius:50%;background:rgba(255,255,255,.09)}
.ma-ilp-kicker{display:inline-flex;padding:6px 9px;border-radius:99px;background:rgba(255,255,255,.13);font-size:9px;font-weight:900;letter-spacing:.8px;text-transform:uppercase}.ma-ilp-hero h1{position:relative;z-index:2;margin:13px 0 8px;font-size:32px;line-height:1.02;letter-spacing:-1.1px}.ma-ilp-hero p{position:relative;z-index:2;max-width:700px;margin:0;color:rgba(255,255,255,.86);font-size:12px;line-height:1.55}.ma-ilp-actions{position:relative;z-index:2;display:flex;gap:9px;flex-wrap:wrap;margin-top:18px}.ma-ilp-btn{display:inline-flex;align-items:center;justify-content:center;padding:10px 14px;border-radius:11px;font-size:10px;font-weight:900}.ma-ilp-btn-primary{background:#fff;color:#086b57}.ma-ilp-btn-ghost{border:1px solid rgba(255,255,255,.3);color:#fff;background:rgba(255,255,255,.08)}
.ma-ilp-note{margin-top:14px;padding:13px 15px;border:1px solid #fde68a;background:#fffbeb;border-radius:14px;color:#713f12;font-size:11px;line-height:1.55}.ma-ilp-note strong{color:#92400e}
.ma-ilp-check{margin-top:24px;display:grid;grid-template-columns:minmax(0,1.05fr) minmax(300px,.95fr);gap:14px}.ma-ilp-panel{border:1px solid #e0ebe6;border-radius:20px;background:#fff;padding:18px;box-shadow:0 8px 22px rgba(28,63,53,.045)}.ma-ilp-panel-head{margin-bottom:14px}.ma-ilp-panel-head .eyebrow{display:block;color:#087d64;font-size:8px;font-weight:950;letter-spacing:1px}.ma-ilp-panel-head h2{margin:4px 0;font-size:20px;letter-spacing:-.4px}.ma-ilp-panel-head p{margin:0;color:#71817a;font-size:10px;line-height:1.5}
.ma-ilp-form{display:grid;gap:13px}.ma-ilp-field label{display:block;margin-bottom:6px;color:#29443b;font-size:9px;font-weight:900}.ma-ilp-field select,.ma-ilp-field input{width:100%;height:40px;border:1px solid #d3e1dc;border-radius:11px;background:#fff;color:#17352d;padding:0 11px;font:inherit;font-size:10px;outline:none}.ma-ilp-field select:focus,.ma-ilp-field input:focus{border-color:#72b8a5;box-shadow:0 0 0 4px rgba(8,125,100,.07)}.ma-ilp-choice-row{display:flex;gap:8px;flex-wrap:wrap}.ma-ilp-choice{display:inline-flex;align-items:center;gap:6px;padding:9px 11px;border:1px solid #dce7e3;border-radius:11px;background:#f9fcfb;color:#476159;font-size:9px;font-weight:800}.ma-ilp-choice input{width:auto;height:auto}.ma-ilp-row{display:grid;grid-template-columns:1fr 1fr;gap:11px}.ma-ilp-submit{border:0;border-radius:11px;background:#087d64;color:#fff;padding:11px 15px;font-size:10px;font-weight:950;cursor:pointer}.ma-ilp-submit:disabled{opacity:.55;cursor:wait}.ma-ilp-status{min-height:15px;color:#71817a;font-size:9px;line-height:1.4}
.ma-ilp-result{margin-top:14px;padding:15px;border-radius:16px;border:1px solid #dceae5;background:#f7fbf9}.ma-ilp-result[hidden]{display:none}.ma-ilp-result-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px}.ma-ilp-result-head h3{margin:2px 0 0;font-size:15px}.ma-ilp-result-kicker{color:#087d64;font-size:8px;font-weight:950;letter-spacing:1px}.ma-ilp-confidence{padding:5px 8px;border-radius:999px;background:#e8f6f0;color:#087d64;font-size:8px;font-weight:950}.ma-ilp-result-message{margin-top:9px;color:#52665f;font-size:10px;line-height:1.5}.ma-ilp-block{margin-top:13px;padding-top:12px;border-top:1px solid #e1ebe7}.ma-ilp-block h4{margin:0 0 7px;font-size:9px;letter-spacing:.5px;text-transform:uppercase;color:#5e736b}.ma-ilp-list{margin:0;padding:0;list-style:none;display:grid;gap:6px}.ma-ilp-list li{position:relative;padding-left:19px;color:#52665f;font-size:9px;line-height:1.45}.ma-ilp-list li:before{content:'✓';position:absolute;left:0;top:0;color:#087d64;font-weight:950}.ma-ilp-missing li:before{content:'!';color:#b45309}.ma-ilp-source{display:flex;flex-wrap:wrap;gap:8px;align-items:center}.ma-ilp-source a{color:#087d64;font-size:9px;font-weight:900}.ma-ilp-source small{color:#7a8984;font-size:8px}
.ma-ilp-knowledge .ma-ilp-section-head{margin-bottom:12px}.ma-ilp-section-head h2{margin:0;font-size:20px;letter-spacing:-.4px}.ma-ilp-section-head p{margin:5px 0 0;color:#71817a;font-size:10px;line-height:1.45}.ma-ilp-grid{display:grid;grid-template-columns:1fr;gap:9px}.ma-ilp-card{border:1px solid #e3ece8;border-radius:15px;background:#fbfdfc;padding:13px}.ma-ilp-card strong{display:block;font-size:11px}.ma-ilp-card .validity{display:inline-flex;margin-top:6px;padding:4px 7px;border-radius:99px;background:#eef8f4;color:#087d64;font-size:7px;font-weight:900}.ma-ilp-card p{margin:7px 0 5px;color:#52665f;font-size:9px;line-height:1.45}.ma-ilp-card small{color:#7a8984;font-size:8px;line-height:1.4;display:block}
.ma-ilp-bottom{margin-top:24px;padding:19px;border:1px solid #dceae5;border-radius:18px;background:#f7fbf9}.ma-ilp-bottom h2{margin:0;font-size:16px}.ma-ilp-bottom p{margin:7px 0 14px;color:#65766f;font-size:10px;line-height:1.55}.ma-ilp-bottom a{display:inline-flex;padding:10px 13px;border-radius:10px;background:#087d64;color:#fff;font-size:9px;font-weight:900}.ma-ilp-error{margin-top:10px;padding:10px 12px;border:1px solid #fecaca;background:#fff1f2;color:#9f1239;border-radius:11px;font-size:9px}.ma-ilp-error[hidden]{display:none}
@media(max-width:850px){.ma-ilp-check{grid-template-columns:1fr}.ma-ilp-knowledge{order:2}}@media(max-width:650px){.ma-ilp{padding:12px 14px 100px}.ma-ilp-hero{padding:23px 19px;border-radius:22px}.ma-ilp-hero h1{font-size:27px}.ma-ilp-row{grid-template-columns:1fr}.ma-ilp-btn{font-size:9px}.ma-ilp-note{font-size:10px}.ma-ilp-panel{padding:15px}}
</style>

<div class="ma-ilp">
    <section class="ma-ilp-hero">
        <span class="ma-ilp-kicker">ManipurApp • Visitor Information</span>
        <h1>Digital ILP Helper</h1>
        <p>Understand the main Inner Line Permit categories and check which category you may need before travelling to Manipur. For the actual application, always use the official Government portal.</p>
        <div class="ma-ilp-actions">
            <a class="ma-ilp-btn ma-ilp-btn-primary" href="<?= $esc($officialUrl) ?>" target="_blank" rel="noopener noreferrer">Open Official ILP Portal ↗</a>
            <a class="ma-ilp-btn ma-ilp-btn-ghost" href="<?= $esc($basePath) ?>/member/tourism">Explore Manipur</a>
        </div>
    </section>

    <div class="ma-ilp-note">
        <strong>Important:</strong> This is an informational pre-check only. ILP rules, documents, fees, validity and procedures can change. The Government of Manipur ILP portal and latest Government notifications remain authoritative.
    </div>

    <div class="ma-ilp-check">
        <section class="ma-ilp-panel">
            <div class="ma-ilp-panel-head">
                <span class="eyebrow">QUICK PRE-CHECK</span>
                <h2>Check your ILP requirement</h2>
                <p>Answer a few simple questions and the helper will show the permit category to review, missing information and the relevant checklist.</p>
            </div>

            <form class="ma-ilp-form" data-ilp-form action="<?= $esc($basePath) ?>/member/ilp/analyze" method="post">
                <div class="ma-ilp-field">
                    <label>Are you entering Manipur from outside the state?</label>
                    <div class="ma-ilp-choice-row">
                        <label class="ma-ilp-choice"><input type="radio" name="outside_manipur" value="1" checked> Yes</label>
                        <label class="ma-ilp-choice"><input type="radio" name="outside_manipur" value="0"> No</label>
                    </div>
                </div>

                <div class="ma-ilp-field">
                    <label for="member-ilp-purpose">Purpose of visit</label>
                    <select id="member-ilp-purpose" name="purpose" required>
                        <?php
                        $options = [
                            'TOURISM' => 'Tourism / holiday',
                            'BUSINESS_VISIT' => 'Short business visit',
                            'SHORT_TERM' => 'Other short-term visit',
                            'FREQUENT_VISITOR' => 'Frequent visitor',
                            'BUSINESS_ESTABLISHMENT' => 'Establishing / operating a business',
                            'INVESTOR' => 'Investor',
                            'TRADER' => 'Trader',
                            'CONTRACTOR' => 'Contractor activity',
                            'LABOUR' => 'Labour engagement',
                            'TEMPORARY_WORK' => 'Temporary employment / work',
                            'REGULAR_EMPLOYEE' => 'Regular employee',
                            'OTHER' => 'Other / not sure',
                        ];
                        foreach ($options as $key => $label):
                        ?>
                            <option value="<?= $esc($key) ?>"><?= $esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="ma-ilp-row">
                    <div class="ma-ilp-field">
                        <label for="member-ilp-duration">Planned stay (days)</label>
                        <input id="member-ilp-duration" type="number" min="1" max="3650" name="duration_days" value="7" placeholder="e.g. 7">
                    </div>
                    <div class="ma-ilp-field">
                        <label>Do you have the required sponsor / agency arrangement?</label>
                        <div class="ma-ilp-choice-row">
                            <label class="ma-ilp-choice"><input type="radio" name="sponsor_available" value="1"> Yes</label>
                            <label class="ma-ilp-choice"><input type="radio" name="sponsor_available" value="0" checked> No</label>
                        </div>
                    </div>
                </div>

                <div class="ma-ilp-actions" style="position:static;margin-top:2px">
                    <button class="ma-ilp-btn ma-ilp-btn-primary ma-ilp-submit" type="submit" data-ilp-submit>Check ILP Requirement</button>
                    <span class="ma-ilp-status" data-ilp-status>Simple informational pre-check — no application is submitted.</span>
                </div>
                <div class="ma-ilp-error" data-ilp-error hidden></div>
            </form>

            <div class="ma-ilp-result" data-ilp-result hidden>
                <div class="ma-ilp-result-head">
                    <div><span class="ma-ilp-result-kicker">CHECK RESULT</span><h3 data-ilp-result-title></h3></div>
                    <span class="ma-ilp-confidence" data-ilp-confidence hidden></span>
                </div>
                <div class="ma-ilp-result-message" data-ilp-result-message></div>
                <div class="ma-ilp-block" data-ilp-permit hidden><h4>Permit category to review</h4><strong data-ilp-permit-name></strong></div>
                <div class="ma-ilp-block" data-ilp-reasons hidden><h4>Why this was suggested</h4><ul class="ma-ilp-list" data-ilp-reasons-list></ul></div>
                <div class="ma-ilp-block" data-ilp-requirements hidden><h4>Information / checklist</h4><ul class="ma-ilp-list" data-ilp-requirements-list></ul></div>
                <div class="ma-ilp-block" data-ilp-missing hidden><h4>Still needed</h4><ul class="ma-ilp-list ma-ilp-missing" data-ilp-missing-list></ul></div>
                <div class="ma-ilp-block" data-ilp-source hidden><h4>Official source</h4><div class="ma-ilp-source"><a data-ilp-source-link href="<?= $esc($officialUrl) ?>" target="_blank" rel="noopener noreferrer">Government of Manipur ILP Portal ↗</a><small data-ilp-source-date></small></div></div>
            </div>
        </section>

        <section class="ma-ilp-panel ma-ilp-knowledge">
            <div class="ma-ilp-section-head">
                <h2>ILP categories</h2>
                <p>Information currently maintained in the ManipurApp Digital ILP Helper knowledge base.</p>
            </div>
            <div class="ma-ilp-grid">
                <?php foreach ($permitTypes as $permit): ?>
                    <article class="ma-ilp-card">
                        <strong><?= $esc($permit['name'] ?? '') ?></strong>
                        <?php if (!empty($permit['validity_summary'])): ?><span class="validity"><?= $esc($permit['validity_summary']) ?></span><?php endif; ?>
                        <?php if (!empty($permit['short_description'])): ?><p><?= $esc($permit['short_description']) ?></p><?php endif; ?>
                        <?php if (!empty($permit['sponsor_summary'])): ?><small><?= $esc($permit['sponsor_summary']) ?></small><?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <section class="ma-ilp-bottom">
        <h2>Ready for the actual application?</h2>
        <p>Use the official Government of Manipur ILP Online Portal for the current application process, forms, fees, document requirements, status checking and permit download.</p>
        <a href="<?= $esc($officialUrl) ?>" target="_blank" rel="noopener noreferrer">Go to Government ILP Portal ↗</a>
    </section>
</div>

<nav class="ma-bottom-nav" aria-label="Primary navigation">
    <a class="ma-nav-item" href="<?= $esc($basePath) ?>/member"><span class="ma-nav-symbol">⌂</span><span>Home</span></a>
    <a class="ma-nav-item" href="<?= $esc($basePath) ?>/member/tourism"><span class="ma-nav-symbol">⌖</span><span>Explore</span></a>
    <a class="ma-nav-item" href="<?= $esc($basePath) ?>/member/bookings"><span class="ma-nav-symbol">▣</span><span>Bookings</span></a>
    <a class="ma-nav-item active ma-nav-ilp" href="<?= $esc($basePath) ?>/member/ilp"><span class="ma-nav-symbol">▤</span><span>ILP</span></a>
    <a class="ma-nav-item ma-nav-ai" href="<?= $esc($basePath) ?>/member#ask-ai" data-ai-open><span class="ma-nav-symbol">✦</span><span>Ask AI</span></a>
    <a class="ma-nav-item" href="<?= $esc($basePath) ?>/member/profile"><span class="ma-nav-symbol">♙</span><span>Profile</span></a>
</nav>

<?php include __DIR__ . '/../AI/freeform.php'; ?>

<script>
(function(){
    const form=document.querySelector('[data-ilp-form]');
    if(!form || form.dataset.bound==='1') return;
    form.dataset.bound='1';
    const submit=form.querySelector('[data-ilp-submit]');
    const status=form.querySelector('[data-ilp-status]');
    const error=form.querySelector('[data-ilp-error]');
    const result=form.parentElement.querySelector('[data-ilp-result]');
    const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    const list=(el,items)=>{el.innerHTML='';(Array.isArray(items)?items:[]).forEach(item=>{const li=document.createElement('li');li.textContent=String(item);el.appendChild(li)})};
    const block=(sel,show)=>{const el=result.querySelector(sel);if(el)el.hidden=!show;return el};
    form.addEventListener('submit',async e=>{
        e.preventDefault();
        submit.disabled=true;error.hidden=true;result.hidden=true;status.textContent='Checking the ILP information…';
        try{
            const r=await fetch(form.action,{method:'POST',body:new FormData(form),credentials:'same-origin',headers:{'X-Requested-With':'XMLHttpRequest',Accept:'application/json'}});
            const d=await r.json().catch(()=>({}));
            if(!r.ok||!d.success) throw new Error(d.message||'Unable to check the ILP requirement right now.');
            const x=d.data||{};
            result.querySelector('[data-ilp-result-title]').textContent=x.permit?.name||'ILP guidance';
            result.querySelector('[data-ilp-result-message]').textContent=x.message||'Please review the official ILP portal for the current requirements.';
            const conf=result.querySelector('[data-ilp-confidence]');
            if(x.confidence){conf.textContent=String(x.confidence).replace(/_/g,' ').toUpperCase();conf.hidden=false}else conf.hidden=true;
            const permit=block('[data-ilp-permit]',!!x.permit);if(permit)permit.querySelector('[data-ilp-permit-name]').textContent=x.permit?.name||'';
            const reasons=block('[data-ilp-reasons]',Array.isArray(x.reasons)&&x.reasons.length);if(reasons)list(reasons.querySelector('[data-ilp-reasons-list]'),x.reasons);
            const req=block('[data-ilp-requirements]',Array.isArray(x.requirements)&&x.requirements.length);if(req)list(req.querySelector('[data-ilp-requirements-list]'),x.requirements);
            const missing=block('[data-ilp-missing]',Array.isArray(x.missing_information)&&x.missing_information.length);if(missing)list(missing.querySelector('[data-ilp-missing-list]'),x.missing_information.map(v=>String(v).replace(/_/g,' ')));
            const source=block('[data-ilp-source]',!!x.source);if(source){const a=source.querySelector('[data-ilp-source-link]');a.href=x.source?.url||<?=json_encode($officialUrl)?>;a.textContent=(x.source?.title||'Government of Manipur ILP Portal')+' ↗';source.querySelector('[data-ilp-source-date]').textContent=x.source?.verified_at?'Verified '+x.source.verified_at:''}
            result.hidden=false;status.textContent='Pre-check complete. No ILP application was submitted.';result.scrollIntoView({behavior:'smooth',block:'nearest'});
        }catch(ex){error.textContent=ex.message||'Unable to check the ILP requirement right now.';error.hidden=false;status.textContent='Please try again.'}
        finally{submit.disabled=false}
    });
})();
</script>
<?php include __DIR__ . '/../Shared/member-desktop-footer.php'; ?>
