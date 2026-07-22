<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
        'node_modules'
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,

        'ordered_imports' => true,
        'no_unused_imports' => true,
    ])
    ->setFinder($finder)
;
