<?php

namespace Internet\InterCache\SimpleCache;
use Composer\Cache;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\SimpleCache\CacheInterface as SimpleCacheInterface;
use Psr\Cache\CacheItemPoolInterface as CacheInterface;
use Internet\InterCache\Exceptions\SimpleCacheInvalidArgumentException;

/** Provides a wrapper for a PSR-6 compliant cache, providing a PSR-16 interface
 * Class Wrapper
 * @package Internet\InterCache\SimpleCache
 */
class Wrapper implements SimpleCacheInterface {
	/**
	 * @var CacheInterface Internal PSR-6 cache to use for the interface.
	 */
	private $cache;

	/**
	 * Wrapper constructor.
	 * @param CacheInterface $cache PSR-6 cache to use internally.
	 */
	public function __construct(CacheInterface $cache){
		$this->cache = $cache;
	}

	/**
	 * {@inheritDoc}
	 * @throws SimpleCacheInvalidArgumentException
	 */
	public function get($key, $default = null){
		try {
			$res = $this->cache->getItem($key);

			if ($res->isHit()){
				return $res->get();
			} else {
				return $default;
			}
		} catch (InvalidArgumentException $exception){
			throw new SimpleCacheInvalidArgumentException("Bad Key provided.", 1, $exception);
		}
	}

	/**
	 * {@inheritDoc}
	 * @throws SimpleCacheInvalidArgumentException
	 */
	public function set($key, $value, $ttl = null){
		try {
			$res = $this->cache->getItem($key);
			$res->expiresAfter($ttl);
			$res->set($value);
			return $this->cache->save($res);
		} catch (InvalidArgumentException $exception){
			throw new SimpleCacheInvalidArgumentException("Bad Key provided to set.", 1, $exception);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function clear(){
		return $this->cache->clear();
	}

	/**
	 * {@inheritDoc}
	 * @param string[] $keys
	 * @throws SimpleCacheInvalidArgumentException
	 */
	public function getMultiple($keys, $default = null){
		if (!is_iterable($keys)){
			throw new SimpleCacheInvalidArgumentException("No iterable provided to getMultiple");
		}

		try {
			$out = [];
			foreach ($keys as $key){
				$out[$key] = $this->get($key, $default);
			}
			return $out;
		} catch (InvalidArgumentException $exception){
			throw new SimpleCacheInvalidArgumentException("Bad Key: ${key}", 1, $exception);
		}
	}

	/**
	 * {@inheritDoc}
	 * @throws SimpleCacheInvalidArgumentException
	 */
	public function setMultiple($values, $ttl = null){
		if (!is_iterable($values)){
			throw new SimpleCacheInvalidArgumentException("No iterable provided to setMultiple");
		}

		try {
			foreach ($values as $key => $value){
				if (is_int($key)){$key = (string)$key;}
				$this->set($key, $value, $ttl);
			}
			return true;
		} catch (InvalidArgumentException $exception){
			throw new SimpleCacheInvalidArgumentException("Bad Key: ${key}", 1, $exception);
		}
	}

	/** {@inheritDoc}
	 * @param string $key
	 * @return bool|void
	 * @throws SimpleCacheInvalidArgumentException
	 */
	public function delete($key){
		try {
			$this->cache->deleteItem($key);
			return true;
		} catch (InvalidArgumentException $exception){
			throw new SimpleCacheInvalidArgumentException("Bad Key provided to delete()", 1, $exception);
		}
	}

	/**
	 * {@inheritDoc}
	 * @throws SimpleCacheInvalidArgumentException
	 */
	public function deleteMultiple($keys){
		if (!is_iterable($keys)){
			throw new SimpleCacheInvalidArgumentException("No iterable provided to deleteMultiple");
		}

		try {
			foreach ($keys as $key){
				$this->delete($key);
			}
			return true;
		} catch (InvalidArgumentException $exception){
			throw new SimpleCacheInvalidArgumentException("Bad Key: ${key}", 1, $exception);
		}
	}

	/** {@inheritDoc}
	 * @param string $key
	 * @return bool
	 * @throws SimpleCacheInvalidArgumentException
	 */
	public function has($key){
		try {
			return $this->cache->hasItem($key);
		} catch (InvalidArgumentException $exception){
			throw new SimpleCacheInvalidArgumentException("Bad Key provided to has()", 1, $exception);
		}
	}
}