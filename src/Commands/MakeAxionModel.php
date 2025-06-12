<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class MakeAxionModel extends Command
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
    }

    public function handle(array $arguments = []): void
    {
        $modelName = $arguments[0] ?? null;

        if (!$modelName) {
            $this->error("❌ Please provide the model name.");
            return;
        }

        $createMigration = in_array('-m', $arguments);

        // Folder untuk model
        $modelDir = $this->basePath . '/axion/models';
        if (!is_dir($modelDir)) {
            mkdir($modelDir, 0755, true);
        }

        $modelClass = ucfirst($modelName);
        $tableName = $this->pluralizeSnakeCase($modelClass);
        $modelFile = "{$modelDir}/{$modelClass}.php";

        if (file_exists($modelFile)) {
            $this->error("❌ Model {$modelClass} already exists.");
            return;
        }

        $modelContent = <<<PHP
<?php

namespace Velto\Axion\Models;

use Velto\Axion\Model;

class {$modelClass} extends Model
{
    protected string \$table = '{$tableName}';

    protected array \$fillable = [];
}
PHP;

        file_put_contents($modelFile, $modelContent);
        $this->info("✅ Model {$modelClass} created at axion/models/{$modelClass}.php");

        // Migration jika -m
        if ($createMigration) {
            $migrationDir = $this->basePath . '/axion/database/migrations';
            if (!is_dir($migrationDir)) {
                mkdir($migrationDir, 0755, true);
            }

            $timestamp = date('Y_m_d_His');
            $migrationClass = 'Create' . str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName))) . 'Table';
            $migrationFile = "{$migrationDir}/{$timestamp}_create_{$tableName}_table.php";

            $migrationContent = <<<PHP
<?php

use Velto\Axion\Migration;

class {$migrationClass} extends Migration
{
    public function up()
    {
        \$this->createTable('{$tableName}', function (\$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down()
    {
        \$this->dropTable('{$tableName}');
    }
}
PHP;

            file_put_contents($migrationFile, $migrationContent);
            $this->info("✅ Migration created: axion/database/migrations/{$timestamp}_create_{$tableName}_table.php");
        }
    }

    /**
     * Ubah nama model menjadi snake_case + bentuk jamak
     * Contoh: BlogCategory -> blog_categories
     */
    protected function pluralizeSnakeCase(string $name): string
    {
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));

        // Penanganan kata berakhiran y → ies
        if (preg_match('/y$/', $snake)) {
            return preg_replace('/y$/', 'ies', $snake);
        }

        // Default → tambah s
        return $snake . 's';
    }
}
