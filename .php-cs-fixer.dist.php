<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
        // Keep diffs minimal: do not enforce strict_types, yoda style, etc.
        'array_syntax'                       => ['syntax' => 'short'],
        'no_unused_imports'                  => true,
        'ordered_imports'                    => ['sort_algorithm' => 'alpha'],
        'single_quote'                       => true,
        'trailing_comma_in_multiline'        => ['elements' => ['arrays']],
        'no_trailing_whitespace'             => true,
        'no_whitespace_in_blank_line'        => true,
        'blank_line_after_opening_tag'       => false,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');
