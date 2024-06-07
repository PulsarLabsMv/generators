<?php

namespace PulsarLabs\Generators\Tests\TestSupport\Support;

enum PostStatuses: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
