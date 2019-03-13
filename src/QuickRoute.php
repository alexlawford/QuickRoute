<?php

namespace AlexLawford\QuickRoute;

class QuickRoute
{
  public function __invoke(string $requestMethod, string $requestUri, array $routes): ?Route
  {
    if(count($routes) === 0){
      return null;
    }

    [$method, $pattern, $callback] = $routes[0];

    if($method === $requestMethod) {
      $matches = $this->match($requestUri, $pattern);
      if(count($matches) > 0) {
        return new Route($callback, $matches);
      }
    }

    return $this->__invoke($requestMethod, $requestUri, array_slice($routes, 1));
  }

  private function match(string $requestUri, string $pattern): array
  {
    return ["test"];
  }
}
