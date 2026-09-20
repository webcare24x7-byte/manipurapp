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
@media(max-width:1050px){.ai-records{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:650px){.ai-records{grid-template-columns:1fr}}
@media(max-width:900px){.ai-hero{grid-template-columns:1fr}.ai-fields{grid-template-columns:1fr}}
</style>

<div class="page-header">
    <div>
        <h1>ManipurApp AI</h1>
        <p>Ask about Tourism or Restaurants using live tenant records first, then let Gemini explain and organize the results.</p>
    </div>
</div>

<div class="ai-wrap">
    <?php if($error): ?><div class="ai-error"><b>AI module:</b> <?=$e($error)?></div><?php endif; ?>

    <div class="ai-hero">
        <section class="ai-card">
            <h2 class="ai-title">Ask ManipurApp AI</h2>
            <p class="ai-muted">Choose what you are asking about. ManipurApp checks the selected live records first, then sends the relevant data to Gemini.</p>

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
                <li>Gemini explains the data and answers the question.</li>
                <li>The Q&A is saved to AI history.</li>
            </ol>
            <p class="ai-muted">Gemini is not allowed to invent live ManipurApp records.</p>
        </section>
    </div>

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

    function esc(value){
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
            let x=esc(v);
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
            out.push('<div class=\"ai-subsection\"><strong>Room options</strong><ul>'+r.room_types.slice(0,3).map(x=>'<li>'+esc(x.name||'Room')+(x.price_per_night!=null?' · ₹'+Number(x.price_per_night).toLocaleString()+'/night':'')+(x.max_guests?' · up to '+esc(x.max_guests)+' guests':'')+'</li>').join('')+'</ul></div>');
        }
        if(scope==='PACKAGES' && Array.isArray(r.destinations) && r.destinations.length){
            out.push('<div class=\"ai-subsection\"><strong>Itinerary</strong><ul>'+r.destinations.slice(0,5).map(x=>'<li>Day '+esc(x.day_number||'')+' · '+esc(x.name||'Destination')+(x.city?', '+esc(x.city):'')+'</li>').join('')+'</ul></div>');
        }
        if(scope==='GUIDES'){
            if(r.languages) out.push('<div class=\"ai-detail\"><strong>Languages:</strong> '+esc(r.languages)+'</div>');
            if(r.specializations) out.push('<div class=\"ai-detail\"><strong>Specializes in:</strong> '+esc(r.specializations)+'</div>');
        }
        if(scope==='EXPERIENCES' && r.meeting_point) out.push('<div class=\"ai-detail\"><strong>Meeting point:</strong> '+esc(r.meeting_point)+'</div>');
        if(scope==='EVENTS' && r.ticket_info) out.push('<div class=\"ai-detail\"><strong>Tickets:</strong> '+esc(r.ticket_info)+'</div>');
        if(scope==='RESTAURANTS' && Array.isArray(r.menu_items) && r.menu_items.length){
            const items=r.menu_items.slice(0,5).map(function(x){
                const path=x._ai && x._ai.member_path ? basePath+x._ai.member_path : '';
                const label=esc(x.name || 'Menu item');
                const price=(x.price!==null && x.price!==undefined) ? '₹'+Number(x.price).toLocaleString() : '';
                return path
                    ? '<a class=\"ai-menu-link\" href=\"'+esc(path)+'\" target=\"_blank\" rel=\"noopener\"><strong>'+label+'</strong><span class=\"ai-menu-price\">'+price+'</span></a>'
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
            const titleHtml=path ? '<h3 class="ai-record-title"><a href="'+esc(basePath+path)+'" target="_blank" rel="noopener">'+esc(title)+'</a></h3>' : '<h3 class="ai-record-title">'+esc(title)+'</h3>';
            body.innerHTML=titleHtml+'<div class="ai-record-location">'+esc(location || 'Manipur')+'</div><div class="ai-record-desc">'+esc(desc)+'</div>'+recordDetails(r,scope);
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
            article.innerHTML='<div class="ai-q">'+esc(data.get('question'))+'</div>'+
                '<span class="ai-source" style="margin-top:7px">'+esc(d.scope || data.get('scope'))+'</span>'+
                '<div class="ai-a">'+renderMarkdown(d.answer || '')+'</div>'+
                '<div class="ai-meta">'+new Date().toLocaleString()+' · '+esc(d.model||'')+' · '+esc(d.latency_ms||0)+' ms · '+esc(d.records_found||0)+' records</div>';
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
