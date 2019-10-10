<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Internet\InterCache\Cache\JsonFileCache;

final class JsonCacheTestManual extends TestCase {
	public function testBasic(): void{
		$cache = new JsonFileCache(dirname(__DIR__) . '/.testcache');
		$res = $cache->getItem("test");
//		var_dump($res);

		$res->set("dick");
		$cache->save($res);

		$res = $cache->getItem("test");
//		var_dump($res);

		$this->assertTrue($res->isHit());
	}
}