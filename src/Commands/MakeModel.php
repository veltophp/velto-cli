<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class MakeModel extends Command
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
    }

    public function handle(array $arguments = []): void
    {
        $moduleName = $arguments[0] ?? null;
        $modelName = $arguments[1] ?? null;
        $withMigration = in_array('-m', $arguments);

        if (!$moduleName || !$modelName) {
            $this->error("❌ Usage: php velto make:model -ModuleName -ModelName [-m]");
            return;
        }

        $module = ltrim($moduleName, '-');
        $model = ltrim($modelName, '-');
        $modelClass = ucfirst($model);
        $table = $this->pluralizeSnakeCase($modelClass);

        $modelDir = "{$this->basePath}/modules/{$module}/Models";
        $modelFile = "{$modelDir}/{$modelClass}.php";

        if (!is_dir("{$this->basePath}/modules/{$module}")) {
            $this->error("❌ Module '{$module}' does not exist.");
            return;
        }

        if (!is_dir($modelDir)) {
            mkdir($modelDir, 0755, true);
        }

        if (file_exists($modelFile)) {
            $this->error("❌ Model '{$modelClass}' already exists.");
            return;
        }

        $namespace = "Modules\\{$module}\\Models";

        $modelContent = <<<PHP
<?php

namespace {$namespace};

use Velto\Core\Model\Model;

class {$modelClass} extends Model
{
    protected string \$table = '{$table}';
    protected array \$fillable = [];
    protected array \$searchable = [];

}

PHP;

        file_put_contents($modelFile, $modelContent);
        $this->info("✅ Model '{$modelClass}' created at modules/{$module}/Models/{$modelClass}.php");

        if ($withMigration) {
            $this->createMigration($modelClass, $table);
        }
    }

    protected function createMigration(string $modelClass, string $table): void
    {
        $timestamp = date('Y_m_d_His');
        $className = 'Create' . str_replace(' ', '', ucwords(str_replace('_', ' ', $table))) . 'Table';
        $migrationDir = "{$this->basePath}/storage/database/migrations";

        if (!is_dir($migrationDir)) {
            mkdir($migrationDir, 0755, true);
        }

        $file = "{$migrationDir}/{$timestamp}_create_{$table}_table.php";

        $content = <<<PHP
<?php

use Velto\Core\Migration\Migration;

class {$className} extends Migration
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

        file_put_contents($file, $content);
        $this->info("📦 Migration created: storage/database/migrations/{$timestamp}_create_{$table}_table.php");
    }

    protected function pluralizeSnakeCase(string $name): string
    {
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));

        // Kata berakhiran y → ies
        if (preg_match('/y$/', $snake)) {
            return preg_replace('/y$/', 'ies', $snake);
        }

        return $snake . 's';
    }
}
