

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
