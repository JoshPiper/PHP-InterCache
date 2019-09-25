<?php

use Cache\IntegrationTests\CachePoolTest;
use Cache\IntegrationTests\SimpleCacheTest;
use Internet\InterCache\Cache\JsonFileCache;
use Internet\InterCache\SimpleCache\Wrapper;

class JsonFileCachePSR16Test extends SimpleCacheTest {
	public function createSimpleCache(){
		return new Wrapper(new JsonFileCache());
	}
}