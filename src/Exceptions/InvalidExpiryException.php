<?php

namespace Internet\InterCache\Exceptions;

use Psr\Cache\InvalidArgumentException;

class InvalidExpiryException extends \InvalidArgumentException implements InvalidArgumentException {}