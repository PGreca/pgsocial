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
	'ACP_PG_SOCIAL_GENERAL'				=> 'General',
	'ACP_PG_SOCIAL_SETTINGS_EXPLAIN'	=> 'This is the settings page for "Phpbb PGreca Social" extension"',
	'ACP_PG_SOCIAL_VERSION'				=> 'Version',
	'ACP_PG_SOCIAL_ENABLED'				=> 'Enable PG Social',
	'ACP_PG_SOCIAL_STATUS'				=> 'Status settings',
	'ACP_PG_SOCIAL_COLOR'				=> 'Choose color',
	'ACP_PG_SOCIAL_NOCOLOR'				=> 'None',
	'ACP_PG_SOCIAL_BLUE'				=> 'Blue',
	'ACP_PG_SOCIAL_GREEN'				=> 'Green',
	'ACP_PG_SOCIAL_RED'					=> 'Red',
	'ACP_PG_SOCIAL_BBCODE_ENABLED'		=> 'Enable BBCode',
	'ACP_PG_SOCIAL_SMILIES_ENABLED'		=> 'Enable Smiles',
	'ACP_PG_SOCIAL_URL_ENABLED'			=> 'Enable URL',
	'ACP_PG_SOCIAL_PROFILE'				=> 'Enable PG Social Profiles',
	'ACP_PG_SOCIAL_PROFILE_EXPLAIN'		=> 'Replace default Profiles',
	'ACP_PG_SOCIAL_SETTINGS'			=> 'PG Social Settings',
	'ACP_SOCIAL_SIDEBAR_RIGHT'			=> 'Enable right sidebar',
	'ACP_SOCIAL_SIDEBAR_RIGHT_FRIENDSRANDOM'	=> 'Enabled Random Friends on the right sidebar',
	'ACP_PG_SOCIAL_CHAT'				=> 'Chat Settings',
	'ACP_PG_SOCIAL_CHAT_BBCODE_ENABLED'	=> 'Enable BBCode on messages',
	'ACP_PG_SOCIAL_CHAT_URL_ENABLED'	=> 'Enable URL on messagges',
	'ACP_SOCIAL_SOCIAL_CHAT_ENABLED'	=> 'Enable Chat',
	'ACP_PG_SOCIAL_SETTING_SAVED'		=> 'Setting saved.',
	
	'ACTIVITY'							=> 'Activity',
	'ACTIVITY_PAGE'						=> 'Page Activity',
	'ALL'								=> 'All',
	'ARE_YOU_SURE'						=> 'Are you sure?',
	'ARE_YOU_SURE_PHOTO'				=> 'Are you sure?',
	'ATTACH_PICTURE'					=> 'Attach picture',
	'AVATAR'							=> 'Avatar',
	'COMMENT'							=> 'Comment',
	'COMMENT_NO'						=> 'No comment',
	'COMMENT_THIS_POST'					=> 'Comment this post!',
	'COMMENTS'							=> 'Comments',
	'COVER'								=> 'Cover photo',
	'EDIT'								=> 'Edit',
	'EDIT_PROFILE'						=> 'Edit profile',
	'FRIENDS'							=> 'Friends',
	'GALLER'							=> 'Gallery',
	'GENDER'							=> 'Gender',
	'GENDER_FEMALE'						=> 'Female',
	'GENDER_MALE'						=> 'Male',
	'GENDER_UNKNOWN'					=> 'Unknown',
	'HAS_UPLOADED_AVATAR'				=> 'has uploaded a new profile picture',
	'HAS_UPLOADED_COVER'				=> 'has uploaded a new cover picture',
	'HAS_WRITE_IN'						=> 'has written on the wall of',
	'INFO'								=> 'Info',
	'LESS_MINUTE'						=> 'less than a minute',
	'LIKE'								=> 'Like',
	'LIKES'								=> 'Likes',
	'MINUTES'							=> '%1 minuti',
	/*'NOTIFICATION_TYPE_SOCIAL_STATUS'	=> 'is writing something on the wall',
	'NOTIFICATION_TYPE_SOCIAL_COMMENTS'	=> 'is commenting your post',
	'NOTIFICATION_TYPE_SOCIAL_LIKES'	=> 'like your post',*/
	'PAGES'								=> 'Pages',
	'ONLY_YOU'							=> 'Only you',
	'PG_SOCIAL_FRIENDS'					=> 'Friends',
	'PG_SOCIAL_FRIENDS_ACCEPT_REQ'		=> 'Accept friend request',
	'PG_SOCIAL_FRIENDS_ADD'				=> 'Add as friend',
	'PG_SOCIAL_FRIENDS_CANCEL_REQ'		=> 'Delete friend request',
	'PG_SOCIAL_FRIENDS_DECLINE_REQ'		=> 'Refuse friend request',
	'PG_SOCIAL_FRIENDS_REMOVE'			=> 'Remove friend',
	'PHOTO_DELETE'						=> 'Delete this photo',
	'PHOTOS'							=> 'Pictures',	
	'PRIVACY_ALL'						=> 'All',
	'PRIVACY_ONLY_FRIENDS'				=> 'Friends',
	'PRIVACY_ONLY_ME'					=> 'Only me',	
	'PRIVACY_VISIBLE_FOR'				=> 'Visible for',
	'PROFILE_AVATAR_UPDATE'				=> 'Change Profile picture',
	'PROFILE_COVER_UPDATE'				=> 'Change Cover picture',
	'PROFILE_UPDATE'					=> 'Update profile',
	'PUBLIC'							=> 'Post',
	'QUOTE'								=> 'Quote',
    'USER_FORUM'						=> 'Statistics',
	'WALL'								=> 'Wall',
	'WALL_TIME_AGO'						=> '%1$u %2$s ago',
	'WALL_TIME_FROM_NOW'			  => '%1$u %2$s ago',
	'WALL_TIME_PERIODS'				  => array(
		'SECOND'	 => 'second',
		'SECONDS'	 => 'seconds',
		'MINUTE'	 => 'minute',
		'MINUTES'	 => 'minutes',
		'HOUR'		 => 'hour',
		'HOURS'		 => 'hours',
		'DAY'		 => 'day',
		'DAYS'		 => 'days',
		'WEEK'		 => 'week',
		'WEEKS'		 => 'weeks',
		'MONTH'		 => 'month',
		'MONTHS'	 => 'months',
		'YEAR'		 => 'year',
		'YEARS'		 => 'years',
		'DECADE'	 => 'decade',
		'DECADES'	 => 'decades',
	),
	'WRITE_A_MESSAGE'				=> 'Write a message',
	'WRITE_SOMETHING'				=> 'Write something',
));
?>