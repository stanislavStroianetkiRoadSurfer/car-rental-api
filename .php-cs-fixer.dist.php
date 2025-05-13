<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('config')
    ->exclude('var')
    ->notPath('bin/console')
    ->notPath('public/index.php');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'linebreak_after_opening_tag' => false,
        'mb_str_functions' => true,
        'no_php4_constructor' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'php_unit_strict' => true,
        'phpdoc_order' => true,
        'semicolon_after_instruction' => true,
        'php_unit_method_casing' => ['case' => 'snake_case'],
        'strict_comparison' => true,
        'strict_param' => true,
        'concat_space' => ['spacing' => 'one'], // Enforces spaces around `.`
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false,
        ],
        'phpdoc_align' => false,
        'single_line_throw' => false,
        'no_unused_imports' => true,
        'fully_qualified_strict_types' => [
            'leading_backslash_in_global_namespace' => true,
            'import_symbols' => false,
        ],
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/var/.php_cs.cache');
