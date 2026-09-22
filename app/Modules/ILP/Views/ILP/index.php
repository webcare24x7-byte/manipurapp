<?php
declare(strict_types=1);
$base = config('app.base_path');
$esc = static fn($v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$old = is_array($old ?? null) ? $old : [];
$result = is_array($result ?? null) ? $result : null;
$purpose = (string)($old['purpose'] ?? 'TOURISM');
?>
<div class="page-header">
    <div>
        <h1>Digital ILP Helper</h1>
        <p>Pre-check the likely Manipur Inner Line Permit category and prepare an application checklist before using the official Government portal.</p>
    </div>
</div>

<div class="ilp-notice">
    <strong>Guidance only</strong>
    <span>This first version is a deterministic helper. It does not submit an ILP application, issue a permit, or replace the latest Government of Manipur rules.</span>
</div>

<div class="ilp-grid">
    <section class="card">
        <div class="card-head">
            <div><span class="eyebrow">BACKEND TEST</span><h2>Check your ILP requirement</h2></div>
        </div>
        <form method="post" action="<?= $esc($base) ?>/ilp/analyze" class="ilp-form">
            <div class="field">
                <label>Are you entering Manipur from outside the state?</label>
                <div class="choice-row">
                    <label class="choice"><input type="radio" name="outside_manipur" value="1" <?= (($old['outside_manipur'] ?? true) ? 'checked' : '') ?>> Yes</label>
                    <label class="choice"><input type="radio" name="outside_manipur" value="0" <?= (($old['outside_manipur'] ?? true) ? '' : 'checked') ?>> No</label>
                </div>
            </div>

            <div class="field">
                <label for="purpose">Purpose of visit</label>
                <select id="purpose" name="purpose" required>
                    <?php
                    $options = [
                        'TOURISM' => 'Tourism / holiday',
                        'BUSINESS_VISIT' => 'Short business visit',
                        'SHORT_TERM' => 'Other short-term visit',
                        'FREQUENT_VISITOR' => 'Frequent visitor',
                        'BUSINESS_ESTABLISHMENT' => 'Establishing / operating a business',
                        'INVESTOR' => 'Investor',
                        'TRADER' => 'Trader',
                        'CONTRACTOR' => 'Government contractor / contractor activity',
                        'LABOUR' => 'Labour engagement',
                        'TEMPORARY_WORK' => 'Temporary employment / work',
                        'REGULAR_EMPLOYEE' => 'Regular employee',
                        'OTHER' => 'Other / not sure',
                    ];
                    foreach ($options as $key => $label):
                    ?>
                        <option value="<?= $esc($key) ?>" <?= $purpose === $key ? 'selected' : '' ?>><?= $esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="field">
                    <label for="duration_days">Planned stay (days)</label>
                    <input id="duration_days" type="number" min="1" max="3650" name="duration_days" value="<?= $esc($old['duration_days'] ?? '') ?>" placeholder="e.g. 7">
                </div>
                <div class="field">
                    <label>Do you have the required sponsor / agency arrangement?</label>
                    <div class="choice-row">
                        <label class="choice"><input type="radio" name="sponsor_available" value="1" <?= (($old['sponsor_available'] ?? false) ? 'checked' : '') ?>> Yes</label>
                        <label class="choice"><input type="radio" name="sponsor_available" value="0" <?= (($old['sponsor_available'] ?? false) ? '' : 'checked') ?>> No</label>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary" type="submit">Check ILP Requirement</button>
        </form>
    </section>

    <section class="card">
        <span class="eyebrow">CURRENT KNOWLEDGE BASE</span>
        <h2>Permit categories</h2>
        <div class="permit-list">
            <?php foreach ($permitTypes as $p): ?>
                <div class="permit-item">
                    <div class="permit-title"><strong><?= $esc($p['name']) ?></strong><span><?= $esc($p['validity_summary']) ?></span></div>
                    <p><?= $esc($p['short_description']) ?></p>
                    <small><?= $esc($p['sponsor_summary']) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php if ($result !== null): ?>
<section class="card result-card">
    <div class="result-head">
        <div>
            <span class="eyebrow">RESULT</span>
            <h2><?= $esc($result['message'] ?? 'ILP analysis') ?></h2>
        </div>
        <?php if (!empty($result['confidence'])): ?><span class="confidence <?= $esc($result['confidence']) ?>"><?= $esc(strtoupper((string)$result['confidence'])) ?></span><?php endif; ?>
    </div>

    <?php if (!empty($result['permit'])): ?>
        <div class="recommendation">
            <span>Permit category to review</span>
            <strong><?= $esc($result['permit']['name']) ?></strong>
            <p><?= $esc($result['permit']['purpose_summary']) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($result['missing_information'])): ?>
        <div class="result-section warning"><h3>Information still needed</h3><ul>
            <?php foreach ($result['missing_information'] as $item): ?><li><?= $esc(ucwords(str_replace('_', ' ', (string)$item))) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <?php if (!empty($result['reasons'])): ?>
        <div class="result-section"><h3>Why this was selected</h3><ul>
            <?php foreach ($result['reasons'] as $reason): ?><li><?= $esc($reason) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <?php if (!empty($result['requirements'])): ?>
        <div class="result-section"><h3>Preparation checklist</h3><div class="checklist">
            <?php foreach ($result['requirements'] as $req): ?>
                <div class="check"><span><?= $req['requirement_type'] === 'WARNING' ? '!' : '✓' ?></span><div><strong><?= $esc($req['title']) ?></strong><?php if (!empty($req['description'])): ?><p><?= $esc($req['description']) ?></p><?php endif; ?></div></div>
            <?php endforeach; ?>
        </div></div>
    <?php endif; ?>

    <?php if (!empty($result['permit']['official_url'])): ?>
        <div class="source-box"><div><strong>Official source</strong><small><?= $esc($result['permit']['source_title'] ?? 'Government of Manipur ILP Portal') ?> · verified <?= $esc($result['permit']['source_verified_at'] ?? '') ?></small></div><a class="btn btn-secondary" target="_blank" rel="noopener noreferrer" href="<?= $esc($result['permit']['official_url']) ?>">Open Official Portal ↗</a></div>
    <?php endif; ?>
</section>
<?php endif; ?>

<div class="card source-card">
    <h2>Official source and scope</h2>
    <p>The helper's initial permit categories and guidance are based on the Government of Manipur ILP Online Portal. Rules, documentary requirements, fees, forms and procedures can change. The official portal and latest Government notifications remain authoritative.</p>
    <a href="https://manipurilponline.mn.gov.in/aboutIlp.aspx" target="_blank" rel="noopener noreferrer">Government of Manipur — ILP information ↗</a>
</div>

<style>
.ilp-notice{display:flex;gap:12px;align-items:flex-start;padding:14px 16px;border:1px solid #fde68a;background:#fffbeb;border-radius:14px;margin-bottom:16px}.ilp-notice strong{white-space:nowrap;color:#92400e}.ilp-notice span{color:#713f12;line-height:1.5}.ilp-grid{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(320px,.85fr);gap:16px}.card-head{margin-bottom:20px}.card-head h2{margin:5px 0 0}.eyebrow{font-size:11px;font-weight:800;letter-spacing:.12em;color:#0369a1}.ilp-form{display:grid;gap:18px}.field{display:grid;gap:7px}.field label{font-weight:700;font-size:14px}.field input,.field select{width:100%;box-sizing:border-box;border:1px solid #d1d5db;border-radius:10px;padding:11px 12px;background:#fff}.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}.choice-row{display:flex;gap:12px;flex-wrap:wrap}.choice{display:flex!important;align-items:center;gap:7px;font-weight:500!important;border:1px solid #e5e7eb;padding:10px 12px;border-radius:10px}.choice input{width:auto}.permit-list{display:grid;gap:10px;margin-top:16px}.permit-item{padding:14px;border:1px solid #e5e7eb;border-radius:12px}.permit-title{display:flex;justify-content:space-between;gap:10px}.permit-title span{font-size:12px;color:#64748b;text-align:right}.permit-item p{margin:6px 0;color:#475569;font-size:13px;line-height:1.5}.permit-item small{color:#64748b}.result-card{margin-top:16px}.result-head{display:flex;justify-content:space-between;gap:16px;align-items:flex-start}.result-head h2{margin:5px 0 0;line-height:1.3}.confidence{padding:6px 9px;border-radius:999px;background:#ecfdf5;color:#047857;font-size:11px;font-weight:800}.confidence.medium{background:#fffbeb;color:#92400e}.confidence.needs_more_information{background:#eff6ff;color:#1d4ed8}.recommendation{margin-top:18px;padding:18px;border-radius:14px;background:#eff6ff;border:1px solid #bfdbfe}.recommendation span{display:block;color:#1d4ed8;font-size:12px;font-weight:700}.recommendation strong{display:block;font-size:22px;margin:4px 0}.recommendation p{margin:0;color:#334155;line-height:1.5}.result-section{margin-top:18px}.result-section h3{margin:0 0 10px;font-size:16px}.result-section ul{margin:0;padding-left:20px;color:#475569;line-height:1.7}.warning{padding:14px;background:#fffbeb;border:1px solid #fde68a;border-radius:12px}.checklist{display:grid;grid-template-columns:1fr 1fr;gap:10px}.check{display:flex;gap:10px;padding:12px;border:1px solid #e5e7eb;border-radius:12px}.check>span{display:grid;place-items:center;width:24px;height:24px;border-radius:50%;background:#ecfdf5;color:#047857;font-weight:800;flex:none}.check strong{font-size:13px}.check p{margin:4px 0 0;color:#64748b;font-size:12px;line-height:1.5}.source-box{margin-top:20px;padding:14px;border-top:1px solid #e5e7eb;display:flex;justify-content:space-between;gap:16px;align-items:center}.source-box small{display:block;color:#64748b;margin-top:4px}.source-card{margin-top:16px}.source-card p{color:#64748b;line-height:1.6}.source-card a{font-weight:700}.card{border-radius:16px}@media(max-width:850px){.ilp-grid{grid-template-columns:1fr}.form-row,.checklist{grid-template-columns:1fr}}@media(max-width:520px){.ilp-notice{display:block}.ilp-notice span{display:block;margin-top:5px}.permit-title{display:block}.permit-title span{display:block;text-align:left;margin-top:4px}.source-box{display:block}.source-box .btn{display:inline-block;margin-top:12px}}
</style>
