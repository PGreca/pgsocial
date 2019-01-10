<?php

/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2018 pgreca <https:/pgreca.it>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
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
	'UCP_PG_SOCIAL_CHAT'				=> 'Imposta la chat',
	'UCP_PG_SOCIAL_MAIN'				=> 'Social',
	'UCP_PG_SOCIAL_CHAT_SOUND'			=> 'Abilita suono alla ricezione di nuovi messaggi in chat',
	'UCP_PG_SOCIAL_SIGNATURE_STATUS'	=> 'Sostituisci la tua firma con ogni tuo nuovo stato nella tua bacheca',
));
