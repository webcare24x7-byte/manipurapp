<?php declare(strict_types=1); $base=config('app.base_path'); ?><div class="page-header"><div><h1>Edit Restaurant</h1><p>Update the business and restaurant profile information.</p></div><div class="page-actions"><a class="btn btn-secondary" href="<?= $base ?>/restaurant/restaurants/<?= (int)$record['id'] ?>">Back</a></div></div><?php if(!empty($error)): ?><div class="card"><p><?= htmlspecialchars((string)$error,ENT_QUOTES,'UTF-8') ?></p></div><?php endif; ?><form enctype="multipart/form-data" method="post" action="<?= $base ?>/restaurant/restaurants/<?= (int)$record['id'] ?>"><?php require __DIR__.'/form.php'; ?><div class="page-actions"><button class="btn btn-primary" type="submit">Save Restaurant</button></div></form>\n\n<style>
.restaurant-hours-card { overflow: hidden; }
.restaurant-hours-table { width: 100%; border-collapse: collapse; }
.restaurant-hours-table th,
.restaurant-hours-table td { padding: 14px 16px; border-bottom: 1px solid #e5e7eb; text-align: left; vertical-align: middle; }
.restaurant-hours-table th { font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; font-weight: 700; background: #f8fafc; }
.restaurant-hours-table tr:last-child td { border-bottom: 0; }
.restaurant-hours-table .day-cell { font-weight: 600; white-space: nowrap; }
.restaurant-hours-table .closed-cell { width: 110px; }
.restaurant-hours-table .time-cell { width: 180px; }
.restaurant-hours-table input[type="time"] {
    display: block;
    width: 125px;
    min-width: 125px;
    height: 40px;
    padding: 0 10px;
    box-sizing: border-box;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #fff;
    color: #111827;
    font: inherit;
    line-height: 40px;
    color-scheme: light;
}
.restaurant-hours-table input[type="time"]:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
.restaurant-hours-table input[type="time"]:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }
.restaurant-hours-table input[type="checkbox"] { width: 17px; height: 17px; vertical-align: middle; }
.restaurant-hours-note { margin: 0 0 16px; color: #64748b; font-size: 13px; }
@media (max-width: 760px) {
    .restaurant-hours-table { min-width: 620px; }
    .restaurant-hours-table th,
    .restaurant-hours-table td { padding: 12px; }
}
</style>
<?php $days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; $by=[]; foreach(($hours??[]) as $h) $by[(int)$h['day_of_week']]=$h; ?>
<div class="page-header">
    <div>
        <h2>Operating Hours</h2>
        <p>Set when this restaurant accepts customers each day.</p>
    </div>
</div>
<form method="post" action="<?= $base ?>/restaurant/restaurants/<?= (int)$record['id'] ?>/hours">
    <div class="card restaurant-hours-card">
        <p class="restaurant-hours-note">Mark a day as closed, or provide both opening and closing times.</p>
        <div class="table-responsive">
            <table class="table restaurant-hours-table">
                <thead>
                    <tr><th>Day</th><th class="closed-cell">Closed</th><th class="time-cell">Opens</th><th class="time-cell">Closes</th></tr>
                </thead>
                <tbody>
                <?php foreach($days as $day=>$label): $h=$by[$day]??[]; $closed=!empty($h['is_closed']); ?>
                    <tr>
                        <td class="day-cell"><?= $label ?></td>
                        <td class="closed-cell"><input class="hours-closed" type="checkbox" name="is_closed[<?= $day ?>]" value="1" <?= $closed?'checked':'' ?> data-day="<?= $day ?>"></td>
                        <td class="time-cell"><input class="hours-time hours-open" type="time" name="opens_at[<?= $day ?>]" value="<?= htmlspecialchars(substr((string)($h['opens_at']??''),0,5),ENT_QUOTES,'UTF-8') ?>" <?= $closed?'disabled':'' ?> data-day="<?= $day ?>" aria-label="<?= $label ?> opening time"></td>
                        <td class="time-cell"><input class="hours-time hours-close" type="time" name="closes_at[<?= $day ?>]" value="<?= htmlspecialchars(substr((string)($h['closes_at']??''),0,5),ENT_QUOTES,'UTF-8') ?>" <?= $closed?'disabled':'' ?> data-day="<?= $day ?>" aria-label="<?= $label ?> closing time"></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="page-actions"><button class="btn btn-primary" type="submit">Save Hours</button></div>
</form>
<script>
document.querySelectorAll('.hours-closed').forEach(function (checkbox) {
    function syncHours() {
        var day = checkbox.getAttribute('data-day');
        document.querySelectorAll('.hours-time[data-day="' + day + '"]').forEach(function (input) {
            input.disabled = checkbox.checked;
            if (checkbox.checked) input.value = '';
        });
    }
    checkbox.addEventListener('change', syncHours);
    syncHours();
});
</script>
