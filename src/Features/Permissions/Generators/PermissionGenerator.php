<?php

namespace PulsarLabs\Generators\Features\Permissions\Generators;

use Illuminate\Console\Command;

class PermissionGenerator
{
    public function handle(Command $command, string $table_name): void
    {
        $target_file = $this->getTargetFile();
        $file_contents = file_get_contents($target_file);

        $update_content = $this->getUpdateContent($table_name);

        // find "return [" in the file
        $start = strpos($file_contents, 'return [');

        // check if the content already exists
        if (str_contains($file_contents, $table_name)) {
            $command->info('Permission already exists for ' . $table_name);
            return;
        }

        // add the new content after "return ["
        $file_contents = substr_replace($file_contents, $update_content, $start + 8, 0);

        file_put_contents($target_file, $file_contents);
    }

    private function getTargetFile(): string
    {
        return database_path('seeders/PermissionsSeeder.php');
    }

    private function getUpdateContent(string $table_name): string
    {
        $content = "\n";
        $content .= "\t\t\t" . '"' . $table_name . '" => [' . PHP_EOL;
        $content .= "\t\t\t\t" . '"view ' . str($table_name)->replace('_', '') . '",' . PHP_EOL;
        $content .= "\t\t\t\t" . '"edit ' . str($table_name)->replace('_', '') . '",' . PHP_EOL;
        $content .= "\t\t\t\t" . '"delete ' . str($table_name)->replace('_', '') . '",' . PHP_EOL;
        $content .= "\t\t\t" . '],' . PHP_EOL;

        return $content;
    }
}
