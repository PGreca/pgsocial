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
	'UCP_PG_SOCIAL_CHAT'				=> 'Paramètres du Chat',
	'UCP_PG_SOCIAL_MAIN'				=> 'Social',
	'UCP_PG_SOCIAL_CHAT_SOUND'			=> 'Activer une sonorité lorsque vous recevez un nouveau message sur le Chat',
	'UCP_PG_SOCIAL_SIGNATURE_STATUS'	=> 'Remplacer votre signature par votre dernière activité sur votre mur',
));
