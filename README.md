
# Quick Route

## Introduction
Quick route provides a simple alternative to more advanced routers, suitable for small projects and prototyping. It is also purely functional and has no dependencies, so it's easy to slot into any project.

Routes patterns look like this:

    'users/alpha:name/number:age'

## Install

Via composer:

    composer require alexlawford/quickroute

## Example Usage

    <?php

    require '../vendor/autoload.php';

    // Get method and URI from wherever, e.g
    // $method = $_SERVER['REQUEST_METHOD'];
    // $uri = $_SERVER['REQUEST_URI'];
    // the below is for easy testing:
    $method = 'GET';
    $uri = 'hello/alex/';

    $route = (new AlexLawford\QuickRoute)(
        $method,
        $uri,
        [
            ['GET', 'hello/string:name', function($args) {
                echo "<p>Hello $args['name']</p>"
            }],
        ]
    );

    if($route !== null) {
        ($route->callback)($route->args);
    } else {
        // 404
    }
