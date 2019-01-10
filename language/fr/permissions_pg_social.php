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
	'ACL_CAT_PG_SOCIAL'		=> 'PG Social',
	'ACL_U_PAGE_CREATE'		=> 'Créer une page',
	'ACL_A_PAGE_MANAGE'		=> 'Gérer une page',

	'ACL_M_PAGE_MANAGE'		=> 'Gérer une page',
));
