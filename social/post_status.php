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

class post_status
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
	* @param \phpbb\config\config			$config
	* @param \phpbb\db\driver\driver_interface	$db 		
	*/
	
	public function __construct($template, $user, $helper, $pg_social_helper, $notifyhelper, $social_photo, $social_tag, $social_zebra, $social_page, $config, $db, $root_path, $php_ext, $table_prefix)
	{
		$this->template					= $template;
		$this->user						= $user;
		$this->helper					= $helper;
		$this->pg_social_helper 		= $pg_social_helper;
		$this->notify 					= $notifyhelper;
		$this->social_photo				= $social_photo;
		$this->social_tag				= $social_tag;
		$this->social_zebra				= $social_zebra;
		$this->social_page				= $social_page;
		$this->config 					= $config;
		$this->db 						= $db;	
	    $this->root_path				= $root_path;	
		$this->php_ext 					= $php_ext;
        $this->table_prefix 			= $table_prefix;
	}
	
	/**
	 * The wall
	*/
	public function getStatus($post_where, $wall_id, $lastp, $type, $order, $template)
	{
		switch($post_where)
		{
			case 'page':	
				$where = "(w.wall_id = '".$wall_id."') AND post_where = '1' AND (w.user_id = u.user_id) AND (u.user_type != '2') AND ";
			break;
			default:
				switch($type){
					case "profile":
						$where = "(w.wall_id = '".$wall_id."') AND post_where = '0' AND ";
					break;
					case "all":	
						$where = "(w.user_id = u.user_id) AND ";
					break;
				}
				$where .= "(w.user_id = u.user_id) AND (u.user_type != '2') AND ";
			break;
		}
		
		
		switch($lastp)
		{
			case 0:
				$limit = 5; 
				$orderby = "DESC"; 
			break;
			default:
				$limit = 1; 
				$orderby = "ASC";
			break;
		}
		
		switch($order)
		{
			case 'prequel':
				$order_vers = '<';
				$orderby = "DESC";
				$limit = 1;
			break;
			case 'seguel':
				$order_vers = '>';
			break;
		}
		$sql = "SELECT w.*, w.*, u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_colour
		FROM ".$this->table_prefix."pg_social_wall_post as w, ".USERS_TABLE." as u WHERE ".$where." w.user_id = u.user_id
		AND (w.post_ID ".$order_vers." '".$lastp."') GROUP BY post_ID ORDER BY w.time ".$orderby;
		$result = $this->db->sql_query_limit($sql, $limit);
		while($row = $this->db->sql_fetchrow($result))
		{	
			$this->status($post_where, $wall_id, $type, $template, $row);
			
		}
		if($template == "on")
		{
				return $this->helper->render('activity_status.html', "");
			} 
	}
	
	/**
	 * Information or Output of the Activity
	*/
	public function status($post_where, $wall_id, $type, $template, $row)
	{
		$rele = true;
		$author_action = '';
		$user_id = (int) $this->user->data['user_id'];
		if($post_where == 'page'|| $row['post_where'] == 1 && $this->social_page->user_likePages($user_id, $row['wall_id']) == $row['wall_id'] || $row['post_where'] == 0 && ($row['wall_id'] == $user_id || $row['post_privacy'] == 0 && $row['wall_id'] == $user_id || $row['post_privacy'] == 1 && $this->social_zebra->friendStatus($row['wall_id'])['status'] == 'PG_SOCIAL_FRIENDS' || $row['post_privacy'] == 2))
		{
			$share = $row['post_ID'];
			
			switch($row['post_where'])
			{
				case 1:
					$sqlpage = "SELECT * FROM ".$this->table_prefix."pg_social_pages WHERE page_id = '".$row['wall_id']."'";
					$resultpage = $this->db->sql_query($sqlpage);
					$page = $this->db->sql_fetchrow($resultpage);
					$status_title = $page['page_username'];
					$status_avatar = '<img class="avatar" src="'.generate_board_url().'/ext/pgreca/pg_social/images/transp.gif" style="background-image:url('.generate_board_url().'/ext/pgreca/pg_social/images/';
					if($page['page_avatar'] != "")
					{
						$status_avatar .= 'upload/'.$page['page_avatar']; 
					}
					else
					{
						$status_avatar .= 'page_no_avatar.jpg';
					}
					$status_avatar .= ')" />';
					$status_username = $page['page_username'];
					$status_aut_id = $page['page_id'];
					$status_profile = $this->helper->route('pages_page', array('name' => $page['page_username_clean']));
					$status_color = '';
				break;
				case 0:
					$status_title = $this->user->lang['ACTIVITY'];
					$status_username = $row['username'];
					$status_avatar = $this->pg_social_helper->social_avatar_thumb($row['user_avatar'], $row['user_avatar_type']);
					$status_aut_id = $row['user_id'];
					$status_profile = get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']);
					$status_color = "#".$row['user_colour'];
					if(($row['user_id'] != $row['wall_id']) && $type != "profile")
					{
						$sqla = "SELECT user_id, username, username_clean, user_colour FROM ".USERS_TABLE."
						WHERE user_id = '".$row['wall_id']."'";
						$resulta = $this->db->sql_query($sqla);
						$wall = $this->db->sql_fetchrow($resulta);					
						$wall_action = $this->user->lang("HAS_WRITE_IN");
					}
					else
					{
						$wall['user_id'] = '';
						$wall['username'] = '';
						$wall['user_colour'] = '';
						$wall_action = '';
					}
					switch($row['post_type'])
					{						
						case 4:
							$posts = explode("#p", $row['post_extra']);
							$sql_post = "SELECT * FROM ".TOPICS_TABLE." WHERE topic_id = '".$posts[0]."'";
							$res = $this->db->sql_query($sql_post);
							$post = $this->db->sql_fetchrow($res);
							
							if(!$post['topic_id']) $author_action = $this->user->lang("HAS_WRITED_POST_ON_CANCEL"); else $author_action = $this->user->lang("HAS_WRITED_POST_ON", '<a href="'.append_sid(generate_board_url()).'/viewtopic.php?t='.$post['topic_id'].'#p'.$posts[1].'">'.$post['topic_title'].'</a>');
							$msg = '';
							$msg_align = '';						
						break;						
					}	
				break;
			}
			switch($row['post_type'])
			{
				case 1:
						$author_action = $this->user->lang("HAS_UPLOADED_AVATAR");
						$photo = $this->photo($row['post_extra']);
						$msg = $photo['msg'];
						$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
					break;
					case 2:
						$author_action = $this->user->lang("HAS_UPLOADED_COVER");
						$photo = $this->photo($row['post_extra']);
						$msg = $photo['msg'];
						$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
					break;
				case '3':
				default:
					if($row['post_parent'] != 0)
					{
						$share = $row['post_parent'];
						if($this->status_where($row['post_parent']) == 1)
						{
							$sqlpar = "p.page_id as user_id, p.page_username as username, p.page_username_clean as username_clean, p.page_avatar as user_avatar ";
							$sqlfro = $this->table_prefix."pg_social_pages as p";
							$sqlwhe = "p.page_id";
						}
						else
						{
							$sqlpar = "u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_colour ";
							$sqlfro = USERS_TABLE." as u ";
							$sqlwhe = "u.user_id";
						}
						$sql = "SELECT w.*, ".$sqlpar."
						FROM ".$this->table_prefix."pg_social_wall_post as w, ".$sqlfro."
						WHERE w.post_ID = '".$row['post_parent']."' AND ".$sqlwhe." = w.user_id
						GROUP BY post_ID";
						$post_parent = $this->db->sql_query($sql);
						$parent = $this->db->sql_fetchrow($post_parent);
						$parent['url'] = get_username_string('profile', $parent['user_id'], $parent['username'], $parent['user_colour']);
						if(isset($parent['post_ID']))
						{
							$author_action = $this->user->lang("HAS_SHARED_STATUS", '<a href="'.append_sid($this->helper->route("status_page", array("id" => $parent['post_ID']))).'">'.$this->user->lang('STATUS').'</a>');
							$msg = generate_text_for_display($row['message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $flags);
							$msg .= $this->pg_social_helper->extraText($row['message']);
							$msg .= '<div class="post_parent_cont">';
							if($parent['post_extra'] != "")
							{
								$photo = $this->photo($parent['post_extra']);
								$msg .= $photo['msg'];
								$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
							}
							else
							{
								$allow_bbcode = $this->config['pg_social_bbcode'];
								$allow_urls = $this->config['pg_social_url'];
								$allow_smilies = $this->config['pg_social_smilies'];
								$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);
			
								$msg .= generate_text_for_display($parent['message'], $parent['bbcode_uid'], $parent['bbcode_bitfield'], $flags);
								$msg .= $this->pg_social_helper->extraText($parent['message']);
							}	
							$msg .= '<div class="post_parent_info">';
							$msg .= '<div class="post_parent_author"><a href="'.$parent['url'].'">'.$parent['username'].'</a></div>';
							$msg .= '<div class="post_parent_date">'.$this->pg_social_helper->time_ago($parent['time']).'</div>';
							$msg .= '</div>';
							$msg .= '</div>';
						}
					}
					else
					{
						if($row['post_extra'] != "" && $row['post_type'] != 4)
						{
							$photo = $this->photo($row['post_extra']);
							$msg = $photo['msg'];
							$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
						}
						else
						{
							$allow_bbcode = $this->config['pg_social_bbcode'];
							$allow_urls = $this->config['pg_social_url'];
							$allow_smilies = $this->config['pg_social_smilies'];
							$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);
		
							$msg = generate_text_for_display($row['message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $flags);
							$msg = $this->social_tag->showTag($msg);	
							$msg .= $this->pg_social_helper->extraText($row['message']);
						}		
					}							
					$msg_align = '';
				break;					
			}
			$comment = "<span>".$this->pg_social_helper->countAction("comments", $row['post_ID'])."</span> ";
			if($this->pg_social_helper->countAction("comments", $row['post_ID']) == 0 || $this->pg_social_helper->countAction("comments", $row['post_ID']) > 1)
			{
				$comment .= $this->user->lang('COMMENTS');
			}
			else
			{
				$comment .= $this->user->lang('COMMENT');
			}
		
			if($row['wall_id'] == $user_id || $user_id == $row['user_id']) $action = "yes";
			
			$this->template->assign_block_vars('post_status', array(
				'USER_AVATAR'				=> $this->pg_social_helper->social_avatar_thumb($this->user->data['user_avatar'], $this->user->data['user_avatar_type']),				
				"POST_STATUS_ID"            => $row['post_ID'],
				"AUTHOR_ACTION"				=> $author_action,
				"AUTHOR_PROFILE"			=> $status_profile,
				"AUTHOR_ID"					=> $status_aut_id,
				"AUTHOR_USERNAME"			=> $status_username,
				"AUTHOR_AVATAR"				=> $status_avatar,
				"AUTHOR_COLOUR"				=> $status_color,
				"WALL_ACTION"				=> $wall_action,
				"WALL_PROFILE"				=> get_username_string('profile', $wall['user_id'], $wall['username'], $wall['user_colour']),	
				"WALL_ID"					=> $row['wall_id'],	
				"WALL_USERNAME"				=> $wall['username'],
				"WALL_COLOUR"				=> "#".$wall['user_colour'],
				"POST_TYPE"					=> $row['post_type'],
				"POST_URL"					=> $this->helper->route("status_page", array("id" => $row['post_ID'])),
				"POST_DATE"					=> date('c', $row['time']),
				"POST_DATE_AGO"				=> $this->pg_social_helper->time_ago($row['time']),
				"MESSAGE"					=> htmlspecialchars_decode($msg),
				"MESSAGE_ALIGN"				=> $msg_align,
				"POST_PRIVACY"				=> $this->user->lang($this->pg_social_helper->social_privacy($row['post_privacy'])),
				"ACTION"					=> $action,
				"LIKE"						=> $this->pg_social_helper->countAction("like", $row['post_ID']),
				"IFLIKE"					=> $this->pg_social_helper->countAction("iflike", $row['post_ID']),
				"COMMENT"					=> $comment,
				"SHARE"						=> $share		
			)); 	
			$this->getComments($row['post_ID'], $row['post_type'], false);
		}
		if($template == "half")
		{
			return $this->helper->render('status.html', 'Stai vedendo uno stato di'. $status_username);
		}
		$this->db->sql_freeresult($result);
	}
		
	/**
	 * Add new activity on wall
	*/
	public function addStatus($post_where, $wall_id, $text, $privacy, $type = 0, $extra = NULL)
	{
		switch($post_where)
		{
			case 'page':
				$post_where = 1;
			break;	
			default:
				$post_where = 0;
			break;
		}
		
		$user_id = (int) $this->user->data['user_id'];
		$time = time();
		
		$allow_bbcode = $this->config['pg_social_bbcode'];
		$allow_urls = $this->config['pg_social_url'];
		$allow_smilies = $this->config['pg_social_smilies'];
		$text = urldecode($text);
		$time = time();
		if(!$extra) $extra = "";
		generate_text_for_storage($text, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);
			
		$text = str_replace('&amp;nbsp;', ' ', $text);
		$sql_arr = array(
			'post_parent'		=> 0,
			'post_where'		=> $post_where,
			'wall_id'			=> $wall_id,
			'user_id'			=> $user_id,
			'message'			=> $text,
			'time'				=> $time,
			'post_privacy'		=> $privacy,
			'post_type'			=> $type,
			'post_extra'		=> $extra,
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid,
			'tagged_user'		=> ''
		);
		
		if($text != "" || $extra != "") $sql = "INSERT INTO " . $this->table_prefix . 'pg_social_wall_post' . $this->db->sql_build_array('INSERT', $sql_arr);
		if($this->db->sql_query($sql))
		{	
			$last_status = "SELECT post_ID FROM ".$this->table_prefix."pg_social_wall_post WHERE time = '".$time."' AND user_id = '".$user_id."' AND wall_id = '".$wall_id."' ORDER BY time DESC LIMIT 0, 1";
			$last = $this->db->sql_query($last_status);
			$row = $this->db->sql_fetchrow();	
			if($post_where == 0 && $wall_id == $user_id && $this->user->data['user_signature_replace'] && $privacy != 0 && $type != 4 && !$this->pg_social_helper->extraText($text))
			{
				$new_sign = $text."<br /><a class=\"profile_signature_status\" href=\"".$this->helper->route("status_page", array("id" => $row['post_ID']))."\">#status</a></a>";
				//generate_text_for_storage($new_sign, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);
				
				$sql = "UPDATE ".USERS_TABLE." SET user_sig = '".$new_sign."' WHERE user_id = '".$this->user->data['user_id']."'";
				$this->db->sql_query($sql);
			}
			if($post_where == 0 && $wall_id != $user_id) $this->notify->notify('add_status', $row['post_ID'], $text, (int) $wall_id, (int) $user_id, 'NOTIFICATION_SOCIAL_STATUS_ADD');		
			$this->social_tag->addTag($row['post_ID'], $text);
			$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, "STATUS_NEW", "<a href='".$this->helper->route("status_page", array("id" => $row['post_ID']))."'>#".$row['post_ID']."</a>");
		
		}
		
		$this->template->assign_vars(array(
			"ACTION"	=> $sql.'',
		));
		if($type != 4) return $this->helper->render('activity_status_action.html', $this->user->lang['ACTIVITY']);	
	}
	
	/**
	 * Delete the activity from the wall 
	*/
	public function deleteStatus($post)
	{
		$sql_status = "DELETE FROM ".$this->table_prefix."pg_social_wall_post WHERE ".$this->db->sql_in_set('post_ID', array($post));
		$sql_comment = "DELETE FROM ".$this->table_prefix."pg_social_wall_comment WHERE ".$this->db->sql_in_set('post_ID', array($post));
		$sql_like = "DELETE FROM ".$this->table_prefix."pg_social_wall_like WHERE ".$this->db->sql_in_set('post_ID', array($post));
		
		$this->db->sql_query($sql_status);
		$this->db->sql_query($sql_comment);
		$this->db->sql_query($sql_like);
		
		$this->template->assign_vars(array(
			"ACTION"	=> "delete",
		));
		$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, "STATUS_REMOVE", "");
		return $this->helper->render('activity_status_action.html', "");
	}
	
	/**
	 * Share the activity on the wall
	*/
	public function shareStatus($post)
	{
		$time = time();
		$sql_arr = array(
			'post_parent'		=> $post,
			'wall_id'			=> $this->user->data['user_id'],
			'user_id'			=> $this->user->data['user_id'],
			'message'			=> '',
			'time'				=> $time,
			'post_privacy'		=> 1,
			'post_type'			=> 0,
			'post_extra'		=> '',
			'bbcode_bitfield'	=> '',
			'bbcode_uid'		=> '',
			'tagged_user'		=> ''
		);
		
		$sql = "INSERT INTO " . $this->table_prefix . 'pg_social_wall_post' . $this->db->sql_build_array('INSERT', $sql_arr);
		if($this->db->sql_query($sql))
		
		$last_status = "SELECT post_ID FROM ".$this->table_prefix."pg_social_wall_post WHERE time = '".$time."' AND user_id = '".$this->user->data['user_id']."' AND wall_id = '".$this->user->data['user_id']."' ORDER BY time DESC LIMIT 0, 1";
		$last = $this->db->sql_query($last_status);
		$row = $this->db->sql_fetchrow();	
		$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, "STATUS_SHARE", "<a href='".$this->helper->route("status_page", array("id" => $row['post_ID']))."'>#".$row['post_ID']."</a> -> <a href='".$this->helper->route("status_page", array("id" => $post))."'>#".$post."</a>");
		$this->template->assign_vars(array(
			"ACTION"	=> $sql,
		));
		return $this->helper->render('activity_status_action.html', "");
	}
	
	/** 
	 * Like / Dislike
	 * Count Like on the activity
	*/
	public function likeAction($post)
	{
		$post_info = "SELECT user_id, wall_id FROM ".$this->table_prefix."pg_social_wall_post WHERE post_ID = '".$post."'";
		$res = $this->db->sql_query($post_info);
		$post_info = $this->db->sql_fetchrow($res);
		
		$user_id = (int) $this->user->data['user_id'];
		$sql = "SELECT post_like_ID FROM ".$this->table_prefix."pg_social_wall_like
		WHERE post_ID = '".$post."' AND user_id = '".$user_id."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		if($row['post_like_ID'] != "")
		{
			$sql = "DELETE FROM ".$this->table_prefix."pg_social_wall_like WHERE post_ID = '".$post."' AND user_id = '".$user_id."'";
			$action = "dislike";
			if($post_info['user_id'] != $user_id || $post_info['wall_id'] == $user_id) $this->notify->notify('remove_like', $post, '', (int) $post_info['user_id'], (int) $user_id, 'NOTIFICATION_SOCIAL_LIKE_ADD');		
			$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, "DISLIKE_NEW", "<a href='".$this->helper->route("status_page", array("id" => $post))."'>#".$post."</a>");
		}
		else
		{
			$sql_arr = array(
				'post_ID'			=> $post,
				'user_id'			=> $user_id,
				'post_like_time'	=> time(),
			);
			$sql = "INSERT INTO ".$this->table_prefix.'pg_social_wall_like'.$this->db->sql_build_array('INSERT', $sql_arr);
			$action = "like";
			if($post_info['user_id'] != $user_id) $this->notify->notify('add_like', $post, '', (int) $post_info['user_id'], (int) $user_id, 'NOTIFICATION_SOCIAL_LIKE_ADD');		
			$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, "LIKE_NEW", "<a href='".$this->helper->route("status_page", array("id" => $post))."'>#".$post."</a>");
		}
		if($this->db->sql_query($sql)) $this->db->sql_freeresult($result); 
		
		$this->template->assign_vars(array(
			"ACTION"	=> $action,
			"LIKE_TOT"	=> $this->pg_social_helper->countAction('like', $post),
		));
		return $this->helper->render('activity_status_action.html', '');
	}
	
	/**
	 * The comments of activity
	*/
	public function getComments($post, $type, $template = true)
	{
		$user_id = (int) $this->user->data['user_id'];
						
		$sql = "SELECT *
		FROM ".$this->table_prefix."pg_social_wall_comment
		WHERE post_ID = '".$post."'
		ORDER BY time DESC";
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result))
		{
			$allow_bbcode = false; //$this->config['pg_social_bbcode'];
			$allow_urls = false; //$this->config['pg_social_url'];
			$allow_smilies = false; //$this->config['pg_social_smilies'];
			$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);
			
			$sql_use = "SELECT user_id, username, username_clean, user_colour, user_avatar, user_avatar_type FROM ".USERS_TABLE."
			WHERE user_id = '".$row['user_id']."'";
			$resulta = $this->db->sql_query($sql_use);
			$wall = $this->db->sql_fetchrow($resulta);
			if($row['user_id'] == $this->user->data['user_id']) $comment_action = true; else $comment_action = false;
			$this->template->assign_block_vars('post_comment', array(
				"COMMENT_ID"				=> $row['post_comment_ID'],
				"COMMENT_ACTION"			=> $comment_action,
				"AUTHOR_PROFILE"			=> get_username_string('profile', $wall['user_id'], $wall['username'], $wall['user_colour']),	
				"AUTHOR_ID"					=> $wall['user_id'],
				"AUTHOR_USERNAME"			=> $wall['username'],
				"AUTHOR_AVATAR"				=> $this->pg_social_helper->social_avatar_thumb($wall['user_avatar'], $wall['user_avatar_type']),
				"AUTHOR_COLOUR"				=> "#".$wall['user_colour'],
				'COMMENT_TEXT'				=> $this->pg_social_helper->social_smilies(generate_text_for_display($row['message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $flags)),			
				'COMMENT_TIME'				=> date('c', $row['time']),
			));		
		}
		//$this->db->sql_freeresult($result);	
		if($template) return $this->helper->render('activity_comment.html', '');				
	}
	
	/**
	 * Add new comment on activity
	*/
	public function addComment($post, $comment)
	{
		$post_info = "SELECT user_id, wall_id FROM ".$this->table_prefix."pg_social_wall_post WHERE post_ID = '".$post."'";
		$res = $this->db->sql_query($post_info);
		$post_info = $this->db->sql_fetchrow($res);
		
		$user_id = (int) $this->user->data['user_id'];
		$time = time();
		
		$allow_bbcode = false; //$this->config['pg_social_bbcode'];
		$allow_urls = false; //$this->config['pg_social_url'];
		$allow_smilies = false; //$this->config['pg_social_smilies'];
		
		$comment = urldecode($comment);
		generate_text_for_storage($text, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);
			
		$text = str_replace('&amp;nbsp;', ' ', $text);
		generate_text_for_storage($comment, $uid, $bitfield, $flags, $allow_bbcode, true, $allow_smilies);
		
		$sql_arr = array(
			'post_ID'	=> $post,
			'user_id'			=> $user_id,
			'time'				=> time(),
			'message'			=> $comment,
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid
		);
		$sql = "INSERT INTO " . $this->table_prefix . 'pg_social_wall_comment' . $this->db->sql_build_array('INSERT', $sql_arr);
		$this->db->sql_query($sql);
		if($post_info['wall_id'] != $user_id) $this->notify->notify('add_comment', $post, '', (int) $post_info['wall_id'], (int) $user_id, 'NOTIFICATION_SOCIAL_COMMENT_ADD');		
			
		$this->template->assign_vars(array(
			"ACTION"	=> "",
		));
		$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, "COMMENT_NEW", "<a href='".$this->helper->route("status_page", array("id" => $post))."'>#".$post."</a>");
		return $this->helper->render('activity_status_action.html', '');
	}

	/**
	 * Remove the comment on activity
	*/
	public function removeComment($comment)
	{
		$sql = "DELETE FROM ".$this->table_prefix."pg_social_wall_comment WHERE post_comment_ID = '".$comment."' AND user_id = '".$this->user->data['user_id']."'";
		$this->db->sql_query($sql);		
		$this->template->assign_vars(array(
			"ACTION"	=> $sql,
		));
		$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, "COMMENT_REMOVE", "");
		return $this->helper->render('activity_status_action.html', '');
	}
		
	/**
	 * Array information photo
	*/
	public function photo($photo)
	{
		$img = $this->social_photo->getPhoto($photo);
		
		return array(
			'img' => '<img src="'.$img['photo_file'].'" class="photo_popup" data-photo="'.$photo.'" />',
			'msg' => htmlspecialchars_decode($img['photo_desc']),
		);
	}
	
	/**
	 * Profile or page is published the activity?
	*/
	public function status_where($status)
	{
		$sql = "SELECT post_where FROM ".$this->table_prefix."pg_social_wall_post WHERE post_ID = '".$status."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		
		return $row['post_where'];
	}
}