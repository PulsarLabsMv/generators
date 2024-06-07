<?php

return [

    'database_reader' => \PulsarLabs\Generators\Support\MySqlDatabaseReader::class,

    'generators' => [
        \PulsarLabs\Generators\Features\Models\Commands\GenerateModelCommand::class,
        \PulsarLabs\Generators\Features\Permissions\Commands\GeneratePermissionCommand::class,
        \PulsarLabs\Generators\Features\Policies\Commands\GeneratePolicyCommand::class,
        \PulsarLabs\Generators\Features\Requests\Commands\GenerateRequestCommand::class,
    ]
];
