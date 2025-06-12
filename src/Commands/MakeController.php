<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class MakeController extends Command
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
    }

    public function handle(array $arguments = []): void
    {
        $controllerName = $arguments[0] ?? null;

        if (!$controllerName) {
            $this->error("❌ Please provide the controller name.");
            return;
        }

        $originalName = ucfirst($controllerName);
        $controllerClass = str_ends_with($originalName, 'Controller') 
            ? $originalName 
            : $originalName . 'Controller';

        $controllerFile = $this->basePath . '/app/Controllers/' . $controllerClass . '.php';

        if (file_exists($controllerFile)) {
            $this->error("❌ Controller {$controllerClass} already exists.");
            return;
        }

        $content = <<<PHP
<?php

namespace App\Controllers;

use Velto\Core\Controller;

class {$controllerClass} extends Controller
{
    public function index()
    {
        // return view('some-view');
    }
}

PHP;

        if (!is_dir(dirname($controllerFile))) {
            mkdir(dirname($controllerFile), 0755, true);
        }

        file_put_contents($controllerFile, $content);
        $this->info("✅ Controller {$controllerClass} created at app/Controllers/{$controllerClass}.php");
    }
}
