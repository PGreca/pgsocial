<?php

/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2015 pgreca <http://www.livemembersonly.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB')) {
	exit;
}

if (empty($lang) || !is_array($lang)) {
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_PG_SOCIAL_TITLE'				=> 'PG Social',
	'ACP_PG_SOCIAL_GENERAL'				=> 'Generale',
	'ACP_PG_SOCIAL_SETTINGS_EXPLAIN'	=> 'Ceci est la page des paramètres pour l\' extension "Phpbb PGreca Social"',
	'ACP_PG_SOCIAL_VERSION'				=> 'Version',
	'ACP_PG_SOCIAL_ENABLED'				=> 'Active PG Social',
	'ACP_PG_SOCIAL_STATUS'				=> 'Paramètres statut',
	'ACP_PG_SOCIAL_SMILIES_ENABLED'		=> 'Active les Smiles',
	'ACP_PG_SOCIAL_BBCODE_ENABLED'		=> 'Active les BBCode',
	'ACP_PG_SOCIAL_PROFILE'				=> 'Active les Profils PG Social',
	'ACP_PG_SOCIAL_PROFILE_EXPLAIN'		=> 'Remplace les Profils prédéfinis',
	'ACP_PG_SOCIAL_SETTINGS'			=> 'Paramètres PG Social',
	'ACP_SOCIAL_SIDEBAR_RIGHT'			=> 'Active la barre latérale à Droite',
	'ACP_SOCIAL_SIDEBAR_RIGHT_FRIENDSRANDOM'	=> 'Active les Amis Aléatoires dans la barre latérale à Droite',
	'ACP_PG_SOCIAL_CHAT'				=> 'Paramètre CHAT',
	'ACP_SOCIAL_SOCIAL_CHAT_ENABLED'	=> 'Active Chat',
	'ACP_PG_SOCIAL_SETTING_SAVED'		=> 'Configuration actualisée.',
	
	'ACTIVITY'							=> 'Activité',
	'ACTIVITY_PAGE'						=> 'Activité Page',
	'ARE_YOU_SURE'						=> '',  //TRADURRE
	'COMMENT'							=> 'Commentaire',
	'COMMENT_THIS_POST'					=> 'Commente ce post!',
	'COMMENTS'							=> 'Commentaires',
	'FRIENDS'							=> 'Amis',
	'LIKE'								=> 'J\' aime',
	'LIKES'								=> 'J\' aime',
	'PG_SOCIAL_FRIEND'					=> 'Amis',
	'PG_SOCIAL_FRIENDS_ACCEPT_REQ'		=> 'Accepte  demande d\'ami',
	'PG_SOCIAL_FRIENDS_CANCEL_REQ'		=> 'Efface demande d\'ami',
	'PG_SOCIAL_FRIENDS_REMOVE'			=> 'Supprime des amis',
		
	'PUBLIC'							=> '',  //TRADURRE
	'USER_FORUM'						=> 'Statistiques',
	'WALL_TIME_AGO'						=> '%1$u %2$s fa',
	'WALL_TIME_FROM_NOW'			  => '%1$u %2$s fa',
	'WALL_TIME_PERIODS'				  => array(
		'SECOND'	 => 'seconde',
		'SECONDS'	 => 'secondes',
		'MINUTE'	 => 'minute',
		'MINUTES'	 => 'minutes',
		'HOUR'		 => 'heure',
		'HOURS'		 => 'heures',
		'DAY'		 => 'jour',
		'DAYS'		 => 'jours',
		'WEEK'		 => 'semaine',
		'WEEKS'		 => 'semaines',
		'MONTH'		 => 'mois',
		'MONTHS'	 => 'mois',
		'YEAR'		 => 'an',
		'YEARS'		 => 'ans',
		'DECADE'	 => 'décennie',
		'DECADES'	 => 'décennies',
	),
	'WRITE_SOMETHING'				=> '',  //TRADURRE
));


