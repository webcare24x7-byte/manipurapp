<?php declare(strict_types=1); $e=static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8"); ?><?php $data=$record??$old??[]; ?><div class="card"><div class="form-grid">
<div class="form-group"><label>Business Name <span class="required">*</span></label><input name="business_name" required value="<?= $e($data['business_name'] ?? '') ?>"></div>
<div class="form-group"><label>Legal Name</label><input name="legal_name" value="<?= $e($data['legal_name'] ?? '') ?>"></div>
<div class="form-group"><label>Phone</label><input name="phone" value="<?= $e($data['phone'] ?? '') ?>"></div>
<div class="form-group"><label>Email</label><input type="email" name="email" value="<?= $e($data['email'] ?? '') ?>"></div>
<div class="form-group full-width"><label>Address</label><input name="address" value="<?= $e($data['address'] ?? '') ?>"></div>
<div class="form-group"><label>City / Town</label><input name="city" value="<?= $e($data['city'] ?? '') ?>"></div>
<div class="form-group"><label>District</label><input name="district" value="<?= $e($data['district'] ?? '') ?>"></div>
<div class="form-group"><label>State</label><input name="state" value="<?= $e($data['state'] ?? 'Manipur') ?>"></div>
<div class="form-group"><label>Postal Code</label><input name="postal_code" value="<?= $e($data['postal_code'] ?? '') ?>"></div>
<div class="form-group"><label>Latitude</label><input id="cr_lat" name="latitude" inputmode="decimal" value="<?= $e($data['latitude'] ?? '') ?>"></div>
<div class="form-group"><label>Longitude</label><input id="cr_lng" name="longitude" inputmode="decimal" value="<?= $e($data['longitude'] ?? '') ?>"></div>
<div class="form-group full-width"><button type="button" class="btn btn-secondary" onclick="crLocate()">Use Current Location</button> <span class="cr-muted">Coordinates are stored for future nearest-provider ranking. No ETA or distance pricing.</span></div>
<div class="form-group full-width"><label>Service Areas</label><textarea name="service_areas" rows="4" placeholder="e.g. Imphal West, Imphal East, Thoubal..." ><?= $e($data['service_areas'] ?? '') ?></textarea></div>
<div class="form-group full-width"><label>Description</label><textarea name="description" rows="4"><?= $e($data['description'] ?? '') ?></textarea></div>
<div class="form-group"><label>Status</label><select name="status"><option value="Active" <?= ($data['status']??'Active')==='Active'?'selected':'' ?>>Active</option><option value="Inactive" <?= ($data['status']??'')==='Inactive'?'selected':'' ?>>Inactive</option><option value="Suspended" <?= ($data['status']??'')==='Suspended'?'selected':'' ?>>Suspended</option></select></div>
</div></div><script>function crLocate(){if(!navigator.geolocation){alert('Geolocation is not available.');return;}navigator.geolocation.getCurrentPosition(function(p){document.getElementById('cr_lat').value=p.coords.latitude.toFixed(7);document.getElementById('cr_lng').value=p.coords.longitude.toFixed(7);},function(){alert('Unable to get current location. You can enter coordinates manually.');});}</script>
