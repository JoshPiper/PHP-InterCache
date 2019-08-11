<?php

namespace Internet\InterCache\Result;

/**
 * The cache interface allows overwriting of individual implementations of cache results.
 * Interface CacheResult
 * @package Internet\InterCache
 */
interface CacheResult {
	/** Check if this result has expired.
	 * @return bool
	 */
	public function expired() : bool;

	/** Fetch the value of this result.
	 * @return bool
	 */
	public function value() : bool;
};