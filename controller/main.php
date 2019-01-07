<?php
/**
*
* Social extension for the phpBB Forum Software package.
*
* @copyright (c) 2017 Antonio PGreca (PGreca)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace pgreca\pgsocial\controller;

class main
{
	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\request\request */
	protected $request;

	/** @var \pgreca\pgsocial\controller\helper */
	protected $pg_social_helper;

	/** @var \pgreca\pgsocial\controller\notifyhelper */
	protected $notifyhelper;

	/** @var \pgreca\pgsocial\social\post_status */
	protected $post_status;

	/** @var \pgreca\pgsocial\social\social_zebra */
	protected $social_zebra;

	/** @var \pgreca\pgsocial\social\social_chat */
	protected $social_chat;

	/** @var \pgreca\pgsocial\social\social_photo */
	protected $social_photo;

	/** @var \pgreca\pgsocial\social\social_tag */
	protected $social_tag;

	/** @var \pgreca\pgsocial\social\social_page */
	protected $social_page;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var string phpBB root path */
	protected $root_path;

	/* @var string phpEx */
	protected $php_ext;

	/** @var string */
	protected $pgsocial_table_wallpost;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth			$auth
	* @param \phpbb\config\config      $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\controller\helper  $helper
	* @param \phpbb\request\request	$request
	* @param \pgreca\pgsocial\controller\helper $pg_social_helper
	* @param \pgreca\pgsocial\controller\notifyhelper $notifyhelper Notification helper.
	* @param \pgreca\pgsocial\social\post_status $post_status
	* @param \pgreca\pgsocial\social\social_zebra $social_zebra
	* @param \pgreca\pgsocial\social\social_chat $social_chat
	* @param \pgreca\pgsocial\social\social_photo $social_photo
	* @param \pgreca\pgsocial\social\social_tag $social_tag
	* @param \pgreca\pgsocial\social\social_page $social_page
	* @param \phpbb\template\template  $template
	* @param \phpbb\user				$user
	 * @param string $pgsocial_table_wallpost
	*/
	public function __construct($auth, $config, $db, $helper, $request, $pg_social_helper, $notifyhelper, $post_status, $social_zebra, $social_chat, $social_photo, $social_tag, $social_page, $template, $user, $root_path, $php_ext, $pgsocial_table_wallpost)
	{
		$this->auth					= $auth;
		$this->config				= $config;
		$this->db					= $db;
		$this->helper				= $helper;
		$this->request				= $request;
		$this->pg_social_helper		= $pg_social_helper;
		$this->notifyhelper			= $notifyhelper;
		$this->post_status 			= $post_status;
		$this->social_zebra 		= $social_zebra;
		$this->social_chat			= $social_chat;
		$this->social_photo			= $social_photo;
		$this->social_tag			= $social_tag;
		$this->social_page			= $social_page;
		$this->template				= $template;
		$this->user					= $user;
	    $this->root_path			= $root_path;
		$this->php_ext				= $php_ext;
		$this->pgsocial_wallpost	= $pgsocial_table_wallpost;
	}

	/**
	* Profile controller for route /social
	*
	* @param string		$name
	* @return mixed
	*/
	public function handle($name)
	{
		if(!$this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
		{
			/*if($this->user->data['user_id'] != ANONYMOUS){
				echo $this->user->data['user_id'];
				trigger_error('NO_VIEW_USERS');
			}
			login_box('', ((isset($this->user->lang['LOGIN_EXPLAIN_'.strtoupper('viewprofile')])) ? $this->user->lang['LOGIN_EXPLAIN_'.strtoupper('viewprofile')] : $this->user->lang['LOGIN_EXPLAIN_MEMBERLIST']));
			*/

			redirect($this->root_path);
		}
		else
		{
			$mode = $this->request->variable('mode', '');
			$profile_id = $this->request->variable('profile_id', '');
			$where = $this->request->variable('where', '');

			switch($mode)
			{
				case 'get_status':
					return $this->post_status->get_status($this->request->variable('post_where', ''), $profile_id, $this->request->variable('lastp', ''), $where, '', $this->request->variable('order', ''), true);
				break;
				case 'add_status':
					return $this->post_status->add_status($this->request->variable('post_where', ''), $profile_id, $this->request->variable('text', ''), $this->request->variable('privacy', ''), 0, '', true);
				break;
				case 'delete_status':
					return $this->post_status->delete_status($this->request->variable('post_status', ''));
				break;
				case 'shareStatus':
					return $this->post_status->shareStatus($this->request->variable('status', ''));
				break;
				case 'like_action':
					return $this->post_status->like_action($this->request->variable('post_like', ''));
				break;
				case 'get_comments':
					return $this->post_status->get_comments($this->request->variable('post_status', ''), $this->request->variable('type', ''));
				break;
				case 'add_comment':
					return $this->post_status->add_comment($this->request->variable('post_status', ''), $this->request->variable('comment', ''));
				break;
				case 'remove_comment':
					return $this->post_status->remove_comment($this->request->variable('comment', ''));
				break;
				case 'get_friends':
					if($this->request->variable('friend', ''))
					{
						$friends = $this->request->variable('friend', '');
					}

					$this->social_zebra->get_friends($profile_id, $where, $friends);
				break;
				case 'request_friend':
					return $this->social_zebra->request_friend($profile_id, $this->request->variable('request', ''));
				break;
				case 'pgsocial_chat_setting':
					return $this->social_chat->chat_setting($this->request->variable('setting', ''), $this->request->variable('value', 0));
				break;
				case 'pgsocial_chat_check':
					return $this->social_chat->pgsocial_chat_check();
				break;
				case 'message_check':
					return $this->social_chat->message_check($this->request->variable('exclude', ''));
				break;
				case 'getchat_people':
					return $this->social_chat->getchat_people($this->request->variable('people', ''));
				break;
				case 'getchat_person':
					return $this->social_chat->getchat_person($this->request->variable('person', ''), $this->request->variable('read', ''));
				break;
				case 'getchat_message':
					return $this->social_chat->getchat_message($this->request->variable('person', ''), $this->request->variable('order', ''), $this->request->variable('lastmessage', ''));
				break;
				case 'message_send':
					return $this->social_chat->message_send($this->request->variable('person', ''), $this->request->variable('message', ''));
				break;
				case 'message_read':
					$this->social_chat->message_read($this->request->variable('person', ''));
				break;
				case 'get_photo':
					return $this->social_photo->get_photo($this->request->variable('photo', ''), 1);
				break;
				case 'add_gallery':
					return $this->social_photo->add_gallery($this->request->variable('gallery_name', ''));
				break;
				case 'addPhoto':
					return $this->social_photo->photo_upload($this->request->variable('post_where', ''), $this->request->variable('profile_id', ''), $this->request->variable('msg', ''), $this->request->variable('type', ''), $where, $this->request->file('photo'), $this->request->variable('top', ''));
				break;
				case 'delete_photo':
					return $this->social_photo->delete_photo($this->request->variable('photo', ''));
				break;
				case 'prenext_photo':
					return $this->social_photo->prenext_photo($this->request->variable('photo', ''), $this->request->variable('ord', ''), $this->request->variable('where', ''));
				break;
				case 'tag_system_search':
					return $this->social_tag->tag_system_search($this->request->variable('who', ''));
				break;
				case 'pagelike_action':
					return $this->social_page->pagelike_action($this->request->variable('page', ''));
				break;
			}


			if($name == 'mp')
			{
				$time = $this->user->create_datetime();
				$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

				// Display birthdays of 29th february on 28th february in non-leap-years
				$leap_year_birthdays = '';
				if ($now['mday'] == 28 && $now['mon'] == 2 && !$time->format('L'))
				{
					$leap_year_birthdays = " OR u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', 29, 2)) . "%'";
				}

				$sql_ary = array(
					'SELECT' => 'u.user_id, u.username, u.user_colour, u.user_birthday, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height',
					'FROM' => array(
						USERS_TABLE => 'u',
					),
					'LEFT_JOIN' => array(
						array(
							'FROM' => array(BANLIST_TABLE => 'b'),
							'ON' => 'u.user_id = b.ban_userid',
						),
					),
					'WHERE' => "(b.ban_id IS NULL OR b.ban_exclude = 1)
						AND (u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%' $leap_year_birthdays)
						AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')',
				);
				$birthdays_sql = $this->db->sql_build_query('SELECT', $sql_ary);
				$birthdays_result = $this->db->sql_query($birthdays_sql);
				while($birthday = $this->db->sql_fetchrow($birthdays_result))
				{
					$birthday_username	= get_username_string('full', $birthday['user_id'], $birthday['username'], $birthday['user_colour']);
					$birthday_year		= (int) substr($birthday['user_birthday'], -4);
					$birthday_age		= ($birthday_year) ? max(0, $now['year'] - $birthday_year) : '';

					$this->template->assign_block_vars('friend_birthday', array(
						'USERNAME'		=> $birthday_username,
						'AGE'			=> $birthday_age,
						'AVATAR'		=> $this->pg_social_helper->social_avatar_thumb($birthday['user_avatar'], $birthday['user_avatar_type'], $birthday['user_avatar_width'], $birthday['user_avatar_height']),
						'PROFILE'		=> get_username_string('profile', $birthday['user_id'], $birthday['username'], $birthday['user_colour']),
					));
				}
				$this->db->sql_freeresult($birthdays_result);

				if($this->config['pg_social_block_posts_last'])
				{
					$a_f_auth_read = $this->auth->acl_getf('f_read');
					$a_f_read = array();
					if(!empty($a_f_auth_read))
					{
						foreach($a_f_auth_read as $i_f_id => $a_auth)
						{
							if($a_auth['f_read'] == 1)
							{
								$a_f_read[] = $i_f_id;
							}
						}
					}
					$last_posts = "SELECT p.post_id, p.topic_id, p.post_time, p.forum_id, t.topic_title, f.forum_name
						FROM ".POSTS_TABLE." p
						LEFT JOIN ".TOPICS_TABLE." t ON p.topic_id = t.topic_id
						LEFT JOIN ".FORUMS_TABLE." f ON p.forum_id = f.forum_id
						WHERE p.post_visibility = 1
						AND ".$this->db->sql_in_set('p.forum_id', $a_f_read, false, true)."
						ORDER BY p.post_id DESC";
					$last_topics = "SELECT t.topic_id, t.topic_title, t.topic_last_post_id, t.topic_last_post_time, t.forum_id, f.forum_name
						FROM ".TOPICS_TABLE." t
						LEFT JOIN ".FORUMS_TABLE." f ON t.forum_id = f.forum_id
						LEFT JOIN ".POSTS_TABLE." p ON t.topic_last_post_id = p.post_id
						WHERE p.post_visibility = 1
						AND ".$this->db->sql_in_set('t.forum_id', $a_f_read, false, true)."
						ORDER BY t.topic_last_post_id DESC";
					$last_topics_result = $this->db->sql_query_limit($last_topics, 10);
					$last_topics_rowset = $this->db->sql_fetchrowset($last_topics_result);
					$this->db->sql_freeresult($last_topics_result);
					for($i = 0; isset($last_topics_rowset[$i]); $i++)
					{
						$last_topics = $last_topics_rowset[$i];
						$this->template->assign_block_vars('last_topics', array(
						'TOPIC_TITLE'		 => $last_topics['topic_title'],
						'POST_LINK'			 => append_sid($this->root_path."viewtopic.".$this->php_ext, "t=".$last_topics['topic_id']."&amp;p=".$last_topics['topic_last_post_id']."#p".$last_topics['topic_last_post_id']),
						'TOPIC_FORUM'		 => $last_topics['forum_name'],
						'TOPIC_FORUM_LINK'	 => append_sid($this->root_path."viewforum.".$this->php_ext, "f=".$last_topics['forum_id']),
						'POST_TIME'			 => $this->pg_social_helper->time_ago($last_topics['topic_last_post_time']),
						));
					}
				}

				$this->template->assign_vars(array(
					'PG_SOCIAL_SIDEBAR_RIGHT'				=> $this->config['pg_social_sidebarRight'],
					'PG_SOCIAL_SIDEBAR_RIGHT_FRIENDSRANDOM'	=> $this->config['pg_social_sidebarRight_friendsRandom'],
					'PG_SOCIAL_SIDEBAR_RIGHT_LAST_POST'		=> $this->config['pg_social_block_posts_last'],

					'PROFILE'								=> $this->user->data['user_id'],
					'PROFILE_URL'							=> get_username_string('profile', $this->user->data['user_id'], $this->user->data['username'], $this->user->data['user_colour']),
					'PROFILE_EDIT'							=> append_sid($this->root_path."ucp.".$this->php_ext, "i=ucp_profile&amp;mode=profile_info"),
					'PROFILE_AVATAR'						=> $this->pg_social_helper->social_avatar_thumb($this->user->data['user_avatar'], $this->user->data['user_avatar_type'], $this->user->data['user_avatar_width'], $this->user->data['user_avatar_height']),
					'PROFILE_USERNAME'						=> $this->user->data['username'],
					'PROFILE_USERNAME_CLEAN'				=> $this->user->data['username_clean'],
					'PROFILE_COLOUR'						=> "#".$this->user->data['user_colour'],
					'PROFILE_QUOTE'							=> $this->user->data['user_quote'],
					'PROFILE_GENDER'						=> $this->user->data['user_gender'],
					'PROFILE_RANK'							=> $this->pg_social_helper->social_rank($this->user->data['user_rank'])['rank_title'],
					'PROFILE_RANK_IMG'						=> $this->pg_social_helper->social_rank($this->user->data['user_rank'])['rank_image'],

					'PAGES_URL'								=> $this->helper->route("pages_page"),
				));

				$this->post_status->get_status('all', $this->user->data['user_id'], 0, "all", 0, "seguel", "");
				$this->social_page->page_likeif($this->user->data['user_id'], "pagesMaylike", false);
				$this->social_zebra->get_friends($profile_id, $where, "no");
				$this->template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $this->user->lang('ACTIVITY'),
					'U_VIEW_FORUM'	=> $this->helper->route('profile_page'),
				));

				return $this->helper->render('activity_body.html', $this->user->lang['ACTIVITY']);
			}
		}
	}

		/**
	* Status controller for route /status/{id}
	*
	* @param string		$id
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle_status($id)
	{
		if(!$this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
		{
			if($this->user->data['user_id'] != ANONYMOUS)
			{
				trigger_error('NO_VIEW_USERS');
			}
			login_box('', ((isset($this->user->lang['LOGIN_EXPLAIN_'.strtoupper('viewprofile')])) ? $this->user->lang['LOGIN_EXPLAIN_'.strtoupper('viewprofile')] : $this->user->lang['LOGIN_EXPLAIN_MEMBERLIST']));
		}
		else
		{
			$sql = "SELECT w.*, u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_width, u.user_avatar_height, u.user_avatar_type, u.user_colour
			FROM ".$this->pgsocial_wallpost." as w, ".USERS_TABLE." as u	
			WHERE post_ID = '".$id."' AND (w.user_id = u.user_id) AND u.user_type != '2'";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			if($row['post_ID'])
			{
				$this->template->assign_vars(array(
					'PROFILE_ID'	=> $row['wall_id'],
				));
				return $this->post_status->status(0, $row['wall_id'], $row['post_type'], 'half', $row, 0);
			}
			else
			{
				redirect($this->helper->route('profile_page'));
			}
		}
	}
}
