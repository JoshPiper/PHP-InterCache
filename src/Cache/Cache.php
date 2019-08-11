<?php

namespace Internet\InterCache\Cache;
use Internet\InterCache\Result\CacheResult;

/**
 * The cache interface is designed to allow abstraction in loading / data store implementation.
 * Interface Cache
 * @package Internet\InterCache
 */
interface Cache {
	/** Check if a key is stored in the cache.
	 * @param string $key The cache key to check.
	 * @return bool If the cache has that key in store.
	 */
	public function has(string $key): bool;

	/** Pull a var out of the cache.
	 * @param $key
	 * @return CacheResult|null
	 */
	public function pull($key): ?CacheResult;

	/** Store a var in the cache.
	 * @param string $key The key to store at.
	 * @param mixed $value The value to store.
	 * @param int $ttl Time to live
	 */
	public function store(string $key, $value, $ttl=60): void;

	/**
	 * Load the internal cache data store from a backing source.
	 */
	public function load(): void;

	/**
	 * Save the internal cache data store to a backing source.
	 */
	public function save(): void;
}