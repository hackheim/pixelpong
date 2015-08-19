<?php

use DI\ContainerBuilder;
use Doctrine\Common\Cache\ArrayCache;

require __DIR__ . '/../vendor/autoload.php';
$containerBuilder = new ContainerBuilder();
$containerBuilder->setDefinitionCache(new ArrayCache());
$containerBuilder->addDefinitions(__DIR__ . '/di-config.php');
$containerBuilder->useAutowiring(true);
$container = $containerBuilder->build();
return $container;
