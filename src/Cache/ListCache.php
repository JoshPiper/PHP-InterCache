<?php

namespace Internet\InterCache\Cache;

use Fig\Cache\KeyValidatorTrait;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Internet\InterCache\Result\StdCacheResult;
use Internet\InterCache\Exceptions\InvalidKeyException;

/**
 * Class ListCache
 * @package Internet\InterCache\Cache
 */
abstract class ListCache implements CacheItemPoolInterface {
	use KeyValidatorTrait;
	protected $data = [];

	public function coerceKey($key){
		$this->validateKey($key);
		return $key;
	}

	/** {@inheritDoc}
	 * @param string $key
	 * @return StdCacheResult
	 * @throws InvalidKeyException
	 */
	public function getItem($key): StdCacheResult{
		$key = $this->coerceKey($key);

		if (isset($this->data[$key])){
			[$expiry, $data] = $this->data[$key];
			return new StdCacheResult($key, true, $data, $expiry);
		}

		return new StdCacheResult($key);
	}

	/**
	 * @param string[] $keys
	 * @return array
	 * @throws InvalidKeyException
	 * @throws InvalidArgumentException
	 */
	public function getItems(array $keys = []): array{
		if (count($keys) === 0){
			return [];
		}

		$data = [];
		foreach ($keys as $key){
			$data[$key] = $this->getItem($key);
		}

		return $data;
	}

	/**
	 * @param string $key
	 * @return bool
	 * @throws InvalidKeyException
	 */
	public function hasItem($key){
		$key = $this->coerceKey($key);
		return isset($this->data[$key]) && (!$this->data[$key][0] || $this->data[$key][0] > time());
	}

	public function clear(){
		$this->data = [];
		return $this->commit();
	}

	/**
	 * @param string $key
	 * @return bool
	 * @throws InvalidKeyException
	 */
	public function deleteItem($key){
		unset($this->data[$this->coerceKey($key)]);
		return $this->commit();
	}

	/**
	 * @param array $keys
	 * @return array|bool
	 * @throws InvalidKeyException
	 */
	public function deleteItems(array $keys){
		if (count($keys) === 0){
			return true;
		}

		array_map([$this, 'deleteItem'], $keys);
		return true;
	}

	public function save(CacheItemInterface $item){
		$this->saveDeferred($item);
		return $this->commit();
	}

	public function saveDeferred(CacheItemInterface $item){
		/** @var $item StdCacheResult */
		if (is_object($item->get())){
			$this->data[$item->getKey()] = [$item->getExpiry(), clone $item->get()];
		} else {
			$this->data[$item->getKey()] = [$item->getExpiry(), $item->get()];
		}

		return true;
	}

	public function __destruct(){
		$this->commit();
	}
}