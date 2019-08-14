<?php

declare(strict_types=1);

use Internet\InterCache\Exceptions\CacheStoreException;
use Internet\InterCache\Result\CacheResult;
use PHPUnit\Framework\TestCase;
use Internet\InterCache\Cache\JsonFileCache;

final class JsonCacheTest extends TestCase {
	/**
	 * Test the creation of the Json File Cache object
	 * @return JsonFileCache
	 */
	public function testIsCreatable(): JsonFileCache{
		$cache = new JsonFileCache(dirname(__DIR__) . '/cache/.testcache');

		$this->assertInstanceOf(JsonFileCache::class, $cache);
		return $cache;
	}

	public function testValidKey(){
		$this->assertFalse(JsonFileCache::illegalKey("help"));
		$this->assertFalse(JsonFileCache::illegalKey("big-dickers_123"));
	}

	public function testInvalidKey(){
		$this->assertTrue(JsonFileCache::illegalKey("help!"));
		$this->assertTrue(JsonFileCache::illegalKey("+big-dickers_123"));
	}
}