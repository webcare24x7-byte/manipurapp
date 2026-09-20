<?php declare(strict_types=1); ?>

<div class="page-header"><div><h1>Edit Taxi Vehicle</h1><p>Update taxi vehicle information.</p></div><div class="page-actions"><a href="<?= config('app.base_path') ?>/taxi/vehicles/<?= (int) ($record['id'] ?? 0) ?>" class="btn btn-secondary">Back</a></div></div>
<?php if (!empty($error)): ?><div class="alert alert-error"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data" action="<?= config('app.base_path') ?>/taxi/vehicles/<?= (int) ($record['id'] ?? 0) ?>"><?php require __DIR__ . '/form.php'; ?><div class="page-actions"><a href="<?= config('app.base_path') ?>/taxi/vehicles/<?= (int) ($record['id'] ?? 0) ?>" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Taxi Vehicle</button></div></form>
