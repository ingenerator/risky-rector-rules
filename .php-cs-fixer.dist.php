<?php


use PhpCsFixer\Finder;
use PhpCsFixer\Config;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = new Finder()
    ->in(__DIR__);

return new Config()
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        // --- Initial set taken from Behat/Behat
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'concat_space' => false, // override Symfony
        'global_namespace_import' => [ //override Symfony
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'single_line_throw' => false, //override Symfony
        'yoda_style' => false, //override Symfony
        // --- Custom inGenerator overrides
        'not_operator_with_space' => true, // ensure space either side of the `!` operator
        'phpdoc_to_comment' => [ // Keep as a phpdoc if it contains the `@var` annotation for phpstorm
            'ignored_tags' => ['var'],
        ],
    ])
    ->setFinder($finder);
