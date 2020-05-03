<?php

/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2018 pgreca <https:/pgreca.it>
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

$lang = array_merge($lang, [
	'PG_NOTICE'					=> 'PG Social Network necessita dell\' estensione "phpBB Media Embed Plugin".',
	
	'EXT_ENABLE_ERROR' 		=> 'This extension requires phpBB 3.2.2 (or greater).',
	'COOKIE_POLICY_FOUND'	=> 'You cannot install this extension while you still have the “Cookie policy” extension installed.<br />Please disable and delete the data for the “Cookie policy” extension and then try again.',
]);
/**
* Translators ignore this.
*
* Overwrite core error message keys with a more specific message.
*/
global $ver_error, $cookie_error;
if ($ver_error)
{
	$lang = array_merge($lang, array(
		'EXTENSION_NOT_ENABLEABLE' 		=> isset($lang['EXTENSION_NOT_ENABLEABLE']) ? $lang['EXTENSION_NOT_ENABLEABLE'] . '<br /><br /><strong>' . $lang['EXT_ENABLE_ERROR'] . '</strong>' : null,
		'CLI_EXTENSION_ENABLE_FAILURE' 	=> isset($lang['CLI_EXTENSION_ENABLE_FAILURE']) ? $lang['CLI_EXTENSION_ENABLE_FAILURE'] . ' : ' . $lang['EXT_ENABLE_ERROR'] : null,
	));
}
if ($cookie_error)
{
	$lang = array_merge($lang, array(
		'EXTENSION_NOT_ENABLEABLE' 		=> isset($lang['EXTENSION_NOT_ENABLEABLE']) ? $lang['EXTENSION_NOT_ENABLEABLE'] . '<br /><br /><strong>' . $lang['COOKIE_POLICY_FOUND'] . '</strong>' : null,
		'CLI_EXTENSION_ENABLE_FAILURE' 	=> isset($lang['CLI_EXTENSION_ENABLE_FAILURE']) ? $lang['CLI_EXTENSION_ENABLE_FAILURE'] . ' - ' . $lang['COOKIE_POLICY_FOUND'] : null,
	));
}
