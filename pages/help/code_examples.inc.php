<h1><?php echo $I18N->msg('rexseo42_help_codeexamples'); ?></h1>

<?php
$codeExample1 = '<head>
	<?php echo rexseo42::getHtml(); ?>
</head>';

$codeExample2 = '<?php
echo rex_getUrl(42);
// --> ' . rexseo42::getUrlStart() . 'questions/the-ultimate-answer.html

echo rexseo42::getUrlStart() . "js/jquery.min.js"; 
// --> ' . rexseo42::getUrlStart() . 'js/jquery.min.js

echo rexseo42::getMediaDir();
// --> ' . rexseo42::getUrlStart() . 'files/

echo rexseo42::getMediaFile("image.png");
// --> ' . rexseo42::getUrlStart() . 'files/foo.png
?>';

$codeExample3 = '<?php
echo rexseo42::getImageManagerUrl("image.png", "rex_mediapool_detail"); 
// --> /files/imagestypes/rex_mediapool_detail/image.png

echo rexseo42::getImageTag("image.png");
// --> <img src="/files/image.png" width="300" height="200" alt="' . $I18N->msg('rexseo42_help_codeexamples_ex3_alt') . '" />

echo rexseo42::getImageTag("image.png", "rex_mediapool_detail", 150, 100);
// --> <img src="/files/imagestypes/rex_mediapool_detail/image.png" width="150" height="100" alt="' . $I18N->msg('rexseo42_help_codeexamples_ex3_alt') . '" />;
?>';

$codeExample4 = '<html lang="<?php echo rexseo42::getLangCode(); ?>">';

$codeExample5 = '<?php echo rexseo42::getTitle(" - "); ?>';

$codeExample6 = '<?php
// ' . $I18N->msg('rexseo42_help_codeexamples_ex6_comment1') . '
class rexseo42_ex extends rexseo42
	public static function getTitle() {
		if (self::getArticleValue("seo_title") == "") {
			// use article name as title
			$titlePart = self::getArticleName();
		} else {
			// use title that user defined
			$titlePart = self::getArticleValue("seo_title");
		}
		
		if (self::getArticleValue("seo_ignore_prefix") == "1") {
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

		// ' . $I18N->msg('rexseo42_help_codeexamples_ex6_comment2') . '
		return strtolower(htmlspecialchars($fullTitle));
	}
}

// ' . $I18N->msg('rexseo42_help_codeexamples_ex6_comment3') . '
echo rexseo42_ex::getTitle(); // ' . $I18N->msg('rexseo42_help_codeexamples_ex6_comment4') . '
echo rexseo42_ex::getDescription();
echo rexseo42_ex::getKeywords();
?>';

?>

<h2>1) <?php echo $I18N->msg('rexseo42_help_codeexamples_title1'); ?></h2>
<p><?php echo $I18N->msg('rexseo42_help_codeexamples_description1'); ?></p>
<?php rex_highlight_string($codeExample1); ?>

<h2>2) <?php echo $I18N->msg('rexseo42_help_codeexamples_title2'); ?></h2>
<p><?php echo $I18N->msg('rexseo42_help_codeexamples_description2'); ?></p>
<?php rex_highlight_string($codeExample2); ?>

<h2>3) <?php echo $I18N->msg('rexseo42_help_codeexamples_title3'); ?></h2>
<p><?php echo $I18N->msg('rexseo42_help_codeexamples_description3'); ?></p>
<?php rex_highlight_string($codeExample3); ?>

<h2>4) <?php echo $I18N->msg('rexseo42_help_codeexamples_title4'); ?></h2>
<p><?php echo $I18N->msg('rexseo42_help_codeexamples_description4'); ?></p>
<?php rex_highlight_string($codeExample4); ?>

<h2>5) <?php echo $I18N->msg('rexseo42_help_codeexamples_title5'); ?></h2>
<p><?php echo $I18N->msg('rexseo42_help_codeexamples_description5'); ?></p>
<?php rex_highlight_string($codeExample6); ?>

<style type="text/css">
.rex-addon-content h2 {
	font-size: 14px;
}
</style>


