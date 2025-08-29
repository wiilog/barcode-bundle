<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'var',
        'vendor'
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PHP71Migration' => true,
        '@PHP73Migration' => true,
        '@PHP74Migration' => true,
        '@Symfony' => true,
        'function_declaration' => ['closure_function_spacing' => 'none'],
        'heredoc_indentation' => ['indentation' => 'same_as_start'],
        'list_syntax' => ['syntax' => 'long'],
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'remove_inheritdoc' => true],
        'no_useless_else' => true,
        'no_useless_return' => true,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_types_order' => false,
        'single_line_throw' => false,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache');
