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

class social_zebra
{
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
	* @param \pg_social\\controller\helper $pg_social_helper
	* @param \wall\controller\notifyhelper $notifyhelper Notification helper.	
	* @param \phpbb\config\config 	$config
	* @param \phpbb\db\driver\driver_interface			$db 
	*/
	
	public function __construct($template, $user, $helper, $pg_social_helper, $notifyhelper, $config, $db, $root_path, $php_ext, $table_prefix)
	{	
		$this->template							= $template;
	    $this->user								= $user;
		$this->helper							= $helper;
		$this->pg_social_helper					= $pg_social_helper;
		$this->notify 							= $notifyhelper;
		$this->config							= $config;
		$this->db								= $db;
	    $this->root_path						= $root_path;	
		$this->php_ext							= $php_ext;
        $this->table_prefix						= $table_prefix;			
	}	
	
	/**
	 * Return the friends or no-friends
	*/
	public function getFriends($profile, $type = NULL, $friend = "yes")
	{		
		if($friend == "no")
		{
			//return $this->last_register(); exit();
			return $this->noFriends();
		}
		$sql = "SELECT u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_colour
		FROM ".ZEBRA_TABLE." AS z, ".USERS_TABLE." AS u
		WHERE (z.zebra_id != '".$profile."' AND z.user_id = '".$profile."')
		AND u.user_id = z.zebra_id
		AND u.user_type NOT IN (".USER_INACTIVE.", ".USER_IGNORE.")
		AND z.friend = '1'
		ORDER BY u.username_clean ASC";
		if($type != 'profile') $result = $this->db->sql_query_limit($sql, 2); else $result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result))
		{	
			$this->template->assign_block_vars('profileFriends', array(
				'SQL'						=> '',
				'PROFILE_ID'				=> $row['user_id'],
				'PROFILE_URL'				=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),	
				'USERNAME'					=> $row['username'],				
				'USERNAME_CLEAN'			=> $row['username_clean'],
				'FRIEND_COLOUR'				=> "#".$row['user_colour'],
				'AVATAR'					=> $this->pg_social_helper->social_avatar($row['user_avatar'], $row['user_avatar_type']),
				
				'PROFILE_FRIEND_ACTION'		=> $this->friendStatus($row['user_id'])['status'],
			));
		}
		//return $this->helper->render('pg_social_friends.html', '');
	}
	
	/**
	 * Zebra status
	*/	
	public function friendStatus($user_id)
	{
		if($user_id != $this->user->data['user_id'])
		{
			$sql = "SELECT * FROM ".ZEBRA_TABLE."
			WHERE zebra_id IN (".$user_id.", ".$this->user->data['user_id'].")
			AND user_id IN (".$user_id.", ".$this->user->data['user_id'].")";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			if($row['user_id'] == $user_id && $row['zebra_id'] == $this->user->data['user_id'] && $row['approval'] == 1)
			{
				$data = array(
					"status"  => 'PG_SOCIAL_FRIENDS_ACCEPT_REQ',		
					"icon"    => 'check',								
				);	
			}
			else
			{
				$data = array(
					"status"  => ($row['friend'] ? 'PG_SOCIAL_FRIENDS' : ($row['approval'] ? 'PG_SOCIAL_FRIENDS_CANCEL_REQ' : ($row['foe'] ? 'PG_SOCIAL_FRIENDS_REMOVE_BLOCK' : 'PG_SOCIAL_FRIENDS_ADD'))),		
					"icon"    => ($row['friend'] ? 'ok' : ($row['approval'] ? 'remove' : ($row['foe'] ? 'ban-circle' : 'plus'))),		
					"friends" => $row['friend'] ? true : false,								
				);	
			}
			$this->db->sql_freeresult($result);
			return $data;
		}
	}
	
	/**
	 * Send request friend
	*/
	public function requestFriend($profile, $request)
	{
		switch($request)
		{
			case 'addFriend':
				$sql_arr = array(
					'user_id'	=> $this->user->data['user_id'],
					'zebra_id'	=> $profile,
					'friend'	=> 0,
					'foe'		=> 0,
					'approval'	=> 1,
				);
				$sql = "INSERT INTO ".ZEBRA_TABLE.$this->db->sql_build_array('INSERT', $sql_arr);
				$this->notify->notify('add_friend', '', '', $profile, $this->user->data['user_id'], 'NOTIFICATION_SOCIAL_FRIEND_ADD');		
				if($this->db->sql_query($sql)) $action = "REQUEST_SEND";
			break;
			case 'undoFriend':
				$sql = "DELETE FROM ".ZEBRA_TABLE." WHERE (zebra_id = '".$this->user->data['user_id']."' AND user_id = '".$profile."') OR (user_id = '".$this->user->data['user_id']."' AND zebra_id = '".$profile."')";
				if($this->db->sql_query($sql)) $action = "REQUEST_DELETE";
			break;
			case 'acceptFriend':
				$sql = "UPDATE ".ZEBRA_TABLE." SET friend = '1', approval = '0' 
				WHERE user_id = '".$profile."' AND zebra_id = '".$this->user->data['user_id']."'";
				$sql_arr = array(
					'user_id'	=> $this->user->data['user_id'],
					'zebra_id'	=> $profile,
					'friend'	=> 1,
					'foe'		=> 0,
					'approval'	=> 0,
				);
				$sqltwo = "INSERT INTO ".ZEBRA_TABLE.$this->db->sql_build_array('INSERT', $sql_arr);
				if($this->db->sql_query($sql))
				{
                    $this->db->sql_freeresult($result);
					if($this->db->sql_query($sqltwo)) $action = "REQUEST_ACC";
				}
			break;
			case 'declineFriend':
			case 'cancelFriend':
				$sql = "DELETE FROM ".ZEBRA_TABLE." 
				WHERE (zebra_id = '".$profile."' AND user_id = '".$this->user->data['user_id']."') OR (user_id = '".$profile."' AND zebra_id = '".$this->user->data['user_id']."')";
				if($this->db->sql_query($sql)) $action = "FRIEND_DELETE";
			break;			
		}
		$this->template->assign_vars(array(
			"ACTION"	=> $action,
		));
		
		return $this->helper->render('activity_status_action.html', "");
	}
		
	/**
	 * Count the friends
	*/
	public function countFriends($user)
	{
		$sql = "SELECT COUNT(friend) AS count
		FROM ".ZEBRA_TABLE."
		WHERE user_id = '".$user."' AND friend = '1'";
		$result = $this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('count');
		$return = $count;
		return $return;
	}
		
	/**
	 * The $s last register
	*/
	public function last_register()
	{
		$user_id = (int) $this->user->data['user_id'];
		$sql = "SELECT * FROM ".USERS_TABLE." WHERE user_id != '".$user_id."' AND user_type NOT IN (".USER_INACTIVE.", ".USER_IGNORE.") ORDER BY user_regdate DESC";
		$result = $this->db->sql_query_limit($sql, 3);
		while($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('profileFriends', array(
				'SQL'						=> '',
				'PROFILE_ID'				=> $row['user_id'],
				'PROFILE_URL'				=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),
				'USERNAME'					=> $row['username'],				
				'USERNAME_CLEAN'			=> $row['username_clean'],
				'FRIEND_COLOUR'				=> "#".$row['user_colour'],
				'AVATAR'					=> $this->pg_social_helper->social_avatar($row['user_avatar'], $row['user_avatar_type']),
				
			));
		}
		//return $this->helper->render('pg_social_friends.html', '');
	}
	
	/**
	 * Return who not is your friend
	*/
	public function noFriends()
	{
		$user_id = (int) $this->user->data['user_id'];
		$sql = "SELECT u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_colour, u.user_email
		FROM ".USERS_TABLE." AS u 
		LEFT JOIN ".BANLIST_TABLE." b ON (u.user_id = b.ban_userid)
		WHERE (b.ban_id IS NULL	OR b.ban_exclude = 1)
			AND u.user_id != '".$user_id."' 
			AND u.user_type NOT IN (1, 2) 
		ORDER BY RAND()";
		$result = $this->db->sql_query_limit($sql, 3);
		while($row = $this->db->sql_fetchrow($result))
		{
			if($this->friendStatus($row['user_id'])['status'] == 'PG_SOCIAL_FRIENDS_ADD')
			{
				$this->template->assign_block_vars('profileFriends', array(
					'SQL'						=> '',
					'PROFILE_ID'				=> $row['user_id'],
					'PROFILE_URL'				=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),
					'USERNAME'					=> $row['username'],
					'USERNAME_CLEAN'			=> $row['username_clean'],
					'AVATAR'					=> $this->pg_social_helper->social_avatar_thumb($row['user_avatar'], $row['user_avatar_type']),				
				
					
					'PROFILE_FRIEND_ACTION'		=> $this->friendStatus($row['user_id'])['status'],
				));	
			}
		}		
	}
}
