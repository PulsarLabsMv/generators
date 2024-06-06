<?php

namespace PulsarLabs\Generators\Features\Permissions\Generators;

use Illuminate\Console\Command;
use PulsarLabs\Generators\Contracts\DatabaseReader;
use PulsarLabs\Generators\Support\Traits\HasGuardedProperties;
use PulsarLabs\Generators\Support\Traits\HasAttributesProperty;
use PulsarLabs\Generators\Features\Models\Updaters\ImportsUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\ClassNameUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\CastsPropertyUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\HasManyRelationsUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\FillablePropertyUpdater;
use PulsarLabs\Generators\Support\GlobalUpdaters\RemovePlaceholdersUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\BelongsToRelationsUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\BelongsToManyRelationsUpdater;

class PermissionGenerator
{

    public function handle(Command $command, string $table_name): void
    {
        dd('dd');
    }
}
