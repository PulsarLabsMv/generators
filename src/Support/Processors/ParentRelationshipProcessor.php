<?php

namespace PulsarLabs\Generators\Support\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class ParentRelationshipProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $class_name = $command_data->table_name;
        $class_relationship = str($class_name)->plural()->camel()->toString();
        $command_data->stub_contents = str_replace('{{ ParentRelationship }}', $class_relationship, $command_data->stub_contents);

        return $next($command_data);
    }
}
