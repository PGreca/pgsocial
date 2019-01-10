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
	'MCP_PG_SOCIAL_TITLE'				=> 'Social',
	'MCP_PG_SOCIAL_MAIN'				=> 'Menu principal',
	'MCP_PG_SOCIAL_PAGE_MANAGE'			=> 'Modérer les pages',
	'MCP_PG_SOCIAL_PAGES_AWAITING'		=> 'Pages en attention d’approbation',
	'MCP_PG_SOCIAL_PAGE_NOAPPROVE'		=> 'Il n’y a pas de pages en attente d’approbation.',
	'MCP_NO_MANAGE'						=> 'Vous n’avez pas les permissions pour gérer les pages.'
));
