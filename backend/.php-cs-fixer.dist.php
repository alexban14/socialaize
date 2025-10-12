<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/database',
        __DIR__ . '/tests',
    ])
    ->filter(function (\Symfony\Component\Finder\SplFileInfo $file) {
        $ignoreFiles = [
            __DIR__ . '/app/Http/routes.php'
        ];

        return !in_array($file->getRealPath(), $ignoreFiles);
    })
;

return (new PhpCsFixer\Config())
    ->setRules([
//        '@PHP82Migration' => true,
//        '@PHP82Migration:risky' => true,
        '@PSR12' => true,

        // Set overrides
        'assign_null_coalescing_to_coalesce_equal' => false,
        'trailing_comma_in_multiline' => false,
        'use_arrow_functions' => false,
        'declare_strict_types' => false, // should be enabled after PHPStan can detect types
        'void_return' => false, // should be enabled after PHPStan can detect types

        // Array Notation
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => [
            'ensure_single_space' => true
        ],

        // String Notation
        'single_quote' => true,

        // Imports
        'fully_qualified_strict_types' => [
            'import_symbols' => true
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true
        ],
        'no_leading_import_slash' => true,
        'no_unused_imports' => true,
        'ordered_imports' => [
            'imports_order' => [
                'class',
                'function',
                'const'
            ],
            'sort_algorithm' => 'alpha'
        ],
        'single_import_per_statement' => [
            'group_to_single_imports' => false
        ],
        'single_line_after_imports' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_between_import_groups' => true,
        'blank_lines_before_namespace' => true,
        'blank_line_after_namespace' => true,

        // PHPDoc
        'phpdoc_array_type' => true,
        'no_empty_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => [
            'remove_inheritdoc' => true,
            'allow_mixed' => true // should be disabled after PHPStan can detect types
        ],
        'no_blank_lines_after_phpdoc' => true,
        'phpdoc_order' => [
            'order' => [
                'param',
                'return',
                'throws'
            ]
        ],
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_to_comment' => [
            'allow_before_return_statement' => false
        ],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_indent' => true,
        'phpdoc_align' => [
            'align' => 'left'
        ],
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_var_without_name' => true,
        'phpdoc_types' => [
            'groups' => [
                'simple',
                'alias',
                'meta'
            ]
        ],
        'phpdoc_types_order' => [
            'sort_algorithm' => 'none',
            'null_adjustment' => 'always_last'
        ],
        'phpdoc_scalar' => true,
        'phpdoc_param_order' => true,
        'align_multiline_comment' => [
            'comment_type' => 'phpdocs_only'
        ]
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/.tools/cache/php-cs-fixer.json');
