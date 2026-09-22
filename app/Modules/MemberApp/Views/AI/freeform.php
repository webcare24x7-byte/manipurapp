<?php
declare(strict_types=1);
$memberAiBase = rtrim((string) config('app.base_path'), '/');
$memberAiCsrf = (string)($csrf ?? '');
$isMemberLoggedIn = is_array($member ?? null);
?>

<style>
.ma-smart-ai-overlay{position:fixed;inset:0;z-index:99990;display:grid;place-items:center;padding:18px;background:rgba(8,25,20,.62);backdrop-filter:blur(8px)}
.ma-smart-ai-overlay[hidden]{display:none}
.ma-smart-ai-dialog{width:min(900px,100%);max-height:min(88vh,820px);overflow:auto;border:1px solid #dceae5;border-radius:25px;background:#f8fbfa;color:#10241f;box-shadow:0 30px 90px rgba(0,0,0,.24)}
.ma-smart-ai-head{position:sticky;top:0;z-index:2;display:flex;align-items:flex-start;gap:14px;padding:20px 21px;border-bottom:1px solid #e2ebe7;background:rgba(255,255,255,.96);backdrop-filter:blur(12px)}
.ma-smart-ai-icon{display:grid;place-items:center;width:44px;height:44px;border-radius:14px;background:linear-gradient(145deg,#0c9677,#075743);color:#fff;font-size:21px;flex:none;box-shadow:0 8px 20px rgba(8,125,100,.18)}
.ma-smart-ai-head-copy{min-width:0;flex:1}.ma-smart-ai-head-copy small{display:block;color:#087d64;font-size:8px;font-weight:950;letter-spacing:1px}.ma-smart-ai-head-copy h2{margin:3px 0 3px;font-size:21px;letter-spacing:-.55px}.ma-smart-ai-head-copy p{margin:0;color:#71817a;font-size:10px;line-height:1.45}.ma-smart-ai-close{width:34px;height:34px;border:1px solid #dbe6e2;border-radius:50%;background:#fff;color:#50655e;font-size:20px;line-height:1;cursor:pointer}
.ma-smart-ai-body{padding:20px}.ma-smart-ai-form{display:grid;gap:12px}.ma-smart-ai-question{width:100%;min-height:118px;resize:vertical;border:1px solid #cfded8;border-radius:16px;padding:15px;background:#fff;color:#10241f;font:inherit;font-size:13px;line-height:1.5;outline:none;box-shadow:0 7px 20px rgba(20,65,53,.035)}.ma-smart-ai-question:focus{border-color:#72b8a5;box-shadow:0 0 0 4px rgba(8,125,100,.08)}
.ma-smart-ai-suggestions{display:flex;flex-wrap:wrap;gap:7px}.ma-smart-ai-suggestion{border:1px solid #d7e5df;border-radius:999px;background:#fff;color:#46625a;padding:7px 10px;font-size:9px;font-weight:800;cursor:pointer}.ma-smart-ai-suggestion:hover{border-color:#9bcbbd;color:#087d64}
.ma-smart-ai-actions{display:flex;align-items:center;gap:10px;justify-content:space-between}.ma-smart-ai-status{color:#71817a;font-size:9px;line-height:1.4}.ma-smart-ai-submit{border:0;border-radius:11px;padding:10px 15px;background:#087d64;color:#fff;font-size:10px;font-weight:950;cursor:pointer}.ma-smart-ai-submit:disabled{opacity:.55;cursor:wait}
.ma-smart-ai-result{margin-top:18px}.ma-smart-ai-result[hidden]{display:none}.ma-smart-ai-message{padding:15px 16px;border-radius:16px;background:#eaf7f2;border:1px solid #cce8dd;color:#174d40;font-size:12px;line-height:1.55}.ma-smart-ai-meta{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}.ma-smart-ai-chip{padding:5px 8px;border-radius:999px;background:#eef3f1;color:#5a7069;font-size:8px;font-weight:850}.ma-smart-ai-results-title{margin:17px 0 9px;font-size:10px;font-weight:950;letter-spacing:.7px;text-transform:uppercase;color:#73837d}.ma-smart-ai-groups{display:grid;gap:15px}.ma-smart-ai-group{border:1px solid #dfeae5;border-radius:18px;background:#fff;padding:12px;box-shadow:0 7px 20px rgba(20,65,53,.035)}.ma-smart-ai-group-head{display:flex;align-items:center;gap:9px;margin-bottom:9px}.ma-smart-ai-group-icon{display:grid;place-items:center;width:32px;height:32px;border-radius:10px;background:#edf7f3;color:#087d64;font-size:15px;flex:none}.ma-smart-ai-group-head-copy{min-width:0;flex:1}.ma-smart-ai-group-head-copy strong{display:block;font-size:11px;color:#18362e}.ma-smart-ai-group-head-copy small{display:block;margin-top:2px;color:#7a8984;font-size:8px}.ma-smart-ai-group-count{padding:4px 7px;border-radius:999px;background:#f1f6f4;color:#60746d;font-size:8px;font-weight:900}.ma-smart-ai-results{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.ma-smart-ai-card{display:flex;align-items:center;gap:9px;min-width:0;padding:11px;border:1px solid #e5ece9;border-radius:13px;background:#fbfdfc;text-decoration:none;color:#18362e;transition:.15s ease}.ma-smart-ai-card:hover{border-color:#a8cec1;transform:translateY(-1px);background:#fff}.ma-smart-ai-card-icon{display:grid;place-items:center;width:33px;height:33px;border-radius:10px;background:#edf7f3;color:#087d64;flex:none}.ma-smart-ai-card-copy{min-width:0;flex:1}.ma-smart-ai-card-copy strong{display:block;font-size:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ma-smart-ai-card-copy small{display:block;margin-top:3px;color:#71817a;font-size:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ma-smart-ai-card-arrow{font-size:14px;color:#087d64;flex:none}
.ma-smart-ai-login{padding:24px;text-align:center;border:1px solid #dceae5;border-radius:18px;background:#fff}.ma-smart-ai-login .big{font-size:28px}.ma-smart-ai-login h3{margin:7px 0 5px;font-size:17px}.ma-smart-ai-login p{margin:0 auto 14px;max-width:480px;color:#71817a;font-size:10px;line-height:1.5}.ma-smart-ai-login a{display:inline-flex;padding:10px 14px;border-radius:11px;background:#087d64;color:#fff;text-decoration:none;font-size:10px;font-weight:900}
.ma-smart-ai-error{margin-top:12px;padding:11px 13px;border:1px solid #fecaca;border-radius:12px;background:#fff1f2;color:#9f1239;font-size:10px;line-height:1.45}.ma-smart-ai-error[hidden]{display:none}
@media(max-width:650px){.ma-smart-ai-overlay{padding:9px}.ma-smart-ai-dialog{max-height:94vh;border-radius:22px}.ma-smart-ai-head{padding:16px}.ma-smart-ai-body{padding:14px}.ma-smart-ai-head-copy h2{font-size:18px}.ma-smart-ai-results{grid-template-columns:1fr}.ma-smart-ai-actions{align-items:stretch}.ma-smart-ai-submit{padding:10px 12px}}
</style>

<div class="ma-smart-ai-overlay" id="ma-smart-ai-overlay" data-smart-ai-overlay hidden>
    <section class="ma-smart-ai-dialog" role="dialog" aria-modal="true" aria-labelledby="ma-smart-ai-title">
        <header class="ma-smart-ai-head">
            <span class="ma-smart-ai-icon">✦</span>
            <div class="ma-smart-ai-head-copy">
                <small>MANIPURAPP SMART AI</small>
                <h2 id="ma-smart-ai-title">Ask anything about ManipurApp</h2>
                <p>Ask a natural question. Smart AI selects the relevant ManipurApp tools, while PHP retrieves the verified live results.</p>
            </div>
            <button type="button" class="ma-smart-ai-close" data-smart-ai-close aria-label="Close">×</button>
        </header>

        <div class="ma-smart-ai-body">
            <?php if (!$isMemberLoggedIn): ?>
                <div class="ma-smart-ai-login">
                    <div class="big">✦</div>
                    <h3>Login to use Smart AI</h3>
                    <p>Smart AI questions are linked to your MemberApp account so your questions and results can be handled safely.</p>
                    <a href="<?= htmlspecialchars($memberAiBase . '/member/login?return_to=' . rawurlencode('/member'), ENT_QUOTES, 'UTF-8') ?>">Login to ManipurApp →</a>
                </div>
            <?php else: ?>
                <form class="ma-smart-ai-form" data-smart-ai-form action="<?= htmlspecialchars($memberAiBase . '/member/ai/freeform', ENT_QUOTES, 'UTF-8') ?>" method="post">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($memberAiCsrf, ENT_QUOTES, 'UTF-8') ?>">
                    <textarea class="ma-smart-ai-question" name="question" maxlength="4000" required data-smart-ai-question placeholder="Example: I have 3 days in Manipur with my family. What places should we visit, where can we eat Manipuri food, and what taxi service could we use?"></textarea>
                    <div class="ma-smart-ai-suggestions">
                        <button type="button" class="ma-smart-ai-suggestion" data-smart-ai-prompt="Find vegetarian restaurants in Imphal.">Vegetarian restaurants</button>
                        <button type="button" class="ma-smart-ai-suggestion" data-smart-ai-prompt="I am visiting Manipur for 3 days. Suggest places to visit and where to eat.">3-day trip</button>
                        <button type="button" class="ma-smart-ai-suggestion" data-smart-ai-prompt="Find suitable tourism stays in Ukhrul.">Stays in Ukhrul</button>
                        <button type="button" class="ma-smart-ai-suggestion" data-smart-ai-prompt="I need a taxi for airport transfer in Imphal.">Airport taxi</button>
                    </div>
                    <div class="ma-smart-ai-actions">
                        <span class="ma-smart-ai-status" data-smart-ai-status>One AI request → PHP tools → verified results</span>
                        <button class="ma-smart-ai-submit" type="submit" data-smart-ai-submit>Ask Smart AI →</button>
                    </div>
                </form>

                <div class="ma-smart-ai-error" data-smart-ai-error hidden></div>
                <div class="ma-smart-ai-result" data-smart-ai-result hidden>
                    <div class="ma-smart-ai-message" data-smart-ai-message></div>
                    <div class="ma-smart-ai-meta" data-smart-ai-meta></div>
                    <div class="ma-smart-ai-results-title" data-smart-ai-results-title hidden>Verified ManipurApp results</div>
                    <div class="ma-smart-ai-groups" data-smart-ai-groups></div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
(function(){
    const overlay=document.querySelector('[data-smart-ai-overlay]');
    if(!overlay || overlay.dataset.bound==='1') return;
    overlay.dataset.bound='1';
    const triggers=[...document.querySelectorAll('[data-ai-open]')];
    const close=overlay.querySelector('[data-smart-ai-close]');
    const form=overlay.querySelector('[data-smart-ai-form]');
    const input=overlay.querySelector('[data-smart-ai-question]');
    const submit=overlay.querySelector('[data-smart-ai-submit]');
    const status=overlay.querySelector('[data-smart-ai-status]');
    const error=overlay.querySelector('[data-smart-ai-error]');
    const result=overlay.querySelector('[data-smart-ai-result]');
    const message=overlay.querySelector('[data-smart-ai-message]');
    const meta=overlay.querySelector('[data-smart-ai-meta]');
    const groups=overlay.querySelector('[data-smart-ai-groups]');
    const resultsTitle=overlay.querySelector('[data-smart-ai-results-title]');
    const base=<?=json_encode($memberAiBase)?>;
    const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    const open=()=>{overlay.hidden=false;document.body.classList.add('ma-smart-ai-open');setTimeout(()=>input?.focus(),30)};
    const hide=()=>{overlay.hidden=true;document.body.classList.remove('ma-smart-ai-open')};
    triggers.forEach(t=>{t.addEventListener('click',open);t.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();open()}})});
    if(window.location.hash==='#ask-ai'){setTimeout(open,40);}
    close?.addEventListener('click',hide);overlay.addEventListener('click',e=>{if(e.target===overlay)hide()});document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!overlay.hidden)hide()});
    overlay.querySelectorAll('[data-smart-ai-prompt]').forEach(b=>b.addEventListener('click',()=>{if(input){input.value=b.dataset.smartAiPrompt||'';input.focus()}}));

    const groupMeta={
        DESTINATIONS:{label:'Places & Destinations',icon:'📍',desc:'Places you can visit'},
        STAYS:{label:'Hotels, Stays & Homestays',icon:'🏨',desc:'Places to stay'},
        PACKAGES:{label:'Tour Packages',icon:'🧳',desc:'Ready-made travel packages'},
        GUIDES:{label:'Local Guides',icon:'🧑‍🏫',desc:'Local guide services'},
        EXPERIENCES:{label:'Experiences',icon:'✨',desc:'Things to do'},
        EVENTS:{label:'Events & Festivals',icon:'🎉',desc:'Events and festivals'},
        RESTAURANTS:{label:'Restaurants',icon:'🍛',desc:'Restaurants and food options'},
        RESTAURANT_MENU:{label:'Food & Menu Items',icon:'🍽️',desc:'Matching dishes and menu items'},
        TAXI_SERVICE:{label:'Taxi & Vehicles',icon:'🚕',desc:'Taxi services and vehicles'},
        COMMERCIAL_RENTAL:{label:'Commercial Vehicles',icon:'🚚',desc:'Commercial rental options'},
        MARKETPLACE:{label:'Marketplace',icon:'🛍️',desc:'Marketplace results'},
        DEFAULT:{label:'Other Results',icon:'📌',desc:'Additional ManipurApp results'}
    };
    const groupKey=row=>{const ai=row&&row._ai&&typeof row._ai==='object'?row._ai:{};const type=String(ai.record_type||'').toUpperCase();return groupMeta[type]?type:'DEFAULT'};
    const labelFor=row=>row?.name||row?.title||row?.full_name||row?.business_name||row?.restaurant_name||row?.service_name||'ManipurApp result';
    const detailFor=row=>{
        const parts=[];
        if(row?.restaurant_name && row?.name) parts.push(String(row.restaurant_name));
        const loc=row?.district||row?.city||row?.state||row?.location||row?.category_name||row?.service_type||'';
        if(loc) parts.push(String(loc));
        if(row?.price!==undefined&&row?.price!==null&&row?.price!=='') parts.push('₹'+row.price);
        if(row?.starting_price_per_night!==undefined&&row?.starting_price_per_night!==null&&row?.starting_price_per_night!=='') parts.push('From ₹'+row.starting_price_per_night+'/night');
        if(row?.daily_rate!==undefined&&row?.daily_rate!==null&&row?.daily_rate!=='') parts.push('₹'+row.daily_rate+'/day');
        if(row?.seating_capacity) parts.push(row.seating_capacity+' seats');
        return parts.filter(Boolean).join(' · ');
    };
    const iconFor=row=>{const type=String((row?._ai||{}).record_type||'').toUpperCase();return groupMeta[type]?.icon||'📌'};
    function render(data){
        if(!result)return;
        const text=data?.message||data?.answer||'Here are the relevant ManipurApp results.';
        message.innerHTML=esc(text).replace(/\n/g,'<br>');
        const calls=Array.isArray(data?.tool_calls)?data.tool_calls:[];
        meta.innerHTML='';
        if(data?.provider)meta.innerHTML+='<span class="ma-smart-ai-chip">'+esc(data.provider)+'</span>';
        if(data?.model)meta.innerHTML+='<span class="ma-smart-ai-chip">'+esc(data.model)+'</span>';
        if(calls.length)meta.innerHTML+='<span class="ma-smart-ai-chip">'+calls.length+' tool'+(calls.length===1?'':'s')+' used</span>';
        if(data?.records_found!==undefined)meta.innerHTML+='<span class="ma-smart-ai-chip">'+esc(data.records_found)+' verified results</span>';

        groups.innerHTML='';
        const rows=Array.isArray(data?.records)?data.records:[];
        const buckets={};
        const order=['DESTINATIONS','STAYS','PACKAGES','GUIDES','EXPERIENCES','EVENTS','RESTAURANTS','RESTAURANT_MENU','TAXI_SERVICE','COMMERCIAL_RENTAL','MARKETPLACE','DEFAULT'];
        rows.forEach(row=>{
            if(!row||typeof row!=='object')return;
            const ai=row._ai&&typeof row._ai==='object'?row._ai:{};
            const path=String(ai.member_path||'');
            if(!path)return;
            const key=path;
            const group=groupKey(row);
            buckets[group] ||= [];
            if(buckets[group].some(x=>x.key===key))return;
            buckets[group].push({row,key});
        });
        let groupCount=0;
        order.forEach(type=>{
            const items=buckets[type]||[];
            if(!items.length)return;
            const metaInfo=groupMeta[type]||groupMeta.DEFAULT;
            const section=document.createElement('section');
            section.className='ma-smart-ai-group';
            section.innerHTML='<div class="ma-smart-ai-group-head"><span class="ma-smart-ai-group-icon">'+metaInfo.icon+'</span><span class="ma-smart-ai-group-head-copy"><strong>'+esc(metaInfo.label)+'</strong><small>'+esc(metaInfo.desc)+'</small></span><span class="ma-smart-ai-group-count">'+items.length+'</span></div><div class="ma-smart-ai-results"></div>';
            const list=section.querySelector('.ma-smart-ai-results');
            items.slice(0,12).forEach(({row,key})=>{
                const ai=row._ai&&typeof row._ai==='object'?row._ai:{};
                const path=String(ai.member_path||'');
                const card=document.createElement('a');
                card.className='ma-smart-ai-card';
                card.href=base+path;
                card.setAttribute('data-result-key',key);
                card.innerHTML='<span class="ma-smart-ai-card-icon">'+iconFor(row)+'</span><span class="ma-smart-ai-card-copy"><strong>'+esc(labelFor(row))+'</strong><small>'+esc(detailFor(row)||'Open verified result')+'</small></span><span class="ma-smart-ai-card-arrow">→</span>';
                list.appendChild(card);
            });
            groups.appendChild(section);
            groupCount++;
        });
        resultsTitle.hidden=groupCount===0;
        result.hidden=false;
    }
    form?.addEventListener('submit',async e=>{e.preventDefault();const q=input?.value.trim()||'';if(!q){input?.focus();return}submit.disabled=true;error.hidden=true;result.hidden=true;status.textContent='Thinking and checking live ManipurApp data…';try{const body=new FormData(form);const r=await fetch(form.action,{method:'POST',body,credentials:'same-origin',headers:{'X-Requested-With':'XMLHttpRequest',Accept:'application/json'}});const d=await r.json().catch(()=>({}));if(r.status===401&&d.login_required){window.location.href=base+'/member/login?return_to='+encodeURIComponent('/member');return}if(!r.ok||!d.success)throw new Error(d.message||d.error||'Unable to answer right now.');render(d.data||{});status.textContent='Answer ready — results are from live ManipurApp data.'}catch(ex){error.textContent=ex.message||'Unable to answer right now.';error.hidden=false;status.textContent='Please try again.'}finally{submit.disabled=false}});
})();
</script>
