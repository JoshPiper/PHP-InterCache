<?php

namespace Internet\InterCache\Cache;

use Fig\Cache\KeyValidatorTrait;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Internet\InterCache\Result\StdCacheResult;
use Internet\InterCache\Exceptions\InvalidKeyException;

/**
 * Class ListCache
 * @package Internet\InterCache\Cache
 */
abstract class ListCache implements CacheItemPoolInterface {
	use KeyValidatorTrait;
	protected $data = [];

	/** {@inheritDoc}
	 * @param string $key
	 * @return StdCacheResult
	 * @throws InvalidKeyException
	 */
	public function getItem($key): StdCacheResult{
		if (!$this->validateKey($key)){
			throw new InvalidKeyException();
		}

		if (isset($this->data[$key])){
			[$expiry, $data] = $this->data[$key];
			return new StdCacheResult($key, $data, $expiry);
		}

		return new StdCacheResult($key);
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

		array_map($keys, [$this, 'validateKey']);
		return array_map([$this, 'getItem'], $keys);
	}

	/**
	 * @param string $key
	 * @return bool
	 * @throws InvalidKeyException
	 */
	public function hasItem($key){
		if (!$this->validateKey($key)){
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
		if (!$this->validateKey($key)){
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

		array_map($keys, [$this, 'validateKey']);
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

	public function validate($key){
		return $this->validateKey($key);
	}
}