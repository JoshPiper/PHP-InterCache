<?php

namespace Internet\InterCache\Cache;

class JsonFileCache extends ListCache {
	private $filePath = './.cachestore';

	public function __construct(string $filePath = '.cachestore'){
		$this->filePath = $filePath;
	}

	public function load(): void {
		$cnt = file_get_contents($this->filePath);
		try {
			$cnt = gzuncompress($cnt);
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

		file_put_contents($this->filePath, $cnt);
	}
}