<?php

namespace PulsarLabs\Generators\Features\Routes\Commands;

use Illuminate\Console\Command;
use PulsarLabs\Generators\Contracts\DatabaseReader;

class GenerateRouteCommand extends Command
{
    protected $signature = 'generate:routes';
    protected $description = 'Generates administrator routes';

    protected array $processors = [];

    public function handle(): void
    {
        $stub = $this->getStub();
        /** @var DatabaseReader $databaseReaderClass */
        $databaseReaderClass = config('generators.database_reader');
        $databaseReader = new $databaseReaderClass();

        $this->writeConfigFile();
        $this->writeRouteServiceProvider();

        $tables = $databaseReader->listTables();
        $routes_list = "";
        $import_controller_list = "";
        $routes_list_item_stub = file_get_contents(__DIR__ . '/../stubs/route_list_item.stub');
        foreach ($tables as $table) {
            $model_route_plural_resource_name = str($table)->kebab()->plural();
            $model_class_name = str($table)->studly()->singular();
            $routes_list .= str_replace([
                '{{ ModelClassName }}',
                '{{ ModelRoutePluralResourceName }}'
            ], [
                $model_class_name,
                $model_route_plural_resource_name
            ], $routes_list_item_stub);
            $import_controller_list .= "use App\\Http\\Controllers\\{$model_class_name}Controller;\n";
        }

        $admin_routes_stub = file_get_contents(__DIR__ . '/../stubs/admin_routes.stub');
        $admin_routes = str_replace([
            '{{ imports }}',
            '{{ routes }}'
        ], [
            $import_controller_list,
            $routes_list
        ], $admin_routes_stub);

        $file_path = $this->getTargetFilePath();

        file_put_contents($file_path, $admin_routes);

        $this->info('Routes generated successfully');
    }

    private function getTargetFilePath(): string
    {
        return base_path('routes/admin.php');
    }

    private function writeConfigFile(): void
    {
        $admin_config_stub = file_get_contents(__DIR__ . '/../stubs/admin_config.stub');
        $admin_config_path = base_path('config/admin.php');
        file_put_contents($admin_config_path, $admin_config_stub);
    }

    private function writeRouteServiceProvider(): void
    {
        $route_service_provider_stub = file_get_contents(__DIR__ . '/../stubs/route_service_provider.stub');
        $route_service_provider_path = app_path('Providers/RouteServiceProvider.php');
        file_put_contents($route_service_provider_path, $route_service_provider_stub);
    }


    protected function getStub(): string
    {
        return file_get_contents(__DIR__ . '/../stubs/admin_routes.stub');
    }
}
