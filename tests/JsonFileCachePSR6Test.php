<?php

use Cache\IntegrationTests\CachePoolTest;
use Internet\InterCache\Cache\JsonFileCache;

final class JsonFileCachePSR6Test extends CachePoolTest {
	private $cacheFile = __DIR__ . '/.cachestore.json';

	public function createCachePool(){
		return new JsonFileCache($this->cacheFile);
	}

	public function tearDown(){
		parent::tearDown();

		if (file_exists($this->cacheFile)){
			unlink($this->cacheFile);
		}
	}
}