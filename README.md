SEO42 AddOn für REDAXO 4.5+
===========================

Ein intergalaktisches SEO-Addon für REDAXO 4.5+

Features
--------

* Generierung von suchmaschinenfreundlichen URLs (Apache Webserver benötigt Modul `mod_rewrite`)
* Sauber eingestelltes Caching sowie Komprimierung für Resourcen wie Bildern, Fonts, CSS und JS Dateien (.htaccess)
* Automatische Umschreibung der Startseite der Website in `/` (für alle Sprachen möglich)
* Verschiedene URL-Endungen einstellbar (z.B. Endung `.html` oder `/`)
* Automatische Titel-Generierung. Mitgeliefertes Titel-Schema aus [Google-PDF](http://www.google.de/webmasters/docs/einfuehrung-in-suchmaschinenoptimierung.pdf) entnommen.
* Extra SEO-Page für jeden Artikel inkl. Titel-Vorschau und Zeichen/Wörter Zähler
* Extra URL-Page für jeden Artikel inkl. 7+ URL-Typen zur Manipulierung der generierten URL
* Automatische `sitemap.xml` und `robots.txt` Generierung
* Neue vereinfachte Setup-Routine, benötigt keine MetaInfos mehr
* Option um vollständige URLs inkl. Domainname wie bei WordPress zu erzeugen
* One Page Mode für Websites die nur über eine Seite verfügen (z.B. Parallax-Websites etc.)
* Suchmaschinenfreundliche Image Manager Urls durch Verwendung der verfügbaren PHP-Methoden
* SEO Tools inkl. Live PageRank Checker sowie Anzeige des Google Index der aktuellen Website
* Einrichtung von 301 Weiterleitungen. Parameter in der alten URL sind ohne Probleme möglich.
* Spezielle sprachabhängige sowie sprachunabhängige Sonderzeichen-Umschreibungen einstellbar
* Pro Sprache kann Urlencode genutzt oder auch die URLs einer anderen Sprache übernommen werden
* Lang Slugs (de, en) können unabhängig von den REDAXO Sprachnamen gesetzt werden
* Automatische `rel="alternate"` Tags für mehrsprachige Websites
* Option um die Indizierung von Seiten durch Suchmaschinen zu verhindern
* Automatische sowie individuelle Canonical URLs
* One Domain Only sowie Nicht-WWW zu WWW Umleitung (und umgekehrt) inkl. autom. HTTPS Berücksichtigung.
* 5 verschiedene Redirects Varianten, u.a. Smart Redirects: Automatische Umleitungen für falsch eingegebene Urls z.B. von Url-Endung `/` nach `.html`
* Force Download Funktionalität inkl. suchmaschinenfreundlicher URLs und Canonical Header (z.B. für PDF Downloads)
* Google Site Verification Unterstützung ohne dass man noch die Html-Datei uploaden muss
* Keine Abhängigkeiten zu weiteren Addons wie Textile oder XForm
* Kompatibel zum [Website Manager](https://github.com/RexDude/website_manager) sowie [Community](https://github.com/dergel/redaxo4_community) AddOn
* Offline 404 Modus: Offline Artikel sind nicht mehr erreichbar (404 Seite). 
* Enthält die Antwort auf die eine Frage ;)

Features Resourceneinbindung
----------------------------

* Kombinieren von mehreren JS/CSS Dateien zu einer einzigen Datei um HTTP Request zu minimieren
* Versions-String Mechanismus damit trotz Caching immer die neuste Version einer JS/CSS Datei heruntergeladen wird
* Integrierte LESS sowie SCSS (SASS) Compiler
* Automatische Neukompilierung sowie Neukombinierung der Dateien bei Änderungen der Quell-Dateien
* Überführung von Variablenwerten von PHP nach LESS möglich. Mehr Infos in den Codebeispielen und [hier](http://leafo.net/lessphp/docs/#setting_variables_from_php)
* Einbindung von JavaScript Code aus einem REDAXO Template (oder einer Datei) heraus inkl. PHP Interpretierung

Verfügbare Plugins für SEO42
----------------------------

* [url_control](https://github.com/tbaddade/redaxo_plugin_url_control) - Plugin zur URL-Generierung für eigene AddOns
* [min42](https://github.com/webghostx/min42) Minifizierung von CSS/JS Dateien

Hinweis zur mitgelieferten .htaccess Datei
------------------------------------------

Das AddOn Resource Includer inkl. `.htaccess` Datei wurde direkt in SEO42 3.0+ integriert. Da nun die Cachingdauer von CSS/JS Dateien auf 4 Wochen eingestellt ist sollte unbedingt entweder die Methoden `seo42::getCSSFile()` / `seo42::getJSFile()` genutzt werden oder man reduziert in der `.htaccess` Datei die Cachingdauer (z.B. auf 1 Woche).

Alle URL-Typen aktivieren
-------------------------

* Einige Url-Typen greifen erst, wenn bei der Ausgabe der Navigation auf diese reagiert wird.
* Die Navigationsfunktionen von des Navigation Factory Addons unterstützt diese Typen automatisch.
* Über die Option Einstellungen > User Interface > Alle URL Typen können diese aktiviert werden.

Entwicklung von Plugins für SEO42
---------------------------------

* SEO42 bindet automatisch seine installierten und aktvierten Plugins in das Addon-Menü ein.
* Es wird ausserdem automatisch die Sprachdatei des Plugins eingebunden. Im Plugin selbst muss man also nichts weiter tun.
* Plugins sollten die SEO42 API verwendet. Aktuell gibt ein Übersicht der PHP-Methoden unter Hilfe > Debug.
* Möchte man z.B. Titel, Beschreibung, usw. für einen bestimmten Artikel bekommen, so ruft man vor dem jeweiligen Methoden-Aufruf die Methode `seo42::initArticle($articleId)` auf. Zum Schluss sollte man wieder den aktuellen Artikel zurücksetzen mit `seo42::initArticle($REX['ARTICLE_ID'])` (aber eigentlich nur fürs Frontend nötig).

Language Presets
----------------

* ISO Language Codes: <http://www.w3schools.com/tags/ref_language_codes.asp>
* Sprach-Sonderzeichen-Tabelle: <http://unicode.e-workers.de/>
* hreflang Link-Attribut Markup-Generator (für die Region Codes u.a.): <http://www.sistrix.de/hreflang-guide/generator/>
* hreflang-Attribut für Sprach- oder Regions-URL Ihrer Seiten verwenden: https://support.google.com/webmasters/answer/189077

RegEx Redirect Beispiel
-----------------------

Vor der Benutzung nicht vergessen die RegEx Redirects einzuschalten in den "Einstellungen".

* Alte Url: `/kategorie/artikel_*`
* Neue Url: `/kategorie/artikel-{1}.html`

Folgende Urls werden damit umgeleitet (hier nur 1 Beispiel):

* Von: `/kategorie/artikel_foo`
* Nach: `/kategorie/artikel-foo.html`

Redirects Varianten
-------------------

* No Double Content Redirects: Hiermit werden hauptsächlich die WWW/Nicht-WWW Redirects gesteuert
* Auto Redirects: Für alte REDAXO Urls sowie für nicht umgeschriebene REDAXO Urls
* Smart Redirects: Für vom Benutzer im Browser falsch eingegebene Urls, z.B. wird dann `/foo/` nach `/foo.html` umgeleitet (wenn `.html` als Url-Endung angegeben)
* Sync Redirects: Beim Umbenennen/Verschieben von Katgeorien/Artikel werden Redirects automatisch angelegt
* Benutzerdef. Redirects: Von Hand eingetragene Redirects (mit und ohne RegEx Notation möglich)

Hinweise
--------

* Läuft nur mit REDAXO 4.5+
* AddOn-Ordner lautet: `seo42`
* Wenn der Webserver einen 500 Server Error meldet, die Zeile `Options -Indexes` in der `.htaccess` auskommentieren.
* Getestete und unterstützte Skins: `agk_skin` von REDAXO und `ppx_skin` von [polarpixel](https://github.com/polarpixel).
* Geändertes Verhalten für REDAXO Unterordner-Installationen. Bitte FAQ in der Hilfe des AddOns anschauen für weitere Infos.
* Der Fehlerartikel unter REDAXO > System sollte nicht gleich dem Startartikel der Website entsprechen. Es sollte aufjedenfall ein eigener Fehlerartikel angelegt werden.
* Implementiert man sein eigenes Titel-Schema, ist es vielleicht sinnvoll die Optionen `title_preview` und `no_prefix_checkbox` auf `false` zu setzen.
* `$REX["MOD_REWRITE"]` braucht nicht mehr auf `true` gesetzt werden (z.B. über die System-Page von REDAXO). Wenn SEO42 aktiv, wird es automatisch gesetzt.
* Für Websites die bei 1und1 gehostet werden: es empfiehlt sich die Einstellung `HTTP Header` > `Image Manager Cache Control Header Fix` zu setzen, da sonst Image Manager Bilder nicht gecachet werden.
* Der One Domain Only Redirect leitet automatisch auf die WWW bzw. die Nicht-WWW Version um, je nachdem wie man die "URL der Website" im Setup angegeben hat.
* Der X-UA-Compatible Meta Tag kann im Template weggelassen werden, da SEO42 diesen automatisch als HTTP Header sendet.


Hinweise Resourceneinbindung
----------------------------

* Nutzt man NICHT die PHP-Methoden `seo42::getCSSFile()` / `seo42::getJSFile()` so sollte man unbedingt in der `.htaccess` Datei die Cachingdauer für CSS/JS Dateien von 4 Wochen auf 1 Woche oder weniger einstellen.
* Aktuell wird keine JavaScript Kompression (minify) durchgeführt. Es sollten immer die `min.js` Dateien angegeben werden. 
* Variable von PHP nach LESS: <http://leafo.net/lessphp/docs/#setting_variables_from_php>
* Warum `foo.1234567.css`? <http://stevesouders.com/blog/2008/08/23/revving-filenames-dont-use-querystring>
* Bei Verwendung von Bootstrap muss man das Plugin [lessdotphp](https://github.com/DanielWeitenauer/lessdotphp) installieren, sofern man Bootstrap neu Compilieren will

Artikel/Tutorials
-----------------

* REDAXO Tutorial: [SEO-AddOns - URL-Umschreibung und (viel) mehr](http://www.redaxo.org/de/doku/sinnvolle-addons/seo-addons---urls-und-mehr/)

Changelog
---------

siehe [CHANGELOG.md](CHANGELOG.md)

Updatehinweise
--------------

siehe [UPDATE.md](UPDATE.md)

FAQ
---

siehe [FAQ.md](FAQ.md)

Lizenz
------

siehe [LICENSE.md](LICENSE.md)

Credits
-------

* [GN2](https://github.com/gn2netwerk) und [jdlx](https://github.com/jdlx) für RexSEO
* [Markus Staab](https://github.com/staabm) für url_rewrite
* [Jan Kristinus](http://github.com/dergel) für REDAXO und den neuen EP in REDAXO 4.5
* [Gregor Harlan](https://github.com/gharlan) und [Thomas Blum](https://github.com/tbaddade) für Hilfe, Code und Bugmeldungen :)
* [Peter Bickel](https://github.com/polarpixel) für generelle Unterstützung und die Hilfe bei der englischen Übersetzung
* Péter Kalmár für die Logo-Optimierung 
* Danke ausserdem an alle die sich mit Ideen, Tests und Bugmeldungen eingebracht haben :)
* Google PageRank Checker Class by David Walsh and Jamie Scott
* [Parsedown](http://parsedown.org/) Class by Emanuil Rusev
* SEO42 nutzt die [scssphp](https://github.com/leafo/scssphp/) PHP-Klasse
* SEO42 nutzt die [lessphp](https://github.com/leafo/lessphp/) PHP-klasse
* [QTip2](http://qtip2.com/) by Craig Thompson
* [jQuery-tagEditor](https://github.com/Pixabay/jQuery-tagEditor) by Pixabay.com
* jQuery UI: <http://jqueryui.com/>
* jQuery Bootstrap-style Dropdowns: <https://github.com/claviska/jquery-dropdown>
* Hitchhiker's Guide to the Galaxy Icons by [Iconshock](http://www.iconarchive.com/artist/iconshock.html)
* Status Icons from [FamFamFam Silk Icons](http://www.famfamfam.com/lab/icons/silk/) and [Oxygen Icons](http://www.oxygen-icons.org/)
* Macht’s gut und danke für den Fisch ;)

