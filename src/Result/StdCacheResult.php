<?php

namespace Internet\InterCache\Result;

class StdCacheResult implements CacheResult {
	/**
	 * @var int Internal store for expiry time.
	 */
	private $expiry = 0;

	/**
	 * @var mixed The internal data store for this result.
	 */
	private $data = null;

	/**
	 * StdResult constructor.
	 * @param mixed $data
	 * @param int $expiry Unix timestamp past which this result expires.
	 */
	public function __construct($data, $expiry = 0){
		$this->expiry = $expiry;
		$this->data = $data;
	}

	/** Check if this result has expired.
	 * @return bool
	 */
	public function expired(): bool{
		return time() >= $this->expiry;
	}

	/** Fetch the stored data value.
	 * @return mixed
	 */
	public function value(){
		return $this->data;
	}
}