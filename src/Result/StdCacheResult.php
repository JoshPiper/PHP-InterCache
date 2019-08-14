<?php

namespace Internet\InterCache\Result;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Internet\InterCache\Exceptions\InvalidExpiryException;
use Psr\Cache\CacheItemInterface;

class StdCacheResult implements CacheItemInterface {
	/**
	 * @var int Internal store for expiry time.
	 */
	private $expiry = 0;

	/**
	 * @var mixed The internal data store for this result.
	 */
	private $data = null;

	/**
	 * @var string Cache key.
	 */
	private $key = "";

	/**
	 * @var bool If the data was found in cache.
	 */
	private $hit = false;

	/**
	 * StdCacheResult constructor.
	 * @param string $key
	 * @param null $data
	 * @param int|bool $expiry Unix timestamp past which this result expires. True if the result doesn't expire.
	 */
	public function __construct(string $key, $data = null, $expiry = false){
		$this->key = $key;
		$this->data = $data;
		$this->expiry = $expiry;
		$this->hit = $expiry && $expiry >= time();
	}

	public function getKey(){
		return $this->key;
	}

	public function set($value){
		$this->data = $value;
	}

	public function get(){
		return $this->data;
	}

	public function isHit(){
		return $this->hit;
	}

	/**
	 * @param DateTimeInterface|null $expiration
	 * @return CacheItemInterface|void
	 * @throws InvalidExpiryException
	 */
	public function expiresAt($expiration){
		if ($expiration instanceof DateTimeInterface){
			$this->expiry = $expiration->getTimestamp();
		} elseif (!$expiration) {
			$this->expiry = false;
		} else {
			throw new InvalidExpiryException("expiration not DateTimeInterface, false or null.");
		}
	}

	/**
	 * @param DateInterval|int|null $time
	 * @return CacheItemInterface|void
	 * @throws InvalidExpiryException
	 */
	public function expiresAfter($time){
		if (is_int($time)){
			$this->expiry = time() + $time;
		} elseif ($time instanceof DateInterval){
			$this->expiry = (new DateTime())->add($time)->getTimestamp();
		} elseif (!$time){
			$this->expiry = false;
		} else {
			throw new InvalidExpiryException("time not integer, DateInterval, false or null.");
		}
	}

	/** Get the time that this result should expire, or false if it shouldn't.
	 * @return int|bool
	 */
	public function getExpiry(){
		return $this->expiry;
	}
}