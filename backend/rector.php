<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;
use RectorLaravel\Rector\Class_\ModelCastsPropertyToCastsMethodRector;
use RectorLaravel\Rector\ClassMethod\MigrateToSimplifiedAttributeRector;
use RectorLaravel\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/database',
        __DIR__ . '/tests',
    ])
    ->withCache(__DIR__ . '/.tools/cache/rector')
    ->withParallel(120, 16, 16)
    ->withSets([
        // PHP
        LevelSetList::UP_TO_PHP_84,
        SetList::PRIVATIZATION,
        //SetList::INSTANCEOF,

        // PHPUnit
        PHPUnitSetList::PHPUNIT_110,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,

        // Laravel
        LaravelSetList::LARAVEL_90,
        LaravelSetList::LARAVEL_100,
        LaravelSetList::LARAVEL_110,
        LaravelSetList::LARAVEL_120,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
    ])
    ->withImportNames(true, true, true, true);
//    ->withSkip([
//        // Disable for now
//        ClosureToArrowFunctionRector::class,
//        ModelCastsPropertyToCastsMethodRector::class,
//        MigrateToSimplifiedAttributeRector::class,
//        ScopeNamedClassMethodToScopeAttributedClassMethodRector::class,
//
//        // Disable until PHPStan is able to detect type errors
//        NullToStrictStringFuncCallArgRector::class,
//        ReturnNeverTypeRector::class
//    ]);
