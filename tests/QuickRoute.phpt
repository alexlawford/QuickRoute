<?php

use Tester\Assert;
use AlexLawford\QuickRoute\QuickRoute;

require '../vendor/autoload.php';

Tester\Environment::setup();

$router = new QuickRoute;

$route = $router('GET', 'greet/alex', [
    ['GET', 'greet/string:name', function($args) {
        echo "Hello " . $args['name'];
    }],
]);

Assert::same(($route->callback)($route->args), "Hello alex");
