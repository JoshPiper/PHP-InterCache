<?php

namespace Internet\InterCache\SimpleCache;
use Composer\Cache;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\SimpleCache\CacheInterface as SimpleCacheInterface;
use Psr\Cache\CacheItemPoolInterface as CacheInterface;

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
	 * @throws InvalidArgumentException
	 */
	public function get($key, $default = null){
		$res = $this->cache->getItem($key);

		if ($res->isHit()){
			return $res->get();
		} else {
			return $default;
		}
	}

	/**
	 * {@inheritDoc}
	 * @throws InvalidArgumentException
	 */
	public function set($key, $value, $ttl = null){
		$res = $this->cache->getItem($key);
		$res->expiresAfter($ttl);
		$res->set($value);
		$this->cache->save($res);
	}

	/**
	 * {@inheritDoc}
	 */
	public function clear(){
		return false;
	}

	/**
	 * {@inheritDoc}
	 * @param string[] $keys
	 * @throws InvalidArgumentException
	 */
	public function getMultiple($keys, $default = null){
		$out = [];
		foreach ($this->cache->getItems($keys) as $res){
			/** @var $res CacheItemInterface */
			if ($res->isHit()){
				$out[$res->getKey()] = $res->get();
			}
		}
		return $out;
	}

	/**
	 * {@inheritDoc}
	 * @throws InvalidArgumentException
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function setMultiple($values, $ttl = null){
		foreach ($values as $key => $value){
			$this->set($key, $value, $ttl);
		}
		return true;
	}

	/** {@inheritDoc}
	 * @param string $key
	 * @return bool|void
	 * @throws InvalidArgumentException
	 */
	public function delete($key){
		$this->cache->deleteItem($key);
		return true;
	}

	/**
	 * {@inheritDoc}
	 * @throws InvalidArgumentException
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function deleteMultiple($keys){
		foreach ($keys as $key){
			$this->delete($key);
		}
		return true;
	}

	/** {@inheritDoc}
	 * @param string $key
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function has($key){
		return $this->cache->hasItem($key);
	}
}