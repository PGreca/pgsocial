<?php
/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2015 pgreca <http://www.livemembersonly.com>
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
	'MCP_PG_SOCIAL_MAIN'				=> 'Main menu',
	'MCP_PG_SOCIAL_PAGE_MANAGE'			=> 'Moderate pages',
	'MCP_PG_SOCIAL_PAGES_AWAITING'		=> 'Pages awaiting approval',
	'MCP_PG_SOCIAL_PAGE_NOAPPROVE'		=> 'There are no pages waiting for approval.',
	'MCP_NO_MANAGE'						=> 'You haven\'t the permissions for manage the pages.'	
));