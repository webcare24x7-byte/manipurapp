<?php
declare(strict_types=1);
$memberAiBase = rtrim((string)config('app.base_path'), '/');
$memberAiScope = strtoupper((string)($aiScope ?? 'TOURISM'));
$memberAiContext = trim((string)($aiContext ?? ''));
$memberAiTriggerTitle = trim((string)($aiTriggerTitle ?? 'Ask about Manipur'));
$memberAiTriggerSubtitle = trim((string)($aiTriggerSubtitle ?? 'Get help from ManipurApp AI'));
$memberAiTriggerLabel = trim((string)($aiTriggerLabel ?? 'Ask AI →'));
$memberAiDialogTitle = trim((string)($aiDialogTitle ?? 'How can we help?'));
$memberAiDialogSubtitle = trim((string)($aiDialogSubtitle ?? 'Ask about Manipur using information available in ManipurApp.'));
$memberAiLoggedIn = !empty($_SESSION['memberapp_auth']['user_id']);
$memberAiHideTrigger = !empty($aiHideTrigger);
$memberAiCsrf = '';
if ($memberAiLoggedIn) {
    $memberAiCsrf = (new \App\Modules\MemberApp\Services\MemberCsrfService())->token();
}
$memberAiReturn = (string)($_SERVER['REQUEST_URI'] ?? ($memberAiBase . '/member'));
$memberAiReturnPath = parse_url($memberAiReturn, PHP_URL_PATH) ?: '/member';
if ($memberAiBase !== '' && ($memberAiReturnPath === $memberAiBase || str_starts_with($memberAiReturnPath, $memberAiBase . '/'))) {
    $memberAiReturnPath = substr($memberAiReturnPath, strlen($memberAiBase));
}
if ($memberAiReturnPath === '' || $memberAiReturnPath[0] !== '/') $memberAiReturnPath = '/' . ltrim($memberAiReturnPath, '/');
$memberAiReturnQuery = parse_url($memberAiReturn, PHP_URL_QUERY);
$memberAiReturn = $memberAiReturnPath . ($memberAiReturnQuery ? '?' . $memberAiReturnQuery : '');
?>
<div class="ma-ai-widget<?= $memberAiHideTrigger ? ' ma-ai-widget-hidden-trigger' : '' ?>" data-ai-ask
     data-ai-scope="<?=htmlspecialchars($memberAiScope, ENT_QUOTES, 'UTF-8')?>"
     data-ai-context="<?=htmlspecialchars($memberAiContext, ENT_QUOTES, 'UTF-8')?>"
     data-ai-endpoint="<?=htmlspecialchars($memberAiBase . '/member/ai/ask', ENT_QUOTES, 'UTF-8')?>">
    <button type="button" class="ma-ai-trigger" data-ai-open aria-haspopup="dialog" aria-controls="ma-ai-dialog">
        <span class="ma-ai-trigger-icon">✦</span>
        <span><small>MANIPURAPP AI</small><strong><?=htmlspecialchars($memberAiTriggerTitle, ENT_QUOTES, 'UTF-8')?></strong><em><?=htmlspecialchars($memberAiTriggerSubtitle, ENT_QUOTES, 'UTF-8')?></em></span>
        <b><?=htmlspecialchars($memberAiTriggerLabel, ENT_QUOTES, 'UTF-8')?></b>
    </button>

    <div class="ma-ai-overlay" data-ai-overlay hidden>
        <div class="ma-ai-dialog" id="ma-ai-dialog" role="dialog" aria-modal="true" aria-labelledby="ma-ai-title">
            <button type="button" class="ma-ai-close" data-ai-close aria-label="Close">×</button>
            <div class="ma-ai-dialog-head">
                <div class="ma-ai-dialog-icon">✦</div>
                <div><small>MANIPURAPP AI</small><h2 id="ma-ai-title"><?=htmlspecialchars($memberAiDialogTitle, ENT_QUOTES, 'UTF-8')?></h2><p><?=htmlspecialchars($memberAiDialogSubtitle, ENT_QUOTES, 'UTF-8')?></p></div>
            </div>

            <?php if ($memberAiLoggedIn): ?>
                <?php $memberAiPrompts = match ($memberAiScope) {
                    'RESTAURANTS', 'RESTAURANT_MENU' => ['What restaurants are good for Manipuri food?','Show vegetarian food under ₹300.','Which restaurants offer delivery?'],
                    'DESTINATIONS' => ['What are the best places to see?','How long should I stay?','What is the best time to visit?'],
                    'STAYS' => ['Find affordable stays.','Which stays are in this area?','Show stays under ₹3000 per night.'],
                    default => ['Help me plan a trip in Manipur.','What places should I visit?','What can I do in Manipur?'],
                }; ?>
                <div class="ma-ai-prompts" data-ai-prompts>
                    <?php foreach ($memberAiPrompts as $prompt): ?><button type="button" class="ma-ai-chip" data-ai-prompt="<?=htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8')?></button><?php endforeach; ?>
                </div>
                <form class="ma-ai-form" data-ai-form method="post" action="<?=htmlspecialchars($memberAiBase . '/member/ai/ask', ENT_QUOTES, 'UTF-8')?>">
                    <input type="hidden" name="_token" value="<?=htmlspecialchars($memberAiCsrf, ENT_QUOTES, 'UTF-8')?>">
                    <input type="hidden" name="scope" value="<?=htmlspecialchars($memberAiScope, ENT_QUOTES, 'UTF-8')?>">
                    <input type="text" name="question" maxlength="1000" autocomplete="off" placeholder="Ask anything about Manipur…" aria-label="Ask ManipurApp AI">
                    <button type="submit"><span>Ask AI</span><b>→</b></button>
                </form>
                <div class="ma-ai-status" data-ai-status hidden></div>
                <div class="ma-ai-result" data-ai-result hidden></div>
            <?php else: ?>
                <div class="ma-ai-guest">
                    <div class="ma-ai-guest-icon">💡</div>
                    <strong>Have a question about Manipur?</strong>
                    <p>Sign in to ask ManipurApp AI about destinations, stays, experiences, events, restaurants and menus.</p>
                    <a href="<?=htmlspecialchars($memberAiBase . '/member/login?return_to=' . rawurlencode($memberAiReturn), ENT_QUOTES, 'UTF-8')?>">Login to ask AI →</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.ma-ai-widget{position:relative;z-index:20}.ma-ai-widget-hidden-trigger .ma-ai-trigger{display:none}.ma-ai-trigger{width:100%;display:flex;align-items:center;gap:11px;text-align:left;border:1px solid #bfe1d5;border-radius:17px;padding:10px 12px;background:linear-gradient(135deg,#f2fbf7,#fff);color:#14342b;box-shadow:0 9px 24px rgba(8,82,64,.09);cursor:pointer}.ma-ai-trigger:hover{border-color:#83c5b2;transform:translateY(-1px)}.ma-ai-trigger-icon{width:38px;height:38px;display:grid;place-items:center;border-radius:13px;background:#087d64;color:#fff;font-size:18px;box-shadow:0 6px 16px rgba(8,125,100,.2);flex:none}.ma-ai-trigger span:nth-child(2){min-width:0;flex:1}.ma-ai-trigger small{display:block;color:#087d64;font-size:7px;font-weight:950;letter-spacing:1.2px}.ma-ai-trigger strong{display:block;font-size:13px;margin-top:2px}.ma-ai-trigger em{display:block;margin-top:2px;color:#71817a;font-size:8px;font-style:normal;line-height:1.25}.ma-ai-trigger>b{flex:none;padding:8px 11px;border-radius:10px;background:#087d64;color:#fff;font-size:9px;font-weight:950}.ma-ai-overlay{position:fixed;inset:0;z-index:9999;background:rgba(9,30,25,.54);backdrop-filter:blur(6px);display:grid;place-items:center;padding:18px}.ma-ai-overlay[hidden]{display:none}.ma-ai-dialog{position:relative;width:min(680px,100%);max-height:min(88vh,760px);overflow:auto;background:#f8fcfa;border:1px solid rgba(255,255,255,.7);border-radius:25px;box-shadow:0 28px 80px rgba(0,0,0,.25);padding:22px}.ma-ai-close{position:absolute;right:14px;top:12px;width:35px;height:35px;border:1px solid #dbe9e4;border-radius:50%;background:#fff;color:#526861;font-size:23px;line-height:1;cursor:pointer}.ma-ai-dialog-head{display:flex;gap:12px;align-items:center;padding-right:40px}.ma-ai-dialog-icon{width:48px;height:48px;border-radius:16px;display:grid;place-items:center;background:#087d64;color:#fff;font-size:21px}.ma-ai-dialog-head small{font-size:8px;font-weight:950;letter-spacing:1.3px;color:#087d64}.ma-ai-dialog-head h2{margin:2px 0;font-size:23px;letter-spacing:-.5px}.ma-ai-dialog-head p{margin:0;color:#74847e;font-size:10px;line-height:1.45}.ma-ai-prompts{display:flex;gap:7px;overflow:auto;padding:16px 0 10px;scrollbar-width:none}.ma-ai-prompts::-webkit-scrollbar{display:none}.ma-ai-chip{white-space:nowrap;border:1px solid #d3e5de;background:#fff;border-radius:999px;padding:8px 10px;color:#35584d;font-size:9px;font-weight:800;cursor:pointer}.ma-ai-form{display:flex;gap:8px;margin-top:4px}.ma-ai-form input{min-width:0;flex:1;border:1px solid #cddfd8;border-radius:13px;background:#fff;padding:13px 13px;font:inherit;font-size:12px;outline:0;color:#17342b}.ma-ai-form input:focus{border-color:#70b9a6;box-shadow:0 0 0 3px rgba(8,125,100,.08)}.ma-ai-form button{border:0;border-radius:13px;background:#087d64;color:#fff;padding:0 17px;font-size:10px;font-weight:950;cursor:pointer}.ma-ai-form button b{font-size:14px;margin-left:3px}.ma-ai-status{font-size:10px;margin-top:9px}.ma-ai-status.loading{color:#087d64}.ma-ai-status.error{color:#a34b43}.ma-ai-result{margin-top:12px;padding:16px;border-radius:15px;background:#fff;border:1px solid #dceae5;font-size:11px;line-height:1.6;color:#354a43}.ma-ai-answer{white-space:normal}.ma-ai-source-title{font-size:8px;font-weight:950;text-transform:uppercase;letter-spacing:.7px;color:#75847e;margin-top:14px;margin-bottom:7px}.ma-ai-sources{display:flex;flex-direction:column;gap:6px}.ma-ai-source{display:flex;align-items:center;justify-content:space-between;gap:8px;text-decoration:none;color:#145f4f;background:#f2f8f5;border-radius:10px;padding:9px 10px;font-size:10px;font-weight:850}.ma-ai-source span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.ma-ai-source b{font-size:12px;flex:none}.ma-ai-guest{margin-top:18px;padding:22px;border:1px solid #dceae5;border-radius:16px;background:#fff;text-align:center}.ma-ai-guest-icon{font-size:28px}.ma-ai-guest strong{display:block;margin-top:8px;font-size:15px}.ma-ai-guest p{margin:6px auto 15px;max-width:470px;color:#728079;font-size:10px;line-height:1.5}.ma-ai-guest a{display:inline-flex;text-decoration:none;background:#087d64;color:#fff;border-radius:11px;padding:10px 14px;font-size:10px;font-weight:900}
@media(min-width:900px){.ma-ai-widget{margin:12px 0 18px}.ma-ai-trigger{max-width:760px;margin:0 auto}.ma-ai-trigger strong{font-size:14px}.ma-ai-trigger em{font-size:9px}.ma-ai-trigger>b{font-size:10px;padding:9px 13px}}
@media(max-width:600px){.ma-ai-widget{margin:10px 0 14px}.ma-ai-trigger{padding:9px 10px}.ma-ai-trigger-icon{width:35px;height:35px}.ma-ai-trigger strong{font-size:11px}.ma-ai-trigger em{font-size:7px}.ma-ai-trigger>b{padding:7px 9px;font-size:8px}.ma-ai-dialog{padding:18px;border-radius:21px}.ma-ai-dialog-head h2{font-size:20px}.ma-ai-form button span{display:none}.ma-ai-form button{width:47px;padding:0}.ma-ai-overlay{padding:10px}.ma-ai-dialog{max-height:92vh}}
</style>

<script>
(function(){
    const root = document.querySelector('[data-ai-ask]');
    if (!root || root.dataset.aiBound === '1') return;
    root.dataset.aiBound = '1';
    const overlay=root.querySelector('[data-ai-overlay]');
    const openButtons=[...document.querySelectorAll('[data-ai-open]')];
    const close=root.querySelector('[data-ai-close]');
    const form=root.querySelector('[data-ai-form]');
    const input=form?.querySelector('input[name="question"]');
    const result=root.querySelector('[data-ai-result]');
    const status=root.querySelector('[data-ai-status]');
    const endpoint=root.dataset.aiEndpoint, scope=root.dataset.aiScope||'TOURISM', context=root.dataset.aiContext||'';
    const base=<?=json_encode($memberAiBase)?>;
    const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    const answer=v=>esc(v).replace(/\n/g,'<br>');
    const show=()=>{if(!overlay)return;overlay.hidden=false;document.body.classList.add('ma-ai-open');setTimeout(()=>input?.focus(),30)};
    const hide=()=>{if(!overlay)return;overlay.hidden=true;document.body.classList.remove('ma-ai-open')};
    openButtons.forEach(btn=>btn.addEventListener('click',show));
    close?.addEventListener('click',hide);
    overlay?.addEventListener('click',e=>{if(e.target===overlay)hide()});
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!overlay?.hidden)hide()});
    function showResult(data){
        if(!result)return;
        const text=data?.answer||'I could not find an answer for that question.'; const records=Array.isArray(data?.records)?data.records:[]; let html='<div class="ma-ai-answer">'+answer(text)+'</div>'; const links=[];
        records.forEach(row=>{const ai=row&&row._ai?row._ai:{};const path=ai.member_path||'';if(!path||links.some(x=>x.path===path))return;links.push({path,label:row.name||row.title||row.full_name||row.business_name||'Open result'})});
        if(links.length){html+='<div class="ma-ai-source-title">Related results</div><div class="ma-ai-sources">';links.slice(0,8).forEach(x=>html+='<a class="ma-ai-source" href="'+esc(base+x.path)+'"><span>'+esc(x.label)+'</span><b>→</b></a>');html+='</div>'}
        result.innerHTML=html;result.hidden=false;
    }
    async function ask(q){
        if(!form||!input){show();return;}
        q=String(q||'').trim();if(!q){input.focus();return}
        show();status.hidden=false;status.className='ma-ai-status loading';status.textContent='Thinking…';result.hidden=true;
        const body=new FormData(form);body.set('question',context?(context+'. '+q):q);body.set('scope',scope);
        try{const r=await fetch(endpoint,{method:'POST',body,credentials:'same-origin',headers:{'X-Requested-With':'XMLHttpRequest'}});const d=await r.json();if(!r.ok||!d.success)throw new Error(d.message||'Unable to answer right now.');status.hidden=true;showResult(d.data||{})}
        catch(e){status.className='ma-ai-status error';status.hidden=false;status.textContent=e.message||'Unable to answer right now.'}
    }
    form?.addEventListener('submit',e=>{e.preventDefault();ask(input.value)});
    root.querySelectorAll('[data-ai-prompt]').forEach(b=>b.addEventListener('click',()=>{input.value=b.dataset.aiPrompt||'';ask(input.value)}));
})();
</script>
