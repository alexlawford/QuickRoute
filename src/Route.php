<?php

namespace AlexLawford\QuickRoute;

class Route
{
  /**
   * @var callable $callback
   */
  public $callback;

  /**
   * @var array $args
   */
  public $args;

  /**
   * Constructor
   * @param callable $callback
   * @param array $args
   * @return void
   */
  function __construct(callable $callback, array $args)
  {
    $this->callback = $callback;
    $this->args = $args;
  }
}
