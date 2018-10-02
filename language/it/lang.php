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
	'ACP_PG_SOCIAL_MAIN'				=> 'Menù principale',
	'ACP_PG_SOCIAL_GENERAL'				=> 'Generale',
	'ACP_PG_SOCIAL_PAGE'				=> 'Menù pagine',
	'ACP_PG_SOCIAL_PAGE_MANAGE'			=> 'Amministra pagine',
	'ACP_PG_SOCIAL_SETTINGS_EXPLAIN'	=> 'Questa è la pagina delle impostazioni per l\' estensione "Phpbb PGreca Social"',
	'ACP_PG_SOCIAL_VERSION'				=> 'Versione',
	'ACP_PG_SOCIAL_ENABLED'				=> 'Attiva PG Social',
	'ACP_PG_SOCIAL_INDEX_REPLACE'		=> 'Sostituisci la Home con la pagina Attività',
	'ACP_PG_SOCIAL_STATUS'				=> 'Impostazioni stato',
	'ACP_PG_SOCIAL_BBCODE_ENABLED'		=> 'Abilita i BBCode',
	'ACP_PG_SOCIAL_SMILIES_ENABLED'		=> 'Abilita gli Smiles',
	'ACP_PG_SOCIAL_URL_ENABLED'			=> 'Abilita i Link', //TRADURRE
	'ACP_PG_SOCIAL_PROFILE'				=> 'Attiva i Profili PG Social',
	'ACP_PG_SOCIAL_PROFILE_EXPLAIN'		=> 'Sostituisce i Profili predefiniti',
	'ACP_PG_SOCIAL_SETTINGS'			=> 'Impostazioni PG Social',
	'ACP_SOCIAL_SIDEBAR_RIGHT'			=> 'Abilita la barra laterale a Destra',
	'ACP_SOCIAL_SIDEBAR_RIGHT_FRIENDSRANDOM'	=> 'Abilita gli Amici Casuali nella barra laterale a destra',
	'ACP_SOCIAL_SIDEBAR_RIGHT_LAST_POST'=> 'Abilita gli Ultimi Post nella barra laterale a destra',
	'ACP_PG_SOCIAL_CHAT'				=> 'Impostazioni CHAT',
	'ACP_SOCIAL_SOCIAL_CHAT_ENABLED'	=> 'Abilita Chat',
	'ACP_PG_SOCIAL_CHAT_BBCODE_ENABLED'	=> 'Abilita i BBCode nei messaggi',
	'ACP_PG_SOCIAL_CHAT_URL_ENABLED'	=> 'Abilita gli URL nei messaggi', 
	'ACP_PG_SOCIAL_SETTING_SAVED'		=> 'Configurazione aggiornata.',
	
	'ACTIVITY'							=> 'Attività',
	'ACTIVITY_PAGE'						=> 'Attività',
	'ALL'								=> 'Tutti', //TRADURRE
	'APPROVE_PAGE'						=> 'Approva',
	'ARE_YOU_SURE'						=> 'Questo stato sarà eliminato e non potrai trovarlo più. Confermi?',
	'ATTACH_PICTURE'					=> 'Allega immagine',
	'AVATAR'							=> 'Immagine di profilo',
	'COMMENT'							=> 'Commento',
	'COMMENT_NO'						=> 'Nessun commento', //TRADURRE
	'COMMENT_THIS_POST'					=> 'Commenta questo stato!',
	'COMMENTS'							=> 'Commenti',
	'COVER'								=> 'Immagine di copertina',
	'DO_YOU_WANT_SHARE'					=> 'Vuoi condividere questo stato?',
	'EDIT_PROFILE'						=> 'Modifica profilo',
	'FRIENDS'							=> 'Amici',
	'GALLER'							=> 'Galleria', //TRADURRE
	'GENDER'							=> 'Sesso', //TRADURRE
	'GENDER_FEMALE'						=> 'Femmina', //TRADURRE
	'GENDER_MALE'						=> 'Maschio', //TRADURRE
	'GENDER_UNKNOWN'					=> 'Sconosciuto', //TRADURRE
	'HAS_COMMENT_YOUR_POST'				=> '%s ha commentato uno stato',
	'HAS_LIKE_YOUR_POST'				=> 'A %s piace un tuo stato', //TRADURRE
	'HAS_UPLOADED_AVATAR'				=> 'ha caricato una nuovo foto di profilo', //TRADURRE
	'HAS_UPLOADED_COVER'				=> 'ha caricato una nuova foto di copertina', //TRADURRE
	'HAS_TAGGED_YOU'					=> '%s ti ha taggato in uno stato', //TRADURRE
	'HAS_WRITE_IN'						=> '%s ha scritto sulla bacheca di', //TRADURRE
	'HAS_WRITE_IN_YOUR'					=> '%s ha scritto sulla tua bacheca', //TRADURRE
	'INFO'								=> 'Informazioni', //TRADURRE
	'LIKE'								=> 'Mi Piace',
'LIKE_ACTIVE'  => 'Ti Piace già',
	'LIKES'								=> 'Mi Piace',
	
	'MCP_PG_SOCIAL_TITLE'				=> 'Social',
	'MCP_PG_SOCIAL_MAIN'				=> 'Menù principale',
	'MCP_PG_SOCIAL_PAGE_MANAGE'			=> 'Modera pagine',
	
	
	'NOTIFICATION_PG_SOCIAL'			=> 'Notifiche social', //TRADURRE
	'NOTIFICATION_TYPE_SOCIAL_STATUS'	=> 'Qualcuno scrive uno stato nella tua bacheca', //TRADURRE
	'NOTIFICATION_TYPE_SOCIAL_COMMENTS'	=> 'Qualcuno scrive un commento in un tuo stato', //TRADURRE
	'NOTIFICATION_TYPE_SOCIAL_LIKES'	=> 'Qualcuno mette un like ad un tuo stato', //TRADURRE
	'NOTIFICATION_TYPE_SOCIAL_TAG'		=> 'Qualcuno ti tagga in uno stato', //TRADURRE
	'ONLY_YOU'							=> 'Solo tu',
	'PAGE_USERNAME'						=> 'Nome pagina',
	'PAGES'								=> 'Pagine',
	'PG_SOCIAL_COMMENT_NEW_LOG'			=> '<strong>Ha commentato uno stato</strong><br />» %s',
	'PG_SOCIAL_COMMENT_REMOVE_LOG'		=> '<strong>Ha rimosso un suo commento da uno stato</strong>',
	'PG_SOCIAL_FRIENDS'					=> 'Amici',
	'PG_SOCIAL_FRIENDS_ACCEPT_REQ'		=> 'Accetta richiesta d\' amicizia',
	'PG_SOCIAL_FRIENDS_ADD'				=> 'Aggiungi agli amici',
	'PG_SOCIAL_FRIENDS_CANCEL_REQ'		=> 'Cancella richiesta d\' amicizia',
	'PG_SOCIAL_FRIENDS_DECLINE_REQ'		=> 'Rifiuta richiesta d\' amicizia',
	'PG_SOCIAL_FRIENDS_REMOVE'			=> 'Rimuovi dagli amici',
	'PG_SOCIAL_DISLIKE_NEW_LOG'			=> '<strong>Non piace più uno stato</strong><br />» %s',
	'PG_SOCIAL_LIKE_NEW_LOG'			=> '<strong>Piace uno stato</strong><br />» %s',
	'PG_SOCIAL_STATUS_NEW_LOG'			=> '<strong>Ha pubblicato uno stato</strong><br />» %s',
	'PG_SOCIAL_STATUS_SHARE_LOG'		=> '<strong>Ha condiviso uno stato</strong><br />» %s',
	'PG_SOCIAL_STATUS_REMOVE_LOG'		=> '<strong>Ha cancellato uno stato</strong>',
	'PHOTOS'							=> 'Foto',
	'PRIVACY_ALL'						=> 'Tutti',
	'PRIVACY_ONLY_FRIENDS'				=> 'Amici',
	'PRIVACY_ONLY_ME'					=> 'Solo io',
	'PRIVACY_VISIBLE_FOR'				=> 'Visibile per',
	'PROFILE_AVATAR_UPDATE'				=> 'Cambia immagine di Profilo',
	'PROFILE_COVER_UPDATE'				=> 'Cambia immagine di Copertina',
	'PUBLIC'							=> 'Pubblica',
	'QUOTE'								=> 'Citazione',
	'SHARE'								=> 'Condividi',
	
	'UCP_PG_SOCIAL_CHAT'				=> 'Imposta la chat',
	'UCP_PG_SOCIAL_MAIN'				=> 'Social',
	'UCP_PG_SOCIAL_CHAT_SOUND'			=> 'Abilita suono alla ricezione di nuovi messaggi in chat',
	'UCP_PG_SOCIAL_SIGNATURE_STATUS'	=> 'Sostituisci la tua firma con ogni tuo nuovo stato nella tua bacheca',
	'USER_FORUM'						=> 'Statistiche',
	'WALL'								=> 'Bacheca',
	'WALL_TIME_AGO'						=> '%1$u %2$s fa',
	'WALL_TIME_FROM_NOW'			  => '%1$u %2$s fa',
	'WALL_TIME_PERIODS'				  => array(
		'SECOND'	 => 'secondo',
		'SECONDS'	 => 'secondi',
		'MINUTE'	 => 'minuto',
		'MINUTES'	 => 'minuti',
		'HOUR'		 => 'ora',
		'HOURS'		 => 'ore',
		'DAY'		 => 'giorno',
		'DAYS'		 => 'giorni',
		'WEEK'		 => 'settimana',
		'WEEKS'		 => 'settimane',
		'MONTH'		 => 'mese',
		'MONTHS'	 => 'mesi',
		'YEAR'		 => 'anno',
		'YEARS'		 => 'anni',
		'DECADE'	 => 'decade',
		'DECADES'	 => 'decadi',
	),
	'WRITE_A_MESSAGE'				=> 'Scrivi un messaggio',
	'WRITE_SOMETHING'				=> 'Scrivi qualcosa',
	'VERSION'						=> 'Versione',
));
