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

	/** @var \pgreca\pgsocial\controller\helper */
	protected $pg_social_helper;

	/** @var \pgreca\pgsocial\social\social_photo */
	protected $social_photo;

	/** @var \pgreca\pgsocial\social\social_tag */
	protected $social_tag;

	/** @var \pgreca\pgsocial\social\social_zebra */
	protected $social_zebra;

	/** @var \pgreca\pgsocial\social\social_page */
	protected $social_page;

	/* @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\textformatter\parser_interface */
	protected $parser;

	/* @var string phpBB root path */
	protected $root_path;

	/** @var string */
	protected $pgsocial_table_wallpost;

	/** @var string */
	protected $pgsocial_table_wallpostlike;

	/** @var string */
	protected $pgsocial_table_wallpostcomment;

	/** @var string */
	protected $pgsocial_table_pages;

	/** @var string */
	protected $pgsocial_table_photo;

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
	* @param \phpbb\textformatter\parser_interface $parser_interface
	*/

	public function __construct(
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\pgreca\pgsocial\controller\helper $pg_social_helper,
		$notifyhelper,
		\pgreca\pgsocial\social\social_photo $social_photo,
		\pgreca\pgsocial\social\social_tag $social_tag,
		\pgreca\pgsocial\social\social_zebra $social_zebra,
		\pgreca\pgsocial\social\social_page $social_page,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\textformatter\parser_interface $parser = null,
		$root_path,
		$pgsocial_table_wallpost,
		$pgsocial_table_wallpostlike,
		$pgsocial_table_wallpostcomment,
		$pgsocial_table_pages,
		$pgsocial_table_photo
	)
	{
		$this->template 									= $template;
		$this->user 											= $user;
		$this->helper											= $helper;
		$this->pg_social_helper 					= $pg_social_helper;
		$this->notify											= $notifyhelper;
		$this->social_photo								= $social_photo;
		$this->social_tag									= $social_tag;
		$this->social_zebra								= $social_zebra;
		$this->social_page								= $social_page;
		$this->config 										= $config;
		$this->db 												= $db;
		$this->parser											= $parser;
		$this->root_path 									= $root_path;
		$this->pgsocial_wallpost 					= $pgsocial_table_wallpost;
		$this->pgsocial_wallpostlike 			= $pgsocial_table_wallpostlike;
		$this->pgsocial_wallpostcomment 	= $pgsocial_table_wallpostcomment;
		$this->pgsocial_pages							= $pgsocial_table_pages;
		$this->pgsocial_photo							= $pgsocial_table_photo;
		$this->pg_social_path							= generate_board_url().'/ext/pgreca/pgsocial';
	}

	/**
	* The Wall
	*
	* @param int $post_where
	* @param int $wall_id
	* @param int $lastp
	* @param string $type
	* @param int $select
	* @param string $order
	* @param bool $template
	* @return function status
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function get_status($post_where, $wall_id, $lastp, $type, $select, $order, $template)
	{
		$where = '';
		switch ($post_where)
		{
			case 'page':
				$where = "(w.wall_id = '".$wall_id."') AND post_where = '1' AND (w.user_id = u.user_id) AND (u.user_type != '2') AND ";
			break;
			default:
				switch ($type) {
					case 'profile':
						$where = "(w.wall_id = '".$wall_id."') AND post_where = '0' AND ";
					break;
					case 'all':
						$where = "(w.user_id = u.user_id) AND ";
					break;
				}
				$where .= "(w.user_id = u.user_id) AND (u.user_type != '2') AND ";
			break;
		}
		if ($select != 0 && $type == 'profile')
		{
			$where .= 'post_type = "'.$select.'" AND ';
		}
		elseif ($type != 'all')
		{
			$where .= 'post_type IN ("0", "1", "2", "3", "4") AND ';
		}

		switch ($lastp)
		{
			case 0:
				$limit = 20;
				$orderby = 'DESC';
			break;
			default:
				$limit = 10;
				$orderby = 'ASC';
			break;
		}

		switch ($order)
		{
			case 'prequel':
				$order_vers = '<';
				$orderby = 'DESC';
				$limit = 1;
			break;
			case 'seguel':
				$order_vers = '>';
			break;
		}
		$sql = "SELECT w.*, w.*, u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
		FROM ".$this->pgsocial_wallpost." as w, ".USERS_TABLE." as u
		WHERE ".$where." w.user_id = u.user_id
			AND (w.post_ID ".$order_vers." '".$lastp."')
		GROUP BY post_ID ORDER BY w.time ".$orderby;
		$result = $this->db->sql_query_limit($sql, $limit);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->status($post_where, $wall_id, $type, $template, $row, $select);
			if ($order == 'prequel')
			{
				$lastp = $lastp - 1;
				$this->get_status($post_where, $wall_id, $lastp, $type, $select, $order, $template);
			}
		}
		$this->db->sql_freeresult($result);
		if ($template)
		{
			return $this->helper->render('activity_status.html', '');
		}
	}

	/**
	* Information or Output of the Activity
	*
	* @param int $post_where
	* @param int $wall_id
	* @param string $type
	* @param bool $template
	* @param array $row
	* @param int $order
	* @return array
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function status($post_where, $wall_id, $type, $template, $row, $select = 0)
	{
		$block_vars = '';
		if ($select == 0)
		{
			$block_vars = 'post_status';
		}
		$rele = $actionprivacy = true;
		$author_action = '';
		$action = false;
		$share = $row['post_ID'];
		$msg = '';
		$msg_align = '';
		$wall['user_id'] = $wall['username'] = $wall['user_colour'] = '';
		$wall_action = '';
		$allow_bbcode = $this->config['pg_social_bbcode'];
		$allow_urls = $this->config['pg_social_url'];
		$allow_smilies = $this->config['pg_social_smilies'];
		$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);
		$status_privacy = $row['post_privacy'];
		switch ($row['post_where'])
		{
			case 1:
				$sqlpage = "SELECT *, '' as user_colour FROM ".$this->pgsocial_pages." WHERE page_id = '".$row['wall_id']."'";
				$resultpage = $this->db->sql_query($sqlpage);
				$page = $this->db->sql_fetchrow($resultpage);
				$status_title = $page['page_username'];
				$status_avatar = '<img class="avatar" src="'.$this->pg_social_path.'/images/transp.gif" style="background-image:url('.$this->pg_social_path.'/images/';
				if ($page['page_avatar'] != '')
				{
					$status_avatar .= 'upload/'.$page['page_avatar'];
				} else
				{
					$status_avatar .= 'page_no_avatar.jpg';
				}
				$status_avatar .= ')" />';
				$status_username = $page['page_username'];
				$status_aut_id = $page['page_id'];
				$status_profile = append_sid($this->helper->route('pages_page'), 'u='.$page['page_username_clean']);
				$status_color = '';
			break;
			case 0:
				$status_title = $this->user->lang['ACTIVITY'];
				$status_username = $row['username'];
				$status_avatar = $this->pg_social_helper->social_avatar_thumb($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']);
				$status_aut_id = $row['user_id'];
				$status_profile = get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']);
				$status_color = '#'.$row['user_colour'];
				if (($row['user_id'] != $row['wall_id']) && $type != 'profile')
				{
					$sqla = "SELECT user_id, username, username_clean, user_colour FROM ".USERS_TABLE."
					WHERE user_id = '".$row['wall_id']."'";
					$resulta = $this->db->sql_query($sqla);
					$wall = $this->db->sql_fetchrow($resulta);
					$wall_action = $this->user->lang('HAS_WRITE_IN');
					$this->db->sql_freeresult($resulta);
				} else
				{
					$wall['user_id'] = $wall['username'] = '';
				}
				switch ($row['post_type'])
				{
					case 4:
						$posts = explode('#p', $row['post_extra']);
						$sql_post = "SELECT topic_id, topic_title FROM ".TOPICS_TABLE." WHERE topic_id = '".$posts[0]."'";
						$res = $this->db->sql_query($sql_post);
						$post = $this->db->sql_fetchrow($res);
						if (!$post['topic_id'])
						{
							$author_action = $this->user->lang('HAS_WRITED_POST_ON_CANCEL');
						} else
						{
							$author_action = $this->user->lang('HAS_WRITED_POST_ON', '<a href="'.append_sid(generate_board_url()).'/viewtopic.php?t='.$post['topic_id'].'#p'.$posts[1].'">'.$post['topic_title'].'</a>');
						}
						$this->db->sql_freeresult($res);
					break;
				}
			break;
		}
		$msg .= $this->social_tag->show_tag($msg);
		$msg .= $this->pg_social_helper->extra_text($row['message']);
		$msg .= $this->pg_social_helper->noextra(generate_text_for_display($row['message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']));
		switch ($row['post_type'])
		{
			case 0:
				if ($row['post_parent'] != 0)
				{
					$share = $row['post_parent'];
					if ($this->status_where($row['post_parent']) == 0)
					{
						$sqlpar = 'u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour';
						$sqlfro = USERS_TABLE.' as u ';
						$sqlwhe = 'u.user_id = w.user_id';
					}
					elseif ($this->status_where($row['post_parent']) == 1)
					{
						$sqlpar = "p.page_id as user_id, p.page_username as username, p.page_username_clean as username_clean, p.page_avatar as user_avatar, '' as user_colour";
						$sqlfro = $this->pgsocial_pages.' as p';
						$sqlwhe = 'p.page_id = w.wall_id';
					}
					$sql = "SELECT w.*, ".$sqlpar."
					FROM ".$this->pgsocial_wallpost." as w, ".$sqlfro."
					WHERE w.post_ID = '".$row['post_parent']."' AND ".$sqlwhe."
					GROUP BY post_ID";
					$post_parent = $this->db->sql_query($sql);
					$parent = $this->db->sql_fetchrow($post_parent);
					$this->db->sql_freeresult($post_parent);
					if ($this->status_where($row['post_parent']) == 0)
					{
						$parent['url'] = get_username_string('profile', $parent['user_id'], $parent['username'], $parent['user_colour']);
					}
					elseif ($this->status_where($row['post_parent']) == 1)
					{
						$parent['url'] = append_sid($this->helper->route('pages_page'), 'u='.$parent['username_clean']);
					}
					if ($parent['post_ID'])
					{
						$sauthor_action = '';
						$author_action = $this->user->lang('HAS_SHARED_STATUS', '<a href="'.append_sid($this->helper->route('status_page', array('id' => $parent['post_ID']))).'">'.$this->user->lang('STATUS').'</a>');
						if ($parent['post_extra'] != '')
						{
							$submsg = '';
							switch ($parent['post_type'])
							{
								case 5:
									$sauthor_action .= ' '.$this->user->lang('HAS_WRITED_ARTICLE');
								break;
								case 4:
									$posts = explode('#p', $parent['post_extra']);
									$sql_post = "SELECT * FROM ".TOPICS_TABLE." WHERE topic_id = '".$posts[0]."'";
									$res = $this->db->sql_query($sql_post);
									$post = $this->db->sql_fetchrow($res);
									$this->db->sql_freeresult($res);
									if (!$post['topic_id'])
									{
										$sauthor_action = ' '.$this->user->lang('HAS_WRITED_POST_ON_CANCEL');
									} else
									{
										$sauthor_action = " ".$this->user->lang("HAS_WRITED_POST_ON", '<a href="'.append_sid(generate_board_url()).'/viewtopic.php?t='.$post['topic_id'].'#p'.$posts[1].'">'.$post['topic_title'].'</a>');
									}
									$msg_align = '';
								break;
								default :
									$photo = $this->photo($parent['post_extra']);
									//$msg .= $photo['msg'];
									$submsg .= '<div class="status_photos">'.$photo['img'].'</div>';
									if ($photo['gallery_id'] == '0')
									{
										$sauthor_action = ' '.$this->user->lang('HAS_UPLOADED_AVATAR');
									} else
									{
										if ($photo['gallery_privacy'] == 0 && $this->user->data['user_id'])
										{
											$actionprivacy = false;
											$status_privacy = $photo['gallery_privacy'];
											$albumlink = '<a href="'.$photo['gallery_url'].'">'.$photo['gallery_name'].'</a>';
											$sauthor_action = ' '.$this->user->lang('HAS_PUBLISHED_PHOTO_ALBUM', $albumlink);
										}
									}
								break;
							}
						}
						$msg .= '<div class="post_parent_cont">';
						$msg .= '<div class="post_parent_info">';
						$msg .= '<div class="post_parent_author"><a href="'.$parent['url'].'">'.$parent['username'].'</a>'.$sauthor_action.'</div>';
						$msg .= '<div class="post_parent_date">'.$this->pg_social_helper->time_ago($parent['time']).'</div>';
						$msg .= '<div class="post_parent_cont">'.$this->pg_social_helper->noextra(generate_text_for_display($parent['message'], $parent['bbcode_uid'], $parent['bbcode_bitfield'], $flags)).'</div>';
						$msg .= $this->pg_social_helper->extra_text($parent['message']);
						$msg .= '</div>';
						$msg .= '</div>';
						if ($parent['post_extra'] != '')
						{
							switch ($parent['post_type'])
							{
								case 5:
									$sauthor_action = '';
								break;
								case 4:
									$posts = explode('#p', $parent['post_extra']);
									$sql_post = "SELECT * FROM ".TOPICS_TABLE." WHERE topic_id = '".$posts[0]."'";
									$res = $this->db->sql_query($sql_post);
									$post = $this->db->sql_fetchrow($res);
									$this->db->sql_freeresult($res);
									if (!$post['topic_id'])
									{
										$sauthor_action .= ' '.$this->user->lang('HAS_WRITED_POST_ON_CANCEL');
									} else
									{
										$sauthor_action .= " ".$this->user->lang("HAS_WRITED_POST_ON", '<a href="'.append_sid(generate_board_url()).'/viewtopic.php?t='.$post['topic_id'].'#p'.$posts[1].'">'.$post['topic_title'].'</a>');
									}
									$msg_align = '';
								break;
								case 3:
									if ($row['post_extra'] != '')
									{
										$sauthor_action .= ' '.$this->user->lang('HAS_PUBLISHED_PHOTO');
									}
								break;
								case 2:
									$sauthor_action .= ' '.$this->user->lang('HAS_UPLOADED_COVER');
								break;
								case 1:
									$sauthor_action .= ' '.$this->user->lang('HAS_UPLOADED_AVATAR');
								break;
							}
							if ($parent['post_type'] == '0' || $parent['post_type'] == '1' || $parent['post_type'] == '2' || $parent['post_type'] == '3')
							{
								$photo = $this->photo($parent['post_extra']);
								//$msg .= $photo['msg'];
								$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
							}
						}
					}
				}
				if(!$msg) $msg .= generate_text_for_display($row['message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']);
			break;
			case 1:
				$photo = $this->photo($row['post_extra']);
				if ($photo['gallery_id'] == '0')
				{
					$author_action = $this->user->lang('HAS_UPLOADED_AVATAR');
				} else
				{
					$actionprivacy = false;
					$status_privacy = $photo['gallery_privacy'];
					$album = '<a href="'.$photo['gallery_url'].'">'.$photo['gallery_name'].'</a>';
					$author_action = $this->user->lang("HAS_PUBLISHED_PHOTO_ALBUM", $album);
				}
				$msg .= $photo['msg'];
				$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
			break;
			case 2:
				$author_action = $this->user->lang('HAS_UPLOADED_COVER');
				$photo = $this->photo($row['post_extra']);
				$msg = $photo['msg'];
				$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
			break;
			case 3:
				if ($row['post_extra'] != '')
				{
					$photo = $this->photo($row['post_extra']);
					if ($photo['album'])
					{
						$actionprivacy = false;
						$status_privacy = $photo['gallery_privacy'];
						$albumlink = '<a href="'.$photo['gallery_url'].'">'.$photo['gallery_name'].'</a>';
						$author_action .= ' '.$this->user->lang('HAS_PUBLISHED_PHOTO_ALBUM', $albumlink);
					} else
					{
						$author_action .= ' '.$this->user->lang('HAS_PUBLISHED_PHOTO');
					}
					$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
				}
			break;
			default:
				$activity = $this->pg_social_helper->pg_status_type($row['post_type'], $row['post_extra'], $msg, $author_action, $type, $block_vars);
				$block_vars = $activity['block_vars'];
				$msg .= $activity['msg'];
			break;
		}
		$comment = '<span>'.$this->pg_social_helper->count_action('comments', $row['post_ID']).'</span> ';
		if ($this->pg_social_helper->count_action('comments', $row['post_ID']) == 0 || $this->pg_social_helper->count_action('comments', $row['post_ID']) > 1)
		{
			$comment .= $this->user->lang('COMMENT', 2);
		} else
		{
			$comment .= $this->user->lang('COMMENT', 1);
		}
		$likes = '<span>'.$this->pg_social_helper->count_action('like', $row['post_ID']).'</span> ';
		if ($this->pg_social_helper->count_action('like', $row['post_ID']) == 0 | $this->pg_social_helper->count_action('like', $row['post_ID']) > 1)
		{
			$likes .= $this->user->lang('LIKE', 1);
		} else
		{
			$likes .= $this->user->lang('LIKE', 1);
		}
		$rele = false;
		if (
			($status_privacy == 0 && $row['wall_id'] == $this->user->data['user_id']) ||
			($status_privacy == 1 && ($this->social_zebra->friend_status($row['wall_id'])['status'] == 'PG_SOCIAL_FRIENDS' || $this->user->data['user_id'] == $row['wall_id'])) ||
			(($status_privacy == 2 && $this->social_page->user_like_pages($this->user->data['user_id'], $row['wall_id']) && $type == 'all') || ($status_privacy == 2 && $type == 'page') || ($status_privacy == 2 && $row['post_where'] == 0)))
		{
			$rele = true;
		}

		//if(!$msg) $rele = false;
		if ($rele)
		{
			if ($row['wall_id'] == $this->user->data['user_id'] || $this->user->data['user_id'] == $row['user_id']) $action = true;
			$this->template->assign_block_vars($block_vars, array(
				'POST_STATUS_ID'            => $row['post_ID'],
				'AUTHOR_ACTION'							=> $author_action,
				'AUTHOR_PROFILE'						=> $status_profile,
				'AUTHOR_ID'									=> $status_aut_id,
				'AUTHOR_USERNAME'						=> $status_username,
				'AUTHOR_AVATAR'							=> $status_avatar,
				'AUTHOR_COLOUR'							=> $status_color,
				'WALL_ACTION'								=> $wall_action,
				'WALL_PROFILE'							=> get_username_string('profile', $wall['user_id'], $wall['username'], $wall['user_colour']),
				'WALL_ID'										=> $row['wall_id'],
				'WALL_USERNAME'							=> $wall['username'],
				'WALL_COLOUR'								=> '#'.$wall['user_colour'],
				'POST_TYPE'									=> $row['post_type'],
				'POST_URL'									=> $this->helper->route('status_page', array('id' => $row['post_ID'])),
				'POST_DATE'									=> $row['time'],
				'POST_DATE_AGO'							=> $this->pg_social_helper->time_ago($row['time']),
				'MESSAGE'										=> $msg,
				'MESSAGE_ALIGN'							=> $msg_align,
				'POST_PRIVACY'							=> $status_privacy,
				'POST_PRIVACYACTION'				=> $actionprivacy,
				'ACTION'										=> $action,
				'LIKE'											=> $likes,
				'IFLIKE'										=> $this->pg_social_helper->count_action('iflike', $row['post_ID']),
				'COMMENT'										=> $comment,
				'SHARE'											=> $share
			));

			if ($template == 'half')
			{
				
				return $this->helper->render('status.html', $this->user->lang('YOU_SEE_ACTIVITY', $status_username));
			}
		}
	}


	/**
	* Action on activity
	*
	* @param int $post
	* @param int $action
	* @param int $value
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function status_action($post, $action, $value)
	{
		$sql = 'SELECT post_ID, user_id, wall_id FROM '.$this->pgsocial_wallpost.' WHERE '.$this->db->sql_build_array('SELECT', array('post_ID' => $post));
		$result = $this->db->sql_query($sql);
		$post = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		switch ($action)
		{
			case 'privacy':
				if ($post['wall_id'] == $this->user->data['user_id'])
				{
					$sql_arr = array(
						'post_privacy'	=> (int) $value,
					);
				}
			break;
		}
		$sql = 'UPDATE '.$this->pgsocial_wallpost.' SET '.$this->db->sql_build_array('UPDATE', $sql_arr).' WHERE '.$this->db->sql_build_array('UPDATE', array('post_ID' => $post['post_ID']));
		$this->db->sql_query($sql);
		$this->template->assign_vars(array(
			'ACTION'	=> '',
		));
		return $this->helper->render('activity_status_action.html', '');
	}

	/**
	* Add new activity on wall
	*
	* @param int $post_where
	* @param int $wall_id
	* @param string $text
	* @param int $privacy
	* @param int $type
	* @param string $extra
	* @param bool $template
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function add_status($post_where, $wall_id, $text, $privacy, $type = 0, $extra = NULL, $template = true)
	{
		switch ($post_where)
		{
			case 'page':
				$post_where = 1;
				$privacy = 2;
			break;
			default:
				$post_where = 0;
			break;
		}

		$time = time();
		if (!$extra)
		{
			$extra = '';
		}	
		
		$sql_arr = $this->pg_social_helper->pgMessage($text);

		$sql_arr = array_merge($sql_arr, array(
			'post_parent'				=> 0,
			'post_where'				=> $post_where,
			'wall_id'					=> $wall_id,
			'user_id'					=> $this->user->data['user_id'],
			'time'						=> $time,
			'post_privacy'				=> $privacy,
			'post_type'					=> $type,
			'post_extra'				=> $extra,
			'tagged_user'				=> ''
		));

		if ($text || $extra)
		{
			$sql = 'INSERT INTO '.$this->pgsocial_wallpost.' '.$this->db->sql_build_array('INSERT', $sql_arr);
			if ($this->db->sql_query($sql))
			{
				$last_status = $this->db->sql_nextid();
				if ($post_where == 0 && $wall_id == $this->user->data['user_id'] && $this->user->data['user_signature_replace'] && $privacy != 0 && $type != 4 && !$this->pg_social_helper->extra_text($text))
				{
					$new_sign = $text.' [url='.generate_board_url().$this->helper->route('status_page', array('id' => $last_status)).']#status[/url]';
					$new_sign = $this->pg_social_helper->pgMessage($new_sign);
	
					$sql = "UPDATE ".USERS_TABLE." SET user_sig = '".$new_sign['message']."', user_sig_bbcode_uid = '".$new_sign['bbcode_uid']."', user_sig_bbcode_bitfield = '".$new_sign['bbcode_bitfield']."' WHERE user_id = '".$this->user->data['user_id']."'";
					$this->db->sql_query($sql);
				}
				if ($post_where == 0 && $wall_id != $this->user->data['user_id'])
				{
					$this->notify->notify('add_status', $last_status, $text, (int) $wall_id, (int) $this->user->data['user_id'], 'NOTIFICATION_SOCIAL_STATUS_ADD');
				}
				$this->social_tag->add_tag($last_status, $text);
				$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, 'STATUS_NEW', "<a href='".$this->helper->route("status_page", array('id' => $last_status))."'>#".$last_status."</a>");

				$this->template->assign_vars(array(
					'ACTION'	=> '',
				));
				if ($type != 4 && $template)
				{
					return $this->helper->render('activity_status_action.html', $this->user->lang['ACTIVITY']);
				}
			}
		}
	}

	/**
	 * Delete the activity from the wall
	 *
	 * @param int $post
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function delete_status($post)
	{
		if ($this->social_photo->photo_of_post($post))
		{
			$this->social_photo->delete_photo($this->social_photo->photo_of_post($post));
		}
		$sql_status = 'DELETE FROM '.$this->pgsocial_wallpost.' WHERE '.$this->db->sql_in_set('post_ID', array($post));
		$sql_comment = 'DELETE FROM '.$this->pgsocial_wallpostcomment.' WHERE '.$this->db->sql_in_set('post_ID', array($post));
		$sql_like = 'DELETE FROM '.$this->pgsocial_wallpostlike.' WHERE '.$this->db->sql_in_set('post_ID', array($post));
		$this->db->sql_query($sql_status);
		$this->db->sql_query($sql_comment);
		$this->db->sql_query($sql_like);
		

		$this->template->assign_vars(array(
			'ACTION'	=> '',
		));
		$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, 'STATUS_REMOVE', '');
		return $this->helper->render('activity_status_action.html', '');
	}

	/**
	 * Share the activity on the wall
	 *
	 * @param int $post
	 * @return
	 */
	public function shareStatus($post)
	{
		$time = time();
		$sql_arr = array(
			'post_parent'			=> $post,
			'wall_id'					=> $this->user->data['user_id'],
			'user_id'					=> $this->user->data['user_id'],
			'message'					=> '',
			'time'						=> $time,
			'post_privacy'		=> 1,
			'post_type'				=> 0,
			'post_extra'			=> '',
			'bbcode_bitfield'	=> '',
			'bbcode_uid'			=> '',
			'tagged_user'			=> ''
		);

		$sql = 'INSERT INTO '.$this->pgsocial_wallpost.' '.$this->db->sql_build_array('INSERT', $sql_arr);
		if ($this->db->sql_query($sql))

		$last_status = $this->db->sql_nextid();
		$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, 'STATUS_SHARE', "<a href='".$this->helper->route('status_page', array('id' => $last_status))."'>#".$last_status."</a> -> <a href='".$this->helper->route('status_page', array('id' => $post))."'>#".$post."</a>");
		$this->template->assign_vars(array(
			'ACTION'	=> $sql,
		));
		return $this->helper->render('activity_status_action.html', '');
	}

	/**
	* Like / Dislike and Count Like on the activity
	*
	* @param int $post
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function like_action($post)
	{
		$post_info = 'SELECT user_id, wall_id FROM '.$this->pgsocial_wallpost.' WHERE post_ID = "'.$post.'"';
		$res = $this->db->sql_query($post_info);
		$post_info = $this->db->sql_fetchrow($res);
		$this->db->sql_freeresult($res);

		$sql = "SELECT post_like_ID FROM ".$this->pgsocial_wallpostlike."
		WHERE post_ID = '".$post."' AND user_id = '".(int) $this->user->data['user_id']."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		if ($row['post_like_ID'] != '')
		{
			$sql = "DELETE FROM ".$this->pgsocial_wallpostlike." WHERE post_ID = '".$post."' AND user_id = '".(int) $this->user->data['user_id']."'";
			$action = 'dislike';
			if ($post_info['user_id'] != (int) $this->user->data['user_id'] || $post_info['wall_id'] == (int) $this->user->data['user_id']) $this->notify->notify('remove_like', $post, '', (int) $post_info['user_id'], (int) $this->user->data['user_id'], 'NOTIFICATION_SOCIAL_LIKE_ADD');
			$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, 'DISLIKE_NEW', "<a href='".$this->helper->route('status_page', array('id' => $post))."'>#".$post."</a>");
		}
		else
		{
			$sql_arr = array(
				'post_ID'					=> $post,
				'user_id'					=> (int) $this->user->data['user_id'],
				'post_like_time'	=> time(),
			);
			$sql = 'INSERT INTO '.$this->pgsocial_wallpostlike.' '.$this->db->sql_build_array('INSERT', $sql_arr);
			$action = 'like';
			if ($post_info['user_id'] != (int) $this->user->data['user_id']) $this->notify->notify('add_like', $post, '', (int) $post_info['user_id'], (int) $this->user->data['user_id'], 'NOTIFICATION_SOCIAL_LIKE_ADD');
			$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, 'LIKE_NEW', "<a href='".$this->helper->route('status_page', array('id' => $post))."'>#".$post."</a>");
		}
		$this->db->sql_query($sql);

		$this->template->assign_vars(array(
			'ACTION'	=> $action,
			'LIKE_TOT'	=> $this->pg_social_helper->count_action('like', $post),
		));
		return $this->helper->render('activity_status_action.html', '');
	}

	/**
	* The comments of activity
	*
	* @param int $post
	* @param int $type
	* @param bool $template
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function get_comments($post, $type, $template = true)
	{
		$sql = "SELECT *
		FROM ".$this->pgsocial_wallpostcomment."
		WHERE post_ID = '".$post."'
		ORDER BY time DESC";
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$allow_bbcode = false; //$this->config['pg_social_bbcode'];
			$allow_urls = false; //$this->config['pg_social_url'];
			$allow_smilies = false; //$this->config['pg_social_smilies'];
			$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);

			$sql_use = "SELECT user_id, username, username_clean, user_colour, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height FROM ".USERS_TABLE."
			WHERE user_id = '".$row['user_id']."'";
			$resulta = $this->db->sql_query($sql_use);
			$wall = $this->db->sql_fetchrow($resulta);
			if ($row['user_id'] == $this->user->data['user_id']) $comment_action = true; else $comment_action = false;
			$this->template->assign_block_vars('post_comment', array(
				'COMMENT_ID'					=> $row['post_comment_ID'],
				'COMMENT_ACTION'			=> $comment_action,
				'AUTHOR_PROFILE'			=> get_username_string('profile', $wall['user_id'], $wall['username'], $wall['user_colour']),
				'AUTHOR_ID'						=> $wall['user_id'],
				'AUTHOR_USERNAME'			=> $wall['username'],
				'AUTHOR_AVATAR'				=> $this->pg_social_helper->social_avatar_thumb($wall['user_avatar'], $wall['user_avatar_type'], $wall['user_avatar_width'], $wall['user_avatar_height']),
				'AUTHOR_COLOUR'				=> '#'.$wall['user_colour'],
				'COMMENT_TEXT'				=> generate_text_for_display($row['message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']),
				'COMMENT_TIME'				=> $row['time'],
				'COMMENT_TIME_AGO'		=> $this->pg_social_helper->time_ago($row['time']),
			));
		}
		$this->db->sql_freeresult($result);
		if ($template) return $this->helper->render('activity_comment.html', '');
	}

	/**
	 * Add new comment on activity
	 *
	 * @param int $post
	 * @param string $comment
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function add_comment($post, $comment)
	{
		$post_info = "SELECT user_id, wall_id FROM ".$this->pgsocial_wallpost." WHERE post_ID = '".$post."'";
		$res = $this->db->sql_query($post_info);
		$post_info = $this->db->sql_fetchrow($res);
		$this->db->sql_freeresult($res);

		$time = time();

		$sql_arr = $this->pg_social_helper->pgMessage($comment);

		$sql_arr = array_merge($sql_arr, array(
			'post_ID'					=> $post,
			'user_id'					=> $this->user->data['user_id'],
			'time'						=> $time,
		));
		$sql = 'INSERT INTO '.$this->pgsocial_wallpostcomment.' '.$this->db->sql_build_array('INSERT', $sql_arr);
		$this->db->sql_query($sql);
		if ($post_info['wall_id'] != $this->user->data['user_id']) $this->notify->notify('add_comment', $post, '', (int) $post_info['wall_id'], $this->user->data['user_id'], 'NOTIFICATION_SOCIAL_COMMENT_ADD');

		$this->template->assign_vars(array(
			'ACTION'	=> '',
		));
		$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, 'COMMENT_NEW', "<a href='".$this->helper->route('status_page', array('id' => $post))."'>#".$post."</a>");
		return $this->helper->render('activity_status_action.html', '');
	}

	/**
	 * Remove the comment on activity
	 *
	 * @param int $comment
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function remove_comment($comment)
	{
		$sql = "DELETE FROM ".$this->pgsocial_wallpostcomment." WHERE post_comment_ID = '".$comment."' AND user_id = '".$this->user->data['user_id']."'";
		$this->db->sql_query($sql);
		$this->template->assign_vars(array(
			'ACTION'	=> $sql,
		));
		$this->pg_social_helper->log($this->user->data['user_id'], $this->user->ip, 'COMMENT_REMOVE', '');
		return $this->helper->render('activity_status_action.html', '');
	}

	/**
	 * Array information photo
	 *
	 * @param int $photo
	 * @return array
	 */
	public function photo($photo)
	{
		$album = false;
		$img = $this->social_photo->get_photo($photo);
		if ($img['gallery_id'] != '0')
		{
			$album = true;
		}
		$gallery = $this->social_photo->gallery_info($img['gallery_id'], $album);

		return array(
			'gallery_id'			=> $img['gallery_id'],
			'gallery_name'		=> $gallery['gallery_name'],
			'gallery_url'			=> $img['gallery_url'],
			'gallery_privacy'	=> $gallery['gallery_privacy'],
			'album'						=> $album,
			'img' 						=> '<img src="'.$img['photo_file'].'" class="photo_popup" data-photo="'.$photo.'" />',
			'msg' 						=> htmlspecialchars_decode($img['photo_desc']),
		);
	}

	/**
	 * Profile or page is published the activity?
	 *
	 * @param int $status
	 * @return int $row
	 */
	public function status_where($status)
	{
		$sql = "SELECT post_where FROM ".$this->pgsocial_wallpost." WHERE post_ID = '".$status."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (int) $row['post_where'];
	}


	/**
	* Last activities
	*
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function activity_feed()
	{
		$sql = "SELECT w.post_comment_ID as type_id, w.post_ID as post_id, w.user_id, (SELECT wall.wall_id FROM ".$this->pgsocial_wallpost." as wall WHERE wall.post_ID = w.post_ID LIMIT 1) as wall_id, (SELECT wall.post_where FROM ".$this->pgsocial_wallpost." as wall WHERE wall.post_ID = w.post_ID LIMIT 1) as post_where, w.time, 'wall_comment' as type, u.user_id, u.username,u.username_clean, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
			FROM ".$this->pgsocial_wallpostcomment." as w, ".USERS_TABLE." as u
			WHERE w.user_id = u.user_id AND u.user_id != '".$this->user->data['user_id']."'
		UNION
			SELECT l.post_like_ID as type_id, l.post_ID as post_id, l.user_id, (SELECT wall.wall_id FROM ".$this->pgsocial_wallpost." as wall WHERE wall.post_ID = l.post_ID LIMIT 1) as wall_id, (SELECT wall.post_where FROM ".$this->pgsocial_wallpost." as wall WHERE wall.post_ID = l.post_ID LIMIT 1) as post_where, l.post_like_time as time, 'wall_like' as type, u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
			FROM ".$this->pgsocial_wallpostlike." as l, ".USERS_TABLE." as u
			WHERE l.user_id = u.user_id AND u.user_id != '".$this->user->data['user_id']."' AND u.user_type != '2'
		UNION
			SELECT m.user_id as type_id, '0' as post_id, m.user_id, m.user_id as wall_id, '' as post_where, m.user_regdate as time, 'user_reg' as type, m.user_id, m.username, m.username_clean, m.user_avatar, m.user_avatar_type, m.user_avatar_width, m.user_avatar_height, m.user_colour
			FROM ".USERS_TABLE." as m
			WHERE m.user_id != '".$this->user->data['user_id']."'
		ORDER BY time DESC";
		$result = $this->db->sql_query_limit($sql, 5);
		while($row = $this->db->sql_fetchrow($result))
		{
			$post = $this->helper->route("status_page", array("id" => $row['post_id']));
			$user = '<a href="'.get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']).'">'.$row['username'].'</a>';

			switch($row['post_where'])
			{
				case 0:
					$sqlwall = "SELECT user_id, username, username_clean, user_colour FROM ".USERS_TABLE." WHERE user_id = '".$row['wall_id']."'";
				break;
				case 1:
					$sqlwall = "SELECT page_id as user_id, page_username as username, page_username_clean as username_clean, '' as user_colour FROM ".$this->table_prefix."pg_social_pages WHERE page_id = '".$row['wall_id']."'";
				break;

			}

			$resultwall = $this->db->sql_query($sqlwall);
			$wall = $this->db->sql_fetchrow($resultwall);
			if ($row['post_where'] == 1)
			{
				$walll = '<a href="'.append_sid($this->helper->route('pages_page'), 'u='.$wall['username_clean']).'">'.$wall['username'].'</a>';
			}
			else
			{
				$walll = '<a href="'.get_username_string('profile', $wall['user_id'], $wall['username'], $wall['user_colour']).'">'.$wall['username'].'</a>';
			}

			switch($row['type'])
			{
				case 'wall_like':
					if ($wall['user_id'] == $this->user->data['user_id'])
					{
						$action = $this->user->lang('HAS_LIKE_YOUR_POST', $user, $post);
					}
					elseif ($row['user_id'] != $wall['user_id'])
					{
						$action = $this->user->lang('HAS_LIKE_POST', $user, $post, $walll);
					}
					else
					{
						$action = $this->user->lang('HAS_LIKE_HER_POST', $user, $post);
					}
				break;
				case 'wall_comment':
					if ($wall['user_id'] == $this->user->data['user_id'])
					{
						$action = $this->user->lang('HAS_COMMENT_YOUR_POST', $user, $post);
					}
					elseif ($row['user_id'] != $wall['user_id'])
					{
						$action = $this->user->lang('HAS_COMMENT_POST', $user, $post, $walll);
					}
					else
					{
						$action = $this->user->lang('HAS_COMMENT_HER_POST', $user, $post);
					}
				break;
				case 'user_reg':
					$action = $this->user->lang('HAS_REG', $user);
				break;
				default:
					$action = $row['type'];
				break;
			}

			$this->template->assign_block_vars('activity_feed', array(
				'SQL'							=> $row['type'],
				'AUTHOR_PROFILE'	=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),
				'AUTHOR_AVATAR'		=> $this->pg_social_helper->social_avatar_thumb($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']),
				'ACTION'					=> $action,
				'TIME'						=> $this->pg_social_helper->time_ago($row['time']),
			));
		}
	}
}
