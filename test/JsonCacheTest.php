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
		$cache->load();

		$this->assertInstanceOf(JsonFileCache::class, $cache);
		return $cache;
	}

	/**
	 * @depends testIsCreatable
	 * @param JsonFileCache $cache
	 * @return JsonFileCache
	 * @throws CacheStoreException
	 */
	public function testVarStores(JsonFileCache $cache): JsonFileCache {
		$cache->store("validtest", "value1", 10);
		$cache->store("invalidtest", "value2", -10);

		return $cache;
	}

	/**
	 * @depends testVarStores
	 * @param JsonFileCache $cache
	 * @return JsonFileCache
	 */
	public function testVarRetrieves(JsonFileCache $cache): JsonFileCache {
		$result = $cache->pull("validtest");
		$this->assertInstanceOf(CacheResult::class, $this);
		$this->assertEquals($result->expired(), false);
		$this->assertEquals($result->value(), "value1");

		$result = $cache->pull("invalidtest");
		$this->assertInstanceOf(CacheResult::class, $this);
		$this->assertEquals($result->expired(), true);
		$this->assertEquals($result->value(), "value2");

		return $cache;
	}

	/**
	 * @depends testVarRetrieves
	 * @param JsonFileCache $cache
	 * @return JsonFileCache
	 */
	public function testSaves(JsonFileCache $cache): JsonFileCache{
		$cache->save();
		return $cache;
	}
}