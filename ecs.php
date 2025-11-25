<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (
    ECSConfig $ecsConfig,
): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/ecs.php',
    ]);

    $ecsConfig->import('vendor/sylius-labs/coding-standard/ecs.php');

    $ecsConfig->skip([
        __DIR__ . '/tests/Application/var/',
        VisibilityRequiredFixer::class => ['*Spec.php'],
    ]);

    $ecsConfig->rules([
        NoUnusedImportsFixer::class,
        SingleImportPerStatementFixer::class,
        NoLeadingImportSlashFixer::class,
        FullyQualifiedStrictTypesFixer::class,
    ]);

    $ecsConfig->ruleWithConfiguration(GlobalNamespaceImportFixer::class, [
        'import_classes' => true,
        'import_constants' => false,
        'import_functions' => false,
    ]);

    $ecsConfig->ruleWithConfiguration(ReferenceUsedNamesOnlySniff::class, [
        'allowFallbackGlobalConstants' => true,
        'allowFallbackGlobalFunctions' => true,
        'allowFullyQualifiedGlobalClasses' => false,
        'allowFullyQualifiedGlobalConstants' => true,
        'allowFullyQualifiedGlobalFunctions' => true,
        'allowFullyQualifiedNameForCollidingClasses' => true,
        'allowFullyQualifiedNameForCollidingConstants' => true,
        'allowFullyQualifiedNameForCollidingFunctions' => true,
        'searchAnnotations' => true,
    ]);
};
