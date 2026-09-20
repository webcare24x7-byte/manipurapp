<?php
declare(strict_types=1);
$mnwBase = rtrim((string) config('app.base_path'), '/');
$mnwAuth = $_SESSION['memberapp_auth'] ?? null;
$mnwLoggedIn = is_array($mnwAuth) && !empty($mnwAuth['member_id']);
?>
<?php if ($mnwLoggedIn): ?>
<style>
.ma-global-notification{position:fixed;right:14px;top:14px;z-index:70;width:39px;height:39px;border-radius:50%;display:grid;place-items:center;background:#fff;color:#173f35;border:1px solid #dce9e4;box-shadow:0 7px 20px rgba(15,60,45,.12);text-decoration:none}.ma-global-notification svg{width:20px;height:20px}.ma-global-notification-badge{position:absolute;right:-3px;top:-4px;min-width:18px;height:18px;padding:0 4px;border-radius:99px;background:#ef5b4d;color:#fff;border:2px solid #fff;display:grid;place-items:center;font-size:8px;font-weight:950;line-height:1}.ma-notification-toast{position:fixed;right:14px;top:61px;width:min(350px,calc(100vw - 28px));z-index:69;background:#fff;border:1px solid #dce9e4;border-radius:13px;box-shadow:0 10px 28px rgba(15,60,45,.16);padding:10px;display:none;text-decoration:none;color:#173f35}.ma-notification-toast.show{display:block;animation:maNotifIn .22s ease-out}.ma-notification-toast-title{font-size:11px;font-weight:900}.ma-notification-toast-message{font-size:9px;color:#62756d;line-height:1.4;margin-top:2px}.ma-notification-toast-icon{float:left;width:27px;height:27px;border-radius:9px;background:#eaf7f2;display:grid;place-items:center;margin-right:8px}@keyframes maNotifIn{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:none}}
</style>
<a class="ma-global-notification" href="<?=htmlspecialchars($mnwBase.'/member/notifications',ENT_QUOTES,'UTF-8')?>" aria-label="Notifications" data-member-notification-button>
<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg><b class="ma-global-notification-badge" data-member-notification-badge style="display:none">0</b>
</a>
<a class="ma-notification-toast" data-member-notification-toast href="#"><span class="ma-notification-toast-icon" data-member-notification-icon>🍛</span><span class="ma-notification-toast-title" data-member-notification-title>New update</span><span class="ma-notification-toast-message" data-member-notification-message></span></a>
<script>
(()=>{
 const statusUrl=<?=json_encode($mnwBase.'/member/notifications/status')?>;
 const badge=document.querySelector('[data-member-notification-badge]');
 const toast=document.querySelector('[data-member-notification-toast]');
 const title=document.querySelector('[data-member-notification-title]');
 const message=document.querySelector('[data-member-notification-message]');
 const icon=document.querySelector('[data-member-notification-icon]');
 let knownCount=null, lastTopId=null, timer=null;
 const update=async()=>{
   try{
     const r=await fetch(statusUrl,{headers:{Accept:'application/json'},cache:'no-store'});
     if(!r.ok)return;
     const j=await r.json(); if(!j.success)return;
     const count=Number(j.unread_count||0); const list=Array.isArray(j.notifications)?j.notifications:[];
     badge.textContent=count>99?'99+':String(count); badge.style.display=count>0?'grid':'none';
     const top=list[0];
     if(top && knownCount!==null && Number(top.id)!==lastTopId){
       title.textContent=top.title||'New update'; message.textContent=top.message||''; icon.textContent=top.source==='taxi'?'🚕':(top.source==='fresh_food'?'🥬':(top.source==='commercial_rental'?'🚚':'🍛')); toast.href=<?=json_encode($mnwBase)?>+(top.action_url||'/member/notifications'); toast.classList.add('show');
       setTimeout(()=>toast.classList.remove('show'),6500);
     }
     lastTopId=top?Number(top.id):null; knownCount=count;
   }catch(e){}
 };
 update(); timer=setInterval(update,12000);
 window.addEventListener('beforeunload',()=>{if(timer)clearInterval(timer)});
})();
</script>
<?php endif; ?>
