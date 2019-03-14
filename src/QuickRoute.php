<?php

namespace AlexLawford\QuickRoute;

class QuickRoute
{
  /**
   * Returns the first route (callable + arguments) that matches the method and pattern provided
   * @param string $requestMethod (GET, POST, PUT etc)
   * @param string $requestUri the uri from the server request
   * @param array $routes an array of arrays in the format [ ['METHOD', 'pattern/to/match', $callback] ]
   * @return Route object with two properties: callable $callback and array $args
   */
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

  /**
   * Match a route pattern with optional wildcards (e.g users/string:name)
   * to a string, returning an array of the matches (including the whole string)
   * Array will be empty if no matches are found.
   * @param string $uri the string to match against
   * @param string $pattern pattern, w/ optional wildcards (alpha, number, string)
   * @return array array of matches
   */
  private function match(string $uri, string $pattern) : array
  {
      $namedMatches = ['uri_string'];

      // wildcards
      if(strpos($pattern, ':') !== false) {
          $output = [];

          $array = explode('/', $pattern);

          foreach($array as $segment) {
              if(strpos($segment, ':') !== false) {
                  $match = explode(':', $segment);
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
                          return []; // illegal wildcard
                  }
                  $namedMatches[] = $match[1];
              } else {
                  $output[] = $segment;
              }
          }
          $regex = $this->bookend(implode('/', $output));
      } else {
          // no wildcards
          $regex = $this->bookend($pattern);
      }

      $match = preg_match($regex, $uri, $matches);

      if($match === 1) {
          return $this->applyKeys($namedMatches, $matches);
      }

      return [];
  }

  /**
   * Use the values in one linear array as keys on a target array
   * @param array $keys
   * @param array $target
   * @return array
   */
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

  /**
   * Check if an array is associative
   * @param array $array
   * @return bool
   */
  private function isAssociative(array $array) : bool
  {
      return array_values($array) !== $array;
  }

  /**
   * For match regex, put necessary bits before and after
   * @param string $pattern
   * @return string the decorated string
   */
  private function bookEnd(string $pattern) : string
  {
      // add trailing slash if not already there
      if(substr($pattern, -1) !== '/') {
          $pattern .= '/';
      }
      return '~^' . $pattern . '?$~';
  }
}
