<?php declare(strict_types=1); ?>

<div class="page-header"><div><h1>Add Taxi Driver</h1><p>Create a taxi driver for this tenant.</p></div><div class="page-actions"><a href="<?= config('app.base_path') ?>/taxi/drivers" class="btn btn-secondary">Back</a></div></div>
<?php if (!empty($error)): ?><div class="alert alert-error"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data" action="<?= config('app.base_path') ?>/taxi/drivers"><?php require __DIR__ . '/form.php'; ?><div class="page-actions"><a href="<?= config('app.base_path') ?>/taxi/drivers" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Save Taxi Driver</button></div></form>
