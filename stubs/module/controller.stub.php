<?php

declare(strict_types=1);

namespace App\Modules\{{module}}\Controllers;

use App\Core\Controller;

final class {{module}}Controller extends Controller
{
    public function index(): void
    {
        $this->view('{{module}}.index', [
            'title' => '{{module}}'
        ]);
    }
}