<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude([
        'Locale',
        'Template'
    ])
    ->in(__DIR__)
;
$config = new PhpCsFixer\Config();
$config
    ->setUsingCache(true)
    ->setFinder($finder)
;
return $config;
