<?php

return [

    'database_reader' => \PulsarLabs\Generators\Support\MySqlDatabaseReader::class,

    'generators' => [
        \PulsarLabs\Generators\Features\Models\Commands\GenerateModelCommand::class,
    ]
];
