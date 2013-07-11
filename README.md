REXSEO42 AddOn für REDAXO 4.5+
==============================

Ein intergalaktischer Fork des original RexSEO AddOns für REDAXO mit alternativer Benutzerführung und noch weiterer SEO-Goodies...

Features
--------

* Einstellungen-Seite auf das Wesentliche reduziert 
* Zusätzliche Einstellungen vorerst in die `settings.advanced.inc.php` verlagert
* Neue Setup-Routine
* Kommt ohne MetaInfos aus
* Extra SEO-Page für jeden Artikel inkl. Titel-Vorschau
* SEO Tools
* Enhält die Antwort auf die eine Frage
* Und noch vieles mehr...

Verfügbare Plugins
------------------

* `url_generate` von tbaddade: https://github.com/tbaddade/redaxo_plugin_url_generate

Hinweise
--------

* Läuft nur mit REDAXO 4.5+
* AddOn-Ordner lautet: `rexseo42`
* WICHTIG: Bitte für ALLE Urls immer die PHP-Methoden aus dem Codebeispiel Nr.2 aus der Hilfe nutzen!
* RexSEO Plugins laufen mit REXSEO42 vorerst nicht.
* Geändertes Verhalten für REDAXO Unterordner-Installationen. Bitte FAQ in der Hilfe des AddOns anschauen für weitere Infos.
* Wenn der Webserver einen 500 Server Error meldet die Zeile `Options -Indexes` in der `.htaccess` auskommentieren.
* `$REX["MOD_REWRITE"]` braucht nicht mehr auf `true` gesetzt werden (z.B. über die System-Page von REDAXO). Wenn REXSEO42 aktiv, wirds automatisch gesetzt. Option um den Rewriter aber trotzdem auszuschalten ist geplannt :)

Changelog
---------

siehe [CHANGELOG.md](CHANGELOG.md)

Lizenz
------

siehe [LICENSE.md](LICENSE.md)

Credits
-------

* [GN2](https://github.com/gn2netwerk) und [jdlx](https://github.com/jdlx) für das original RexSEO AddOn
* [Markus Staab](https://github.com/staabm) für das zugrundeliegende url_rewrite AddOn
* [Jan Kristinus](http://github.com/dergel) für REDAXO und den neuen EP in REDAXO 4.5
* [Gregor Harlan](https://github.com/gharlan) und [Thomas Blum](https://github.com/tbaddade) für Hilfe, Code und Bugmeldungen :)
* Danke ausserdem an alle die sich mit Ideen und Bugmeldungen eingebracht haben :)

