<?php
class rex_redirects_utils {
	public static function createDynFile($file) {
		$fileHandle = fopen($file, 'w');

		fwrite($fileHandle, "<?php\r\n");
		fwrite($fileHandle, "// --- DYN\r\n");
		fwrite($fileHandle, "// --- /DYN\r\n");

		fclose($fileHandle);
	}

	public static function getCacheFile() {
		global $REX;

		if (isset($REX['WEBSITE_MANAGER']) && $REX['WEBSITE_MANAGER']->getCurrentWebsiteId() != 1) {
			$file = 'redirects' . $REX['WEBSITE_MANAGER']->getCurrentWebsiteId() . '.inc.php';
		} else {
			$file = 'redirects.inc.php';
		}

		return $file;
	}

	public static function updateCacheFile() {
		global $REX;

		$cacheContent = '';
		$cacheFile = $REX['INCLUDE_PATH'] . '/addons/rexseo42/plugins/redirects/generated/' . self::getCacheFile();

		if (!file_exists($cacheFile)) {
			self::createDynFile($cacheFile);
		}

		// file content
		$cacheContent .= '$REX[\'REXSEO42_REDIRECTS\'] = array(' . PHP_EOL;

		$sql = rex_sql::factory();
		//$sql->debugsql = true;
		$sql->setQuery('SELECT * FROM ' . $REX['TABLE_PREFIX'] . 'redirects');

		for ($i = 0; $i < $sql->getRows(); $i++) {
			$cacheContent .= "\t" . '"' . $sql->getValue('source_url') . '" => "' . $sql->getValue('target_url') . '"';
		
			if ($i < $sql->getRows() - 1) {
				$cacheContent .= ', ' . PHP_EOL;
			}

			$sql->next();
		}

		$cacheContent .= PHP_EOL . ');' . PHP_EOL;

	  	rex_replace_dynamic_contents($cacheFile, $cacheContent);
	}

	public static function redirect() {
		global $REX;

		$file = rex_redirects_utils::getCacheFile();
		$redirectsFile = $REX['INCLUDE_PATH'] . '/addons/rexseo42/plugins/redirects/generated/' . $file;

		if (file_exists($redirectsFile)) {
			include($redirectsFile);

			if (isset($REX['REXSEO42_REDIRECTS']) && count($REX['REXSEO42_REDIRECTS']) > 0 && array_key_exists($_SERVER['REQUEST_URI'], $REX['REXSEO42_REDIRECTS'])) {
				$targetUrl = $REX['REXSEO42_REDIRECTS'][$_SERVER['REQUEST_URI']];
			
				if (strpos($targetUrl, 'http') === false) {
					$location = 'http://' . $_SERVER['SERVER_NAME']  . $targetUrl;
				} else {
					$location = $targetUrl;
				}
		
				header ('HTTP/1.1 301 Moved Permanently');
			 	header ('Location: ' . $location);

				exit;
			}
		}

	}
}
