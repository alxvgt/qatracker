<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_indentation' => true,
        'blank_line_before_statement' => true,
        'declare_strict_types' => true,
        'fopen_flags' => false,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => null,
            'import_functions' => true,
        ],
        'heredoc_to_nowdoc' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false,
        ],
        'multiline_whitespace_before_semicolons' => true,
        'native_constant_invocation' => false,
        'native_function_invocation' => false,
        'no_blank_lines_after_class_opening' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_useless_else' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => [
            'imports_order' => [
                'class',
                'const',
                'function',
            ],
        ],
        'phpdoc_align' => [
            'align' => 'left',
        ],
        'random_api_migration' => false, // conversion to random_int is slow
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => ['arrays', 'arguments', 'parameters', 'match'],
        ],
        'void_return' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'fully_qualified_strict_types' => false,
    ])
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setFinder($finder);
