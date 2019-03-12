<?php

/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2018 pgreca <https:/pgreca.it>
 * @translation (c) 2018 totallybeautiful <https://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=1781476>
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
	'ACP_PG_SOCIAL_MAIN'				=> 'Hauptmenü',
	'ACP_PG_SOCIAL_GENERAL'				=> 'Allgemeines',
	'ACP_PG_SOCIAL_PAGE'				=> 'Menüseiten',
	'ACP_PG_SOCIAL_PAGE_MANAGE'			=> 'Seiten verwalten',
	'ACP_PG_SOCIAL_SETTINGS_EXPLAIN'	=> 'Dies ist die Einstellungsseite für die "Phpbb PGreca Social" Erweiterung".',
	'ACP_PG_SOCIAL_VERSION'				=> 'Version',
	'ACP_PG_SOCIAL_ENABLED'				=> 'Aktiviert PG Social',
	'ACP_PG_SOCIAL_INDEX_REPLACE'		=> 'Ersetzen Sie die Startseite durch die Aktivitätsseite.',
	'ACP_PG_SOCIAL_INDEX_ACTIVITY'		=> 'Aktivieren Sie die rechte Sidebar der letzten Aktivität auf der Forum-Seite.',
	'ACP_PG_SOCIAL_STATUS'				=> 'Status Einstellungen',
	'ACP_PG_SOCIAL_COLOR'				=> 'Wähle eine Farbe',
	'ACP_PG_SOCIAL_NOCOLOR'				=> 'Nichts',
	'ACP_PG_SOCIAL_BLUE'				=> 'Blau',
	//'ACP_PG_SOCIAL_DARK'				=> '',
	'ACP_PG_SOCIAL_LIGHTBLUE'			=> 'Hellblau',
	'ACP_PG_SOCIAL_GREEN'				=> 'Grün',
	'ACP_PG_SOCIAL_RED'					=> 'Rot',
	'ACP_PG_SOCIAL_BBCODE_ENABLED'		=> 'BBCode aktiviert',
	'ACP_PG_SOCIAL_SMILIES_ENABLED'		=> 'Smiles aktiviert',
	'ACP_PG_SOCIAL_URL_ENABLED'			=> 'URL aktiviert',
	//'ACP_PG_SOCIAL_GALLERY'				=> '',
	//'ACP_PG_SOCIAL_GALLERY_LIMIT'		=> '',
	//'ACP_PG_SOCIAL_PHOTO_LIMIT'			=> 'Limit photos for gallery',
	'ACP_PG_SOCIAL_PROFILE'				=> 'Aktiviere PG Social Profile',
	'ACP_PG_SOCIAL_PROFILE_EXPLAIN'		=> 'Standardprofile ersetzen',
	'ACP_PG_SOCIAL_SETTINGS'			=> 'PG Social Einstellungen',
	'ACP_SOCIAL_SIDEBAR_RIGHT'			=> 'Aktiviere rechte Sidebar',
	'ACP_SOCIAL_SIDEBAR_RIGHT_FRIENDSRANDOM'	=> 'Aktiviere zufällige Freunde in der rechten Sidebar',
	'ACP_SOCIAL_SIDEBAR_RIGHT_LAST_POST'=> 'Aktiviere die letzte Aktivität in der rechten Sidebar.',
	'ACP_PG_SOCIAL_CHAT'				=> 'Chat Einstellungen',
	'ACP_SOCIAL_SOCIAL_CHAT_ENABLED'	=> 'Aktiviere Chat',
	'ACP_PG_SOCIAL_CHAT_BBCODE_ENABLED'	=> 'BBCode bei Nachrichten aktivieren',
	'ACP_PG_SOCIAL_CHAT_URL_ENABLED'	=> 'URL bei Nachrichten aktivieren',
	'ACP_PG_SOCIAL_SETTING_SAVED'		=> 'Einstellung gespeichert.',
));
