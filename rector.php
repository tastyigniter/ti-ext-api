<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNewArrayRector;

return RectorConfig::configure()
    ->withPaths([__DIR__.'/src', __DIR__.'/tests'])
    ->withSkip([
        ReturnTypeFromStrictNewArrayRector::class,
    ])
    ->withTypeCoverageLevel(100)
    ->withDeadCodeLevel(100)
    ->withCodeQualityLevel(100);
