<?php

namespace Internet\InterCache\Exceptions;
use Exception;
use Psr\Cache\CacheException;

class InvalidKeyException extends Exception implements CacheException {}