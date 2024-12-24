<?php

use TypistTech\Imposter\ImposterFactory;

require_once __DIR__ . '/vendor/autoload.php';

$imposter = ImposterFactory::forProject(__DIR__);
$imposter->run();
