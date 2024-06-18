<?php

namespace PulsarLabs\Generators\Support\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Exceptions\MissingArgumentException;

class ParentModelPluralNameProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        if (! $argument_parent_name = data_get($command_data->arguments, 'parent_name')) {
            throw new MissingArgumentException('parent_name');
        }
        $parent_name = str($argument_parent_name)->lower()->slug('_')->singular();
        $command_data->stub_contents = str_replace('{{ ParentModelPluralName }}', $parent_name, $command_data->stub_contents);

        return $next($command_data);
    }
}
