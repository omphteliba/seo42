REXSEO42 - Changelog
====================

### Version 1.2.1 - 21. Mai 2013

* Fixed: Es kam ein PHP-Error wenn kein Artikel vorhanden

### Version 1.2.0 - 18. Mai 2013

* Neues Plugin `url_generate` von tbaddade in die README.md mit aufgenommen
* Neuer FAQ Eintrag wegen möglichen 500 Server Error, siehe auch Hinweise in der README.md
* Neues Recht: `rexseo42[tools_only]` für Zugriff auf die Tools-Page für Nicht-Admins (`rexseo42[]` muss mit ausgewählt werden)
* Wenn ein Artikel nicht indiziert werden soll wird zusätzl. noch ein `X-Robots Header` ausgegeben.
* Prefix/Suffix Unterscheidung für die Checkbox in der SEO-Page inkl. Option `hide_no_prefix_checkbox` um die Checkbox zu verstecken, wenn anderes Titelschema benötigt
* Beim WWW-Redirect in der `.htaccess` Datei werden jetzt Sudomains ausgeklammert (wichtig für Website Manager AddOn)
* `ignore_root_cats` Option verbessert
* Fixed #49: Wenn die Url einen Trennstrich hatte, wurde fälschlicherweise ein Unterordner entdeckt
* RewriteRules für Website Manager Addon verbessert

### Version 1.1.42 - 23. April 2013

* Fixed #41: Parameter in der URL (test.html?foo=bar) führten zu einem 404 Fehler
* Neues Feature: man kann nun direkt aus dem Backend heraus sich alle Einstellungen anschauen die gerade aktiv sind
* Verbesserte Debug Info: es werden jetzt auch alle Settings und die .htaccess Datei mit ausgegeben
* REX['MOD_REWRITE'] wird on the fly aktiviert sobald AddOn aktiv (master.inc.php wird dafür nicht angepasst)
* Unterordner Verhalten (hoffentlich) verbessert: `subdir_force_full_urls` hinzugefügt, Base Tag komplett entfernt
* Setup für Unterordner-Installationen angepasst: u.a. Option hinzugefügt um die RewriteBase automatisch zu setzen
* Kleine kosmetische Änderungen sowie Textänderungen
* Einstellungsseite lässt sich wieder speichern

### Version 1.0.42 - 10. April 2013

* Änderungen am Datenbank-Schema: `seo_canonical_url` hinzugefügt, `seo_url` in `seo_custom_url` umbenannt
* Neues Feature: Die Canonical Url kann nun auch per Artikel gesetzt werden. Allerdings muss dies explizit in der `settings.advanced.inc.php` aktiviert werden.
* Neues Feature: Es ist nun mögliche "volle" Urls (also inkl. Domain, wie bei WordPress) über die Option `full_urls` zu erhalten
* Neues Feature: `ignore_root_cats` (experimentell)
* Bei normalen Installationen: Base-Tag kann weggelassen werden.
* "Normale" 404-Seite des Webservers, wenn eine Datei unter `files` oder `redaxo` nicht gefunden wurde
* Neue Debug Seite in der Hilfe
* Neue Hilfe Unterpunkte: Codebeispiele und Links, Faq überarbeitet
* Verzeichnis-Auflistung z.B. für files und `files/addon`s wird per .htaccess Datei unterbunden
* Neues Feature: One Page Mode, für Websites die nur über eine einzige Seite verfügen 
* Neues Feature: SEO Tools - eine Linksammlung zu wichtigen SEO-Tools im Netz
* Neue Permission: `rexseo42[seo_default]` und `rexseo42[seo_extended]` um für Nicht-Admins die Sichtbarkeit der SEO-Page zu steuern
* editContentOnly[] wird nun berücksichtigt
* Neues Feature: Checkbox zum setzen der WWW-Umleitung im Setup
* `REX['MEDIA_DIR']` wird genutzt
* Robots noIndex Option:  Bug gefixt der bei Mehrsprachigkeit auftrat
* Lang Codes für die Lang Slugs können nun vorläufig in der `settings.lang.inc.php` definiert werden
* Auto DB Fix nach DB-Import wenn die DB Felder fehlen sollten
* SEO-Page: Title, Description und Keywords abgesichert. Keywords werden z.B. nur kleingeschrieben übernommen.
* SEO-Page: Html und CSS aufgeräumt und verbessert
* rexseo42 Klasse aufgeräumt und ergänzt: `getHtml()`, `getImageTag()`, `getImageManagerUrl()`, etc.
* Änderungen bis RexSEO 1.5.4 reingenommen
* I18N Support: Alles Strings in die Lang-Datei gepackt
* .htaccess Datei aufgeräumt und vereinfacht, `redaxo/.htaccess` entfernt
* Weitere Einstellungen wurden in die `settings.advanced.inc.php` gepackt
* Code wurde generell vereinfacht und aufgeräumt 
* Lizendatei hinzugefügt
* Installation wird bei bei diesen installierten Addons verweigert: `rexseo`, `url_rewrite`, `yrewrite`
* Bei REDAXO 4.5 Beta Versionen wird die Installation nun nicht mehr verweigert
* Aufruf von ADDONS_INCLUDED auf early gesetzt

### Version 1.0.1 - 17. Feburar 2013

* Changelog eingeführt
* Entfernt: Restore Config/Settings Backup
* Einstellungen aus config.inc.php in settings.inc.php verschoben
* Option in settings.inc.php: bei Deinstallation DB-Felder inkl. Daten erhalten 

### Version 1.0.0 - 09. Feburar 2013

Erstes Release mit folgenden Änderungen/Features gegenüber dem original RexSEO:

* Läuft nur mit REDAXO 4.5+
* Entfernt: Selten gebrauchte Features aus der Benutzeroberfläche
* Entfernt: Hilfe-Icons
* Entfernt: Textile Abhängigkeit 
* Entfernt: Default Beschreibung und Suchbegriffe
* Entfernt: Checkbox "Erweiterte Einstellungen"
* Entfernt: GitHub Anbindung
* Entfernt: JavaScript/jQuery Stuff
* Entfernt: Sitemap Changefreq und Priotity
* Canonical URL wird automatisch pro Domain ermittelt, nicht mehr pro Artikel
* Neue Setup-Routine (Quickstart) die in 3 übersichtliche Schritte aufgeteilt ist
* Backups der .htaccess-Dateien werden in das Backup-Verzeichnis geschoben
* Kommt ohne MetaInfos aus stattdessen extra SEO-Page für jeden Artikel
* SEO-Page: Titel Schema laut Google Empfehlung
* SEO-Page: Titel lässt sich mit der Option "Kein Prefix" komplett selber bestimmen
* SEO-Page: Live Titel Vorschau inkl. Titelkürzung a la Google-Suchergebnis
* SEO-Page: Live Anzeige der Buchstaben/Wörter-Anzahl für Beschreibung/Suchbegriffe
* SEO-Page: Live Vorschau der benutzerdefinierten URL
* SEO-Page: noIndex-Option um Seiten aus dem Suchindex auszuschließen
* Klasse `rexseo_meta` durch statische Klasse `rexseo42` ersetzt
* sitemap.xml sowie robots.txt erhalten die Headeranweisung "X-Robots-tag: noindex"
* Links zur robots.txt und sitemap.xml in der Einstellungen-Seite
* .htaccess-Datei enthält Rewrite-Regel für suchmaschinenfreundliche Image-Manager-URLs 
* PHP Notices entfernt, Strict-Kompatibel
* REX['GENERATED_PATH'] aus REDAXO 4.5 wird genutzt
* FAQ mit der Antwort auf die Fragen aller Fragen ;)
