<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap/autoload.php';

$app = require dirname(__DIR__) . '/bootstrap/app.php';

use App\Core\Migrator;

$migrator = new Migrator();

$migrator->migrate();