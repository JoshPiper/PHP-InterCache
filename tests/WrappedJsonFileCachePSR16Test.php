<?php

use Cache\IntegrationTests\CachePoolTest;
use Cache\IntegrationTests\SimpleCacheTest;
use Internet\InterCache\Cache\JsonFileCache;
use Internet\InterCache\SimpleCache\Wrapper;

final class WrappedJsonFileCachePSR16Test extends SimpleCacheTest {
	private $cacheFile = __DIR__ . '/.cachestore.json';

	public function createSimpleCache(){
		return new Wrapper(new JsonFileCache($this->cacheFile));
	}

	protected function tearDown(){
		parent::tearDown();

		if (file_exists($this->cacheFile)){
			unlink($this->cacheFile);
		}
	}
}