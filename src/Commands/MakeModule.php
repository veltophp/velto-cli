<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class MakeModule extends Command
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
    }

    public function handle(array $arguments = []): void
    {
        $moduleName = $arguments[0] ?? null;
    
        if (!$moduleName) {
            $this->error("❌ Please provide the module name.");
            return;
        }
    
        $moduleSlug = strtolower($moduleName);
        $modulePath = "{$this->basePath}/modules/{$moduleName}";
    
        if (is_dir($modulePath)) {
            $this->error("❌ Module '{$moduleName}' already exists.");
            return;
        }
    
        mkdir("{$modulePath}", 0755, true);
        mkdir("{$modulePath}/Views", 0755, true);
        mkdir("{$modulePath}/Models", 0755, true);
        mkdir("{$modulePath}/Controllers", 0755, true);

    
        $this->info("✅ Module '{$moduleName}' created at modules/{$moduleSlug}");
    
        $this->createController($moduleName, $modulePath);
        $this->createModel($moduleName, $modulePath);
        $this->createView($moduleName, $modulePath);
        $this->createRoutes($moduleName, $modulePath);
        $this->createApi($moduleName, $modulePath);
        $this->createMigration($moduleName);
    }
    

    protected function createController(string $moduleName, string $modulePath): void
    {
        $controllerName = "{$moduleName}Controller";
        $controllerFile = "{$modulePath}/Controllers/{$controllerName}.php";
        $moduleSlug = strtolower($moduleName);

        $content = <<<PHP
<?php

namespace Modules\\{$moduleName}\\Controllers;

use Velto\Core\Controller\Controller;
use Velto\Core\Request\Request;
use Modules\\{$moduleName}\\Models\\{$moduleName};

class {$controllerName} extends Controller
{
    public function {$moduleSlug}()
    {
        return view('{$moduleSlug}.{$moduleSlug}');
    }
}
PHP;

        file_put_contents($controllerFile, $content);
        $this->info("📦 Controller created: {$controllerName}.php");
    }

    protected function createModel(string $moduleName, string $modulePath): void
    {
        $modelName = ucfirst($moduleName);
        $modelFile = "{$modulePath}/Models/{$modelName}.php";
        $table = $this->pluralizeSnakeCase($modelName);

        $content = <<<PHP
<?php

namespace Modules\\{$moduleName}\\Models;

use Velto\\Core\\Model\\Model;

class {$modelName} extends Model
{
    protected string \$table = '{$table}';
    protected array \$fillable = [];
    protected array \$searchable = [];

}
PHP;

        file_put_contents($modelFile, $content);
        $this->info("📦 Model created: Models/{$modelName}.php");
    }

    protected function createView(string $moduleName, string $modulePath): void
    {
        $moduleSlug = strtolower($moduleName);
        $viewFile = "{$modulePath}/Views/{$moduleSlug}.vel.php";

        $content = <<<HTML
@extends('layouts.app')

@section('title')
    {$moduleName} | VeltoPHP V2.0
@endsection

@section('app-content')
<div class="font-thin">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-36">
        <div class="text-center">
            <h1 class="text-3xl">Welcome to {$moduleName} Module</h1>
            <p class="text-red-500">VeltoPHP V.2</p>
        </div>
    </div>
</div>
@endsection
HTML;

        file_put_contents($viewFile, $content);
        $this->info("📦 View created: Views/{$moduleSlug}.vel.php");
    }

    protected function createRoutes(string $moduleName, string $modulePath): void
    {
        $file = "{$modulePath}/Routes.php";
        $moduleSlug = strtolower($moduleName);

        $content = <<<PHP
<?php

use Velto\\Core\\Route\\Route;
use Modules\\{$moduleName}\\Controllers\\{$moduleName}Controller;

Route::get('/{$moduleSlug}', [{$moduleName}Controller::class, '{$moduleSlug}'])->name('{$moduleSlug}');
PHP;

        file_put_contents($file, $content);
        $this->info("📦 Routes file created: Routes.php");
    }

    protected function createApi(string $moduleName, string $modulePath): void
    {
        $file = "{$modulePath}/Api.php";
        $moduleSlug = strtolower($moduleName);

        $content = <<<PHP
<?php

use Velto\\Core\\Route\\Route;
use Modules\\{$moduleName}\\Controllers\\{$moduleName}Controller;


// Define API routes here
// Route::get('/api/{$moduleSlug}', [{$moduleName}Controller::class, '{$moduleSlug}']);
PHP;

        file_put_contents($file, $content);
        $this->info("📦 API file created: Api.php");
    }

    protected function createMigration(string $moduleName): void
    {
        $timestamp = date('Y_m_d_His');
        $modelName = ucfirst($moduleName);
        $table = $this->pluralizeSnakeCase($modelName);
        $migrationClass = 'Create' . str_replace(' ', '', ucwords(str_replace('_', ' ', $table))) . 'Table';
        $migrationFile = "{$this->basePath}/storage/database/migrations/{$timestamp}_create_{$table}_table.php";

        $content = <<<PHP
<?php

use Velto\Core\Migration\Migration;

class {$migrationClass} extends Migration
{
    public function up()
    {
        \$this->createTable('{$table}', function (\$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down()
    {
        \$this->dropTable('{$table}');
    }
}
PHP;

        $migrationDir = "{$this->basePath}/storage/database/migrations";
        if (!is_dir($migrationDir)) {
            mkdir($migrationDir, 0755, true);
        }

        file_put_contents($migrationFile, $content);
        $this->info("📦 Migration created: storage/database/migrations/{$timestamp}_create_{$table}_table.php");
    }

    protected function pluralizeSnakeCase(string $name): string
    {
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        if (preg_match('/y$/', $snake)) {
            return preg_replace('/y$/', 'ies', $snake);
        }
        return $snake . 's';
    }
}
