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
        $moduleName = $arguments[0] ?? null;
        $controllerName = $arguments[1] ?? null;

        if (!$moduleName || !$controllerName) {
            $this->error("❌ Usage: php velto make:controller -ModuleName -ControllerName");
            return;
        }

        $module = ltrim($moduleName, '-');
        $controller = ltrim($controllerName, '-');

        $modulePath = "{$this->basePath}/modules/{$module}";
        $controllerDir = "{$modulePath}/Controllers";

        if (!is_dir($modulePath)) {
            $this->error("❌ Module '{$module}' does not exist.");
            return;
        }

        if (!is_dir($controllerDir)) {
            mkdir($controllerDir, 0755, true);
        }

        $controllerClass = str_ends_with($controller, 'Controller')
            ? $controller
            : $controller . 'Controller';

        $controllerFile = "{$controllerDir}/{$controllerClass}.php";

        if (file_exists($controllerFile)) {
            $this->error("❌ Controller '{$controllerClass}' already exists in module '{$module}/Controllers'.");
            return;
        }

        $namespace = "Modules\\{$module}\\Controllers";

        $content = <<<PHP
<?php

namespace {$namespace};

use Velto\Core\Controller\Controller;
use Velto\Core\Request\Request;

class {$controllerClass} extends Controller
{
    public function index()
    {
        // Display a listing of the resource
    }

    public function show(\$id)
    {
        // Display the specified resource
    }

    public function create()
    {
        // Show the form for creating a new resource
    }

    public function store(Request \$request)
    {
        // Store a newly created resource in storage
    }

    public function edit(\$id)
    {
        // Show the form for editing the specified resource
    }

    public function update(Request \$request, \$id)
    {
        // Update the specified resource in storage
    }

    public function delete(\$id)
    {
        // Remove the specified resource from storage
    }
}

PHP;

        file_put_contents($controllerFile, $content);

        $this->info("✅ Controller '{$controllerClass}' created at modules/{$module}/Controllers/{$controllerClass}.php");
    }
}
