<?php

namespace Internet\InterCache\Cache;
use DateTimeInterface;
use Internet\InterCache\Exceptions\InvalidKeyException;
use Internet\InterCache\Result\StdCacheResult;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class ListCache
 * @package Internet\InterCache\Cache
 */
abstract class ListCache implements CacheItemPoolInterface {
	protected $data = [];

	protected static function illegalKey($key): bool{
		return !preg_match("/[^\w_\-.]/", $key);
	}

	/**
	 * @param string $key
	 * @return StdCacheResult
	 * @throws InvalidKeyException
	 */
	public function getItem($key): StdCacheResult{
		if ($this::illegalKey($key)){throw new InvalidKeyException();}

		if (isset($this->data[$key])){
			[$expiry, $data] = $this->data[$key];
			return new StdCacheResult($key, $data, $expiry);
		} else {
			return new StdCacheResult($key);
		}
	}

	/**
	 * @param array $keys
	 * @return array
	 * @throws InvalidKeyException
	 */
	public function getItems(array $keys = []): array{
		if (count($keys) === 0){
			return [];
		} elseif (count(array_filter($keys, [$this, 'illegalKey'])) > 0){
			throw new InvalidKeyException();
		} else {
			return array_map([$this, 'getItem'], $keys);
		}
	}

	public function hasItem($key){
		if ($this::illegalKey($key)){throw new InvalidKeyException();}
		return isset($this->data[$key]);
	}

	public function clear(){
		$this->data = [];
		$this->commit();
	}

	public function deleteItem($key){
		if ($this::illegalKey($key)){throw new InvalidKeyException();}
		$this->commit();
		return true;
	}

	public function deleteItems(array $keys){
		if (count($keys) === 0){
			return [];
		} elseif (count(array_filter($keys, [$this, 'illegalKey'])) > 0){
			throw new InvalidKeyException();
		} else {
			array_map([$this, 'deleteItem'], $keys);
			return true;
		}
	}

	public function save(CacheItemInterface $item){
		$this->saveDeferred($item);
		$this->commit();
	}

	public function saveDeferred(CacheItemInterface $item){
		$this->data[$item->getKey()] = [$item->getExpiry(), $item->get()];
	}
}