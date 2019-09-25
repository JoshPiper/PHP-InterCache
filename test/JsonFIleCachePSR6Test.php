<?php

use Cache\IntegrationTests\CachePoolTest;
use Internet\InterCache\Cache\JsonFileCache;

class JsonFIleCachePSR6Test extends CachePoolTest {
	public function createCachePool(){
		return new JsonFileCache();
	}
}