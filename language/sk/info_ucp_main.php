<?php

/**
 *
 * PGreca Social extension for phpBB.
 * Slovak translation by Fonzi
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
	'UCP_PG_SOCIAL_CHAT'				=> 'Nastavenie četu',
	'UCP_PG_SOCIAL_MAIN'				=> 'Sociálna sieť',
	'UCP_PG_SOCIAL_CHAT_SOUND'			=> 'Povoliť zvuk pri príjmaní nových správ v čete',
	'UCP_PG_SOCIAL_SIGNATURE_STATUS'	=> 'Nahraďte svoj podpis s novou aktivitou na nástenke',
));