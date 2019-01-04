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
	'UCP_PG_SOCIAL_CHAT'				=> 'Setting the chat',
	'UCP_PG_SOCIAL_MAIN'				=> 'Social',
	'UCP_PG_SOCIAL_CHAT_SOUND'			=> 'Enable sound when receive new messages on chat',
	'UCP_PG_SOCIAL_SIGNATURE_STATUS'	=> 'Replace your signature with your new activity on your wall',
));