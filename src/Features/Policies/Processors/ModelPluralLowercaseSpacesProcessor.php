<?php

namespace PulsarLabs\Generators\Features\Policies\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class ModelPluralLowercaseSpacesProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $model_plural_lowercase = str($command_data->table_name)->lower()->replace('_', ' ')->plural()->toString();
        $command_data->stub_contents = str_replace('{{ model plural lowercase }}', $model_plural_lowercase, $command_data->stub_contents);

        return $next($command_data);
    }
}
