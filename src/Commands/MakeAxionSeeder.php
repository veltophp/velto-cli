<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class MakeAxionSeeder extends Command
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
    }

    public function handle(array $arguments = []): void
    {
        $seederName = $arguments[0] ?? null;

        if (!$seederName) {
            $this->error("❌ Please provide the seeder name.");
            return;
        }

        $originalName = ucfirst($seederName);
        $seederClass = str_ends_with($originalName, 'Seeder')
            ? $originalName
            : $originalName . 'Seeder';

        $seederFile = $this->basePath . '/axion/database/seeders/' . $seederClass . '.php';

        if (file_exists($seederFile)) {
            $this->error("❌ Seeder {$seederClass} already exists.");
            return;
        }

        $content = <<<PHP
<?php

use Velto\\Axion\\Models\\ModelName;

class {$seederClass}
{
    public function run()
    {
        \$now = date('Y-m-d H:i:s');

        ModelName::insert([
            [
                // 'column_name' => 'value',
                'created_at' => \$now,
                'updated_at' => \$now,
            ]
        ]);
    }
}
PHP;

        if (!is_dir(dirname($seederFile))) {
            mkdir(dirname($seederFile), 0755, true);
        }

        file_put_contents($seederFile, $content);
        $this->success("✅ Seeder {$seederClass} created at axion/database/seeders/{$seederClass}.php");
    }
}
