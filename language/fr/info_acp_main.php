<?php

/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2018 pgreca <https:/pgreca.it>
 * @translation (c) 2018 Mathieu M. <https://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=1781476>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_PG_SOCIAL_TITLE'				=> 'PG Social Network',
	'ACP_PG_SOCIAL_MAIN'				=> 'Menu principal',
	'ACP_PG_SOCIAL_GENERAL'				=> 'Général',
	'ACP_PG_SOCIAL_PAGE'				=> 'Pages',
	'ACP_PG_SOCIAL_PAGE_MANAGE'			=> 'Administrer les pages',
	'ACP_PG_SOCIAL_SETTINGS_EXPLAIN'	=> 'Paramètres pour l’extension "phpBB PGreca Social"',
	'ACP_PG_SOCIAL_VERSION'				=> 'Version',
	'ACP_PG_SOCIAL_ENABLED'				=> 'Activer PG Social',
	'ACP_PG_SOCIAL_INDEX_REPLACE'		=> 'Remplacer la page d’accueil par la page d’activités',
	'ACP_PG_SOCIAL_INDEX_ACTIVITY'		=> 'Activer la colonne de droite sur le forum avec les dernières activités',
	'ACP_PG_SOCIAL_STATUS'				=> 'Paramètres des statuts',
	'ACP_PG_SOCIAL_COLOR'				=> 'Choisir une couleur',
	'ACP_PG_SOCIAL_NOCOLOR'				=> 'Aucune',
	'ACP_PG_SOCIAL_BLUE'				=> 'Bleu',
	'ACP_PG_SOCIAL_DARK'				=> 'Sombre',
	'ACP_PG_SOCIAL_LIGHTBLUE'			=> 'Bleu clair',
	'ACP_PG_SOCIAL_GREEN'				=> 'Vert',
	'ACP_PG_SOCIAL_RED'					=> 'Rouge',
	'ACP_PG_SOCIAL_BBCODE_ENABLED'		=> 'Activer les BBCodes',
	'ACP_PG_SOCIAL_SMILIES_ENABLED'		=> 'Activer les Smileys',
	'ACP_PG_SOCIAL_URL_ENABLED'			=> 'Activer les URLs',
	'ACP_PG_SOCIAL_GALLERY'				=> 'Paramètres des albums',
	'ACP_PG_SOCIAL_GALLERY_LIMIT'		=> 'Limite du nombre d’albums',
	'ACP_PG_SOCIAL_PHOTO_LIMIT'			=> 'Limite du nombre de photos par album',
	'ACP_PG_SOCIAL_PROFILE'				=> 'Activer les profils PG Social',
	'ACP_PG_SOCIAL_PROFILE_EXPLAIN'		=> 'Remplace les profils par défaut',
	'ACP_PG_SOCIAL_SETTINGS'			=> 'Paramètres de PG Social',
	'ACP_SOCIAL_SIDEBAR_RIGHT'			=> 'Activer la colonne de droite',
	'ACP_SOCIAL_SIDEBAR_RIGHT_FRIENDSRANDOM'	=> 'Activer les suggestions d’amis dans la colonne de droite',
	'ACP_SOCIAL_SIDEBAR_RIGHT_LAST_POST'=> 'Activer les dernières activités dans la colonne de droite',
	'ACP_PG_SOCIAL_CHAT'				=> 'Paramètres du Chat',
	'ACP_SOCIAL_SOCIAL_CHAT_ENABLED'	=> 'Activer le Chat',
	'ACP_PG_SOCIAL_CHAT_BBCODE_ENABLED'	=> 'Activer les BBCodes dans les messages',
	'ACP_PG_SOCIAL_CHAT_URL_ENABLED'	=> 'Activer les URLs dans les messagges',
	'ACP_PG_SOCIAL_SETTING_SAVED'		=> 'Paramètres sauvegardés.',

));
