<?php
declare(strict_types=1);
$e = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>
<style>
.ai-wrap{max-width:1200px;margin:0 auto}
.ai-hero{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(280px,.75fr);gap:18px;margin-bottom:18px}
.ai-card{background:#fff;border:1px solid #e6e9ef;border-radius:18px;padding:22px;box-shadow:0 8px 28px rgba(16,24,40,.05)}
.ai-title{font-size:28px;margin:0 0 8px}.ai-muted{color:#667085}
.ai-question{width:100%;min-height:145px;resize:vertical;border:1px solid #d0d5dd;border-radius:14px;padding:14px;font:inherit;box-sizing:border-box}
.ai-fields{display:grid;grid-template-columns:1fr 2fr;gap:12px;margin-top:14px}
.ai-select{width:100%;border:1px solid #d0d5dd;border-radius:12px;padding:12px;background:#fff;font:inherit}
.ai-actions{display:flex;justify-content:flex-end;align-items:center;gap:12px;margin-top:12px}
.ai-btn{border:0;border-radius:12px;padding:11px 18px;font-weight:800;cursor:pointer}.ai-btn-primary{background:#111827;color:#fff}
.ai-btn:disabled{opacity:.55;cursor:not-allowed}
.ai-progress{display:none;margin-top:14px}.ai-progress.show{display:block}
.ai-progress-track{height:8px;background:#eef2f6;border-radius:99px;overflow:hidden}.ai-progress-bar{height:100%;width:25%;border-radius:99px;background:#111827;animation:aiPulse 1.2s ease-in-out infinite}
@keyframes aiPulse{0%{transform:translateX(-120%)}100%{transform:translateX(450%)}}
.ai-answer{line-height:1.7;color:#344054}.ai-answer h3{font-size:19px;line-height:1.3;margin:18px 0 8px;color:#101828}.ai-answer h4{font-size:16px;margin:14px 0 6px;color:#101828}.ai-answer p{margin:0 0 12px}.ai-answer ul,.ai-answer ol{margin:8px 0 14px;padding-left:24px}.ai-answer li{margin:5px 0}.ai-answer strong{color:#101828}.ai-answer hr{border:0;border-top:1px solid #eaecf0;margin:16px 0}.ai-meta{font-size:12px;color:#667085;margin-top:10px}
.ai-history{display:grid;gap:12px}.ai-q{font-weight:800}.ai-a{color:#344054;line-height:1.55;margin-top:7px}.ai-a p{margin:0 0 8px}.ai-a ul,.ai-a ol{margin:5px 0 8px;padding-left:22px}.ai-a li{margin:3px 0}.ai-a strong{color:#101828}
.ai-error{background:#fff4f4;border:1px solid #f2b8b8;color:#9b1c1c;padding:12px;border-radius:12px;margin-bottom:15px}
.ai-success{background:#ecfdf3;border:1px solid #abefc6;color:#067647;padding:10px;border-radius:12px;margin-top:12px;display:none}
.ai-tip ul{margin:10px 0 0 18px;padding:0;line-height:1.7}
.ai-source{display:inline-flex;padding:5px 9px;border-radius:999px;background:#f2f4f7;font-size:12px;font-weight:800}
.ai-records{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-top:18px}
.ai-record{border:1px solid #e6e9ef;border-radius:16px;overflow:hidden;background:#fff;display:flex;flex-direction:column;min-width:0}
.ai-record-media{height:130px;background:linear-gradient(135deg,#eef4ff,#f8fafc);display:flex;align-items:center;justify-content:center;font-size:42px;overflow:hidden}
.ai-record-media img{width:100%;height:100%;object-fit:cover}
.ai-record-body{padding:15px}.ai-record-title{font-size:17px;font-weight:800;margin:0 0 5px}.ai-record-title a{color:inherit;text-decoration:none}.ai-record-title a:hover{text-decoration:underline}.ai-record-location{font-size:12px;color:#667085;margin-bottom:9px}.ai-record-desc{font-size:13px;color:#475467;line-height:1.5;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}.ai-record-meta{display:flex;flex-wrap:wrap;gap:6px;margin-top:11px}.ai-chip{background:#f2f4f7;border-radius:999px;padding:5px 8px;font-size:11px;font-weight:700}.ai-subsection{margin-top:10px;font-size:13px;color:#475467}.ai-subsection ul{margin:6px 0 0 18px;padding:0}.ai-detail{margin-top:8px;font-size:13px;color:#475467}.ai-record-actions{display:flex;gap:8px;margin-top:auto;padding:0 15px 15px}.ai-record-btn{flex:1;text-align:center;text-decoration:none;border-radius:10px;padding:9px 10px;font-size:12px;font-weight:800;border:1px solid #d0d5dd;color:#344054;background:#fff}.ai-record-btn.primary{background:#111827;color:#fff;border-color:#111827}.ai-menu-list{display:grid;gap:7px;margin-top:8px}.ai-menu-link{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:9px 10px;border:1px solid #eaecf0;border-radius:10px;background:#fafafa;color:#344054;text-decoration:none;font-size:12px}.ai-menu-link:hover{border-color:#98a2b3;background:#f9fafb}.ai-menu-link strong{color:#101828}.ai-menu-price{font-weight:800;white-space:nowrap}
.ai-freeform{border:1px solid #d9d6fe;background:linear-gradient(135deg,#fafaff,#fff);position:relative;overflow:hidden}
.ai-freeform-badge{display:inline-flex;align-items:center;gap:7px;padding:6px 10px;border-radius:999px;background:#eef2ff;color:#3730a3;font-size:11px;font-weight:900;letter-spacing:.03em}
.ai-freeform-title{font-size:25px;margin:10px 0 6px}.ai-freeform-sub{color:#667085;line-height:1.6;margin:0 0 14px}
.ai-freeform-question{width:100%;min-height:115px;resize:vertical;border:1px solid #cfd4dc;border-radius:14px;padding:14px;font:inherit;box-sizing:border-box;background:#fff}
.ai-freeform-actions{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:12px}
.ai-tool-trace{display:flex;flex-wrap:wrap;gap:7px;margin-top:12px}
.ai-tool-chip{padding:6px 9px;border-radius:999px;background:#f2f4f7;border:1px solid #e4e7ec;font-size:11px;font-weight:800;color:#344054}
.ai-freeform-result{display:none;margin-top:18px;padding-top:18px;border-top:1px solid #eaecf0}
.ai-freeform-result.show{display:block}
.ai-freeform-note{font-size:12px;color:#667085;line-height:1.55}
@media(max-width:1050px){.ai-records{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:650px){.ai-records{grid-template-columns:1fr}}
@media(max-width:900px){.ai-hero{grid-template-columns:1fr}.ai-fields{grid-template-columns:1fr}}
</style>

<div class="page-header">
    <div>
        <h1>ManipurApp AI</h1>
        <p>Ask about Tourism or Restaurants using live tenant records first, then let the configured AI provider explain and organize the results.</p>
    </div>
</div>

<div class="ai-wrap">
    <?php if($error): ?><div class="ai-error"><b>AI module:</b> <?=$e($error)?></div><?php endif; ?>

    <div class="ai-hero">
        <section class="ai-card">
            <h2 class="ai-title">Ask ManipurApp AI</h2>
            <p class="ai-muted">Choose what you are asking about. ManipurApp checks the selected live records first, then sends the relevant data to the configured AI provider.</p>

            <form id="aiAskForm" action="<?=config('app.base_path')?>/ai-assistant/ask" method="post">
                <div class="ai-fields">
                    <div>
                        <label for="aiScope"><strong>Ask about</strong></label>
                        <select id="aiScope" name="scope" class="ai-select">
                            <?php foreach($scopes as $key=>$label): ?>
                                <option value="<?=$e($key)?>"><?=$e($label)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="aiQuestion"><strong>Your question</strong></label>
                        <textarea id="aiQuestion" class="ai-question" name="question" required maxlength="8000" placeholder="Example: Which vegetarian restaurants in Imphal have dishes under ₹300?"></textarea>
                    </div>
                </div>

                <div class="ai-actions">
                    <span id="aiStatus" class="ai-muted">Database → Gemini</span>
                    <button id="aiAskButton" class="ai-btn ai-btn-primary" type="submit">Ask ManipurApp AI</button>
                </div>

                <div id="aiProgress" class="ai-progress">
                    <div class="ai-muted" style="margin-bottom:7px">Checking ManipurApp records and asking Gemini…</div>
                    <div class="ai-progress-track"><div class="ai-progress-bar"></div></div>
                </div>
                <div id="aiSuccess" class="ai-success"></div>
            </form>
        </section>

        <section class="ai-card ai-tip">
            <h2>How it works</h2>
            <ol style="padding-left:20px;line-height:1.7">
                <li>You choose a Tourism or Restaurant category.</li>
                <li>PHP queries the tenant's live records and applies question filters.</li>
                <li>Relevant records are packaged as context.</li>
                <li>The configured AI provider explains the data and answers the question.</li>
                <li>The Q&A is saved to AI history.</li>
            </ol>
            <p class="ai-muted">The AI provider is not allowed to invent live ManipurApp records.</p>
        </section>
    </div>


    <section class="ai-card ai-freeform" style="margin-bottom:18px">
        <span class="ai-freeform-badge">🤖 FREE-FORM SMART ASK · FUNCTION TOOLS</span>
        <h2 class="ai-freeform-title">Ask across ManipurApp</h2>
        <p class="ai-freeform-sub">
            Ask one natural question that can combine <strong>Tourism + Restaurants + Taxi</strong>.
            The configured AI provider decides which live ManipurApp tools are needed, PHP executes those read-only database tools,
            and the provider then builds the final answer from the returned records.
        </p>
        <form id="aiFreeformForm" action="<?=config('app.base_path')?>/ai-assistant/freeform" method="post">
            <textarea id="aiFreeformQuestion" class="ai-freeform-question" name="question" required maxlength="4000"
                placeholder="Example: I have 3 days in Manipur with my family. What places should we visit, where can we eat Manipuri food, and what taxi service could we use for the trip?"></textarea>
            <div class="ai-freeform-actions">
                <span id="aiFreeformStatus" class="ai-freeform-note">AI provider → tools → live database → answer</span><span id="aiFreeformElapsed" class="ai-freeform-note" style="font-weight:800;white-space:nowrap">0s</span>
                <button id="aiFreeformButton" class="ai-btn ai-btn-primary" type="submit">Ask Smart AI</button>
            </div>
            <div id="aiFreeformResult" class="ai-freeform-result">
                <div id="aiFreeformAnswer" class="ai-answer"></div>
                <div id="aiFreeformResultCards"></div>
                <div id="aiFreeformTools" class="ai-tool-trace"></div>
                <div id="aiFreeformMeta" class="ai-meta"></div>
                <details id="aiFreeformDiagnosticsWrap" style="margin-top:14px">
                    <summary style="cursor:pointer;font-weight:700">Smart AI diagnostics</summary>
                    <div id="aiFreeformDiagnostics" style="margin-top:10px;background:#0f172a;color:#dbeafe;border-radius:12px;padding:12px;font:12px/1.6 ui-monospace,SFMono-Regular,Menlo,monospace;max-height:320px;overflow:auto"></div>
                </details>
            </div>
        </form>
    </section>

    <section id="aiLatest" class="ai-card" style="margin-bottom:18px;display:none">
        <h2>Latest answer</h2>
        <span id="aiLatestScope" class="ai-source"></span>
        <div id="aiLatestAnswer" class="ai-answer" style="margin-top:12px"></div>
        <div id="aiLatestMeta" class="ai-meta"></div>
        <div id="aiLatestRecords" class="ai-records"></div>
    </section>

    <section class="ai-card">
        <h2>Recent AI questions</h2>
        <div id="aiHistory" class="ai-history">
        <?php if(!$rows): ?>
            <p id="aiNoHistory" class="ai-muted">No AI questions yet.</p>
        <?php else: foreach($rows as $r): ?>
            <article style="border-top:1px solid #eaecf0;padding-top:14px">
                <div class="ai-q"><?= $e($r['question']) ?></div>
                <?php if(!empty($r['ai_scope'])): ?><span class="ai-source" style="margin-top:7px"><?= $e($r['ai_scope']) ?></span><?php endif; ?>
                <div class="ai-a"><?= $e($r['answer']) ?></div>
                <div class="ai-meta"><?= $e($r['created_at']) ?> · <?= $e($r['model']) ?> · <?= $e($r['latency_ms']) ?> ms · <?= $e($r['records_found'] ?? 0) ?> records</div>
            </article>
        <?php endforeach; endif; ?>
        </div>
    </section>
</div>

<script>
(function(){
    const form=document.getElementById('aiAskForm');
    const button=document.getElementById('aiAskButton');
    const progress=document.getElementById('aiProgress');
    const status=document.getElementById('aiStatus');
    const success=document.getElementById('aiSuccess');
    const latest=document.getElementById('aiLatest');
    const latestScope=document.getElementById('aiLatestScope');
    const latestAnswer=document.getElementById('aiLatestAnswer');
    const latestMeta=document.getElementById('aiLatestMeta');
    const latestRecords=document.getElementById('aiLatestRecords');
    const basePath=<?=json_encode(config('app.base_path'))?>;
    const history=document.getElementById('aiHistory');
    const noHistory=document.getElementById('aiNoHistory');

    function aiEscapeHtml(value){
        return String(value ?? '').replace(/[&<>"']/g,function(c){
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c];
        });
    }

    // Small, safe Markdown renderer for Gemini's human-readable response.
    // It intentionally supports only the formatting we ask Gemini to use.
    function renderMarkdown(value){
        let text=String(value ?? '').replace(/\r\n?/g,'\n').trim();
        if(!text) return '';
        const lines=text.split('\n');
        let html='', list=null;
        function closeList(){ if(list){ html+='</'+list+'>'; list=null; } }
        function inline(v){
            let x=aiEscapeHtml(v);
            x=x.replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>');
            x=x.replace(/__([^_]+?)__/g,'<strong>$1</strong>');
            x=x.replace(/\*([^*\n]+?)\*/g,'<em>$1</em>');
            x=x.replace(/`([^`]+?)`/g,'<code>$1</code>');
            return x;
        }
        let paragraph=[];
        function flushParagraph(){
            if(paragraph.length){ html+='<p>'+paragraph.map(inline).join('<br>')+'</p>'; paragraph=[]; }
        }
        lines.forEach(function(line){
            const t=line.trim();
            if(!t){ flushParagraph(); closeList(); return; }
            if(/^---+$/.test(t) || /^\*\*\*+$/.test(t)){ flushParagraph(); closeList(); html+='<hr>'; return; }
            let m=t.match(/^###\s+(.+)$/);
            if(m){ flushParagraph(); closeList(); html+='<h3>'+inline(m[1])+'</h3>'; return; }
            m=t.match(/^####\s+(.+)$/);
            if(m){ flushParagraph(); closeList(); html+='<h4>'+inline(m[1])+'</h4>'; return; }
            m=t.match(/^\*\s+(.+)$/) || t.match(/^[-•]\s+(.+)$/);
            if(m){ flushParagraph(); if(list!=='ul'){ closeList(); html+='<ul>'; list='ul'; } html+='<li>'+inline(m[1])+'</li>'; return; }
            m=t.match(/^\d+[.)]\s+(.+)$/);
            if(m){ flushParagraph(); if(list!=='ol'){ closeList(); html+='<ol>'; list='ol'; } html+='<li>'+inline(m[1])+'</li>'; return; }
            paragraph.push(t);
        });
        flushParagraph(); closeList();
        return html;
    }

    function recordTitle(r, scope){
        return r.name || r.title || 'ManipurApp record';
    }
    function recordImage(r){
        return r.cover_image_path || r.logo_path || r.profile_image_path || '';
    }
    function recordLocation(r){
        const parts=[];
        if(r.city) parts.push(r.city);
        if(r.district) parts.push(r.district);
        if(!parts.length && r.destination_name) parts.push(r.destination_name);
        return parts.join(' · ');
    }
    function recordMeta(r, scope){
        const chips=[];
        if(scope==='DESTINATIONS'){
            if(r.best_time_to_visit) chips.push('Best: '+r.best_time_to_visit);
            if(r.suggested_duration) chips.push(r.suggested_duration);
        } else if(scope==='STAYS'){
            if(r.stay_type) chips.push(r.stay_type);
            if(r.starting_price_per_night !== null && r.starting_price_per_night !== undefined) chips.push('From ₹'+Number(r.starting_price_per_night).toLocaleString()+'/night');
        } else if(scope==='PACKAGES'){
            if(r.duration_days) chips.push(r.duration_days+' days');
            if(r.duration_nights) chips.push(r.duration_nights+' nights');
            const price=r.discount_price || r.base_price;
            if(price) chips.push('₹'+Number(price).toLocaleString());
        } else if(scope==='GUIDES'){
            if(r.experience_years) chips.push(r.experience_years+' yrs');
            if(r.price_per_day) chips.push('₹'+Number(r.price_per_day).toLocaleString()+'/day');
            if(r.verification_status) chips.push(r.verification_status);
        } else if(scope==='EXPERIENCES'){
            if(r.duration_minutes) chips.push(r.duration_minutes+' min');
            if(r.price) chips.push('₹'+Number(r.price).toLocaleString());
            if(r.max_participants) chips.push('Up to '+r.max_participants);
        } else if(scope==='EVENTS'){
            if(r.start_at) chips.push(new Date(r.start_at.replace(' ','T')).toLocaleString());
            if(r.venue) chips.push(r.venue);
        } else if(scope==='RESTAURANTS'){
            if(r.cuisine_type) chips.push(r.cuisine_type);
            if(r.delivery_available) chips.push('Delivery');
            if(r.pickup_available) chips.push('Pickup');
            if(r.delivery_fee !== null && r.delivery_fee !== undefined) chips.push('Delivery ₹'+Number(r.delivery_fee).toLocaleString());
            if(r.estimated_prep_minutes) chips.push(r.estimated_prep_minutes+' min prep');
        } else if(scope==='RESTAURANT_MENU'){
            chips.push(r.is_veg ? 'Vegetarian' : 'Non-vegetarian');
            if(r.category_name) chips.push(r.category_name);
            if(r.price !== null && r.price !== undefined) chips.push('₹'+Number(r.price).toLocaleString());
            if(r.restaurant_name) chips.push(r.restaurant_name);
        }
        return chips;
    }
    function recordDetails(r, scope){
        const out=[];
        if(scope==='STAYS' && Array.isArray(r.room_types) && r.room_types.length){
            out.push('<div class=\"ai-subsection\"><strong>Room options</strong><ul>'+r.room_types.slice(0,3).map(x=>'<li>'+aiEscapeHtml(x.name||'Room')+(x.price_per_night!=null?' · ₹'+Number(x.price_per_night).toLocaleString()+'/night':'')+(x.max_guests?' · up to '+aiEscapeHtml(x.max_guests)+' guests':'')+'</li>').join('')+'</ul></div>');
        }
        if(scope==='PACKAGES' && Array.isArray(r.destinations) && r.destinations.length){
            out.push('<div class=\"ai-subsection\"><strong>Itinerary</strong><ul>'+r.destinations.slice(0,5).map(x=>'<li>Day '+aiEscapeHtml(x.day_number||'')+' · '+aiEscapeHtml(x.name||'Destination')+(x.city?', '+aiEscapeHtml(x.city):'')+'</li>').join('')+'</ul></div>');
        }
        if(scope==='GUIDES'){
            if(r.languages) out.push('<div class=\"ai-detail\"><strong>Languages:</strong> '+aiEscapeHtml(r.languages)+'</div>');
            if(r.specializations) out.push('<div class=\"ai-detail\"><strong>Specializes in:</strong> '+aiEscapeHtml(r.specializations)+'</div>');
        }
        if(scope==='EXPERIENCES' && r.meeting_point) out.push('<div class=\"ai-detail\"><strong>Meeting point:</strong> '+aiEscapeHtml(r.meeting_point)+'</div>');
        if(scope==='EVENTS' && r.ticket_info) out.push('<div class=\"ai-detail\"><strong>Tickets:</strong> '+aiEscapeHtml(r.ticket_info)+'</div>');
        if(scope==='RESTAURANTS' && Array.isArray(r.menu_items) && r.menu_items.length){
            const items=r.menu_items.slice(0,5).map(function(x){
                const path=x._ai && x._ai.member_path ? basePath+x._ai.member_path : '';
                const label=aiEscapeHtml(x.name || 'Menu item');
                const price=(x.price!==null && x.price!==undefined) ? '₹'+Number(x.price).toLocaleString() : '';
                return path
                    ? '<a class=\"ai-menu-link\" href=\"'+aiEscapeHtml(path)+'\" target=\"_blank\" rel=\"noopener\"><strong>'+label+'</strong><span class=\"ai-menu-price\">'+price+'</span></a>'
                    : '<div class=\"ai-menu-link\"><strong>'+label+'</strong><span class=\"ai-menu-price\">'+price+'</span></div>';
            }).join('');
            out.push('<div class=\"ai-subsection\"><strong>Matching menu items</strong><div class=\"ai-menu-list\">'+items+'</div></div>');
        }
        return out.join('');
    }

    function renderRecords(records, scope, target){
        target.innerHTML='';
        if(!Array.isArray(records) || !records.length) return;
        records.forEach(function(r){
            const a=r._ai || {};
            const path=a.member_path || '';
            const image=recordImage(r);
            const title=recordTitle(r,scope);
            const desc=r.description || r.bio || '';
            const location=recordLocation(r);
            const chips=recordMeta(r,scope);
            const article=document.createElement('article'); article.className='ai-record';
            const media=document.createElement('div'); media.className='ai-record-media';
            if(image){ const img=document.createElement('img'); img.src=image; img.alt=title; img.onerror=function(){this.remove(); media.textContent=scope==='DESTINATIONS'?'📍':scope==='STAYS'?'🏨':scope==='PACKAGES'?'🧳':scope==='GUIDES'?'🧭':scope==='EXPERIENCES'?'✨':scope==='EVENTS'?'🎉':scope==='RESTAURANTS'?'🍽️':'🍛';}; media.appendChild(img); }
            else media.textContent=scope==='DESTINATIONS'?'📍':scope==='STAYS'?'🏨':scope==='PACKAGES'?'🧳':scope==='GUIDES'?'🧭':scope==='EXPERIENCES'?'✨':scope==='EVENTS'?'🎉':scope==='RESTAURANTS'?'🍽️':'🍛';
            const body=document.createElement('div'); body.className='ai-record-body';
            const titleHtml=path ? '<h3 class="ai-record-title"><a href="'+aiEscapeHtml(basePath+path)+'" target="_blank" rel="noopener">'+aiEscapeHtml(title)+'</a></h3>' : '<h3 class="ai-record-title">'+aiEscapeHtml(title)+'</h3>';
            body.innerHTML=titleHtml+'<div class="ai-record-location">'+aiEscapeHtml(location || 'Manipur')+'</div><div class="ai-record-desc">'+aiEscapeHtml(desc)+'</div>'+recordDetails(r,scope);
            if(chips.length){ const meta=document.createElement('div'); meta.className='ai-record-meta'; chips.slice(0,4).forEach(c=>{const x=document.createElement('span');x.className='ai-chip';x.textContent=c;meta.appendChild(x)}); body.appendChild(meta); }
            const actions=document.createElement('div'); actions.className='ai-record-actions';
            if(path){ const view=document.createElement('a'); view.className='ai-record-btn primary'; view.href=basePath+path; view.target='_blank'; view.rel='noopener'; view.textContent=scope==='RESTAURANTS'?'Open Restaurant':scope==='RESTAURANT_MENU'?'Open Menu Item':'View in MemberApp'; actions.appendChild(view); }
            if(scope==='RESTAURANT_MENU' && r.restaurant_id){ const restaurant=document.createElement('a'); restaurant.className='ai-record-btn'; restaurant.href=basePath+'/member/restaurants/'+encodeURIComponent(r.restaurant_id); restaurant.target='_blank'; restaurant.rel='noopener'; restaurant.textContent='Open Restaurant'; actions.appendChild(restaurant); }
            if(scope!=='RESTAURANTS' && scope!=='RESTAURANT_MENU'){
                const trip=document.createElement('a'); trip.className='ai-record-btn'; trip.href=basePath+'/member/tourism/trips'; trip.target='_blank'; trip.rel='noopener'; trip.textContent='Open My Trip'; actions.appendChild(trip);
            }
            article.appendChild(media); article.appendChild(body); article.appendChild(actions); target.appendChild(article);
        });
    }

    form.addEventListener('submit', async function(ev){
        ev.preventDefault();
        success.style.display='none';
        button.disabled=true;
        progress.classList.add('show');
        status.textContent='Checking database records…';

        const data=new FormData(form);

        try{
            await new Promise(r=>setTimeout(r,180));
            status.textContent='Sending verified context to Gemini…';

            const response=await fetch(form.action,{
                method:'POST',
                body:data,
                headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
            });

            const json=await response.json();
            if(!json.success) throw new Error(json.error || 'AI request failed.');

            const d=json.data;
            latest.style.display='block';
            latestScope.textContent=d.scope || data.get('scope');
            latestAnswer.innerHTML=renderMarkdown(d.answer || '');
            latestMeta.textContent='Model: '+(d.model||'')+' · Response time: '+(d.latency_ms||0)+' ms · Database records used: '+(d.records_found||0);
            renderRecords(d.records || [], d.scope || data.get('scope'), latestRecords);
            success.textContent='Answer generated successfully using the selected ManipurApp records.';
            success.style.display='block';

            if(noHistory) noHistory.remove();
            const article=document.createElement('article');
            article.style.cssText='border-top:1px solid #eaecf0;padding-top:14px';
            article.innerHTML='<div class="ai-q">'+aiEscapeHtml(data.get('question'))+'</div>'+
                '<span class="ai-source" style="margin-top:7px">'+aiEscapeHtml(d.scope || data.get('scope'))+'</span>'+
                '<div class="ai-a">'+renderMarkdown(d.answer || '')+'</div>'+
                '<div class="ai-meta">'+new Date().toLocaleString()+' · '+aiEscapeHtml(d.model||'')+' · '+aiEscapeHtml(d.latency_ms||0)+' ms · '+aiEscapeHtml(d.records_found||0)+' records</div>';
            history.prepend(article);

            status.textContent='Database → Gemini → Answer';
        }catch(err){
            success.style.display='block';
            success.style.background='#fff4f4';
            success.style.borderColor='#f2b8b8';
            success.style.color='#9b1c1c';
            success.textContent=err.message || 'Unable to get an answer.';
            status.textContent='Request failed';
        }finally{
            progress.classList.remove('show');
            button.disabled=false;
        }
    });
})();
</script>

<script>
(function(){
    // Shared helpers for the Smart AI freeform panel. The legacy AI form above
    // has its own scoped helpers, so define these again in this independent IIFE.
    function aiEscapeHtml(value){
        return String(value ?? '').replace(/[&<>"']/g,function(c){
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c];
        });
    }
    function renderMarkdown(value){
        let text=String(value ?? '').replace(/\r\n?/g,'\n').trim();
        if(!text) return '';
        const lines=text.split('\n');
        let html='', list=null;
        function closeList(){ if(list){ html+='</'+list+'>'; list=null; } }
        function inline(v){
            let x=aiEscapeHtml(v);
            x=x.replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>');
            x=x.replace(/__([^_]+?)__/g,'<strong>$1</strong>');
            x=x.replace(/\*([^*\n]+?)\*/g,'<em>$1</em>');
            x=x.replace(/`([^`]+?)`/g,'<code>$1</code>');
            return x;
        }
        let paragraph=[];
        function flushParagraph(){
            if(paragraph.length){ html+='<p>'+paragraph.map(inline).join('<br>')+'</p>'; paragraph=[]; }
        }
        lines.forEach(function(line){
            const t=line.trim();
            if(!t){ flushParagraph(); closeList(); return; }
            if(/^---+$/.test(t) || /^\*\*\*+$/.test(t)){ flushParagraph(); closeList(); html+='<hr>'; return; }
            let m=t.match(/^###\s+(.+)$/);
            if(m){ flushParagraph(); closeList(); html+='<h3>'+inline(m[1])+'</h3>'; return; }
            m=t.match(/^####\s+(.+)$/);
            if(m){ flushParagraph(); closeList(); html+='<h4>'+inline(m[1])+'</h4>'; return; }
            m=t.match(/^[-*]\s+(.+)$/);
            if(m){ flushParagraph(); if(list!=='ul'){ closeList(); html+='<ul>'; list='ul'; } html+='<li>'+inline(m[1])+'</li>'; return; }
            m=t.match(/^\d+\.\s+(.+)$/);
            if(m){ flushParagraph(); if(list!=='ol'){ closeList(); html+='<ol>'; list='ol'; } html+='<li>'+inline(m[1])+'</li>'; return; }
            closeList(); paragraph.push(t);
        });
        flushParagraph(); closeList();
        return html;
    }
    const form=document.getElementById('aiFreeformForm');
    if(!form) return;
    const button=document.getElementById('aiFreeformButton');
    const status=document.getElementById('aiFreeformStatus');
    const elapsedEl=document.getElementById('aiFreeformElapsed');
    const result=document.getElementById('aiFreeformResult');
    const answer=document.getElementById('aiFreeformAnswer');
    const tools=document.getElementById('aiFreeformTools');
    const meta=document.getElementById('aiFreeformMeta');
    const diagnostics=document.getElementById('aiFreeformDiagnostics');
    const basePath=<?=json_encode(config('app.base_path'))?>;
    let timer=null, startedAt=0, rawAnswer='';
    let pollTimer=null, pollCompleted=false;

    function elapsed(){ return Math.floor((Date.now()-startedAt)/1000); }
    function stopPoll(){ if(pollTimer){ clearInterval(pollTimer); pollTimer=null; } }
    function applySavedAnswer(row){
        if(!row || !row.answer || pollCompleted) return false;
        pollCompleted=true;
        rawAnswer=String(row.answer||'');
        answer.innerHTML=renderMarkdown(rawAnswer);
        meta.textContent='Provider: '+(row.provider||'')+' · Model: '+(row.model||'')+' · Total response time: '+(row.latency_ms||0)+' ms · Tool records used: '+(row.records_found||0);
        status.textContent='Saved result found in database → answer displayed';
        addDiagnostic('success','db_poll','Saved answer found in database (ID '+(row.id||'unknown')+').');
        stopPoll();
        return true;
    }
    function startPoll(question,since){
        stopPoll();
        const endpoint=form.action.replace(/\/freeform$/,'/freeform/latest');
        const check=async function(){
            if(pollCompleted) return;
            try{
                const url=new URL(endpoint,window.location.href);
                url.searchParams.set('question',question);
                url.searchParams.set('since',since);
                const r=await fetch(url.toString(),{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},cache:'no-store'});
                if(!r.ok) return;
                const j=await r.json();
                if(j && j.success && j.data) applySavedAnswer(j.data);
            }catch(e){}
        };
        check();
        pollTimer=setInterval(check,1500);
    }

    function setStatus(message){ status.textContent=message; }
    function addDiagnostic(level,phase,message){
        if(!diagnostics) return;
        const line=document.createElement('div');
        line.className='ai-diag-'+(level||'info');
        const stamp=new Date().toLocaleTimeString();
        line.textContent='['+stamp+'] '+(phase ? phase+': ' : '')+String(message||'');
        diagnostics.appendChild(line);
        diagnostics.scrollTop=diagnostics.scrollHeight;
    }
    function startTimer(){
        startedAt=Date.now();
        clearInterval(timer);
        elapsedEl.textContent='0s';
        timer=setInterval(function(){ elapsedEl.textContent=elapsed()+'s'; },1000);
    }
    function stopTimer(){ clearInterval(timer); timer=null; }
    function addTool(name, state, records){
        const chip=document.createElement('span');
        chip.className='ai-tool-chip';
        chip.dataset.tool=name;
        chip.textContent=name+' · '+state+(records!==undefined ? ' · '+records+' records' : '');
        tools.appendChild(chip);
        return chip;
    }

    function renderSmartResults(results){
        const container=document.getElementById('aiFreeformResultCards');
        if(!container) return;
        container.innerHTML='';
        if(!Array.isArray(results) || !results.length) return;
        const sections=[];
        const esc=aiEscapeHtml;
        const pathFor=function(r){ return r && r._ai && r._ai.member_path ? basePath+r._ai.member_path : ''; };
        const card=function(r, fallbackTitle, icon){
            const title=esc(r.name||r.title||fallbackTitle||'Result');
            const path=pathFor(r);
            const loc=esc([r.city,r.district,r.state].filter(Boolean).join(', '));
            const price=(r.price!==undefined && r.price!==null && r.price!=='') ? '₹'+Number(r.price).toLocaleString() :
                (r.starting_price_per_night!==undefined && r.starting_price_per_night!==null ? 'From ₹'+Number(r.starting_price_per_night).toLocaleString()+'/night' : '');
            return '<article class="ai-record" style="padding:14px;display:flex;gap:12px;align-items:flex-start"><div style="font-size:25px">'+icon+'</div><div style="flex:1"><h3 class="ai-record-title" style="margin:0 0 5px">'+(path?'<a href="'+esc(path)+'" target="_blank" rel="noopener">'+title+'</a>':title)+'</h3>'+(loc?'<div class="ai-record-location">'+loc+'</div>':'')+(price?'<div class="ai-record-meta"><span class="ai-chip">'+esc(price)+'</span></div>':'')+(path?'<div style="margin-top:8px"><a class="ai-record-btn primary" href="'+esc(path)+'" target="_blank" rel="noopener">Open</a></div>':'')+'</div></article>';
        };
        results.forEach(function(entry){
            const name=String(entry.name||'');
            const data=entry.result||{};
            let items=[];
            let heading=name;
            let icon='🔎';
            if(name==='search_tourism'){
                icon='📍'; heading='Places, stays & tourism';
                Object.keys(data.records||{}).forEach(function(scope){
                    (data.records[scope]||[]).forEach(function(r){ items.push(card(r,scope.replace(/_/g,' '),'📍')); });
                });
            }else if(name==='search_restaurants'){
                icon='🍽️'; heading='Manipuri food & restaurants';
                (data.restaurants||[]).forEach(function(r){ items.push(card(r,'Restaurant','🍽️')); });
                (data.matching_menu_items||[]).slice(0,12).forEach(function(r){
                    const path=(r._ai&&r._ai.member_path)?basePath+r._ai.member_path:'';
                    const title=esc(r.name||'Menu item'); const price=r.price!==null&&r.price!==undefined?'₹'+Number(r.price).toLocaleString():'';
                    items.push('<article class="ai-record" style="padding:14px;display:flex;gap:12px;align-items:flex-start"><div style="font-size:25px">🍛</div><div style="flex:1"><h3 class="ai-record-title" style="margin:0 0 5px">'+(path?'<a href="'+esc(path)+'" target="_blank" rel="noopener">'+title+'</a>':title)+'</h3><div class="ai-record-location">'+esc(r.restaurant_name||'')+'</div>'+(price?'<div class="ai-record-meta"><span class="ai-chip">'+esc(price)+'</span></div>':'')+'</div></article>');
                });
            }else if(name==='search_taxi'){
                icon='🚕'; heading='Taxi services';
                (data.services||data.records||[]).forEach(function(r){ items.push(card(r,'Taxi service','🚕')); });
            }
            if(items.length) sections.push('<section style="margin-top:18px"><h3 style="margin:0 0 10px">'+esc(heading)+'</h3><div class="ai-records">'+items.join('')+'</div></section>');
        });
        container.innerHTML=sections.join('');
    }

    form.addEventListener('submit', async function(ev){
        ev.preventDefault();
        button.disabled=true;
        result.classList.add('show');
        answer.innerHTML='<p style="color:#667085">Preparing Smart AI…</p>';
        const resultCards=document.getElementById('aiFreeformResultCards');
        if(resultCards) resultCards.innerHTML='';
        tools.innerHTML='';
        meta.textContent='';
        rawAnswer='';
        if(diagnostics) diagnostics.innerHTML='';
        addDiagnostic('info','browser','Browser started Smart AI request.');
        startTimer();
        setStatus('Connecting to the configured AI provider…');
        const pollStartedAt=new Date();
        pollStartedAt.setSeconds(pollStartedAt.getSeconds()-1);
        const questionField=form.querySelector('[name=question]');
        startPoll(questionField ? questionField.value.trim() : '', pollStartedAt.toISOString().slice(0,19).replace('T',' '));

        try{
            const response=await fetch(form.action+'/stream',{
                method:'POST',
                body:new FormData(form),
                headers:{'X-Requested-With':'XMLHttpRequest','Accept':'text/event-stream'},
                cache:'no-store'
            });
            addDiagnostic(response.ok?'success':'error','http','HTTP response received: '+response.status+' '+response.statusText);
            if(!response.ok) throw new Error('Smart AI request failed ('+response.status+').');
            if(!response.body) throw new Error('Your browser does not support streaming Smart AI responses.');

            const reader=response.body.getReader();
            const decoder=new TextDecoder();
            let buffer='';

            const handleBlock=function(block){
                let eventName='message', data='';
                block.replace(/\r/g,'').split(/\n/).forEach(function(line){
                    if(line.indexOf('event:')===0) eventName=line.slice(6).trim();
                    else if(line.indexOf('data:')===0) data += line.slice(5).trim();
                });
                if(!data) return;
                let payload;
                try{ payload=JSON.parse(data); }catch(e){ return; }

                if(eventName==='status'){
                    setStatus(payload.message || 'Working…');
                }else if(eventName==='progress'){
                    if(payload.type==='diagnostic'){
                        addDiagnostic(payload.level||'info',payload.phase||'diagnostic',payload.message||'');
                    }else if(payload.type==='provider'){ setStatus(payload.message || ('Connected to '+(payload.provider||'AI provider')+'.')); }else if(payload.type==='final_received'){ setStatus(payload.message || 'AI provider returned the final answer.'); }else if(payload.type==='tool_selected'){
                        setStatus(payload.message || ('Gemini selected '+payload.name+'…'));
                        addTool(payload.name,'selected');
                    }else if(payload.type==='tool_running'){
                        setStatus(payload.message || ('Querying '+payload.name+'…'));
                        const chips=[...tools.querySelectorAll('.ai-tool-chip')];
                        const chip=chips.find(c=>c.dataset.tool===payload.name);
                        if(chip) chip.textContent=payload.name+' · querying live data…';
                    }else if(payload.type==='tool_completed'){
                        setStatus(payload.message || (payload.name+' data loaded.'));
                        const chips=[...tools.querySelectorAll('.ai-tool-chip')];
                        const chip=chips.find(c=>c.dataset.tool===payload.name);
                        if(chip) chip.textContent=payload.name+' · completed · '+(payload.records_found||0)+' records';
                        else addTool(payload.name,'completed',payload.records_found||0);
                    }else if(payload.type==='continue' || payload.type==='gemini_resume'){
                        setStatus(payload.message || 'Gemini is processing the live tool results…');
                    }else if(payload.type==='answer_delta'){
                        rawAnswer += payload.text || '';
                        answer.innerHTML='<div style="white-space:pre-wrap">'+aiEscapeHtml(rawAnswer)+'</div>';
                        setStatus('Gemini is writing the answer…');
                    }else if(payload.type==='tool_failed'){
                        setStatus(payload.message || ('Tool '+payload.name+' failed.'));
                    }else if(payload.type==='interaction_completed'){
                        if(payload.status==='requires_action') setStatus(payload.message || 'Gemini requested tool results…');
                    }
                }else if(eventName==='complete'){
                    const d=(payload.data||{});
                    rawAnswer=d.message||d.answer||rawAnswer;
                    answer.innerHTML=renderMarkdown(rawAnswer);
                    renderSmartResults(d.results||[]);
                    meta.textContent='Provider: '+(d.provider||'')+' · Model: '+(d.model||'')+' · Total response time: '+(d.latency_ms||0)+' ms · Tool records used: '+(d.records_found||0);
                    pollCompleted=true;
                    stopPoll();
                    status.textContent='Completed: '+(d.provider||'AI provider')+' → message + application tools → live database → results displayed';
                    addDiagnostic('success','complete','Final result delivered to browser.');
                }else if(eventName==='error'){
                    addDiagnostic('error','server_error',payload.error || 'Smart AI request failed.');
                    throw new Error(payload.error || 'Smart AI request failed.');
                }
            };

            while(true){
                const part=await reader.read();
                if(part.done){
                    buffer += decoder.decode();
                    break;
                }
                buffer += decoder.decode(part.value,{stream:true});
                let pos;
                while((pos=buffer.indexOf('\n\n'))!==-1){
                    const block=buffer.slice(0,pos);
                    buffer=buffer.slice(pos+2);
                    handleBlock(block);
                }
            }
            if(buffer.trim()) handleBlock(buffer);
            if(!rawAnswer && !status.textContent.includes('failed')){
                throw new Error('Smart AI stream ended before the final answer reached the page. Refreshing would show the saved answer, but the live response was not delivered to the browser.');
            }
        }catch(err){
            addDiagnostic('error','browser_exception',err.message || 'Unable to get an answer.');
            answer.innerHTML='<p style="color:#9b1c1c"><strong>Request failed:</strong> '+aiEscapeHtml(err.message || 'Unable to get an answer.')+'</p>';
            meta.textContent='';
            status.textContent='Smart AI request failed';
        }finally{
            stopTimer();
            button.disabled=false;
            if(!pollCompleted){
                setTimeout(function(){ if(!pollCompleted) stopPoll(); },30000);
            }
        }
    });
})();
</script>
