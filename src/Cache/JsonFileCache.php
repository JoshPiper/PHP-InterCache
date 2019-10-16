<?php

namespace Internet\InterCache\Cache;

use Fig\Cache\CacheException;

/** JsonFileCache implements a Cache, with a json file backing store (with optional compression).
 * Class JsonFileCache
 * @package Internet\InterCache\Cache
 */
class JsonFileCache extends ListCache {
	private $filePath = './.cachestore.json';

	/**
	 * Create the cache and load in data from the FS.
	 * JsonFileCache constructor.
	 * @param string $filePath
	 * @throws CacheException
	 */
	public function __construct(string $filePath = './.cachestore'){
		$info = pathinfo($filePath);
		$path = realpath($info['dirname']);
		if (!$path){
			throw new CacheException("Failed to find path.");
		}

		$path .= '/';
		$path = $info['basename'];
		$this->filePath = $path;

		if (!file_exists($this->filePath)){
			$this->data = [];
		} else {
			$cnt = @file_get_contents($this->filePath);
			if ($cnt === false){
				$cnt = '[]';
			}

			$this->data = json_decode($cnt, true);
		}
		// $this->data["{reserved}"] = [1, serialize("no")]; // Code coverage.

		$now = time();
		if (is_array($this->data)){
			foreach ($this->data as $key => [$expiry, $value]){
				if ($expiry && $expiry < $now){
					unset($this->data[$key]);
				} else {
					$this->data[$key] = [$expiry, unserialize($value)];
				}
			}
		} else {
			throw new CacheException("Failed to load from cachestore. {$this->filePath}");
		}

	}

	public function commit(){
		$data = $this->data;
		$out = [];
		foreach ($data as $key => [$expiry, $value]){
			$out[$key] = [$expiry, serialize($value)];
		}

		$out = json_encode($out);
		file_put_contents($this->filePath, $out);
		return true;
	}
}