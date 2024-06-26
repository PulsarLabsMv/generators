<?php

namespace PulsarLabs\Generators\Support\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Exceptions\MissingArgumentException;

class ParentModelClassNameProcessor
{
    /**
     * @throws MissingArgumentException
     */
    public function handle(CommandData $command_data, Closure $next)
    {
        if (! $argument_parent_name = data_get($command_data->arguments, 'parent_name')) {
            return $next($command_data);
        }

        $parent_name = str($argument_parent_name)->studly()->singular();
        $command_data->stub_contents = str_replace('{{ ParentModelClassName }}', $parent_name, $command_data->stub_contents);

        return $next($command_data);
    }
}
