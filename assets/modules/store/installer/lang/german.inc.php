<?php
/**
 * MODx language File
 *
 * @author davaeron, German translation by Marc Hinse and Bogdan Günther
 * @package MODx
 * @version 2.0
 * @translation
 * Filename:       /install/lang/german.inc.php
 * Language:       German
 * Encoding:       UTF-8
 */
	$_lang["agree_to_terms"] = 'Lizenzbedingungen akzeptieren und Installieren';
	$_lang["alert_database_test_connection"] = 'Sie müssen eine Datenbank erstellen oder die Datenbank-Verbindung testen!';
	$_lang["alert_database_test_connection_failed"] = 'Die Datenbankauswahl ist fehlgeschlagen!';
	$_lang["alert_enter_adminconfirm"] = 'Das Administrator-Passwort und dessen Bestätigung stimmen nicht überein!';
	$_lang["alert_enter_adminlogin"] = 'Sie müssen einen Benutzernamen für das Administrator-Benutzerkonto angeben!';
	$_lang["alert_enter_adminpassword"] = 'Sie müssen ein Passwort das Administrator-Benutzerkonto angeben!';
	$_lang["alert_enter_database_name"] = 'Bitte einen Datenbanknamen eintragen!';
	$_lang["alert_enter_host"] = 'Sie müssen einen Datenbank-Host angeben!';
	$_lang["alert_enter_login"] = 'Sie müssen einen Datenbank-Login-Namen angeben!';
	$_lang["alert_server_test_connection"] = 'Bitte testen Sie Ihre Datenbank-Verbindung!';
	$_lang["alert_server_test_connection_failed"] = 'Die Datenbank-Verbindung konnte nicht hergestellt werden!';
	$_lang["alert_table_prefixes"] = 'Tabellen-Präfixe müssen mit einem Buchstaben beginnen!';
	$_lang["all"] = 'Alle';
	$_lang["and_try_again"] = ', und versuchen Sie es erneut. Falls Sie Hilfe bei der Lösung des Problems benötigen';
	$_lang["and_try_again_plural"] = ', und versuchen Sie es erneut. Falls Sie Hilfe bei der Lösung der Probleme benötigen';
	$_lang["begin"] = 'Anfang';
	$_lang["btnback_value"] = 'zurück';
	$_lang["btnclose_value"] = 'Schließen';
	$_lang["btnnext_value"] = 'Weiter';
	$_lang["cant_write_config_file"] = 'MODx konnte die Konfigurationsdatei nicht erstellen. Bitte fügen Sie folgendes in eine leere Datei ein:';
	$_lang["cant_write_config_file_note"] = 'Sobald dieser Vorgang beendet ist, können Sie sich im MODx-Manager anmelden unter http://ihredomain.de/'.MGR_DIR.'/.';
	$_lang["checkbox_select_options"] = 'Checkbox-Auswahlmöglichkeiten:';
	$_lang["checking_if_cache_exist"] = 'Überprüfen ob Ordner <span class="mono">assets/cache</span> existiert: ';
	$_lang["checking_if_cache_file2_writable"] = 'Überprüfen ob die Datei <span class="mono">assets/cache/sitePublishing.idx.php</span> beschreibbar ist: ';
	$_lang["checking_if_cache_file_writable"] = 'Überprüfen ob die Datei <span class="mono">assets/cache/siteCache.idx.php</span> beschreibbar ist: ';
	$_lang["checking_if_cache_writable"] = 'Überprüfen ob der Ordner <span class="mono">assets/cache</span> beschreibbar ist: ';
	$_lang["checking_if_config_exist_and_writable"] = 'Überprüfen ob die Datei <span class="mono">'.MGR_DIR.'/includes/config.inc.php</span> existiert und beschreibbar ist: ';
	$_lang["checking_if_export_exists"] = 'Überprüfen ob der Ordner <span class="mono">assets/export</span> existiert: ';
	$_lang["checking_if_export_writable"] = 'Überprüfen ob der Ordner <span class="mono">assets/export</span> beschreibbar ist: ';
	$_lang["checking_if_images_exist"] = 'Überprüfen ob der Ordner <span class="mono">assets/images</span>, <span class="mono">assets/files</span>, <span class="mono">assets/flash</span>, <span class="mono">/assets/media</span>, <span class="mono">/assets/backup</span>, <span class="mono">/assets/.thumbs</span> existiert: ';
	$_lang["checking_if_images_writable"] = 'Überprüfen ob der Ordner <span class="mono">assets/images</span>, <span class="mono">assets/files</span>, <span class="mono">assets/flash</span>, <span class="mono">/assets/media</span>, <span class="mono">/assets/backup</span>, <span class="mono">/assets/.thumbs</span>  beschreibbar ist: ';
	$_lang["checking_mysql_strict_mode"] = 'Überprüfe MySQL Strict-Mode: ';
	$_lang["checking_mysql_version"] = 'Überprüfe MySQL-Version: ';
	$_lang["checking_php_version"] = 'Überprüfe PHP-Version: ';
	$_lang["checking_registerglobals"] = 'Überprüfe ob PHP register_globals ausgeschaltet sind: ';
	$_lang["checking_registerglobals_note"] = 'Diese Konfiguration macht Ihre Website angreifbarer für Cross Site Scripting (XSS) Attacken. Sie sollten mit Ihrem Provider sprechen, ob diese Einstellung geändert werden kann. Es gibt normalerweise drei Möglichkeiten: Ändern der globalen php.ini, Hinzufügen von Regeln zu der .htaccess-Datei im Root-Verzeichnis der MODx-Installation oder durch Hinzufügen einer angepassten php.ini in jedem Verzeichnis Ihrer MODx-Installation. Sie können MODx trotzdem installieren, aber Sie müssen sich den möglichen Sicherheitsrisiken bewusst sein.'; //Look at changing this to provide a solution.
	$_lang["checking_sessions"] = 'Überprüfe ob die Sessions sauber definiert sind: ';
	$_lang["checking_table_prefix"] = 'Überprüfe Tabellen-Präfixe `';
	$_lang["chunks"] = 'Chunks';
	$_lang["config_permissions_note"] = 'Für neue Linux/Unix Installationen bitt eine leere Datei <span class="mono">config.inc.php</span> im Ordner <span class="mono">'.MGR_DIR.'/includes/</span> anlegen und die Dateirechte auf 0666 setzen.';
	$_lang["connection_screen_collation"] = 'Kollation:';
	$_lang["connection_screen_connection_method"] = 'Verbindungsmethode:';
	$_lang["connection_screen_database_connection_information"] = 'Datenbankinformationen';
	$_lang["connection_screen_database_connection_note"] = 'Bitte geben Sie den Namen der Datenbank an, die Sie für MODx nutzen wollen. Falls die Datenbank nicht existiert, wird MODx versuchen sie zu erstellen. Dies kann fehlschlagen, falls Sie nicht die nötigen Rechte besitzen. Bei vielen Web-Hosting-Angeboten sind die Datenbanken bereits eingerichtet oder können in der Administrationsoberfläche des Web-Hosting-Angebots erstellt werden. Notieren Sie diesen Namen und geben Sie ihn hier ein.';
	$_lang["connection_screen_database_host"] = 'Datenbank-Host:';
	$_lang["connection_screen_database_info"] = 'Datenbank-Information';
	$_lang["connection_screen_database_login"] = 'Datenbank-Login-Name:';
	$_lang["connection_screen_database_name"] = 'Datenbank-Name:';
	$_lang["connection_screen_database_pass"] = 'Datenbank-Passwort:';
	$_lang["connection_screen_database_test_connection"] = 'Klicken Sie hier, um die Datenbank zu erstellen bzw. um die Verbinding zu testen.';
	$_lang["connection_screen_default_admin_email"] = 'Administrator-E-Mail:';
	$_lang["connection_screen_default_admin_login"] = 'Administrator-Benutzername:';
	$_lang["connection_screen_default_admin_note"] = 'Bitte geben Sie weitere Details zu Ihren Administrator-Benutzerkonto an. Sie können Ihren Namen eingeben und ein Passwort, dass Sie nicht vergessen. Diese Daten benötigen Sie für die Anmeldung im MODx-Manager (dem MODx-Adminbereich) nach dem die Installation abgeschlossen ist.';
	$_lang["connection_screen_default_admin_password"] = 'Administrator-Passwort:';
	$_lang["connection_screen_default_admin_password_confirm"] = 'Passwort bestätigen:';
	$_lang["connection_screen_default_admin_user"] = 'Standard Administrator-Konto';
	$_lang["connection_screen_defaults"] = 'Standard-Manager-Einstellungen';
	$_lang["connection_screen_server_connection_information"] = 'Server-Verbindungs- und Anmeldeinformation';
	$_lang["connection_screen_server_connection_note"] = 'Bitte geben Sie den Datenbank-Server, den Login-Namen sowie das Datenbank-Passwort ein und testen Sie dann die Verbindung.';
	$_lang["connection_screen_server_test_connection"] = 'Klicken Sie hier, um die Datenbank-Verbindung zu testen und die verfügbaren Kollationen aufzulisten.';
	$_lang["connection_screen_table_prefix"] = 'Tabellen-Präfix:';
	$_lang["creating_database_connection"] = 'Stelle Verbindung zur Datenbank her: ';
	$_lang["database_alerts"] = 'Datenbank Meldungen!';
	$_lang["database_connection_failed"] = 'Datenbank-Verbindung fehlgeschlagen!';
	$_lang["database_connection_failed_note"] = 'Bitte Überprüfen Sie Ihre Datenbank-Anmeldung und versuchen Sie es erneut.';
	$_lang["default_language"] = 'Standard-Sprache MODx-Manager';
	$_lang["default_language_description"] = 'Das ist die voreingestellte Sprache die im MODx-Manager (dem MODx-Adminbereich) verwendet wird.';
	$_lang["database_use_failed"] = 'Datenbank konnte nicht ausgewählt werden!';
	$_lang["database_use_failed_note"] = 'Bitte prüfen Sie den Datenbankzugang für den gewählten Benutzer und versuchen Sie es erneut.';
	$_lang["during_execution_of_sql"] = ' während des Ausführens des SQL-Statements ';
	$_lang["encoding"] = 'utf-8';
	$_lang["error"] = 'Fehler';
	$_lang["errors"] = 'Fehler';
	$_lang["failed"] = 'fehlgeschlagen!';
	$_lang["help"] = 'Hilfe!';
	$_lang["help_link"] = 'http://www.modxcms.de/forum/';
	$_lang["help_title"] = 'Unterstützung zur Installation finden Sie in den MODx-Foren';
	$_lang["iagree_box"] = 'Ich stimme den Lizenzbedingungen zu.';
	$_lang["install"] = 'Installieren';
	$_lang["install_overwrite"] = 'Installieren/Überschreiben';
	$_lang["install_results"] = 'Installationsergebnisse';
	$_lang["install_update"] = 'Installation/Update';
	$_lang["installation_error_occured"] = 'Folgende Fehler sind während der Installation aufgetreten';
	$_lang["installation_install_new_copy"] = 'Neue Kopie installieren von ';
	$_lang["installation_install_new_note"] = 'Beachten Sie, dass diese Option alle Daten in der Datenbank überschreibt.';
	$_lang["installation_mode"] = 'Installationsmodus';
	$_lang["installation_new_installation"] = 'Neue Installation';
	$_lang["installation_note"] = '<b>Achtung:</b> Nach dem Anmelden im MODx-Manager sollten Sie die Konfigurationseinstellungen unter <b>Werkzeuge &gt; Konfiguration</b> vornehmen und speichern bevor Sie Ihre Seite aufrufen.';
	$_lang["installation_successful"] = 'Installation war erfolgreich!';
	$_lang["installation_upgrade_advanced"] = 'Upgrade Installation für Fortgeschrittene<br /><small>(Anpassung der Datenbank-Konfiguration)</small>';
	$_lang["installation_upgrade_advanced_note"] = 'For fortgeschrittene Datenbank-Administratoren oder bei Umzug auf einen Server mit anderem Datenbank-Zeichensatz oder anderer Datenbank-Kollation. <b>Sie müssen die vollständigen Datenbank-Anmeldedaten sowie den Datenbank-Zeichensatz und die Datenbank-Kollation kennen.</b>';
	$_lang["installation_upgrade_existing"] = 'Upgrade einer existierenden Installation';
	$_lang["installation_upgrade_existing_note"] = 'Upgrade Ihrer Dateien und der Datenbank.';
	$_lang["installed"] = 'Installiert';
	$_lang["installing_demo_site"] = 'Installiere Beispielinhalte: ';
	$_lang["language_code"] = 'de';
	$_lang["loading"] = 'Laden …';
	$_lang["modules"] = 'Module';
	$_lang["modx_footer1"] = '&copy; 2005-2011 <a href="http://www.modxcms.com/" target="_blank" style="color: green; text-decoration:underline">MODx</a> Content Mangement Framework (CMF) Projekt. Alle Rechte vorbehalten. MODx ist unter der GNU GPL lizenziert.';
	$_lang["modx_footer2"] = 'MODx is freie Software.  Wir ermutigen Sie, kreativ zu sein und MODx so zu nutzen wie es Ihnen am besten passt. Stellen Sie nur sicher, dass Sie bei Veränderungen des Quellcodes und der Weiterverbreitung der modifizierten MODx-Version den Quelltext frei zugänglich belassen!';
	$_lang["modx_install"] = 'MODx &raquo; Installation';
	$_lang["modx_requires_php"] = ', und MODx benötigt PHP 4.2.0. oder höher';
	$_lang["mysql_5051"] = 'Warnung: MySQL-Server-Version ist 5.0.51!';
	$_lang["mysql_5051_warning"] = 'MySQL-Server-Version ist 5.0.51 hat bekannte Bugs. Es wird empfohlen ein Update auf eine neuere Version durchzuführen bevor Sie fortfahren.';
	$_lang["mysql_version_is"] = ' Ihre MySQL-Version ist: ';
	$_lang["none"] = 'Keine';
	$_lang["not_found"] = 'nicht gefunden';
	$_lang["ok"] = 'OK!';
	$_lang["optional_items"] = 'Optionale Einstellungen';
	$_lang["optional_items_note"] = 'Bitte wählen Sie Ihre Installationsoptionen und klicken Sie auf Install:';
	$_lang["php_security_notice"] = '<legend>Sicherheitshinweis</legend><p>MODx wird mit Ihrer PHP-Version wohl laufen, unter dieser PHP-Version wird die Benutzung von MODx nicht empfohlen. Ihre Version von PHP ist angreifbar aufgrund verschiedener Sicherheitslöcher. Bitte führen Sie ein Update auf PHP-Version 4.4.1. oder höher durch, um die Sicherheitsprobleme zu beheben.</p>';
	$_lang["please_correct_error"] = '. Bitte korrigieren Sie den Fehler';
	$_lang["please_correct_errors"] = '. Bitte korrigieren Sie die Fehler';
	$_lang["plugins"] = 'Plugins';
	$_lang["preinstall_validation"] = 'Prüfung vor der Installation';
	$_lang["remove_install_folder_auto"] = 'Installationsordner löschen<br />&nbsp;(Dies erfordert die entsprechenden Zugriffsrechte um den Ordner löschen zu können).';
	$_lang["remove_install_folder_manual"] = 'Bitte denken Sie daran den Ordner <b>install</b> zu löschen bevor Sie sich im MODx-Manager anmelden.';
	$_lang["retry"] = 'Nochmal versuchen';
	$_lang["running_database_updates"] = 'Führe Datenbank-Updates durch: ';
	$_lang["sample_web_site"] = 'Beispiel-Website';
	$_lang["sample_web_site_note"] = 'Beachten Sie, dass damit alle Dokumente und Ressourcen <b>überschrieben</b> werden.';
	$_lang["session_problem"] = 'Ein Problem mit Ihren Server-Session wurde festgestellt. Bitte kontaktieren Sie Ihren Server-Administator um dieses Problem zu beheben.';
	$_lang["session_problem_try_again"] = 'Erneut versuchen?'; 
	$_lang["setup_cannot_continue"] = 'Leider kann die Installation wegen oben aufgeführter Gründe nicht fortgesetzt werden.';
	$_lang["setup_couldnt_install"] = 'Die Tabellen in der gewählten Datenbank konnten nicht angelegt/geändert werden.';
	$_lang["setup_database"] = 'Die MODx-Installation wird nun versuchen die Datenbank einzurichten:<br />';
	$_lang["setup_database_create_connection"] = 'Verbindung zur Datenbank: ';
	$_lang["setup_database_create_connection_failed"] = 'Datenbank-Verbindung fehlgeschlagen!';
	$_lang["setup_database_create_connection_failed_note"] = 'Bitte prüfen Sie die Datenbank-Anmeldedaten und versuchen Sie es erneut.';
	$_lang["setup_database_creating_tables"] = 'Erstelle Datenbanktabellen: ';
	$_lang["setup_database_creation"] = 'Lege Datenbank an `';
	$_lang["setup_database_creation_failed"] = 'Datenbank-Erstellung fehlgeschlagen!';
	$_lang["setup_database_creation_failed_note"] = ' – Die Datenbank konnte nicht angelegt werden!';
	$_lang["setup_database_creation_failed_note2"] = 'Die Datenbank konnte nicht angelegt werden und keine Datenbank mit gleichem Namen wurde gefunden. Höchstwahrscheinlich lässt Ihr Web-Hosting-Provider das Anlegen von Datenbanken mit einem externem Script nicht zu. Bitte legen Sie die Datenbank wie vom Web-Hosting-Provider beschrieben an oder geben Sie die Verbindungsdaten einer bereits angelegten Datenbank an.';
	$_lang["setup_database_selection"] = 'Datenbank wählen`';
	$_lang["setup_database_selection_failed"] = 'Datenbank-Auswahl fehlgeschlagen …';
	$_lang["setup_database_selection_failed_note"] = 'Die Datenbank existiert nicht, es wird versucht versucht sie anzulegen.';
	$_lang["snippets"] = 'Snippets';
	$_lang["some_tables_not_updated"] = 'Manche Tabellen wurden nicht aktualisiert. Dies könnte an zuvor individuell ausgeführten Modifikationen liegen.';
	$_lang["status_checking_database"] = 'Überprüfe Datenbank: ';
	$_lang["status_connecting"] = ' Verbindung zum Host: ';
	$_lang["status_failed"] = 'fehlgeschlagen!';
	$_lang["status_failed_could_not_create_database"] = 'fehlgeschlagen – konnte Datenbank nicht erstellen';
	$_lang["status_failed_database_collation_does_not_match"] = 'fehlgeschlagen – Unterschied in der Datenbank-Kollation; benutzen Sie SET NAMES oder wählen Sie %s';
	$_lang["status_failed_table_prefix_already_in_use"] = 'fehlgeschlagen – Tabellen-Präfix bereits verwendet!';
	$_lang["status_passed"] = 'In Ordnung – Datenbank ausgewählt';
	$_lang["status_passed_database_created"] = 'In Ordnung – Datenbank erstellt';
	$_lang["status_passed_server"] = 'In Ordung – Kollationen sind nun auswählbar';
	$_lang["strict_mode"] = 'Warnung: MySQL-Server hat „sql_mode strict“ aktiviert';
	$_lang["strict_mode_error"] = 'Bestimmte MODx-Funktionen funktionieren nicht korrekt solange „STRICT_TRANS_TABLES sql_mode“ aktiviert ist. Sie können den MySQL-Modus ändern, in dem Sie die Datei „my.cnf“ anpassen oder Ihren Server-Administrator kontaktieren.';
	$_lang["summary_setup_check"] = 'Es wird überprüft, ob alles für die Installation bereit ist.';
	$_lang["table_prefix_already_inuse"] = ' – Tabellen-Präfix wird bereits benutzt!';
	$_lang["table_prefix_already_inuse_note"] = 'Die gewählte Datenbank konnte nicht beschrieben werden, da der Tabellen-Präfix bereits verwendet wird. Bitte wählen Sie einen anderen Präfix und wiederholen Sie die Installation.';
	$_lang["table_prefix_not_exist"] = ' – Tabellen-Präfix existiert nicht in der gewählten Datenbank!';
	$_lang["table_prefix_not_exist_note"] = 'Die gewählte Datenbank konnte nicht beschrieben werden, da keine Tabellen mit dem gewählten Präfix existieren. Bitte wählen Sie einen existierenden Präfix und wiederholen Sie die Installation.';
	$_lang["templates"] = 'Templates';
	$_lang["to_log_into_content_manager"] = 'Um sich im Manager anzumelden, klicken Sie auf den Schließen-Button.';
	$_lang["toggle"] = 'Umschalten';
	$_lang['tvs'] = 'Template-Variablen';
	$_lang["unable_install_chunk"] = 'Konnte Chunk nicht installieren.  Datei';
	$_lang["unable_install_module"] = 'Konnte Modul nicht installieren.  Datei';
	$_lang["unable_install_plugin"] = 'Konnte Plugin nicht installieren.  Datei';
	$_lang["unable_install_snippet"] = 'Konnte Snippet nicht installieren.  Datei';
	$_lang["unable_install_template"] = 'Konnte Template nicht installieren.  Datei';
	$_lang["upgrade_note"] = '<b>Achtung:</b> Nach dem Anmelden im Manager sollten Sie die Konfigurationseinstellungen unter <b>Werkzeuge &gt; Konfiguration</b> überprüfen und speichern bevor Sie Ihre Seite aufrufen.';
	$_lang["upgraded"] = 'Aktualisiert';
	$_lang["visit_forum"] = ', besuchen Sie die <a href="http://www.modxcms.de/forum/" target="_blank">MODx-Foren</a>.';
	$_lang["warning"] = 'ACHTUNG!';
	$_lang["welcome_message_start"] = 'Wählen Sie zunächst den Intallationstyp aus:';
	$_lang["welcome_message_text"] = 'Dieses Programm wird Sie durch die Installation begleiten.';
	$_lang["welcome_message_welcome"] = 'Willkommen beim MODx-Installationsprogramm.';
	$_lang["writing_config_file"] = 'Schreibe Konfigurationsdatei: ';
	$_lang["you_running_php"] = ' – Benutzte PHP-Version ';
?>