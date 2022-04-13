<?php

declare(strict_types=1);

$packageFile = __DIR__ . '/../package.json';
try {
	$json = @file_get_contents($packageFile);
	$package = Nette\Utils\Json::decode($json !== false ? $json : '');
} catch (Nette\Utils\JsonException $e) {
	$package = null;
}

$version = $package->version ?? null;

return [
	'parameters' => [
		'version' => [
			'package' => $version,
			'console' => is_string($version) || is_int($version) || is_float($version) ? $version : 'UNKNOWN',
		],
	],
];
