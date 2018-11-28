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
	'MCP_PG_SOCIAL_MAIN'				=> 'Hauptmenü',
	'MCP_PG_SOCIAL_PAGE_MANAGE'			=> 'Moderiere Seiten',
	'MCP_PG_SOCIAL_PAGES_AWAITING'		=> 'Seiten, die auf die Genehmigung warten',
	'MCP_PG_SOCIAL_PAGE_NOAPPROVE'		=> 'Es gibt keine Seiten, die auf die Genehmigung warten.',
	'MCP_NO_MANAGE'						=> 'Du hast nicht die Berechtigungen für die Verwaltung der Seiten.'	
));