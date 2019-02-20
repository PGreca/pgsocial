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
	'MCP_PG_SOCIAL_TITLE'				=> 'Sociálna sieť',
	'MCP_PG_SOCIAL_MAIN'				=> 'Hlavná ponuka',
	'MCP_PG_SOCIAL_PAGE_MANAGE'			=> 'Spravovať stránky',
	'MCP_PG_SOCIAL_PAGES_AWAITING'		=> 'Stránky čakajú na schválenie',
	'MCP_PG_SOCIAL_PAGE_NOAPPROVE'		=> 'Neexistujú žiadne stránky čakajúce na schválenie.',
	'MCP_NO_MANAGE'						=> 'Nemáte oprávnenia na spravovanie stránok.'	
));