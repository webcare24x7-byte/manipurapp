<?php declare(strict_types=1);$d=$record??$old??[];$loc=$locations??[];$base=config('app.base_path');?><div class="card"><div class="form-grid"><div class="form-group"><label>Destination Name *</label><input name="name" required value="<?= htmlspecialchars((string)($d['name']??''),ENT_QUOTES,'UTF-8') ?>"></div><div class="form-group"><label>Status</label><select name="status"><option <?= ($d['status']??'Active')==='Active'?'selected':'' ?>>Active</option><option <?= ($d['status']??'')==='Inactive'?'selected':'' ?>>Inactive</option></select></div><div class="form-group"><label>City / Town</label><select name="city"><option value="">Select...</option><?php foreach($loc['cities']??[] as $v): ?><option <?= ($d['city']??'')===$v?'selected':'' ?>><?= htmlspecialchars($v,ENT_QUOTES,'UTF-8') ?></option><?php endforeach; ?></select></div><div class="form-group"><label>District</label><select name="district"><option value="">Select...</option><?php foreach($loc['districts']??[] as $v): ?><option <?= ($d['district']??'')===$v?'selected':'' ?>><?= htmlspecialchars($v,ENT_QUOTES,'UTF-8') ?></option><?php endforeach; ?></select></div><div class="form-group"><label>Best Time to Visit</label><input name="best_time_to_visit" placeholder="e.g. October – April" value="<?= htmlspecialchars((string)($d['best_time_to_visit']??''),ENT_QUOTES,'UTF-8') ?>"></div><div class="form-group"><label>Suggested Duration</label><input name="suggested_duration" placeholder="e.g. Half day" value="<?= htmlspecialchars((string)($d['suggested_duration']??''),ENT_QUOTES,'UTF-8') ?>"></div><div class="form-group full-width"><label>Description</label><textarea name="description" rows="5"><?= htmlspecialchars((string)($d['description']??''),ENT_QUOTES,'UTF-8') ?></textarea></div></div></div><div class="card location-card"><h2>Destination Location</h2><p>Click the map to choose the destination coordinates, or drag the marker after selecting it.</p><div class="map-picker" id="dest-map"></div><div class="form-grid location-fields"><div class="form-group"><label>Latitude</label><input id="dest-lat" type="number" step="0.0000001" min="-90" max="90" name="latitude" value="<?= htmlspecialchars((string)($d['latitude']??''),ENT_QUOTES,'UTF-8') ?>"></div><div class="form-group"><label>Longitude</label><input id="dest-lng" type="number" step="0.0000001" min="-180" max="180" name="longitude" value="<?= htmlspecialchars((string)($d['longitude']??''),ENT_QUOTES,'UTF-8') ?>"></div><div class="form-group full-width"><button type="button" class="btn btn-secondary" id="dest-current">Use Current Location</button> <button type="button" class="btn btn-secondary" id="dest-clear">Clear</button><span id="dest-status"></span></div></div></div><style>.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.full-width{grid-column:1/-1}.form-group label{display:block;font-weight:600;margin-bottom:7px}.form-group input,.form-group select,.form-group textarea{width:100%;box-sizing:border-box}.location-card{border:1px solid #dbeafe}.location-card p,#tour-provider-status,#dest-status,#stay-status{color:#64748b;font-size:13px}.map-picker{height:360px;border-radius:14px;overflow:hidden;border:1px solid #cbd5e1;margin:14px 0}.location-fields{margin-top:4px}.form-group small{display:block;color:#64748b;margin-top:6px}@media(max-width:700px){.form-grid{grid-template-columns:1fr}.full-width{grid-column:auto}.map-picker{height:300px}}</style><link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"><script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script><script>(function(){
const lat=document.getElementById('dest-lat'),lng=document.getElementById('dest-lng'),status=document.getElementById('dest-status');
const mapEl=document.getElementById('dest-map'); if(!lat||!lng||!mapEl)return;
const has=()=>lat.value!==''&&lng.value!==''&&Number.isFinite(parseFloat(lat.value))&&Number.isFinite(parseFloat(lng.value));
let map=null,marker=null,ready=false;
function setStatus(t){if(status)status.textContent=t;}
function setMarker(a,b,zoom=true){
  lat.value=Number(a).toFixed(7); lng.value=Number(b).toFixed(7);
  if(marker) marker.setLatLng([a,b]); else {
    marker=L.marker([a,b],{draggable:true}).addTo(map);
    marker.on('dragend',function(){const p=marker.getLatLng();lat.value=p.lat.toFixed(7);lng.value=p.lng.toFixed(7);setStatus('Marker moved.');});
  }
  if(zoom) map.setView([a,b],15); setStatus('Location selected.');
}
function init(){
  if(ready)return true;
  if(!window.L||!window.L.map)return false;
  try{
    const a=has()?parseFloat(lat.value):24.8170,b=has()?parseFloat(lng.value):93.9368;
    map=L.map(mapEl).setView([a,b],has()?15:9);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap contributors'}).addTo(map);
    if(has())setMarker(a,b,false);
    map.on('click',e=>setMarker(e.latlng.lat,e.latlng.lng));
    document.getElementById('dest-current')?.addEventListener('click',()=>{
      if(!navigator.geolocation){setStatus('Geolocation is not supported.');return;}
      setStatus('Getting current location…');
      navigator.geolocation.getCurrentPosition(p=>setMarker(p.coords.latitude,p.coords.longitude),()=>setStatus('Unable to get location. Please choose the map location manually.'));
    });
    document.getElementById('dest-clear')?.addEventListener('click',()=>{
      lat.value='';lng.value='';if(marker){map.removeLayer(marker);marker=null;}setStatus('Location cleared.');
    });
    ready=true; setStatus(has()?'Location loaded.':'Click the map to select a location.');
    setTimeout(()=>map.invalidateSize(),150); setTimeout(()=>map.invalidateSize(),700);
    return true;
  }catch(e){setStatus('Map could not be initialized.');return false;}
}
let tries=0; function boot(){if(init())return;if(++tries<60)setTimeout(boot,100);else setStatus('Map library could not be loaded. Check your internet/CDN access.');}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot);else boot();
window.addEventListener('load',()=>setTimeout(boot,50));
})();</script>