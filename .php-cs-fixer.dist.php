<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(true)
    ->setUnsupportedPhpVersionAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,

        'php_unit_attributes' => true,
        'php_unit_test_annotation' => false,
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,

        'phpdoc_to_comment' => false,

        'php_unit_method_casing' => true,
    ])
    ->setFinder(
        (new Finder())
            ->in(__DIR__)
            ->exclude([
                'vendor',
                'var',
                'cache',
            ])
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
    );
