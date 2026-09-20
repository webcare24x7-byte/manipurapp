<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use App\Core\Auth;

final class View
{

    public function render(
            string $view,
            array $data = [],
            string $layout = 'app'
        ): void {

            $viewFile = $this->findView($view);

            /*
            |--------------------------------------------------------------------------
            | Global View Data
            |--------------------------------------------------------------------------
            */

            $request = app()->get('request');

            $shared = [

                'authUser' => Auth::user(),

                'tenantId' => Auth::tenantId(),

                'currentPath' => $request->path(),

                'requestMethod' => $request->method(),

            ];

            $data = array_merge($shared, $data);
            $data['stylesheets'] ??= [];

            extract($data, EXTR_SKIP);

            ob_start();

            require $viewFile;

            $content = ob_get_clean();

            $layoutFile = base_path(
                "resources/views/layouts/{$layout}.php"
            );

            if (!file_exists($layoutFile)) {

                throw new RuntimeException(
                    "Layout [{$layout}] not found."
                );

            }

            require $layoutFile;
        }

    private function findView(string $view): string
    {
        /*
        |--------------------------------------------------------------------------
        | New syntax:
        |
        | Lookup::LookupTypes.index
        | Attendance::Reports.monthly
        |--------------------------------------------------------------------------
        */

        if (str_contains($view, '::')) {

            [$module, $file] = explode(
                '::',
                $view,
                2
            );

            $module = ucfirst(trim($module));

            $file = str_replace(
                '.',
                '/',
                trim($file)
            );

            $path = base_path(
                "app/Modules/{$module}/Views/{$file}.php"
            );

            if (!file_exists($path)) {

                throw new RuntimeException(
                    "View [{$view}] not found."
                );

            }

            return $path;
        }

        /*
        |--------------------------------------------------------------------------
        | Legacy syntax:
        |
        | Members.index
        | Positions.show
        |--------------------------------------------------------------------------
        */

        $parts = explode('.', $view);

        if (count($parts) < 2) {

            throw new RuntimeException(
                "View name must be Module.View or Module::View."
            );

        }

        $module = ucfirst(array_shift($parts));

        $file = implode('/', $parts);

        $path = base_path(
            "app/Modules/{$module}/Views/{$file}.php"
        );

        if (!file_exists($path)) {

            throw new RuntimeException(
                "View [{$view}] not found."
            );

        }

        return $path;
    }
}