<?php
class rexseo42 {
	protected static $curArticle;
	protected static $startArticleID;
	protected static $titleDelimiter;
	protected static $robotsFollowFlag;
	protected static $robotsArchiveFlag;
	protected static $mediaDir;
	protected static $mediaAddonDir;
	protected static $seoFriendlyImageManagerUrls;
	protected static $fullUrls;
	protected static $serverUrl;
	protected static $websiteName;
	protected static $server;
	protected static $serverProtocol;
	protected static $serverSubDir;
	protected static $isSubDirInstall;
	protected static $urlStart;
	protected static $modRewrite;
	
	public static function init() {
		// to be called before resolve()
		global $REX;

		// default inits
		self::$startArticleID = $REX['START_ARTICLE_ID'];
		self::$titleDelimiter = $REX['ADDON']['rexseo42']['settings']['title_delimiter'];
		self::$robotsFollowFlag = $REX['ADDON']['rexseo42']['settings']['robots_follow_flag'];
		self::$robotsArchiveFlag = $REX['ADDON']['rexseo42']['settings']['robots_archive_flag'];
		self::$mediaDir = $REX['MEDIA_DIR'];
		self::$mediaAddonDir = $REX['MEDIA_ADDON_DIR'];
		self::$seoFriendlyImageManagerUrls = $REX['ADDON']['rexseo42']['settings']['seo_friendly_image_manager_urls'];
		self::$serverUrl = $REX['SERVER'];
		self::$websiteName = $REX['SERVERNAME'];
		self::$modRewrite = $REX['MOD_REWRITE'];

		// pull apart server url
		$urlParts = parse_url(self::$serverUrl);

		self::$serverProtocol = $urlParts['scheme'];
		self::$server = $urlParts['host'];
		self::$serverSubDir = trim($urlParts['path'], '/'); 

		// check for subdir install
		if (self::$serverSubDir == '') {
			self::$isSubDirInstall = false;
		} else {
			self::$isSubDirInstall = true;
		}

		// check for full urls option
		if (self::$isSubDirInstall && $REX['ADDON']['rexseo42']['settings']['subdir_force_full_urls']) {
			self::$fullUrls = true;
		} else {
			self::$fullUrls = $REX['ADDON']['rexseo42']['settings']['full_urls'];
		}

		// set url start 
		if (self::$fullUrls) {
			// full worpresslike urls
			self::$urlStart = self::$serverUrl;
		} else {
			// use url start specified in settings
			self::$urlStart = $REX['ADDON']['rexseo42']['settings']['url_start'];
		}
	}

	public static function initArticle($articleId) {
		// to be called after resolve()
		global $REX;

		self::$curArticle = OOArticle::getArticleById($articleId);
	}

	public static function getBaseUrl() {
		return self::$serverUrl;
	}

	public static function getTitle() {
		if (self::getArticleValue('seo_title') == '') {
			// use article name as title
			$titlePart = self::getArticleName();
		} else {
			// use title that user defined
			$titlePart = self::getArticleValue('seo_title');
		}
		
		if (self::getArticleValue('seo_ignore_prefix') == '1') {
			// no prefix, just the title
			$fullTitle = $titlePart;
		} else { 
			if (self::isStartArticle()) {
				// the start article shows the website name first
				$fullTitle = self::getWebsiteName() . self::getTitleDelimiter() . $titlePart;
			} else {
				// all other articles will show title first
				$fullTitle = $titlePart . self::getTitleDelimiter() . self::getWebsiteName();
			}
		 }

		return htmlspecialchars($fullTitle);
	}

	public static function getDescription() {
		return htmlspecialchars(self::$curArticle->getValue('seo_description'));
	}

	public static function getKeywords() {	
		return htmlspecialchars(self::$curArticle->getValue('seo_keywords'));
	}

	public static function hasNoIndexFlag() {
		$startArticle = OOArticle::getArticleById(self::$startArticleID);

		if (OOArticle::isValid($startArticle) && $startArticle->getValue('seo_noindex') == '1') {
			$noIndexSite = true;
		} else {
			$noIndexSite = false;
		}

		if (self::$curArticle->getValue('seo_noindex') == '1' || $noIndexSite) { 
			return true;
		} else {
			return false;
		}
	}

	public static function getRobotRules() {
		if (self::hasNoIndexFlag()) { 
			$robots = "noindex";
		} else {
			$robots = "index";
		}
		
		if (self::$robotsFollowFlag != '') {
			$robots .= ", " . self::$robotsFollowFlag;
		}

		if (self::$robotsArchiveFlag != '') {
			$robots .= ", " . self::$robotsArchiveFlag;
		}

		return $robots;
	}

	public static function getCanonicalUrl() {
		if (self::$curArticle->getValue('seo_canonical_url') != '') {
			// userdef canonical url
			return self::$curArticle->getValue('seo_canonical_url');
		}

		// automatic canonical url
		return self::getFullUrl(self::$curArticle->getId());
	}

	public static function getImageTag($imageFile, $imageType = '', $width = 0, $height = 0) {
		$media = OOMedia::getMediaByFileName($imageFile);

		// make sure media object is valid
		if (OOMedia::isValid($media)) {
			$mediaWidth = $media->getWidth();
			$mediaHeight = $media->getHeight();
			$altAttribute = $media->getTitle();
		} else {
			$mediaWidth = '';
			$mediaHeight = '';
			$altAttribute = '';
		}

		// image width
		if ($width == 0) {
			$imgWidth = $mediaWidth;
		} else {
			$imgWidth = $width;
		}

		// image height
		if ($height == 0) {
			$imgHeight = $mediaHeight;
		} else {
			$imgHeight = $height;
		}

		// get url
		if ($imageType == '') {
			$url = self::getMediaFile($imageFile);
		} else {
			$url = self::getImageManagerUrl($imageFile, $imageType);
		}

		return '<img src="' . $url . '" width="' . $imgWidth . '" height="' . $imgHeight . '" alt="' . $altAttribute . '" />';
	}

	public static function getImageManagerUrl($imageFile, $imageType) {
		if (self::$seoFriendlyImageManagerUrls && self::$modRewrite) {
			return self::getMediaDir() . 'imagetypes/' . $imageType . '/' . $imageFile;
		} else {
			return '/index.php?rex_img_type=' . $imageType . '&amp;rex_img_file=' . $imageFile;
		}
	}

	public static function getHtml($indent = "\t") {
		$out = '';

		$out .= '<title>' . self::getTitle() . '</title>' . PHP_EOL;
		$out .= $indent . '<meta name="description" content="' . self::getDescription() . '" />' . PHP_EOL;
		$out .= $indent . '<meta name="keywords" content="' . self::getKeywords() . '" />' . PHP_EOL;
		$out .= $indent . '<meta name="robots" content="' . self::getRobotRules() . '" />' . PHP_EOL;
		$out .= $indent . '<link rel="canonical" href="' . self::getCanonicalUrl() . '" />' . PHP_EOL;

		return $out;
	}

	public static function getTitleDelimiter() {
		return self::$titleDelimiter;
	}

	public static function getArticleName() {
		return self::$curArticle->getName();
	}

	public static function getArticleValue($key) {
		return self::$curArticle->getValue($key);
	}
	
	public static function getWebsiteName() {
		return self::$websiteName;
	}
	
	public static function getLangCode($clangID = -1) {
		global $REX;

		if ($clangID == -1) {
			$clangID = $REX['CUR_CLANG'];
		}

		if (isset($REX['ADDON']['rexseo42']['settings']['langcodes'][$clangID])) {
			return $REX['ADDON']['rexseo42']['settings']['langcodes'][$clangID];
		} else {
			return $REX['CLANG'][$clangID];
		}
	}

	public static function getServer() {
		return self::$server;
	}

	public static function getServerUrl() {
		return self::$serverUrl;
	}

	public static function getServerProtocol() {
		return self::$serverProtocol;
	}

	public static function getServerSubDir() {
		return self::$serverSubDir;
	}

	public static function isSubDirInstall() {
		return self::$isSubDirInstall;
	}

	public static function getServerWithSubDir() {
		if (self::$isSubDirInstall) {
			return self::$server . '/' . self::$serverSubDir;
		} else {
			return self::$server;
		}
	}

	public static function getUrlStart() {
		return self::$urlStart;
	}

	public static function setUrlStart($urlStart) {
		self::$urlStart = $urlStart;
	}

	public static function getMediaDir() {
		return self::$urlStart . self::$mediaDir . '/';
	}

	public static function getMediaFile($file) {
		return self::getMediaDir() . $file;
	}

	public static function getMediaAddonDir() {
		global $REX;

		return self::$urlStart . self::$mediaAddonDir . '/';
	}

	public static function isStartArticle() {
		if (self::$curArticle->getId() == self::$startArticleID) {
			return true;
		} else {
			return false;
		}
	}

	public static function getFullUrl($id = '', $clang = '', $params = '', $divider = '&amp;') {
		return self::getBaseUrl() . self::getTrimmedUrl($id, $clang, $params, $divider);
	}

	public static function getTrimmedUrl($id = '', $clang = '', $params = '', $divider = '&amp;') {
		if (self::$fullUrls) {
			return str_replace(self::getServerUrl(), '', rex_getUrl($id, $clang, $params, $divider));
		} else {
			return ltrim(rex_getUrl($id, $clang, $params, $divider), "./");
		}
	}

	public static function getDebugInfo($articleId = 0) {
		global $I18N, $REX;

		if ($articleId != 0) {
			self::initArticle($articleId);			
		}

		if (!OOArticle::isValid(self::$curArticle)) {
			return '';
		}

		$out = '<div id="rexseo42-debug">';

		$out .= '<h1>---------- REXSEO42 DEBUG BEGIN ----------<h1>';

		$out .= '<h2>Class Methods</h2>';
		$out .= '<table>';

		$out .= self::getDebugInfoRow('rex_getUrl', array(self::$curArticle->getId()));
		$out .= self::getDebugInfoRow('rexseo42::getTrimmedUrl', array(self::$curArticle->getId()));
		$out .= self::getDebugInfoRow('rexseo42::getFullUrl', array(self::$curArticle->getId()));
		$out .= self::getDebugInfoRow('rexseo42::getTitle');
		$out .= self::getDebugInfoRow('rexseo42::getDescription');
		$out .= self::getDebugInfoRow('rexseo42::getKeywords');
		$out .= self::getDebugInfoRow('rexseo42::getRobotRules');
		$out .= self::getDebugInfoRow('rexseo42::getCanonicalUrl');
		$out .= self::getDebugInfoRow('rexseo42::getArticleName');
		$out .= self::getDebugInfoRow('rexseo42::isStartArticle');
		$out .= self::getDebugInfoRow('rexseo42::getWebsiteName');
		$out .= self::getDebugInfoRow('rexseo42::getLangCode', array('0'));
		$out .= self::getDebugInfoRow('rexseo42::getServerProtocol');
		$out .= self::getDebugInfoRow('rexseo42::getBaseUrl');
		$out .= self::getDebugInfoRow('rexseo42::getServerUrl');
		$out .= self::getDebugInfoRow('rexseo42::getServer');
		$out .= self::getDebugInfoRow('rexseo42::getServerWithSubDir');
		$out .= self::getDebugInfoRow('rexseo42::getServerSubDir');
		$out .= self::getDebugInfoRow('rexseo42::isSubDirInstall');
		$out .= self::getDebugInfoRow('rexseo42::getTitleDelimiter');
		$out .= self::getDebugInfoRow('rexseo42::getUrlStart');
		$out .= self::getDebugInfoRow('rexseo42::getMediaDir');
		$out .= self::getDebugInfoRow('rexseo42::getMediaFile', array('image.png'));
		$out .= self::getDebugInfoRow('rexseo42::getMediaAddonDir');
		$out .= self::getDebugInfoRow('rexseo42::getHtml');
		$out .= self::getDebugInfoRow('rexseo42::getImageTag', array('image.png', 'rex_mediapool_detail', '150', '100'));
		$out .= self::getDebugInfoRow('rexseo42::getImageManagerUrl', array('image.png', 'rex_mediapool_detail'));
		$out .= self::getDebugInfoRow('rexseo42::getAnswer');

		$out .= '</table>';

		$out .= '<h2>Settings</h2>';

		$out .= '<pre class="rex-code">';
		$out .= rexseo42_utils::print_r_pretty($REX['ADDON']['rexseo42']['settings'], true);
		$out .= '</pre>';

		$out .= '<h2>.htaccess</h2>';

		$htaccessRoot = $REX['FRONTEND_PATH'] . '/.htaccess';
		$content = rex_get_file_contents($htaccessRoot);
		$out .= rex_highlight_string($content, true);

		$out .= '<h1>---------- REXSEO42 DEBUG END ----------</h1>';

		$out .= '</div>';

		$out .= '<style type="text/css">
					#rexseo42-debug h1 {
						font-size: 16px;
						margin: 10px 0;
					}

					#rexseo42-debug h2 {
						margin: 10px 0;
						font-size: 14px;
					}

					#rexseo42-debug .rex-code {
						border: 1px solid #F2353A;
					}

					#rexseo42-debug code,
					#rexseo42-debug .rex-code {
						color: #000;
						background: #FAF9F5;
					}

					#rexseo42-debug table {
						border-collapse: collapse;
						border-spacing: 0;
						background: #FAF9F5;
					}

					#rexseo42-debug table th,
					#rexseo42-debug table thead td {
						font-weight: bold;
					}

					#rexseo42-debug table td, 
					#rexseo42-debug table th {
						padding: 12px;
						border: 1px solid #F2353A;
						text-align: left;
					}

					#rexseo42-debug table td.left {
						width: 280px;
					}
				</style>';

		return $out;
	}

	protected static function getDebugInfoRow($func, $params = array()) {
		$out = '';

		// build function and params for function call
		$function = $func . '(';

		for ($i = 0; $i < count($params); $i++) {
			if (!is_numeric($params[$i])) {
				$function .= '"';
			}

			$function .= $params[$i];

			if (!is_numeric($params[$i])) {
				$function .= '"';
			}


			if (isset($params[$i + 1])) {
				$function .= ', ';
			}
		}

		$function .= ')';

		// convert for bool values to more human readable
		$returnValue = call_user_func_array($func, $params);
		
		if (is_bool($returnValue)) {
			if ($returnValue) {
				$returnValue = 'true';
			} else {
				$returnValue = 'false';
			}
		}

		$out .= '<tr>';
		$out .= '<td class="left"><code>' . $function . '</code></td>';
		$out .= '<td class="right"><code>' . htmlspecialchars($returnValue) . '</code></td>';
		$out .= '</tr>';

		return $out;
	}

	public static function getAnswer() {
		return '42';
	}

	public static function getLinkText() {
		return htmlspecialchars(self::$curArticle->getValue('seo_link_text'));
	}

	public static function getAltAttribute() {
		return htmlspecialchars(self::$curArticle->getValue('seo_alt_attribute'));
	}

	public static function getTitleAttribute() {
		return htmlspecialchars(self::$curArticle->getValue('seo_title_attribute'));
	}
}
