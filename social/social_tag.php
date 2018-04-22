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

class social_tag {
	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\config\config */
	protected $config;

	/**
	* Constructor
	*
	* @param \phpbb\template\template  $template
	* @param \phpbb\user				$user	
	* @param \phpbb\controller\helper		$helper
	* @param \pg_social\\controller\helper $pg_social_helper 
	* @param \wall\controller\notifyhelper $notifyhelper Notification helper.
	* @param \phpbb\config\config			$config
	* @param \phpbb\db\driver\driver_interface	$db 		
	*/
	
	public function __construct($template, $user, $helper, $pg_social_helper, $social_zebra, $notifyhelper, $config, $db, $table_prefix) {
		$this->template					= $template;
		$this->user						= $user;
		$this->helper					= $helper;
		$this->pg_social_helper 		= $pg_social_helper;
		$this->social_zebra				= $social_zebra;
		$this->notify 					= $notifyhelper;
		$this->config 					= $config;
		$this->db 						= $db;
        $this->table_prefix 			= $table_prefix;
	}
	
	public function tag_system_search($who) {
		$who = str_replace("@", "", $who);
		$sql = "SELECT user_id, username, username_clean, user_avatar, user_avatar_type, user_colour FROM ".USERS_TABLE." WHERE (username LIKE '%".$who."%' OR username_clean LIKE '%".$who."%') AND user_id != '".$this->user->data['user_id']."' AND user_type IN (".USER_NORMAL.", ".USER_FOUNDER.") ORDER BY username_clean ASC";
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result)) {
			if($this->social_zebra->friendStatus($row['user_id'])['status'] == 'PG_SOCIAL_FRIENDS') {
				$this->template->assign_block_vars('tag_system_search', array(
					'USER_ID'		=> $row['user_id'],
					'USERNAME'		=> $row['username'],
					'USERNAME_CLEAN'	=> $row['username_clean'],
					'AVATAR'		=> $this->pg_social_helper->social_avatar($row['user_avatar'], $row['user_avatar_type']),
					
				));
			}
		}			
		return $this->helper->render('pg_social_tag_system_search.html', '');
	}
	
	public function showTag($text) {
		$reg_str = '/<span data-people="(.*?)" data-people_tagged="(.*?)" class="people_tagged" contenteditable="false">(.*?)<\/span>/';
		preg_match_all($reg_str, $text, $matches);
		$texta = '';
		for($i=0; $i < count($matches[1]); $i++) {
			$text = str_replace('<span data-people="'.$matches[1][$i].'" data-people_tagged="'.$matches[2][$i].'" class="people_tagged" contenteditable="false">'.$matches[2][$i].'</span>', '<a href="'.get_username_string('profile', $matches[1][$i], $matches[2][$i], '').'" class="people_tagged">'.$matches[2][$i].'</a>', $text);
		}
		return trim($text);
	}
	
	public function addTag($status, $text) {
		$reg_str = '/<span data-people="(.*?)" data-people_tagged="(.*?)" class="people_tagged" contenteditable="false">(.*?)<\/span>/';
		preg_match_all($reg_str, $text, $matches);
		$tagged_user = '';
		for($i=0; $i < count($matches[1]); $i++) {
			$this->notify->notify('add_tag', $status, $text, $matches[1][$i], (int) $this->user->data['user_id'], 'NOTIFICATION_SOCIAL_TAG_ADD');			
			$tagged_user .= $matches[1][$i].', ';
		}
		$sql = "UPDATE ".$this->table_prefix."pg_social_wall_post SET tagged_user = '".$tagged_user."' WHERE post_ID = '".$status."'";
		$this->db->sql_query($sql);
	}
}