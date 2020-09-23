<?php
/**
 * MODx language File (modifié le 03/08/09)
 *
 * @author Grégory Pakosz (guardian) - Modifié par Coroico et Jean-Christophe Brebion (Fairytree) pour EVO 1.0.3
 * @package MODx
 * @version 1.0.3
 * 
 * Filename:       /install/lang/francais-utf8/francais-utf8.inc.php
 * Language:       French
 * Encoding:       UTF-8
 */
$_lang["agree_to_terms"] = 'Acceptation des termes d\'utilisation et installation';
$_lang["alert_database_test_connection"] = 'Vous devez créer votre base de données ou tester la sélection de votre base de données!';
$_lang["alert_database_test_connection_failed"] = 'Le test de sélection de votre base de données a échoué!';
$_lang["alert_enter_adminconfirm"] = 'Le mot de passe administrateur et la confirmation du mot de passe ne correspondent pas!';
$_lang["alert_enter_adminlogin"] = 'Vous devez saisir un nom d\'utilisateur pour le compte administrateur du système!';
$_lang["alert_enter_adminpassword"] = 'Vous devez saisir un mot de passe pour le compte administrateur du système!';
$_lang["alert_enter_database_name"] = 'Vous devez saisir le nom de la base de données!';
$_lang["alert_enter_host"] = 'Vous devez saisir le nom du serveur de votre base de données!';
$_lang["alert_enter_login"] = 'Vous devez saisir un nom d\'utilisateur pour la base de données!';
$_lang["alert_server_test_connection"] = 'Vous devez tester la connexion à votre serveur de base de données';
$_lang["alert_server_test_connection_failed"] = 'Le test de connexion à votre serveur de base de données a échoué!';
$_lang["alert_table_prefixes"] = 'Les préfixes de table doivent commencer par une lettre!';
$_lang["all"] = 'Tout';
$_lang["and_try_again"] = ', et réessayer. Si vous avez besoin d\'aide pour corriger le problème';
$_lang["and_try_again_plural"] = ', et réessayer. Si vous avez besoin d\'aide pour corriger les problèmes';
$_lang["begin"] = 'Démarrer';
$_lang["btnback_value"] = 'Précédent';
$_lang["btnclose_value"] = 'Fermer';
$_lang["btnnext_value"] = 'Suivant';
$_lang["cant_write_config_file"] = 'MODx n\'a pas pu écrire le fichier de configuration. Veuillez copier/coller ceci dans le fichier ';
$_lang["cant_write_config_file_note"] = 'Une fois l\'opération effectuée, vous pouvez vous connecter à l\'interface d\'administration de MODx en utilisant l\'adresse  VotreSite.com/'.MGR_DIR.'/.';
$_lang["checkbox_select_options"] = 'Cochez pour sélectionner les options:';
$_lang["checking_if_cache_exist"] = 'Vérification de l\'existence du répertoire <span class=\"mono\">assets/cache</span>: ';
$_lang["checking_if_cache_file2_writable"] = 'Vérification des droits en écriture du fichier <span class=\"mono\">assets/cache/sitePublishing.idx.php</span>: ';
$_lang["checking_if_cache_file_writable"] = 'Vérification des droits en écriture du fichier <span class=\"mono\">assets/cache/siteCache.idx.php</span>: ';
$_lang["checking_if_cache_writable"] = 'Vérification des droits en écriture du répertoire <span class=\"mono\">assets/cache</span>: ';
$_lang["checking_if_config_exist_and_writable"] = 'Vérification de l\'existence et des droits en écriture du fichier <span class=\"mono\">'.MGR_DIR.'/includes/config.inc.php</span>: ';
$_lang["checking_if_export_exists"] = 'Vérification de l\'existence du répertoire <span class=\"mono\">assets/export</span>: ';
$_lang["checking_if_export_writable"] = 'Vérification des droits en écriture du répertoire <span class=\"mono\">assets/export</span>: ';
$_lang["checking_if_images_exist"] = 'Vérification de l\'existence du répertoire <span class=\"mono\">assets/images</span>: ';
$_lang["checking_if_images_writable"] = 'Vérification des droits en écriture des répertoires <span class="mono">/assets/images</span>, <span class="mono">/assets/files</span>, <span class="mono">/assets/flash</span>, <span class="mono">/assets/media</span>, <span class="mono">/assets/backup</span> et <span class="mono">/assets/.thumbs</span>: ';
$_lang["checking_mysql_strict_mode"] = 'Vérification du mode strict MySQL: ';
$_lang["checking_mysql_version"] = 'Vérification de la version MySQL: ';
$_lang["checking_php_version"] = 'Vérification de la version PHP: ';
$_lang["checking_registerglobals"] = 'Vérification que Register_Globals est sur off: ';
$_lang["checking_registerglobals_note"] = 'Cette configuration rend votre site vulnérable aux attaques <a href="http://www.commentcamarche.net/attaques/cross-site-scripting.php3">XCSS</a> (Cross Site Scripting). Consultez votre hébergeur sur la marche à suivre pour désactiver ce réglage, en général par l\'une de ces trois solutions: modifier le fichier php.ini global, ajouter des règles dans le fichier .htaccess à la racine de votre installation MODx, ou ajouter un fichier php.ini personnalisé pour neutraliser l\'ensemble des fichiers dans chacun des répertoires de votre installation (et il y en a beaucoup). Vous pouvez continuez l\installation de MODx, mais considérez vous comme averti.'; //Look at changing this to provide a solution.
$_lang["checking_sessions"] = 'Vérifications des paramètres de sessions: ';
$_lang["checking_table_prefix"] = 'Vérification du préfixe de table `';
$_lang["chunks"] = 'Chunks';
$_lang["config_permissions_note"] = 'Lors des installations Linux/Unix, veuillez créer un nouveau fichier nommé <span class=\"mono\">config.inc.php</span> dans le répertoire <span class=\"mono\">'.MGR_DIR.'/includes/</span> avec les droits d\'accès 0666.';
$_lang["connection_screen_collation"] = 'Collation:';
$_lang["connection_screen_connection_method"] = 'Méthode de connexion:';
$_lang["connection_screen_database_connection_information"] = 'Informations base de données';
$_lang["connection_screen_database_connection_note"] = 'Veuillez saisir le nom de la base de données créée pour MODx. Si la base est inexistante, le programme d\'installation tentera de la créer pour vous. Cette opération est susceptible d\'échouer en fonction des autorisations de l\'utilisateur MySQL.';
$_lang["connection_screen_database_host"] = 'Serveur hébergeant la base:';
$_lang["connection_screen_database_info"] = 'Informations de la base de données';
$_lang["connection_screen_database_login"] = 'Identifiant utilisateur de la base:';
$_lang["connection_screen_database_name"] = 'Nom de la Base:';
$_lang["connection_screen_database_pass"] = 'Mot de passe:';
$_lang["connection_screen_database_test_connection"] = 'Cliquez ici pour créer votre base de données ou pour tester la sélection de votre base.';
$_lang["connection_screen_default_admin_email"] = 'Email de l\'administrateur:';
$_lang["connection_screen_default_admin_login"] = 'Nom d\'utilisateur de l\'administrateur:';
$_lang["connection_screen_default_admin_note"] = 'Vous allez maintenent saisir des informations du compte administrateur principal. Vous pouvez donner ici votre nom et un mot de passe facile à retenir. Vous aurez besoin de ces informations pour vous connecter comme administrateur après l\'installation.';
$_lang["connection_screen_default_admin_password"] = 'Mot de passe administrateur:';
$_lang["connection_screen_default_admin_password_confirm"] = 'Confirmation du mot de passe:';
$_lang["connection_screen_default_admin_user"] = 'Administrateur par défaut';
$_lang["connection_screen_defaults"] = 'Paramètres par défaut du Gestionnaire';
$_lang["connection_screen_server_connection_information"] = 'Connection au serveur et identification';
$_lang["connection_screen_server_connection_note"] = 'Veuillez saisir l\'hôte du serveur (nom du serveur ou adresse IP), votre identifiant utilisateur et votre mot de passe avant de tester la connexion.';
$_lang["connection_screen_server_test_connection"] = 'Tester la connexion au serveur de base de données et voire les collations disponibles.';
$_lang["connection_screen_table_prefix"] = 'Préfixe de table:';
$_lang["creating_database_connection"] = 'Création de la connexion à la base de données: ';
$_lang["database_alerts"] = 'Alertes de la base!';
$_lang["database_connection_failed"] = 'Échec de connexion à la base de données!';
$_lang["database_connection_failed_note"] = 'Veuillez vérifier les paramètres de connexion à la base de données et réessayez.';
$_lang["database_use_failed"] = 'Impossible d\'accéder à la base de données!';
$_lang["database_use_failed_note"] = 'Veuillez vérifier les droits d\'accès utilisateur à la base de données et réessayez.';
$_lang["default_language"] = 'Langue par défaut du Gestionnaire';
$_lang["default_language_description"] = 'Ceci est la langue par défaut qui sera utilisée dans l\'interface d\'administration de votre Gestionnaire de Contenu MODx.';
$_lang["during_execution_of_sql"] = ' lors de l\'exécution de la requête SQL ';
$_lang["encoding"] = 'utf-8';
$_lang["error"] = 'erreur';
$_lang["errors"] = 'erreurs';
$_lang["failed"] = 'ECHEC!';
$_lang["help"] = 'Aide!';
$_lang["help_link"] = 'http://forums.modx.com/';
$_lang["help_title"] = 'Aide à l\'installation sur les forums de MODx';
$_lang["iagree_box"] = 'J\'accepte les termes de <a href="../assets/docs/license.txt" target="_blank">la licence MODx</a>. Pour consulter une traduction de la licence GPL version 2, visitez le <a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0-translations.html" target="_blank">site du système d\'exploitation GNU</a>.';
$_lang["install"] = 'Installation';
$_lang["install_overwrite"] = 'Installation/Écrasement';
$_lang["install_results"] = 'État de l\'installation';
$_lang["install_update"] = 'Installation/Mise à jour';
$_lang["installation_error_occured"] = 'Les erreurs suivantes se sont produites au cours de l\'installation';
$_lang["installation_install_new_copy"] = 'Installation d\'une nouvelle copie de ';
$_lang["installation_install_new_note"] = 'Attention, cette option est susceptible d\'écraser les données de la base.';
$_lang["installation_mode"] = 'Type d\'installation';
$_lang["installation_new_installation"] = 'Nouvelle installation';
$_lang["installation_note"] = '<strong>NOTE:</strong> Après vous être connecté au Gestionnaire, vous devez éditer et sauvegarder les paramètres de configuration système avant de visiter le site en sélectionnant <strong>Outils</strong> -> Configuration dans le Gestionnaire MODx.';
$_lang["installation_successful"] = 'Installation réalisée avec succès!';
$_lang["installation_upgrade_advanced"] = 'Mise à jour avancée';
$_lang["installation_upgrade_advanced_note"] = 'Destiné aux administrateurs avancés ou à la migration vers un serveur de base de données disposant d\'un encodage différent. <b>Vous devez disposer du nom complet de la base de données, de l\'identifiant utilisateur, du mot de passe et des détails de connexion/collation.</b>';
$_lang["installation_upgrade_existing"] = 'Mise à jour d\'une installation existante';
$_lang["installation_upgrade_existing_note"] = 'Mise à jour des fichiers existants et de la base de données.';
$_lang["installed"] = 'Installé';
$_lang["installing_demo_site"] = 'Installation du site de démonstration: ';
$_lang["language_code"] = 'fr'; // for html element e.g. <html xml:lang="en" lang="en">
$_lang["loading"] = 'Chargement...';
$_lang["modules"] = 'Modules';
$_lang["modx_footer1"] = '&copy; 2005-2011 le projet de Framework de Gestion de Contenu <a href="http://www.modxcms.com/" target="_blank" style="color: green; text-decoration:underline">MODx</a>. Tous droits réservés. MODx est publié sous la licence GNU GPL.';
$_lang["modx_footer2"] = 'MODx est un logiciel libre. Nous vous encourageons à être créatifs et à utiliser MODx comme bon il vous semble. Votre seule obligation est de redistribuer sous licence libre votre version modifiée de MODx.';
$_lang["modx_install"] = 'MODx &raquo; Installation';
$_lang["modx_requires_php"] = ', alors que MODx nécessite PHP 4.2.0 ou supérieur';
$_lang["mysql_5051"] = ' la version serveur de MySQL est 5.0.51!';
$_lang["mysql_5051_warning"] = 'Il existe plusieurs problèmes avec la version MySQL 5.0.51. Il est recommandé de mettre à jour votre version de MySQL avant de continuer.';
$_lang["mysql_version_is"] = ' Votre version de MySQL est: ';
$_lang["no"] = 'No';
$_lang["none"] = 'Aucun';
$_lang["not_found"] = 'non trouvé';
$_lang["ok"] = 'OK!';
$_lang["optional_items"] = 'Options d\'installation';
$_lang["optional_items_note"] = 'Sélectionnez les options d\'installation et cliquez sur «Installer»:';
$_lang["php_security_notice"] = '<legend>Avertissement sécurité</legend><p>Bien que MODx fonctionne avec votre version de PHP, nous n\'en recommandons pas l\'utilisation. Votre version de PHP comporte de nombreuses failles de sécurité. Veuillez mettre à jour PHP vers une version 4.3.8 ou supérieure, afin de corriger ces failles. Cette mise à jour est recommandée pour la sécurité de votre propre site internet.</p>';
$_lang["please_correct_error"] = '. Veuillez corriger l\'erreur';
$_lang["please_correct_errors"] = '. Veuillez corriger les erreurs';
$_lang["plugins"] = 'Plugins';
$_lang["preinstall_validation"] = 'Validation de la phase de pré-installation';
$_lang["recommend_setting_change_title"] = 'Modification des paramètres de configuration recommendée';
$_lang["recommend_setting_change_validate_referer_confirmation"] = 'Modification des paramètres de configuration: <em>Autorisation des entêtes HTTP_REFERER?</em>';
$_lang["recommend_setting_change_validate_referer_description"] = 'Votre site n\'est pas configuré pour autoriser l\'entête HTTP_REFERER des requêtes entrantes dans le Manager. Nous recommandons fortement l\'autorisation de ce paramètre pour réduire le risque d\'attaque CSRF (Cross Site Request Forgery).';
$_lang["remove_install_folder_auto"] = 'Effacer automatiquement le répertoire «install» de mon site <br />&nbsp;(Cette opération nécessite des droits d\'accès en effacement sur le répertoire «install»).';
$_lang["remove_install_folder_manual"] = 'Veuillez effacer le répertoire &quot;<b>install</b>&quot; avant de vous connecter au Gestionnaire de Contenu.';
$_lang["retry"] = 'Réessayer';
$_lang["running_database_updates"] = 'Mise à jour de la base de données: ';
$_lang["sample_web_site"] = 'Exemple de site web';
$_lang["sample_web_site_note"] = 'Attention, cette opération va <b>écraser</b> les Ressources et Éléments existants.';
$_lang["session_problem"] = 'Un problème a été détecté avec vos sessions de serveur. Veuillez contacter votre administrateur pour corriger ce problème.';
$_lang["session_problem_try_again"] = 'Essayer encore?'; 
$_lang["setup_cannot_continue"] = 'Impossible de poursuivre l\'installation';
$_lang["setup_couldnt_install"] = 'Le programme d\'installation n\'a pas pu créer/modifier certaines tables dans la base de données spécifiée.';
$_lang["setup_database"] = 'Le programme d\'installation va tenter de configurer la base de données:<br />';
$_lang["setup_database_create_connection"] = 'Création de la connexion à la base de données: ';
$_lang["setup_database_create_connection_failed"] = 'Échec de connexion à la base de données!';
$_lang["setup_database_create_connection_failed_note"] = 'Veuillez vérifier les paramètres de connexion à la base de données et réessayer.';
$_lang["setup_database_creating_tables"] = 'Création des tables de la base: ';
$_lang["setup_database_creation"] = 'Création de la base de données `';
$_lang["setup_database_creation_failed"] = 'La création de la base de données a échoué!';
$_lang["setup_database_creation_failed_note"] = ' - Le programme d\'installation n\'a pas pu créer la base de données!';
$_lang["setup_database_creation_failed_note2"] = 'Le programme d\'installation n\'a pas pu créer la base de données, et aucune base de données existante avec le même nom n\'a été trouvée. Vraisemblablement, les réglages de sécurité de votre hébergeur n\'autorisent pas les scripts externes à créer une base de données. Veuillez suivre la procédure mise à disposition par votre hébergeur afin de créer la base, puis recommencez l\'installation.';
$_lang["setup_database_selection"] = 'Sélection de la base de données `';
$_lang["setup_database_selection_failed"] = 'La sélection de la base de données a échoué...';
$_lang["setup_database_selection_failed_note"] = 'La base de données n\'existe pas. Le programme d\'installation va essayer de la créer.';
$_lang["snippets"] = 'Snippets';
$_lang["some_tables_not_updated"] = 'Certaines tables n\'ont pas été mises à jour. Ceci peut être dû à des modifications précédentes.';
$_lang["status_checking_database"] = 'Vérification de la base de données: ';
$_lang["status_connecting"] = 'Connexion à l\'hôte: ';
$_lang["status_failed"] = 'échec!';
$_lang["status_failed_could_not_create_database"] = 'échec - impossible de créer la base de données';
$_lang["status_failed_database_collation_does_not_match"] = 'échec - collation différente; utilisez SET NAMES ou choisir %s';
$_lang["status_failed_table_prefix_already_in_use"] = 'échec - préfixe de table déjà utilisé!';
$_lang["status_passed"] = 'succès - base sélectionnée';
$_lang["status_passed_database_created"] = 'succès - base créée';
$_lang["status_passed_server"] = 'succès - collations maintenant disponibles';
$_lang["strict_mode"] = ' MySQL est configuré en mode strict!';
$_lang["strict_mode_error"] = 'MODx nécessite que le mode strict de MySQL soit désactivé. Vous pouvez changer le mode strict en éditant le fichier my.cnf de MySQL ou alors contacter l\'administrateur de votre serveur.';
$_lang["summary_setup_check"] = 'Le programme d\'installation a effectué une série de vérifications afin de déterminer si tout est prêt pour démarrer l\'installation.';
$_lang["system_configuration"] = 'Configuration Système ';
$_lang["system_configuration_validate_referer_description"] = 'L\'<strong>autorisation des entêtes HTTP_REFERER</strong> est recommandée et peut protéger votre site d\'attaques CSRF, mais avec certaines configurations serveurs peut rendre votre manager inaccessible.';
$_lang["table_prefix_already_inuse"] = ' - Le préfixe de table est déjà utilisé dans cette base de données!';
$_lang["table_prefix_already_inuse_note"] = 'Le programme d\'installation n\'a pas pu utiliser la base de données spécifiée parce qu\'elle contient déjà des tables comportant le préfixe que vous avez choisi. Veuillez sélectionner un autre préfixe de table et recommencer l\'installation.';
$_lang["table_prefix_not_exist"] = ' - Le préfixe de table n\'existe pas dans la base de données!';
$_lang["table_prefix_not_exist_note"] = 'Le programme d\'installation n\'a pas pu utiliser la base de données spécifiée parce qu\'elle ne contient pas de tables comportant le préfixe que vous avez choisi pour la mise à jour. Veuillez choisir un préfixe de table existant et recommencer l\'installation.';
$_lang["templates"] = 'Modèles';
$_lang["to_log_into_content_manager"] = 'Pour vous connecter au Gestionnaire de Contenu ('.MGR_DIR.'/index.php), cliquez sur le bouton «Fermer».';
$_lang["toggle"] = 'Intervertir';
$_lang['tvs'] = 'Variables de Template';
$_lang["unable_install_chunk"] = 'Impossible d\'installer le Chunk.  Fichier';
$_lang["unable_install_module"] = 'Impossible d\'installer le Module.  Fichier';
$_lang["unable_install_plugin"] = 'Impossible d\'installer le Plugin.  Fichier';
$_lang["unable_install_snippet"] = 'Impossible d\'installer le Snippet.  Fichier';
$_lang["unable_install_template"] = 'Impossible d\'installer le Modèle.  Fichier';
$_lang["upgrade_note"] = '<strong>NOTE:</strong> Avant de visiter le site, il est conseillé de vous connecter en tant qu\'administrateur au Gestionnaire et de vérifier les paramètres de configuration système.';
$_lang["upgraded"] = 'Mis à jour';
$_lang["validate_referer_title"] = 'Autorisez vous les entêtes HTTP_REFERER?';
$_lang["visit_forum"] = ', visitez les <a href="http://www.modxcms.com/forums/" target="_blank">forums de MODx</a>.';
$_lang["warning"] = 'ATTENTION!';
$_lang["welcome_message_start"] = 'Pour commencer, choisissez le type d\'installation à réaliser:';
$_lang["welcome_message_text"] = 'Ce programme vous guidera tout au long du processus d\'installation.';
$_lang["welcome_message_welcome"] = 'Bienvenue dans le programme d\'installation de MODx.';
$_lang["writing_config_file"] = 'Écriture des fichiers de configuration: ';
$_lang["yes"] = 'Oui';
$_lang["you_running_php"] = ' - Vous utilisez PHP ';
?>