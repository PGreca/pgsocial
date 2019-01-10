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
	'UCP_PG_SOCIAL_CHAT'				=> 'Chateinstellungen',
	'UCP_PG_SOCIAL_MAIN'				=> 'Social',
	'UCP_PG_SOCIAL_CHAT_SOUND'			=> 'Aktiviert einen Ton, wenn du neue Nachrichten im Chat erhälst.',
	'UCP_PG_SOCIAL_SIGNATURE_STATUS'	=> 'Ersetze deine Signatur durch deine neue Aktivität an deiner Wand.',
));
