<?php

namespace Internet\InterCache\Exceptions;
use Exception;
use Psr\Cache\CacheException;

class InvalidExpiryException extends Exception implements CacheException {}