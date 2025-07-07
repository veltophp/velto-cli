<?php

namespace Veltophp\VeltoCli;

abstract class Command
{
    abstract public function handle(): void;

    protected function info(string $msg): void
    {
        echo "\033[36m{$msg}\033[0m\n";
    }

    protected function error(string $msg): void
    {
        fwrite(STDERR, "\033[31m{$msg}\033[0m\n");
    }

    public function warning(string $message): void
    {
        echo "\033[33m{$message}\033[0m\n";
    }

    protected function success(string $msg): void
    {
        echo "\033[32m{$msg}\033[0m\n";
    }

    protected function line(string $msg): void
    {
        echo "{$msg}\n";
    }


    protected function ask(string $prompt, bool $required = true): string
    {
        do {
            echo $prompt . ' ';
            $input = trim(fgets(STDIN));
            if ($required && $input === '') {
                $this->error("Input can't be empety!");
            }
        } while ($required && $input === '');

        return $input;
    }

    protected function askHidden(string $prompt): string
    {
        if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
            $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
            file_put_contents($vbscript, 'wscript.echo(InputBox("' . addslashes($prompt) . '", "", ""))');
            $password = rtrim(shell_exec("cscript //nologo " . escapeshellarg($vbscript)));
            unlink($vbscript);
            return $password;
        } else {
            echo "$prompt ";
            system('stty -echo');
            $password = rtrim(fgets(STDIN), "\n");
            system('stty echo');
            echo "\n";
            return $password;
        }
    }

}
