<?php

namespace Internet\InterCache\Cache;
use DateTimeInterface;
use Internet\InterCache\Exceptions\CacheStoreException;
use Internet\InterCache\Result\CacheResult;
use Internet\InterCache\Result\StdCacheResult;

abstract class ListCache implements Cache {
	protected $data = [];

	public function has(string $key): bool{
		return isset($this->data[$key]);
	}

	public function pull($key): ?CacheResult{
		if (!$this->has($key)){return null;}

		[$expiry, $data] = $this->data[$key];
		return new StdCacheResult($data, $expiry);
	}

	public function store(string $key, $value, $ttl = 60): void{
		$expiry = false;
		if (is_numeric($ttl)){
			$expiry = now() + floatval($ttl);
		} elseif ($ttl instanceof DateTimeInterface){
			$expiry = $ttl->getTimestamp();
		}
		if (!$expiry){
			throw new CacheStoreException("Failed to generate expiry from ttl.");
		}

		$this->data[$key] = [$expiry, $value];
	}
}