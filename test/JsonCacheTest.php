<?php

declare(strict_types=1);

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

	/**
	 * @depends testIsCreatable
	 */
	public function testValidKey(JsonFileCache $cache){
		$this->assertTrue($cache->validate("help"));
		$this->assertTrue($cache->validate("big-dickers_123"));
	}

	/**
	 * @depends testIsCreatable
	 */
	public function testInvalidKey(JsonFileCache $cache){
		$this->expectException(\Fig\Cache\InvalidArgumentException::class);

		$cache->validate("help{}!");
		$cache->validate("+@big-dickers_123");
	}
}