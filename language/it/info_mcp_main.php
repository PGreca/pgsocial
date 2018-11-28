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
	'MCP_PG_SOCIAL_TITLE'				=> 'Social',
	'MCP_PG_SOCIAL_MAIN'				=> 'Menù principale',
	'MCP_PG_SOCIAL_PAGE_MANAGE'			=> 'Modera pagine',	
	'MCP_PG_SOCIAL_PAGES_AWAITING'		=> 'Pagine in attesa di approvazione',
	'MCP_PG_SOCIAL_PAGE_NOAPPROVE'		=> 'Non ci sono pagine che attendono approvazione.',
	'MCP_NO_MANAGE'						=> 'Non disponi dei permessi per la gestione delle pagine.'
));