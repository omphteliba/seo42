<?php

define('REXSEO_PATHLIST', $REX['GENERATED_PATH'] . '/files/rexseo_pathlist.php'); // uses new rex var introduced in REDAXO 4.5

define('SEO42_URL_TYPE_DEFAULT', 0); 
define('SEO42_URL_TYPE_INTERN_REPLACE_CLANG', 1); 
define('SEO42_URL_TYPE_USERDEF_INTERN', 2);
define('SEO42_URL_TYPE_MEDIAPOOL', 3);
define('SEO42_URL_TYPE_LANGSWITCH', 4); // should also be handled by navigation output.
define('SEO42_URL_TYPE_NONE', 5); // should also be handled by navigation output.
define('SEO42_URL_TYPE_REMOVE_ROOT_CAT', 6);
define('SEO42_URL_TYPE_INTERN_REPLACE', 7);
define('SEO42_URL_TYPE_CALL_FUNC', 8); // should also be handled by navigation output.
define('SEO42_URL_TYPE_USERDEF_EXTERN', 9);

class RexseoRewrite
{
  private $use_levenshtein;
  private $use_params_rewrite;


  /* constructor */
  function RexseoRewrite($use_levenshtein = false, $use_params_rewrite = false)
  {
    $this->use_levenshtein = $use_levenshtein;
    $this->use_params_rewrite = $use_params_rewrite;
  }

  /**
  * RESOLVE()
  *
  * resolve url to ARTICLE_ID & CLANG,
  * resolve rewritten params back to GET/REQUEST
  */
  function resolve()
  {
    global $REX, $REXSEO_URLS, $REXSEO_IDS;

    if(!file_exists(REXSEO_PATHLIST)) {
      rexseo_generate_pathlist(array());
    } 

    require_once(REXSEO_PATHLIST);

    if(!$REX['REDAXO'])
    {
      $article_id      = -1;

      $clang           = $REX['CUR_CLANG'];
      $start_id        = $REX['START_ARTICLE_ID'];
      $notfound_id     = $REX['NOTFOUND_ARTICLE_ID'];

      $params_starter  = $REX['ADDON']['seo42']['settings']['params_starter'];
      $install_subdir  = seo42::getServerSubDir(); // 42
      $allow_articleid = $REX['ADDON']['seo42']['settings']['allow_articleid'];
      $homelang        = $REX['ADDON']['seo42']['settings']['homelang'];

      // TRY IMMEDIATE MATCH OF REQUEST_URI AGAINST PATHLIST..
      if(self::resolve_from_pathlist(ltrim($_SERVER['REQUEST_URI'],'/')))
      {
        return;
      } 

      // IF NON_REWRITTEN URLS ALLOWED -> USE ARTICLE_ID FROM REQUEST
      if ($allow_articleid != 0 && rex_get('article_id', 'int') > 0)
      {
        if($allow_articleid == 1)
        {
          $redirect = array('id'    =>rex_request('article_id','int'),
                            'clang' =>rex_request('clang','int',$clang),
                            'status'=>301);
          return self::redirect($redirect); /*todo: include params*/
        }
        else
        {
          return self::setArticleId(rex_request('article_id','int'), rex_request('clang','int',$clang));
        }
      }


      // GET PATH RELATIVE TO INTALL_SUBDIR ---------------> 42
      $path = ltrim($_SERVER['REQUEST_URI'], '/'); 

      if (seo42::isSubDirInstall()) {
        $path = substr($path, strlen($install_subdir));
		$path = ltrim($path, '/');
      }

      // TRIM STANDARD PARAMS
      if(($pos = strpos($path, '?')) !== false)
      {
        $path = substr($path, 0, $pos);
      }

      // TRIM ANCHORS
      if(($pos = strpos($path, '#')) !== false)
      {
        $path = substr($path, 0, $pos);
      }


      // RESOLVE REWRITTEN PARAMS -> POPULATE GET/REQUEST GLOBALS
      if($this->use_params_rewrite && strstr($path,$params_starter.'/'))
      {
        $tmp = explode($params_starter.'/',$path);
        $path = $tmp[0];
        $vars = explode('/',$tmp[1]);
        self::populateGlobals($vars);
      }


      // RETRY RESOLVE VIA PATHLIST
      if(self::resolve_from_pathlist($path)) 
      {
        return;
      }


      // GET ID FROM EXTENSION POINT
      $ep = rex_register_extension_point('REXSEO_ARTICLE_ID_NOT_FOUND', '');
      if(isset($ep['article_id']) && $ep['article_id'] > 0)
      {
        if(isset($ep['clang']) && $ep['clang'] > -1)
        {
          $clang = $ep['clang'];
        }
        return self::setArticleId($ep['article_id'],$clang);
      }


      // CHECK CLOSEST URL MATCH VIA LEVENSHTEIN
      if($this->use_levenshtein)
      {
        foreach ($REXSEO_URLS as $url => $params)
        {
          $levenshtein[levenshtein($path, $url)] = $params['id'].'#'.$params['clang'];
        }

        ksort($levenshtein);
        $best = explode('#', array_shift($levenshtein));

        return self::setArticleId($best[0], $best[1]);
      }

      // check for possible lang slug to load up correct language for 404 article
      $firstSlashPos = strpos($path, '/');

      if ($firstSlashPos !== false) {
        $possibleLangSlug = substr($path, 0, $firstSlashPos);
        $clangId = array_search($possibleLangSlug, $REX['ADDON']['seo42']['settings']['langcodes']);

        if ($clangId !== false) {
          $clang = $clangId;
        }
      }

      // STILL NO MATCH -> 404
      self::setArticleId($notfound_id,$clang);
    }
  }

  /**
  * RESOLVE_FROM_PATHLIST()
  *
  * @param  $path  string  URL to look up in pathlist
  * @return        boolean
  */
  function resolve_from_pathlist($path)
  {
    global $REXSEO_URLS;

    if(isset($REXSEO_URLS[$path]))
    {
      $status = isset($REXSEO_URLS[$path]['status']) ? $REXSEO_URLS[$path]['status'] : 200;

      switch($status)
      {
        case 301:
        case 302:
        case 303:
        case 307:
          $redirect = array('id'    => $REXSEO_URLS[$path]['id'],
                            'clang' => $REXSEO_URLS[$path]['clang'],
                            'status'=> $status);
          self::redirect($redirect);
          return true;
        default:
          if(isset($REXSEO_URLS[$path]['params']))
            self::populateGlobals($REXSEO_URLS[$path]['params'],false);
          self::setArticleId($REXSEO_URLS[$path]['id'],$REXSEO_URLS[$path]['clang']);
          return true;
      }
    }
    return false;
  }


  /**
  * REWRITE()
  *
  * rewrite URL
  * @param $params from EP URL_REWRITE
  */
  function rewrite($params)
  {
    // URL ALREADY SET BY OTHER EXTENSION
    if($params['subject'] != '')
    {
      return $params['subject'];
    }


    // EP "REXSEO_PRE_REWRITE"
    $url = rex_register_extension_point('REXSEO_PRE_REWRITE', false, $params);
    if($url !== false)
    {
      return $url;
    }

    global $REX, $REXSEO_IDS;

    $id             = $params['id'];
    $name           = $params['name'];
    $clang          = $params['clang'];
    $subdir         = ''; // 42 | $REX['ADDON']['seo42']['settings']['install_subdir']
    $notfound_id    = $REX['NOTFOUND_ARTICLE_ID'];

    // GET PARAMS STRING
    $urlparams = self::makeUrlParams($params);

    // GET URL FROM PATHLIST AND APPEND PARAMS

    if(isset($REXSEO_IDS[$id]) && isset($REXSEO_IDS[$id][$clang]))
    {
      $base_url = $REXSEO_IDS[$id][$clang]['url'];
      $url      = $base_url.$urlparams;
      $notfound = false;
    }
    else
    {
      // RexDude
      $url = '';
	  $base_url = '';

	  if (!empty($REXSEO_IDS)) {
	      $url = $base_url = $REXSEO_IDS[$notfound_id][$clang]['url'];
	  }

      $notfound = true;
    }

    // URL START
	// 42
    if ($REX['REDAXO'] && !(rex_request('page', 'string') == 'seo42' && rex_request('subpage', 'string') == 'help')) { // for seo42 debug page urls should look like in frontend
		$subdir = '';
    } else {
		$subdir = seo42::getUrlStart() . $subdir;
    }

    // HACK: EP URL_REWRITE WON'T ACCEPT EMPTY STRING AS RETURN
    if($subdir == '' && $url == '')
    {
      $url = ' ';
    }

    // str_replace fixes a caching bug that appears while updating specific
    // modules/slices in the redaxo backend
    $url = str_replace('/redaxo/','/',$subdir.$url);


	// external urls / javascript urls etc. so we have to remove the url start string
	$trimmedUrl = ltrim($url, seo42::getUrlStart());

	if (!seo42_utils::isInternalCustomUrl($trimmedUrl)) {
		$url = $trimmedUrl;
	}

    // EP "REXSEO_POST_REWRITE"
    $ep_params = array('article_id'     => $id,
                       'clang'          => $clang,
                       'notfound'       => $notfound,
                       'base_url'       => $base_url,
                       'subdir'         => $subdir,
                       'urlparams'      => $urlparams,
                       'params'         => $params['params'],
                       'divider'        => $params['divider'],
                       'params_starter' => $REX['ADDON']['seo42']['settings']['params_starter'],
                       'urlencode'      => $REX['ADDON']['seo42']['settings']['urlencode'],
                       );
    $url = rex_register_extension_point('REXSEO_POST_REWRITE', $url, $ep_params);

    return $url;
  }



  /**
  * REDIRECT()
  *
  * redirect request
  * @param $redirect   (array) params passed through from EP
  */
  protected function redirect($redirect)
  {
    global $REXSEO_IDS;

	$base = $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
	$base .= $_SERVER['HTTP_HOST'] . '/'; // 42 | . $REX['ADDON']['seo42']['settings']['install_subdir'];

    $status   = isset($redirect['status']) ? $redirect['status'] : 200;
    $location = $base.$REXSEO_IDS[$redirect['id']][$redirect['clang']]['url'];

    while(ob_get_level()){
      ob_end_clean();
    }

    header('HTTP/1.1 '.$status);
    header('Location:'.$location);
    exit();
  }



  /**
  * SETARTICLEID()
  *
  * set ARTICLE_ID & CLANG in global var $REX
  * @param $art_id   article id
  * @param $clang_id language id
  */
  protected function setArticleId($art_id, $clang_id = -1)
  {
    global $REX;
    $REX['ARTICLE_ID'] = $art_id;
    if($clang_id > -1)
      $REX['CUR_CLANG'] = $clang_id;
  }



  /**
  * MAKEURLPARAMS()
  *
  * Create params string for url
  * @param $EPparams   (array) urlencoded params from rex_getUrl/URL_REWRITE
  */
  protected function makeUrlParams($EPparams)
  {
    global $REX;
    $divider        = $EPparams['divider'];
    $urlparams      = $EPparams['params'];
    $params_starter = $REX['ADDON']['seo42']['settings']['params_starter'];

    if($this->use_params_rewrite)
    {
      // REWRITE PARAMS
      $urlparams = str_replace(array($divider,'='),'/',$urlparams);
      $urlparams = str_replace(array('%5B','%5D'),array('(',')'),$urlparams); /* pseudo array: brackets "[]" not allowed by RFC1738, replace with "()", */
      $urlparams = $urlparams == '' ? '' : $params_starter.$urlparams.'/';
    }
    else
    {
      // STANDARD PARAMS STRING
      $urlparams = $urlparams == '' ? '' : '?'.$urlparams;
    }
    $urlparams = str_replace(array('/amp;','?&amp;'),array('/','?'),$urlparams);
    return $urlparams;
  }



  /**
  * POPULATEGLOBALS()
  *
  * Populate GET/REQUEST Globals with params from either rex_getUrl/URL_REWRITE
  * (params will come urlencoded) or from pathlist (NOT urlencoded)
  * @param $vars   (array) resolved URL Parameters
  * @param $decode (bool)  urldecode vars yes/no
  */
  protected function populateGlobals($vars,$decode=true)
  {
    if(is_array($vars))
    {
      for($c=0;$c<count($vars);$c+=2)
      {
        if($vars[$c]!='')
        {
          $key = $decode ? urldecode($vars[$c])   : $vars[$c];
          $val = $decode ? urldecode($vars[$c+1]) : $vars[$c+1];

          if(strstr($key,'('))
          {
            $key = rtrim($key,')');
            $key = explode('(',$key);
          }

          if(is_array($key) && count($key)==2)
          {
            $_GET[$key[0]][$key[1]]     = $val;
            $_REQUEST[$key[0]][$key[1]] = $val;
          }
          else
          {
            $_GET[$key]     = $val;
            $_REQUEST[$key] = $val;
          }
        }
      }
    }
  }

}

// END OF CLASS -> OTHER FUNCTIONS
////////////////////////////////////////////////////////////////////////////////

/**
* REXSEO_UNSET_PATHITEM()
*
* delete single article from path-arrays
*/
function rexseo_unset_pathitem($id=false)
{
  global $REXSEO_IDS, $REXSEO_URLS;

  if($id)
  {
    unset($REXSEO_IDS[$id]);

    foreach($REXSEO_URLS as $k => $v)
    {
      if($v['id']==$id)
      {
        unset($REXSEO_URLS[$k]);
        break;
      }
    }
  }
}


/**
* REXSEO_GENERATE_PATHLIST()
*
* generiert die Pathlist, abhängig von Aktion
* @author markus.staab[at]redaxo[dot]de Markus Staab
* @package redaxo4.2
*/
function rexseo_generate_pathlist($params)
{
  global $REX, $REXSEO_IDS, $REXSEO_URLS;

  if(file_exists(REXSEO_PATHLIST))
  {
    require_once (REXSEO_PATHLIST);
  }

  // EXTENSION POINT "REXSEO_PATHLIST_BEFORE_REBUILD"
  $subject = array('REXSEO_IDS'=>$REXSEO_IDS,'REXSEO_URLS'=>$REXSEO_URLS);
  rex_register_extension_point('REXSEO_PATHLIST_BEFORE_REBUILD',$subject);

  $REXSEO_IDS  = !isset($REXSEO_IDS)  ? array() : $REXSEO_IDS;
  $REXSEO_URLS = !isset($REXSEO_URLS) ? array() : $REXSEO_URLS;

  if(!isset($params['extension_point']))
    $params['extension_point'] = '';

  $where = '';
  switch($params['extension_point'])
  {
    // ------- sprachabhängig, einen artikel aktualisieren
    case 'CAT_DELETED':
    case 'ART_DELETED':
      rexseo_unset_pathitem($params['id']);
      break;
    case 'CAT_ADDED':
    case 'CAT_UPDATED':
    case 'ART_ADDED':
    case 'ART_UPDATED':
    case 'ART_TO_CAT':
    case 'CAT_TO_ART':
    case 'ART_META_FORM_SECTION':
      $where = '(id='. $params['id'] .' AND clang='. $params['clang'] .') OR (path LIKE "%|'. $params['id'] .'|%" AND clang='. $params['clang'] .')';

      //rex_deleteCacheArticleContent($params['id'], $REX['CUR_CLANG']);
      //rex_generateArticle($params['id']);

      break;
    // ------- alles aktualisieren
    case 'CLANG_ADDED':
    case 'CLANG_UPDATED':
    case 'CLANG_DELETED':
    case 'ART_TO_STARTPAGE':
    case 'ALL_GENERATED':
    default:
      $REXSEO_IDS = $REXSEO_URLS = array();
      $where = '1=1';
      break;
  }

  if($where != '')
  {
    $db = new rex_sql();

     // REVISION FIX
    $db->setQuery('UPDATE '. $REX['TABLE_PREFIX'] .'article SET revision = 0 WHERE revision IS NULL;');
    $db->setQuery('UPDATE '. $REX['TABLE_PREFIX'] .'article_slice SET revision = 0 WHERE revision IS NULL;');

	if ($REX['ADDON']['seo42']['settings']['ignore_root_cats']) {
		$sqlQuery = 'SELECT `id`, `clang`, `path`, `startpage`,`seo_custom_url` FROM '. $REX['TABLE_PREFIX'] .'article WHERE '. $where.' AND re_id != 0 OR (re_id = 0 AND catname LIKE "") AND revision=0 OR revision IS NULL';
	} else {
		$sqlQuery = 'SELECT `id`, `clang`, `path`, `startpage`,`seo_custom_url` FROM '. $REX['TABLE_PREFIX'] .'article WHERE '. $where.' AND revision=0 OR revision IS NULL';
	}

	$db->setQuery($sqlQuery);

    // HARDCODED PATH: REDIRECT INDEX.PHP TO START-ARTICLE
    $REXSEO_URLS['index.php']  = array('id'  => $REX['START_ARTICLE_ID'], 'clang' => $REX['ADDON']['seo42']['settings']['homelang'], 'status' => 301);

    while($db->hasNext())
    {
      $pathname   = '';
      $id         = $db->getValue('id');
      $clang      = $db->getValue('clang');
      $path       = $db->getValue('path');

      // NORMALE URL ERZEUGUNG
      {
        // LANG SLUG
        if (count($REX['CLANG']) > 1 && $clang != $REX['ADDON']['seo42']['settings']['hide_langslug'])
        {
          $pathname = '';
          $pathname = rexseo_appendToPath($pathname, seo42::getLangCode($clang), $id, $clang); 
        }

        // pfad über kategorien bauen
        $path = trim($path, '|');
        if($path != '')
        {
          $path = explode('|', $path);
          foreach ($path as $p)
          {
            $ooc = OOCategory::getCategoryById($p, $clang);

            // PREVENT FATALS IN RARE CONDITIONS WHERE DB/CACHE ARE OUT OF SYNC
            if(!is_a($ooc,'OOCategory')){
              continue;
			} 

			// 42
			if ($REX['ADDON']['seo42']['settings']['ignore_root_cats'] && $ooc->getParentId() == 0) {
              continue;
            }

            $name = $ooc->getName();
            unset($ooc);

            $pathname = rexseo_appendToPath($pathname, $name, $id, $clang);
          }
        }

        $ooa = OOArticle::getArticleById($id, $clang);

        // PREVENT FATALS IN RARE CONDITIONS WHERE DB/CACHE ARE OUT OF SYNC
        if(!is_a($ooa,'OOArticle')){
          $db->next();
          continue;
        } 

        if($ooa->isStartArticle())
        {
          $ooc = $ooa->getCategory();
          $catname = $ooc->getName();
          unset($ooc);
          $pathname = rexseo_appendToPath($pathname, $catname, $id, $clang);
        }

        if($REX['ADDON']['seo42']['settings']['url_schema'] == 'rexseo')
        {
          if(!$ooa->isStartArticle())
          {
          // eigentlicher artikel anhängen
          $name = $ooa->getName();
          unset($ooa);
          $pathname = rexseo_appendToPath($pathname, $name, $id, $clang);
          }
        }
        else
        {
          // eigentlicher artikel anhängen
          $name = $ooa->getName();
          unset($ooa);
          $pathname = rexseo_appendToPath($pathname, $name, $id, $clang);
        }

        // ALLGEMEINE URL ENDUNG
        $pathname = substr($pathname,0,strlen($pathname)-1).$REX['ADDON']['seo42']['settings']['url_ending'];

        // STARTSEITEN URL FORMAT
        if($db->getValue('id')    == $REX['START_ARTICLE_ID'] &&
           $db->getValue('clang') == $REX['ADDON']['seo42']['settings']['homelang'] &&
           $REX['ADDON']['seo42']['settings']['homeurl'] == 1)
        {
          $pathname = '';
        }
        elseif($REX['ADDON']['seo42']['settings']['homeurl'] == 2 &&
               $db->getValue('id') == $REX['START_ARTICLE_ID'] &&
               count($REX['CLANG']) > 1)
        {
          $pathname = seo42::getLangCode($clang).'/';
        }

      }

      // SANITIZE MULTIPLE "-" IN PATHNAME
      $pathname = preg_replace('/[-]{1,}/', '-', $pathname);

      // UNSET OLD URL FROM $REXSEO_URLS
      if(isset($REXSEO_IDS[$id][$clang]['url']) && isset($REXSEO_URLS[$REXSEO_IDS[$id][$clang]['url']]))
        unset($REXSEO_URLS[$REXSEO_IDS[$id][$clang]['url']]);

      $REXSEO_IDS[$id][$clang] = array('url' => $pathname);
      $REXSEO_URLS[$pathname]  = array('id'  => (int) $id, 'clang' => (int) $clang);

      $db->next();
    }
  }



	// URL MANIPULATION BY SEO42
	// -----------------------------------------------------------------------------------------------------------

	// 1st run
	$db->reset();

	// get data from default lang if clone option is enabled for all other langs
	$REX['SEO42_URL_CLONE'] = array();
	
	for ($i = 0; $i < $db->getRows(); $i++) {
		$jsonData = json_decode($db->getValue('seo_custom_url'), true);
		$articleId = $db->getValue('id');
		$clangId = $db->getValue('clang');

		if (isset($jsonData['url_clone']) && $jsonData['url_clone'] == true && $clangId == $REX['START_CLANG_ID']) {
			$REX['SEO42_URL_CLONE'][$articleId] = $jsonData;
		}

		$db->next();
	}

	// 2nd run
	$db->reset();
	
    for ($i = 0; $i < $db->getRows(); $i++) {
		$urlField = $db->getValue('seo_custom_url');
		$articleId = $db->getValue('id');
		$clangId = $db->getValue('clang');

		if ($urlField != '' || isset($REX['SEO42_URL_CLONE'][$articleId])) {
			$urlData = seo42_utils::getUrlTypeData($urlField);
			$jsonData = json_decode($urlData, true);

			if (isset($REX['SEO42_URL_CLONE'][$articleId]) && !isset($jsonData['url_type'])) {
				$jsonData = $REX['SEO42_URL_CLONE'][$articleId];
			}

			switch ($jsonData['url_type']) {
				case SEO42_URL_TYPE_DEFAULT:
					// do nothing
					break;
				case SEO42_URL_TYPE_USERDEF_INTERN:
					$customUrl = $jsonData['custom_url'];

					$REXSEO_URLS[$customUrl] = $REXSEO_URLS[$REXSEO_IDS[$articleId][$clangId]['url']];
					unset($REXSEO_URLS[$REXSEO_IDS[$articleId][$clangId]['url']]);

					$REXSEO_IDS[$articleId][$clangId] = array('url' => $customUrl);

					break;
				case SEO42_URL_TYPE_USERDEF_EXTERN:
					$customUrl = $jsonData['custom_url'];

					unset($REXSEO_URLS[$REXSEO_IDS[$articleId][$clangId]['url']]);

					$REXSEO_IDS[$articleId][$clangId] = array('url' => $customUrl);
					break;
				case SEO42_URL_TYPE_MEDIAPOOL:
					$customUrl = $REX['MEDIA_DIR'] . '/' . $jsonData['file'];

					unset($REXSEO_URLS[$REXSEO_IDS[$articleId][$clangId]['url']]); 

					$REXSEO_IDS[$articleId][$clangId] = array('url' => $customUrl);

					break;
				case SEO42_URL_TYPE_INTERN_REPLACE:
					$customArticleId = $jsonData['article_id'];

					unset($REXSEO_URLS[$REXSEO_IDS[$articleId][$clangId]['url']]); 

		  			$REXSEO_IDS[$articleId][$clangId] = array('url' => $REXSEO_IDS[$customArticleId][$clangId]['url']);

					break;
				case SEO42_URL_TYPE_INTERN_REPLACE_CLANG:
					$customArticleId = $jsonData['article_id'];
					$customClangId = $jsonData['clang_id'];

					unset($REXSEO_URLS[$REXSEO_IDS[$articleId][$clangId]['url']]); 

		  			$REXSEO_IDS[$articleId][$clangId] = array('url' => $REXSEO_IDS[$customArticleId][$customClangId]['url']);

					break;
				case SEO42_URL_TYPE_REMOVE_ROOT_CAT:
					$curUrl = $REXSEO_IDS[$articleId][$clangId]['url'];
					$newUrl = '';
										
					if ($REX['ADDON']['seo42']['settings']['hide_langslug'] == $clangId) {
						$pos = strpos($curUrl, '/');

						if ($pos !== false) {
							$newUrl = substr($curUrl, $pos + 1);
						}
					} else {
						$pos1 = strpos($curUrl, '/');
						$pos2 = strpos($curUrl, '/', $pos1 + 1);

						if ($pos2 !== false) {
							$langSlug = substr($curUrl, 0, $pos1 + 1);
							$restUrl = substr($curUrl, $pos2 + 1);
							$newUrl = $langSlug . $restUrl;
						}
					}

					if ($newUrl != '') {
						// same as SEO42_URL_TYPE_USERDEF_INTERN
						$REXSEO_URLS[$newUrl] = $REXSEO_URLS[$REXSEO_IDS[$articleId][$clangId]['url']];
						unset($REXSEO_URLS[$REXSEO_IDS[$articleId][$clangId]['url']]); 

						$REXSEO_IDS[$articleId][$clangId] = array('url' => $newUrl);
					}			
					
					break;
				case SEO42_URL_TYPE_CALL_FUNC:
					unset($REXSEO_URLS[$REXSEO_IDS[$articleId][$clangId]['url']]); 

					break;
				case SEO42_URL_TYPE_LANGSWITCH:
					unset($REXSEO_URLS[$REXSEO_IDS[$articleId][$clangId]['url']]); 

					break;
				case SEO42_URL_TYPE_NONE:
					unset($REXSEO_URLS[$REXSEO_IDS[$articleId][$clangId]['url']]); 

		  			$REXSEO_IDS[$articleId][$clangId] = array('url' => '');
					break;	
			}

			unset($jsonData);
		}

		$db->next();
	}

	// -----------------------------------------------------------------------------------------------------------



  // EXTENSION POINT "REXSEO_PATHLIST_CREATED"
  $subject = array('REXSEO_IDS'=>$REXSEO_IDS,'REXSEO_URLS'=>$REXSEO_URLS);
  $subject = rex_register_extension_point('REXSEO_PATHLIST_CREATED',$subject);

  // EXTENSION POINT "REXSEO_PATHLIST_FINAL" - READ ONLY
  rex_register_extension_point('REXSEO_PATHLIST_FINAL',$subject);

  // ASSEMBLE, COMPRESS & WRITE PATHLIST TO FILE
  $pathlist_content = '$REXSEO_IDS = '.var_export($subject['REXSEO_IDS'],true).';'.PHP_EOL.'$REXSEO_URLS = '.var_export($subject['REXSEO_URLS'],true).';';

  $pathlist_content = rexseo_compressPathlist($pathlist_content);

  rex_put_file_contents(REXSEO_PATHLIST,'<?php'.PHP_EOL.$pathlist_content);

  // PURGE *.CONTENT CACHEFILES TO UPDATE INTERNAL LINKS CREATED BY replceLinks() in rex_article_base
  rexseo_purgeCacheFiles();
}


/**
* REXSEO_PURGECACHEFILES()
*
* selectively purge cache files by extension
* @param $type (string) cachefile extension
*/
function rexseo_purgeCacheFiles($ext='.content')
{
  global $REX;

  $pattern = $REX['GENERATED_PATH'] . '/articles/*'.$ext; // uses new rex var introduced in REDAXO 4.5
  $purge_files = glob($pattern);

  if(is_array($purge_files) && count($purge_files)>0)
  {
    foreach ($purge_files as $file)
    {
      unlink($file);
    }
  }
}


/**
* REXSEO_COMPRESSPATHLIST()
*
* replaces excessive linfeeds and whitespaces from var_export
* @param $str (string) the rexseo_pathlist as string
*/
function rexseo_compressPathlist($str)
{
  global $REX;

  $compressMode = 1;

  switch($compressMode)
  {
    case 0:
      return $str;
      break;

    case 1:
      $matrix   = array(
        'array ('.PHP_EOL.'      \'' => 'array (\'',
        '=> '.PHP_EOL.'  array'      => '=> array',
        '=> '.PHP_EOL.'    array'    =>'=> array',
        ','.PHP_EOL.'    ),'         => ',),',
        '('.PHP_EOL.'    \''         => '(\'',
        ','.PHP_EOL.'    \''         => ',\'',
        ','.PHP_EOL.'  ),'           => ',),'
        );
      break;

    case 2:
      $matrix   = array(
        PHP_EOL => '',
        ' '     => ''
        );
    break;
  }

  return str_replace(array_keys($matrix),array_values($matrix),$str);
}


function rexseo_appendToPath($path, $name, $article_id, $clang)
{
  global $REX;

  if ($name != '')
  {
    if ($REX['ADDON']['seo42']['settings']['urlencode'])
    {
      $name = str_replace('/','-',$name);
      $name = rawurlencode($name);
    }
    else
    {
      $name = strtolower(rexseo_parse_article_name($name, $article_id, $clang));
      $name = str_replace('+',$REX['ADDON']['seo42']['settings']['url_whitespace_replace'],$name);
    }

    // SANITIZE LAST CHARACTER
    $name = rtrim($name,'-');

    $path .= $name.'/';
  }
  return $path;
}


/**
* re-implemented from Redaxo core with added EP
*
* replaces special chars in article name
* @param $name       (string) article name
* @param $article_id (string) article id
* @param $clang      (string) clang
*/
function rexseo_parse_article_name($name, $article_id, $clang, $isUrl = false)
{
  static $firstCall = true;
  static $translation;

  if($firstCall)
  {
    global $REX, $I18N;

	if (isset($REX['ADDON']['seo42']['settings']['special_chars'][$clang])) {
		$specialCharsClang = $clang;
	} else {
		$specialCharsClang = 0;
	}

    $translation = array(
      'search'  => explode('|', $REX['ADDON']['seo42']['settings']['special_chars'][$specialCharsClang]),
      'replace' => explode('|', $REX['ADDON']['seo42']['settings']['special_chars_rewrite'][$specialCharsClang]),
      );

    // EXTENSION POINT
	if (!$isUrl) {
		$translation = rex_register_extension_point('REXSEO_SPECIAL_CHARS',$translation,array('article_id'=>$article_id,'clang'=>$clang));
	}

    $firstCall = false;
  }

  // SANITIZE STUFF
  if ($isUrl) {
    $name = trim($name, " \t\r\n");
  } else {
    $name = trim($name, " \t\r\n-.");
    $name = str_replace('/', '-', $name);
    $name = str_replace('.', '-', $name);
  }

  $name = str_replace("'", '-', $name);
  $name = str_replace("’", '-', $name);

  return
    // + durch - ersetzen
    str_replace('+','-',
        // ggf uebrige zeichen url-codieren
        urlencode(
          // mehrfach hintereinander auftretende spaces auf eines reduzieren
          preg_replace('/ {2,}/',' ',
            // alle sonderzeichen raus
            preg_replace('/[^a-zA-Z_\-0-9 ]/', '',
              // sprachspezifische zeichen umschreiben
              str_replace($translation['search'], $translation['replace'], $name)
            )
          )
        )
    );
}
