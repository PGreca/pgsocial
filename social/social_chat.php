<?php
/**
*
* Social extension for the phpBB Forum Software package.
*
* @copyright (c) 2017 Antonio PGreca (PGreca)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace pgreca\pg_social\social;

class social_chat {
	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\config\config */
	protected $config;
	
	/* @var string phpBB root path */
	protected $root_path;	
	
	/* @var string phpEx */
	protected $php_ext;	

	/**
	* Constructor
	*
	* @param \phpbb\template\template  $template
	* @param \phpbb\user				$user	 
	* @param \phpbb\controller\helper		$helper
	* @param \phpbb\config\config			$config
	* @param \phpbb\db\driver\driver_interface	$db 	
	* @param \pg_social\\controller\helper $pg_social_helper	
	*/
	
	public function __construct($template, $user, $helper, $config, $db, $pg_social_helper, $social_zebra, $root_path, $php_ext, $table_prefix) {
		$this->template					= $template;
		$this->user						= $user;
		$this->helper					= $helper;
		$this->pg_social_helper 		= $pg_social_helper;
		$this->social_zebra				= $social_zebra;
		$this->config 					= $config;
		$this->db 						= $db;	
	    $this->root_path				= $root_path;	
		$this->php_ext 					= $php_ext;
        $this->table_prefix 			= $table_prefix;
	}
	
	public function messageCheck($exclude) {
		$user_id = (int) $this->user->data['user_id'];
		$sql = "SELECT user.user_id, user.username, user.user_colour, user.user_avatar, user.user_avatar_type FROM ".$this->table_prefix."pg_social_chat as chat, ".USERS_TABLE." as user
		WHERE (chat.chat_member = '".$user_id."' AND chat.user_id NOT IN (".$exclude.")) AND (chat.user_id = user.user_id AND chat_read = '0')
		GROUP BY chat.user_id ORDER BY chat_time DESC";
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result)) {
			$this->template->assign_block_vars('pg_social_chat_person', array(
				'PROFILE_ID'						=> $row['user_id'],
				'PROFILE_URL'						=> append_sid("{$this->root_path}memberlist.$this->php_ext", "mode=viewprofile&amp;u={$row["user_id"]}"),
				'PROFILE_STATUS'					=> $this->pg_social_helper->social_status($row['user_id']),
				'PROFILE_USERNAME'					=> $row['username'],
				'PROFILE_COLOUR'					=> "#".$row['user_colour'],
				'PROFILE_AVATAR'					=> $this->pg_social_helper->social_avatar($row['user_avatar'], $row['user_avatar_type']),
				'PROFILE_INFO'						=> '',
			));			
		}
		return $this->helper->render('pg_social_chatperson.html', '');
	}
	
	public function getchatPeople($person) {
		$user_id = (int) $this->user->data['user_id'];
		if($person) {
			$searchPerson = "(u.username_clean LIKE '".$person."' OR u.username LIKE '%".$person."%') AND";
		} else {
			$searchPerson = "";
		}
		$sql = "SELECT u.user_id, u.username, u.username_clean, u.user_colour, u.user_avatar, u.user_avatar_type
		FROM ".USERS_TABLE." as u
		WHERE u.user_type IN (".USER_NORMAL.", ".USER_FOUNDER.") AND ".$searchPerson." u.user_id != '".$user_id."'";
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result)) {
			if($this->social_zebra->friendStatus($row['user_id'])['status'] == 'PG_SOCIAL_FRIENDS') {
				$this->template->assign_block_vars('pg_social_chat', array(
					'USER_ID'					=> $row['user_id'],
					'USER_USERNAME'				=> $row['username'],
					'USER_COLOR'				=> $row['user_colour'],
					'USER_AVATAR'				=> $this->pg_social_helper->social_avatar($row['user_avatar'], $row['user_avatar_type']),
					'USER_PROFILE'				=> append_sid("{$this->root_path}memberlist.$this->php_ext", "mode=viewprofile&amp;u={$row["user_id"]}"),
					'USER_STATUS'				=> $this->pg_social_helper->social_status($row['user_id']),
					
					'SQL'						=> '',
				));
			}
		}
		return $this->helper->render('pg_social_chatpeople.html', '');
	}
	
	public function getchatPerson($person) {
		$sql = "SELECT user_id, username, username_clean, user_colour, user_avatar, user_avatar_type
		FROM ".USERS_TABLE."
		WHERE user_id = '".$person."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
				
		$this->template->assign_block_vars('pg_social_chat_person', array(
			'PROFILE_ID'						=> $row['user_id'],
			'PROFILE_URL'						=> append_sid("{$this->root_path}memberlist.$this->php_ext", "mode=viewprofile&amp;u={$row["user_id"]}"),
			'PROFILE_STATUS'					=> $this->pg_social_helper->social_status($row['user_id']),
			'PROFILE_USERNAME'					=> $row['username'],
			'PROFILE_COLOUR'					=> "#".$row['user_colour'],
			'PROFILE_AVATAR'					=> $this->pg_social_helper->social_avatar($row['user_avatar'], $row['user_avatar_type']),
		));
		return $this->helper->render('pg_social_chatperson.html', '');
	}
	
	public function getchatMessage($person, $type, $lastmessage) {
		$limit = 1; 
		$user = (int) $this->user->data['user_id'];
	
		$sql = "SELECT * FROM ".$this->table_prefix."pg_social_chat 
		WHERE chat_id > '".$lastmessage."' AND ((user_id = '".$person."' AND chat_member = '".$user."') OR (user_id = '".$user."' AND chat_member = '".$person."')) AND chat_status  = '1'
		ORDER BY chat_time DESC";
		$result = $this->db->sql_query_limit($sql, $limit);
		while($row = $this->db->sql_fetchrow($result)){			
			if($row['user_id'] == $user) $ifright = 1; else $ifright = 0; $this->messageRead();
			
			$allow_bbcode = false;
			$allow_urls = $this->config['pg_social_chat_message_url_enabled'];
			$allow_smilies = $this->config['pg_social_smilies'];
			$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);
		
			$msg = $this->pg_social_helper->social_smilies(generate_text_for_display($row['chat_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $flags));
			$msg .= $this->pg_social_helper->extraText($row['chat_text']);
			if(($person == $row['user_id']) && $lastmessage != 0 && $this->user->data['user_chat_music'] == 1) $sound = 1; else $sound = 0;
			$this->template->assign_block_vars('pg_social_chat_message', array(
				'MESSAGE_ID'	=> $row['chat_id'],
				'IFRIGHT'		=> $ifright,
				'MESSAGE'		=> $msg,
				'TIME'			=> $this->pg_social_helper->time_ago($row['chat_time']),
				'SOUND'			=> $sound,
			));
		}
		return $this->helper->render('pg_social_chatmessage.html', '');
	}
	
	public function messageRead() {
		$user = (int) $this->user->data['user_id'];
		$sql = "UPDATE ".$this->table_prefix."pg_social_chat SET chat_read = '1' WHERE chat_member = '".$user."'";
		$this->db->sql_query($sql);
	}
	
	public function messageSend($person, $message) {
		$allow_urls = $this->config['pg_social_chat_message_url_enabled'];
		$allow_smilies = $this->config['pg_social_chat_smilies_enabled'];
		generate_text_for_storage($message, $uid, $bitfield, $flags, false, $allow_urls, $allow_smilies);
		
		$user = (int) $this->user->data['user_id'];
		$sql_arr = array(
			'user_id'			=> $user,
			'chat_text'			=> $message,
			'chat_time'			=> time(),
			'chat_member'		=> $person,
			'chat_status'		=> 1,
			'chat_read'			=> 0,
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid
		);
		$sql = "INSERT INTO ".$this->table_prefix."pg_social_chat ".$this->db->sql_build_array('INSERT', $sql_arr);
		$this->db->sql_query($sql);	
		
		$this->template->assign_vars(array(
			"ACTION"	=> "messageSend",
		));
		return $this->helper->render('activity_status_action.html', "ah");
	}
}