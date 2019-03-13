<?php

namespace AlexLawford\QuickRoute;

class Route
{
  public $callback;
  public $args;

  function __construct(callable $callback, array $args)
  {
    $this->callback = $callback;
    $this->args = $args;
  }
}
