<?php

namespace AlexLawford\QuickRoute;

class QuickRoute
{
  public function __invoke(string $requestMethod, string $requestUri, array $routes) : ?Route
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

  // Matches a pretty route (e.g users/string:name) to a uri string
  // returning an array of the matches. Array will be empty [] if
  // no matches are found
  private function match(string $uri, string $pattern) : array
  {
      // Convert pretty routes to regex
      $namedMatches = ['uri_string'];

      // wildcards
      if(strpos($pattern, ':') !== false) {
          $output = [];

          $array = explode('/', $pattern);

          foreach($array as $segment) {
              if(strpos($segment, ':') !== false) {
                  $match = explode(':', $segment);
                  // replace section with correct regex
                  switch($match[0]) {
                      case 'alpha':
                          $output[] = '([a-zA-Z]+)';
                          break;
                      case 'number':
                          $output[] = '([0-9]+)';
                          break;
                      case 'string':
                          $output[] = '([a-zA-Z0-9-_]+)';
                          break;
                      default:
                          return [];
                  }
                  // Save named matches for output later
                  $namedMatches[] = $match[1];
              } else {
                  $output[] = $segment;
              }
          }
          $regex = $this->bookend(implode('/', $output));
      } else {
          $regex = $this->bookend($pattern);
      }

      $match = preg_match($regex, $uri, $matches);

      // apply our named keys to the matches
      // so, for example user/string:name
      // matching user/alex
      // would return "name" => "alex"
      if($match === 1) {
          return $this->applyKeys($namedMatches, $matches);
      }

      return [];
  }

  // Use the values in one linear array
  // as keys on a target array
  private function applyKeys(array $keys, array $target) : array
  {
      if($this->isAssociative($keys) || $this->isAssociative($target)) {
          return [];
      }
      $result = [];
      for($i = 0; $i < count($target); $i++) {
          $result[$keys[$i]] = $target[$i];
      }
      return $result;
  }

  // Check if an array is associative
  private function isAssociative(array $array) : bool
  {
      return array_values($array) !== $array;
  }

  // For regex, put necessary bits before and after
  private function bookEnd(string $pattern) : string
  {
      // add trailing slash if not already there
      if(substr($pattern, -1) !== '/') {
          $pattern .= '/';
      }
      return '~^' . $pattern . '?$~';
  }
}
