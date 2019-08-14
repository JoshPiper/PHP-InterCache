<?php

namespace Internet\InterCache\Cache;

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
	 */
	public function __construct(string $filePath = './.cachestore'){
		$rp = realpath($filePath);
		if ($rp){
			$this->filePath = $rp;
		}

		$cnt = @file_get_contents($this->filePath);
		if ($cnt === false){
			return;
		}

		$this->data = json_decode($cnt, true);
		$now = time();
		foreach ($this->data as $key => [$expiry, &$value]){
			if ($expiry && $expiry < $now){
				unset($this->data[$key]);
			} else {
				$value = unserialize($value);
			}
		}
		unset($value);
	}

	public function commit(){
		$data = $this->data;
		foreach ($data as [$expiry, &$value]){
			$value = serialize($value);
		}

		$data = json_encode($data);
		file_put_contents($this->filePath, $data);
	}
}