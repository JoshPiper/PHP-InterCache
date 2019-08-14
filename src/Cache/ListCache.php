<?php

namespace Internet\InterCache\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Internet\InterCache\Result\StdCacheResult;
use Internet\InterCache\Exceptions\InvalidKeyException;

/**
 * Class ListCache
 * @package Internet\InterCache\Cache
 */
abstract class ListCache implements CacheItemPoolInterface {
	protected $data = [];

	/**
	 * @param string $key
	 * @return StdCacheResult
	 * @throws InvalidKeyException
	 */
	public function getItem($key): StdCacheResult{
		if ($this::illegalKey($key)){
			throw new InvalidKeyException();
		}
		if (isset($this->data[$key])){
			[$expiry, $data] = $this->data[$key];
			return new StdCacheResult($key, $data, $expiry);
		}
		return new StdCacheResult($key);
	}

	/** Check if a key is illegal under PSR-6.
	 * @param $key string
	 * @return bool
	 */
	public static function illegalKey(string $key): bool{
		return preg_match("/[^\w\-.]/", $key);
	}

	/**
	 * @param string[] $keys
	 * @return array
	 * @throws InvalidKeyException
	 */
	public function getItems(array $keys = []): array{
		if (count($keys) === 0){
			return [];
		}

		if (count(array_filter($keys, [$this, 'illegalKey'])) > 0){
			throw new InvalidKeyException();
		}

		return array_map([$this, 'getItem'], $keys);
	}

	/**
	 * @param string $key
	 * @return bool
	 * @throws InvalidKeyException
	 */
	public function hasItem($key){
		if ($this::illegalKey($key)){
			throw new InvalidKeyException();
		}

		return isset($this->data[$key]);
	}

	public function clear(){
		$this->data = [];
		$this->commit();
	}

	/**
	 * @param string $key
	 * @return bool
	 * @throws InvalidKeyException
	 */
	public function deleteItem($key){
		if ($this::illegalKey($key)){
			throw new InvalidKeyException();
		}

		unset($this->data[$key]);

		$this->commit();
		return true;
	}

	/**
	 * @param array $keys
	 * @return array|bool
	 * @throws InvalidKeyException
	 */
	public function deleteItems(array $keys){
		if (count($keys) === 0){
			return [];
		}

		if (count(array_filter($keys, [$this, 'illegalKey'])) > 0){
			throw new InvalidKeyException();
		}

		array_map([$this, 'deleteItem'], $keys);
		return true;
	}

	public function save(CacheItemInterface $item){
		$this->saveDeferred($item);
		$this->commit();
	}

	public function saveDeferred(CacheItemInterface $item){
		/** @var $item StdCacheResult */
		$this->data[$item->getKey()] = [$item->getExpiry(), $item->get()];
	}
}