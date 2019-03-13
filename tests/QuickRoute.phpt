<?php

use Tester\Assert;
use AlexLawford\QuickRoute\QuickRoute;

require '../vendor/autoload.php';

Tester\Environment::setup();

// function allowing us to test private methods
function callPrivateMethod(string $className, string $methodName, array $args)
{
  $method = new ReflectionMethod($className, $methodName);
  $method->setAccessible(true);
  return $method->invokeArgs(new $className, $args);
}

/*
  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
  ROUTER Testing
*/
// Match found
$router = new QuickRoute;

$route = $router('GET', 'greet/alex', [
    ['GET', 'greet/string:name', function($args) {
        return "Hello " . $args['name'];
    }],
]);

Assert::same(($route->args), ['uri_string' => 'greet/alex', 'name' => 'alex']);
Assert::same(($route->callback)($route->args), "Hello alex");

// Matches later route
$route2 = $router('GET', 'greet/alex', [
    ['GET', '/', function($args) {
        return "What?";
    }],
    ['GET', 'greet/string:name', function($args) {
        return "Hello " . $args['name'];
    }],
]);

Assert::same(($route2->callback)($route2->args), "Hello alex");

// No match found
$route3 = $router('GET', 'yo/alex', [
    ['GET', 'greet/string:name', function($args) {
        return "Hello " . $args['name'];
    }],
]);

Assert::null($route3);

/*
  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
  (Private) HELPER METHODS
*/
// Apply keys from one array to another
$result1 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'applyKeys', [['name', 'age'], ['alex', 31]]);
// ...Even if they are different lengths
$result2 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'applyKeys', [['name', 'age', 'occupation'], ['alex', 31]]);
Assert::same(['name' => 'alex', 'age' => 31], $result1);
Assert::same(['name' => 'alex', 'age' => 31], $result2);

// Check if an array is associative
$result3 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'isAssociative', [['name' => 'alex']]);
$result4 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'isAssociative', [['alex']]);
Assert::true($result3);
Assert::false($result4);

// Bookending
$result5 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'bookEnd', ['a']);
$result6 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'bookEnd', ['b/']);
Assert::same('~^a/?$~', $result5);
Assert::same('~^b/?$~', $result6);


/*
  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
  (Private) URI to Pattern Matching
*/
// Straight matching
$result7 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['eat/pizza', 'eat/pizza']);
Assert::same(['uri_string' => 'eat/pizza'], $result7);

// Straight matching with trailing slash ON ROUTE
$result8 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['eat/pizza', 'eat/pizza/']);
Assert::same(['uri_string' => 'eat/pizza'], $result8);

// Straight matching with trailing slash ON URI
$result9 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['eat/pizza/', 'eat/pizza']);
Assert::same(['uri_string' => 'eat/pizza/'], $result9);

// Wildcards
$result10 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['users/alex', 'users/alpha:name']);
Assert::same(['uri_string' => 'users/alex', 'name' => 'alex'], $result10);
$result11 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['users/21', 'users/number:id']);
Assert::same(['uri_string' => 'users/21', 'id' => '21'], $result11);
$result12 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['pizza/ham-and-pineapple', 'pizza/string:topping']);
Assert::same(['uri_string' => 'pizza/ham-and-pineapple', 'topping' => 'ham-and-pineapple'], $result12);

// Straight NOT matching
$result13 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['eat/pizza', 'eat/burger']);
Assert::same([], $result13);

// Wildcards NOT matching
$result14 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['users/alex31', 'users/alpha:name']);
Assert::same([], $result14);
$result15 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['users/alex', 'users/number:id']);
Assert::same([], $result15);
$result16 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['pizza/ham-and-pineapple!!', 'pizza/string:topping']);
Assert::same([], $result16);

// Multiple wildcards
$result17 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['users/alex/777', 'users/string:name/number:age']);
Assert::same(['uri_string' => 'users/alex/777', 'name' => 'alex', 'age' => '777'], $result17);

// No results if unknown wildcards are used
$result18 = callPrivateMethod('AlexLawford\QuickRoute\QuickRoute', 'match', ['users/true', 'users/bool:exists']);
Assert::same([], $result18);









//
