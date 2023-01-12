<?php

declare(strict_types=1);

use PhpCsFixer\Config;

require __DIR__ . '/vendor/autoload.php';

return (new Config())
    ->setFinder(
	PhpCsFixer\Finder::create()
	    ->in(__DIR__ . '/src')
	    ->in(__DIR__ . '/tests')
    )
    ->setRules([
	'line_ending' => false,
	'concat_space' => [
	    'spacing' => 'one',
	],
	'cast_spaces' => [
	    'space' => 'none',
	],
	'not_operator_with_successor_space' => false,
	'simplified_null_return' => false,
	'explicit_string_variable' => true,
	'phpdoc_to_comment' => false,
    ])
    ->setRiskyAllowed(true);
