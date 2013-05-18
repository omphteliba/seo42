<?php

// ****************************************************************
// **  DELETE REDAXO CACHE AFTER YOU MADE CHANGES TO THIS FILE!  **
// ****************************************************************

// if true you get full urls like in wordpress :) rexseo42::getUrlStart() and co. needs to be used consequently for all extra urls (like urls to media files, etc.) | url_start option will be ignored by this
$REX['ADDON']['rexseo42']['settings']['full_urls'] = false;

// if true REDAXO subdir installations will be forced to use full urls so that no base tag is needed (recommended!). If you turn this off look into the faq for further instructions on this topic...
$REX['ADDON']['rexseo42']['settings']['subdir_force_full_urls'] = true;

// url start piece for all urls returned from rex_getUrl(), rexseo42::getUrlStart() and co.. Should to be used for all extra urls!
$REX['ADDON']['rexseo42']['settings']['url_start'] = '/';

// if true user can change canonical url via seo page. please use this only if you exactly know what you are doing or know that your redaxo users and admins exactly know what they are doing ;)
$REX['ADDON']['rexseo42']['settings']['userdef_canonical_url'] = false;

// if true rexseo42::getImageManagerUrl() and rexseo42::getImageTag() will produce seo friendly urls
$REX['ADDON']['rexseo42']['settings']['seo_friendly_image_manager_urls'] = true;

// default title delimiter (including whitespace chars) for seperating name of website and page title
$REX['ADDON']['rexseo42']['settings']['title_delimiter'] = ' | ';

// hides the no prefix/suffix checkbox in seopage if true. only necessary if a different title schema is used and therefore no prefix/suffix checkbox is needed
$REX['ADDON']['rexseo42']['settings']['hide_no_prefix_checkbox'] = false;

// if true seopage will be only visible at start article of website. also the frontend links will all point to start article and sitemap.xml will show only one url
$REX['ADDON']['rexseo42']['settings']['one_page_mode'] = false;

// if true root categories will be completly ignored and not be visible in generated urls (experimental)
$REX['ADDON']['rexseo42']['settings']['ignore_root_cats'] = false;

// only set to true if you want't to have urls wth special chars like in chinese language etc.
$REX['ADDON']['rexseo42']['settings']['urlencode'] = false; 

// 0 = don't allow article_id urls, show 404 error article | 1 = allow and 301 redirect to non-article_id urls | 2 = just allow both (not recommended!)
$REX['ADDON']['rexseo42']['settings']['allow_articleid'] = 0;

// character to replace whitespaces with in urls
$REX['ADDON']['rexseo42']['settings']['url_whitespace_replace']  = '-';

// default follow flag for robots meta tag, can be empty
$REX['ADDON']['rexseo42']['settings']['robots_follow_flag'] = 'follow';

// default archive flag for robots meta tag, can be empty
$REX['ADDON']['rexseo42']['settings']['robots_archive_flag'] = 'noarchive';

// if true pages with similar urls will be accepted as match (not recommended!)
$REX['ADDON']['rexseo42']['settings']['levenshtein'] = false;

// if true parameters will be rewritten to ++/param1/value1/param2/value2 (not recommended!)
$REX['ADDON']['rexseo42']['settings']['rewrite_params']  = false;

// only for rewrite_params settings: start param rewrite with this string
$REX['ADDON']['rexseo42']['settings']['params_starter']  = '++';

// if false seo database fields won't be dropped if rexseo42 will be uninstalled. perhaps someday interesting when updateing rexseo42...
$REX['ADDON']['rexseo42']['settings']['drop_dbfields_on_uninstall'] = true; 

// used to control which article should be used for debug output in help section, default is $REX['START_ARTICLE_ID']
$REX['ADDON']['rexseo42']['settings']['debug_article_id']  = $REX['START_ARTICLE_ID'];

// ****************************************************************
// **  DELETE REDAXO CACHE AFTER YOU MADE CHANGES TO THIS FILE!  **
// ****************************************************************

