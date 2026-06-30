<?php

use Ergebnis\PhpCsFixer\Config;
use PhpCsFixer\Finder;

$ruleSet = Config\RuleSet\Php80::create()
    ->withRules(Config\Rules::fromArray([
        'phpdoc_summary' => false,
        'php_unit_test_class_requires_covers' => false,
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one',
                'method' => 'one',
                'property' => 'one',
                'trait_import' => 'none',
            ],
        ],
        'error_suppression' => [
            'noise_remaining_usages' => false,
        ],
    ]));

$config = Config\Factory::fromRuleSet($ruleSet);

$finder = Finder::create()
    ->files()
    ->in(__DIR__)
    ->exclude('tests/__snapshots__');

$config->setFinder($finder);
$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php-cs-fixer.cache');

return $config;
