<?php
/**
*
* Social extension for the phpBB Forum Software package.
*
* @copyright (c) 2017 Antonio PGreca (PGreca)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace pgreca\pgsocial\social;

class social_tag
{
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
	* @param \pgreca\pgsocial\controller\helper $pg_social_helper
	* @param \pgreca\pgsocial\controller\notifyhelper $notifyhelper Notification helper.
	* @param \phpbb\config\config			$config
	* @param \phpbb\db\driver\driver_interface	$db
	*/

	public function __construct($template, $user, $helper, $pg_social_helper, $social_zebra, $notifyhelper, $config, $db, $pgsocial_table_wallpost)
	{
		$this->template					= $template;
		$this->user						= $user;
		$this->helper					= $helper;
		$this->pg_social_helper 		= $pg_social_helper;
		$this->social_zebra				= $social_zebra;
		$this->notify 					= $notifyhelper;
		$this->config 					= $config;
		$this->db 						= $db;
		$this->pgsocial_wallpost		= $pgsocial_table_wallpost;
	}

	/**
	 * Search user for tag
	*/
	public function tag_system_search($who)
	{
		$who = str_replace('@', '', $who);
		$sql = "SELECT user_id, username, username_clean, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height, user_colour FROM ".USERS_TABLE." WHERE (username LIKE '%".$who."%' OR username_clean LIKE '%".$who."%') AND user_id != '".$this->user->data['user_id']."' AND user_type IN (".USER_NORMAL.", ".USER_FOUNDER.") ORDER BY username_clean ASC";
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result))
		{
			if ($this->social_zebra->friend_status($row['user_id'])['status'] == 'PG_SOCIAL_FRIENDS')
			{
				$this->template->assign_block_vars('tag_system_search', array(
					'USER_ID'			=> $row['user_id'],
					'USERNAME'			=> $row['username'],
					'USERNAME_CLEAN'	=> $row['username_clean'],
					'AVATAR'			=> $this->pg_social_helper->social_avatar_thumb($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']),

				));
			}
		}
		$this->db->sql_freeresult($result);
		return $this->helper->render('pg_social_tag_system_search.html', '');
	}

	/**
	 * Display who can tag
	*/
	public function show_tag($text)
	{
		$reg_str = '/&lt;span data-people="(.*?)" data-people_tagged="(.*?)" class="people_tagged" contenteditable="false"&gt;(.*?)\&lt;\/span&gt;/';
		preg_match_all($reg_str, $text, $matches);
		$texta = '';
		for($i=0; $i < count($matches[1]); $i++)
		{
			$text = str_replace('&lt;span data-people="'.$matches[1][$i].'" data-people_tagged="'.$matches[2][$i].'" class="people_tagged" contenteditable="false"&gt;'.$matches[2][$i].'&lt;/span&gt;', '<a href="'.get_username_string('profile', $matches[1][$i], $matches[2][$i], '').'" class="people_tagged">'.$matches[2][$i].'</a>', $text);
		}
		return trim($text);
	}

	/**
	 * Tag activity query
	*/
	public function add_tag($status, $text)
	{
		$reg_str = '/<span data-people="(.*?)" data-people_tagged="(.*?)" class="people_tagged" contenteditable="false">(.*?)<\/span>/';
		preg_match_all($reg_str, $text, $matches);
		$tagged_user = '';
		for($i=0; $i < count($matches[1]); $i++)
		{
			$this->notify->notify('add_tag', $status, $matches[1][$i], (int) $this->user->data['user_id'], 'NOTIFICATION_SOCIAL_TAG_ADD');
			$tagged_user .= $matches[1][$i].', ';
		}
		$sql = "UPDATE ".$this->pgsocial_wallpost." SET tagged_user = '".$tagged_user."' WHERE post_ID = '".$status."'";
		$this->db->sql_query($sql);
	}
}
