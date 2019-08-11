<?php

namespace Internet\InterCache\Cache;

/** JsonFileCache implements a Cache, with a json file backing store (with optional compression).
 * Class JsonFileCache
 * @package Internet\InterCache\Cache
 */
class JsonFileCache extends ListCache {
	private $filePath = './.cachestore';

	/**
	 * JsonFileCache constructor.
	 * @param string $filePath
	 */
	public function __construct(string $filePath = '.cachestore'){
		$rp = realpath($filePath);
		if ($rp){$this->filePath = $rp;}

	}

	public function load(): void {
		$cnt = @file_get_contents($this->filePath);
		if ($cnt === false){return;}

		try {
			$cnt = gzuncompress($cnt);
		} catch (\Exception $ex){
		} finally {
			$this->data = json_decode($cnt, true);
		}
	}
	
	public function save(): void {
		$cnt = json_encode($this->data);

		$env = isset($_ENV['PHP_ENV']) ? $_ENV['PHP_ENV'] : "development";
		if ($env === 'production' || $env === 'staging'){
			$cnt = gzcompress($cnt, 9);
		}

		$dir = dirname($this->filePath);
		if (!is_dir($dir)){
			mkdir($dir, 0775, true);
		}

		file_put_contents($this->filePath, $cnt);
	}
}