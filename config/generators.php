<?php

return [

    'database_reader' => \Abunooh\Generators\Support\MySqlDatabaseReader::class,

    'generators' => [
        \Abunooh\Generators\Features\Models\Commands\GenerateModelCommand::class,
    ]
];
