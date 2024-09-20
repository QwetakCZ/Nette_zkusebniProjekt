<?php

declare(strict_types=1);
use Nette\Http\Session;
require __DIR__ . '/../vendor/autoload.php';

$configurator = App\Bootstrap::boot();
$container = $configurator->createContainer();

$session = $container->getByType(Session::class);

if (!$session->isStarted()) {
    $session->start();
}

$application = $container->getByType(Nette\Application\Application::class);
$application->run();

