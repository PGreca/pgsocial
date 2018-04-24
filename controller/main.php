<?php
/**
*
* Social extension for the phpBB Forum Software package.
*
* @copyright (c) 2017 Antonio PGreca (PGreca)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace pgreca\pg_social\controller;

class main {
	/** @var \phpbb\files\factory */
	protected $files_factory;
	
	/* @var \phpbb\auth\auth */
	protected $auth;
	
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\db\driver\driver */
	protected $db;
	
	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\request\request */
	protected $request;
	
	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;
	
	/* @var string phpBB root path */
	protected $root_path;	
	
	/* @var string phpEx */
	protected $php_ext;
	/**
	* Constructor
	*
	* @param \phpbb\auth\auth			$auth
	* @param \phpbb\config\config      $config
	* @param \phpbb\db\driver\driver $db
	* @param \phpbb\controller\helper  $helper
	* @param \phpbb\request\request	$request	
	* @param \pg_social\\controller\helper $pg_social_helper	
	* @param \pg_social\controller\notifyhelper $notifyhelper Notification helper.	 	
	* @param \pg_social\social\post_status $post_status 	
	* @param \pg_social\social\$social_zebra $social_zebra	 	
	* @param \pg_social\social\$social_chat $social_chat	 	
	* @param \pg_social\social\$social_photo $social_photo	 	
	* @param \pg_social\social\$social_tag $social_tag	 
	* @param \phpbb\template\template  $template
	* @param \phpbb\user				$user
	*/
	public function __construct($files_factory, $auth, $config, $db, $helper, $request, $pg_social_helper, $notifyhelper, $post_status, $social_zebra, $social_chat, $social_photo, $social_tag, $template, $user, $root_path, $php_ext, $table_prefix) {
		$this->files_factory		= $files_factory;
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
		$this->template				= $template;
		$this->user					= $user;
	    $this->root_path			= $root_path;
		$this->php_ext				= $php_ext;	
        $this->table_prefix 		= $table_prefix;	
	}
	
	/**
	* Profile controller for route /social/
	*
	* @param string		$name
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($name) {
		if(!$this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel')) {
			if($this->user->data['user_id'] != ANONYMOUS) {
				echo $this->user->data['user_id'];
				trigger_error('NO_VIEW_USERS');
			}
			login_box('', ((isset($this->user->lang['LOGIN_EXPLAIN_'.strtoupper('viewprofile')])) ? $this->user->lang['LOGIN_EXPLAIN_'.strtoupper('viewprofile')] : $this->user->lang['LOGIN_EXPLAIN_MEMBERLIST']));
		} else {	
			$mode = $this->request->variable('mode', '');
			$profile_id = $this->request->variable('profile_id', '');
			$where = $this->request->variable('where', '');
			
			switch($mode) {
				case 'getStatus':	
					return $this->post_status->getStatus($profile_id, $this->request->variable('lastp', ''), $where, $this->request->variable('order', ''), true);	
				break;
				case 'addStatus':
					return $this->post_status->addStatus($profile_id, $this->request->variable('text', ''), $this->request->variable('privacy', ''), 0, '');
				break;
				case 'deleteStatus':
					$return = $this->post_status->deleteStatus($this->request->variable('post_status', ''), $name);
					return $return;
				break;
				case 'shareStatus':
					$return = $this->post_status->shareStatus($this->request->variable('status', ''));
					return $return;
				break;
				case 'likeAction':
					$return = $this->post_status->likeAction($this->request->variable('post_like', ''));
					return $return;
				break;
				case 'getComments':
					$return = $this->post_status->getComments($this->request->variable('post_status', ''), $this->request->variable('type', ''));
					return $return;
				break;
				case 'addComment':
					$return = $this->post_status->addComment($this->request->variable('post_status', ''), $this->request->variable('comment', ''));
					return $return;
				break;
				case 'removeComment':
					$return = $this->post_status->removeComment($this->request->variable('comment', ''));
					return $return;
				break;
				case 'getFriends':
					if($this->request->variable('friend', '')) $friends = $this->request->variable('friend', '');
					$return = $this->social_zebra->getFriends($profile_id, $where, $friends);
					return $return;
				break;
				case 'requestFriend':
					$return = $this->social_zebra->requestFriend($profile_id, $this->request->variable('request', ''));
					return $return;
				break;
				case 'messageCheck':
					$return = $this->social_chat->messageCheck($this->request->variable('exclude', ''));
					return $return;
				break;
				case 'getchatPeople':
					$return = $this->social_chat->getchatPeople($this->request->variable('person', ''));
					return $return;
				break;
				case 'getchatPerson':
					$return = $this->social_chat->getchatPerson($this->request->variable('person', ''));
					return $return;
				break;
				case 'getchatMessage':
					$return = $this->social_chat->getchatMessage($this->request->variable('person', ''), $this->request->variable('order', ''), $this->request->variable('lastmessage', ''));
					return $return;
				break;
				case 'messageSend':
					$return = $this->social_chat->messageSend($this->request->variable('person', ''), $this->request->variable('message', ''));
					return $return;
				break;
				case 'getPhoto':
					$return = $this->social_photo->getPhoto($this->request->variable('photo', ''), 1);
					return $return;
				break;
				case 'addPhoto':
					$return = $this->social_photo->photoUpload($this->request->variable('msg', ''), $this->request->variable('type', ''), $this->request->file('photo'), $this->request->variable('top', ''));
					return $return;
				break;
				case 'tag_system_search':
					$return = $this->social_tag->tag_system_search($this->request->variable('who', ''));
					return $return;
				break;
				default: 
				break;
			}			
			
			$this->template->assign_vars(array(
				'PG_SOCIAL_SIDEBAR_RIGHT'	=> $this->config['pg_social_sidebarRight'],	
				'PG_SOCIAL_SIDEBAR_RIGHT_FRIENDSRANDOM'	=> $this->config['pg_social_sidebarRight_friendsRandom'],	
				
				'PROFILE'					=> $this->user->data['user_id'],
				'PROFILE_URL'				=> get_username_string('profile', $this->user->data['user_id'], $this->user->data['username'], $this->user->data['user_colour']),
				'PROFILE_EDIT'				=> append_sid($this->root_path."ucp.".$this->php_ext, "i=ucp_profile&amp;mode=profile_info"),
				'PROFILE_AVATAR'			=> $this->pg_social_helper->social_avatar($this->user->data['user_avatar'], $this->user->data['user_avatar_type']),	     
				'PROFILE_USERNAME'			=> $this->user->data['username'],
				'PROFILE_USERNAME_CLEAN'	=> $this->user->data['username_clean'],
				'PROFILE_COLOUR'			=> "#".$this->user->data['user_colour'],
				'PROFILE_QUOTE'				=> $this->user->data['user_quote'],
				'PROFILE_GENDER'			=> $this->user->data['user_gender'],
				'PROFILE_RANK'				=> $this->pg_social_helper->social_rank($this->user->data['user_rank'])['rank_title'],					
				'PROFILE_RANK_IMG'			=> $this->pg_social_helper->social_rank($this->user->data['user_rank'])['rank_image'],				
			));	
		
			$this->post_status->getStatus($this->user->data['user_id'], 0, "all", "seguel");
			$this->social_zebra->getFriends($profile_id, $where, "no");
			return $this->helper->render('activity_body.html', $this->user->lang['ACTIVITY']);		
		}
	}	
	
		/**
	* Forum controller for route /forum_page/
	*
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle_forum() {	
		if($this->config['pg_social_index_replace']) {
			include($this->root_path.'includes/functions_display.'.$this->php_ext);
			
			global $phpbb_dispatcher;
			global $phpbb_container;
			$this->user->setup('viewforum');

			// Mark notifications read
			if(($mark_notification = $this->request->variable('mark_notification', 0))){
				if($this->user->data['user_id'] == ANONYMOUS) {
					if($this->request->is_ajax()) {
						trigger_error('LOGIN_REQUIRED');
					}
					login_box('', $this->user->lang['LOGIN_REQUIRED']);
				}

				if(check_link_hash($this->request->variable('hash', ''), 'mark_notification_read')) {
					/* @var $phpbb_notifications \phpbb\notification\manager */
					$phpbb_notifications = $phpbb_container->get('notification_manager');

					$notification = $phpbb_notifications->load_notifications('notification.method.board', array(
						'notification_id'	=> $mark_notification,
					));

					if(isset($notification['notifications'][$mark_notification])) {
						$notification = $notification['notifications'][$mark_notification];

						$notification->mark_read();
						if($this->request->is_ajax()) {
							$json_response = new \phpbb\json_response();
							$json_response->send(array(
								'success'	=> true,
							));
						}
						if(($redirect = $this->request->variable('redirect', ''))) {
							redirect(append_sid($phpbb_root_path . $redirect));
						}
						redirect($notification->get_redirect_url());
					}
				}
			}

			display_forums('', $this->config['load_moderators']);

			$order_legend = ($this->config['legend_sort_groupname']) ? 'group_name' : 'group_legend';
			// Grab group details for legend display
			if($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel')) {
				$sql = 'SELECT group_id, group_name, group_colour, group_type, group_legend
					FROM '.GROUPS_TABLE.'
					WHERE group_legend > 0
					ORDER BY '.$order_legend.' ASC';
			} else {
				$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type, g.group_legend
					FROM '.GROUPS_TABLE.' g
					LEFT JOIN '.USER_GROUP_TABLE.' ug
						ON (
							g.group_id = ug.group_id
							AND ug.user_id = '.$this->user->data['user_id'].'
							AND ug.user_pending = 0
						)
					WHERE g.group_legend > 0
						AND (g.group_type <> '.GROUP_HIDDEN.' OR ug.user_id = '.$this->user->data['user_id'].')
					ORDER BY g.'.$order_legend.' ASC';
			}
			$result = $this->db->sql_query($sql);

			/** @var \phpbb\group\helper $group_helper */
			$group_helper = $phpbb_container->get('group_helper');

			$legend = array();
			while($row = $this->db->sql_fetchrow($result)) {
				$colour_text = ($row['group_colour']) ? ' style="color:#'.$row['group_colour'].'"' : '';
				$group_name = $group_helper->get_name($row['group_name']);

				if($row['group_name'] == 'BOTS' || ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile'))) {
					$legend[] = '<span'.$colour_text.'>'.$group_name.'</span>';
				} else {
					$legend[] = '<a'.$colour_text.' href="'.append_sid($this->root_path."memberlist.".$this->php_ext, 'mode=group&amp;g='.$row['group_id']).'">'.$group_name.'</a>';
				}
			}
			$this->db->sql_freeresult($result);

			$legend = implode($this->user->lang['COMMA_SEPARATOR'], $legend);

			// Generate birthday list if required ...
			$show_birthdays = ($this->config['load_birthdays'] && $this->config['allow_birthdays'] && $this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'));

			$birthdays = $birthday_list = array();
			if($show_birthdays) {
				$time = $this->user->create_datetime();
				$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

				// Display birthdays of 29th february on 28th february in non-leap-years
				$leap_year_birthdays = '';
				if($now['mday'] == 28 && $now['mon'] == 2 && !$time->format('L')) {
					$leap_year_birthdays = " OR u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', 29, 2)) . "%'";
				}

				$sql_ary = array(
					'SELECT' => 'u.user_id, u.username, u.user_colour, u.user_birthday',
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
						AND u.user_type IN (" . USER_NORMAL.', '.USER_FOUNDER.')',
				);

				$vars = array('now', 'sql_ary', 'time');
				extract($phpbb_dispatcher->trigger_event('core.index_modify_birthdays_sql', compact($vars)));

				$sql = $this->db->sql_build_query('SELECT', $sql_ary);
				$result = $this->db->sql_query($sql);
				$rows = $this->db->sql_fetchrowset($result);
				$this->db->sql_freeresult($result);

				foreach($rows as $row) {
					$birthday_username	= get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
					$birthday_year		= (int) substr($row['user_birthday'], -4);
					$birthday_age		= ($birthday_year) ? max(0, $now['year'] - $birthday_year) : '';

					$birthdays[] = array(
						'USERNAME'	=> $birthday_username,
						'AGE'		=> $birthday_age,
					);

					// For 3.0 compatibility
					$birthday_list[] = $birthday_username . (($birthday_age) ? " ({$birthday_age})" : '');
				}
				
				
				$vars = array('birthdays', 'rows');
				extract($phpbb_dispatcher->trigger_event('core.index_modify_birthdays_list', compact($vars)));

				$this->template->assign_block_vars_array('birthdays', $birthdays);
			}

			// Assign index specific vars
			$this->template->assign_vars(array(				
				'TOTAL_POSTS'	=> $this->user->lang('TOTAL_POSTS_COUNT', (int) $this->config['num_posts']),
				'TOTAL_TOPICS'	=> $this->user->lang('TOTAL_TOPICS', (int) $this->config['num_topics']),
				'TOTAL_USERS'	=> $this->user->lang('TOTAL_USERS', (int) $this->config['num_users']),
				'NEWEST_USER'	=> $this->user->lang('NEWEST_USER', get_username_string('full', $this->config['newest_user_id'], $this->config['newest_username'], $this->config['newest_user_colour'])),

				'LEGEND'		=> $legend,
				'BIRTHDAY_LIST'	=> (empty($birthday_list)) ? '' : implode($this->user->lang['COMMA_SEPARATOR'], $birthday_list),

				'FORUM_IMG'				=> $this->user->img('forum_read', 'NO_UNREAD_POSTS'),
				'FORUM_UNREAD_IMG'			=> $this->user->img('forum_unread', 'UNREAD_POSTS'),
				'FORUM_LOCKED_IMG'		=> $this->user->img('forum_read_locked', 'NO_UNREAD_POSTS_LOCKED'),
				'FORUM_UNREAD_LOCKED_IMG'	=> $this->user->img('forum_unread_locked', 'UNREAD_POSTS_LOCKED'),

				'S_LOGIN_ACTION'			=> append_sid("{$phpbb_root_path}ucp.".$this->php_ext, 'mode=login'),
				'S_LOGIN_ACTION'			=> append_sid($this->root_path."ucp.".$this->php_ext, 'mode=login'),
				'U_SEND_PASSWORD'           => ($this->config['email_enable']) ? append_sid($this->root_path."ucp.".$this->php_ext, 'mode=sendpassword') : '',
				'S_DISPLAY_BIRTHDAY_LIST'	=> $show_birthdays,
				'S_INDEX'					=> true,

				'U_MARK_FORUMS'		=> ($this->user->data['is_registered'] || $this->config['load_anon_lastread']) ? append_sid($this->root_path."index.".$this->php_ext, 'hash='.generate_link_hash('global').'&amp;mark=forums&amp;mark_time='.time()) : '',
				'U_MCP'				=> ($this->auth->acl_get('m_') || $this->auth->acl_getf_global('m_')) ? append_sid($this->root_path."mcp.".$this->php_ext, 'i=main&amp;mode=front', true, $this->user->session_id) : '')
			);

			$page_title = ($this->config['board_index_text'] !== '') ? $this->config['board_index_text'] : $this->user->lang['INDEX'];

			$vars = array('page_title');
			extract($phpbb_dispatcher->trigger_event('core.index_modify_page_title', compact($vars)));

			// Output page
			page_header($page_title, true);

			$this->template->set_filenames(array(
				'body' => 'index_body.html'
			));
			page_footer();
		} else {
			redirect(append_sid($this->root_path));
		}
	}
		/**
	* Status controller for route /status/{id}
	*
	* @param string		$id
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle_status($id) {
		if(!$this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel')) {
			if($this->user->data['user_id'] != ANONYMOUS) {
				echo $this->user->data['user_id'];
				trigger_error('NO_VIEW_USERS');
			}
			login_box('', ((isset($this->user->lang['LOGIN_EXPLAIN_'.strtoupper('viewprofile')])) ? $this->user->lang['LOGIN_EXPLAIN_'.strtoupper('viewprofile')] : $this->user->lang['LOGIN_EXPLAIN_MEMBERLIST']));
		} else {
			$user_avatar = $this->pg_social_helper->social_avatar($this->user->data['user_avatar'], $this->user->data['user_avatar_type']);
					
			$sql = "SELECT w.*, u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_colour 
			FROM ".$this->table_prefix."pg_social_wall_post as w, ".USERS_TABLE." as u	
			WHERE post_ID = '".$id."' AND (w.user_id = u.user_id) AND u.user_type != '2'";	
			
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);			
								
			if($row['wall_id'] == $this->user->data['user_id'] || $row['post_privacy'] == 0 && $row['wall_id'] == $this->user->data['user_id'] || $row['post_privacy'] == 1 && $this->social_zebra->friendStatus($row['wall_id'])['status'] == 'PG_SOCIAL_FRIENDS' || $row['post_privacy'] == 2) {
				if(($row['user_id'] != $row['wall_id']) && $type != "profile") {
					$sqla = "SELECT user_id, username, username_clean, user_colour FROM ".USERS_TABLE."
					WHERE user_id = '".$row['wall_id']."'";
					$resulta = $this->db->sql_query($sqla);
					$wall = $this->db->sql_fetchrow($resulta);					
					$wall_action = $this->user->lang("HAS_WRITE_IN");
				} else {
					$wall['user_id'] = '';
					$wall['username'] = '';
					$wall['user_colour'] = '';
					$wall_action = '';
				}					
					
				switch($row['post_type']) {
					case '1':
						$author_action = $this->user->lang("HAS_UPLOADED_AVATAR");
						$photo = $this->post_status->photo($row['post_extra']);
						$msg = $photo['msg'];
						$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
					break;
					case '2':
						$author_action = $this->user->lang("HAS_UPLOADED_COVER");
						$photo = $this->post_status->photo($row['post_extra']);
						$msg = $photo['msg'];
						$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
					break;
					case '4':
						$posts = explode("#p", $row['post_extra']);
						$sql_post = "SELECT * FROM ".TOPICS_TABLE." WHERE topic_id = '".$posts[0]."'";
						$res = $this->db->sql_query($sql_post);
						$post = $this->db->sql_fetchrow($res);
						
						$author_action = 'ha scritto un post in <a href="'.append_sid(generate_board_url()).'/viewtopic.php?t='.$post['topic_id'].'#p'.$posts[1].'">'.$post['topic_title'].'</a>';
						$msg = '';
						$msg_align = '';						
					break;
					case '3':
					default:
						if($row['post_parent'] != 0) {
							$sql = "SELECT w.*, u.user_id, u.username, u.username_clean, u.user_avatar, u.user_avatar_type, u.user_colour 
							FROM ".$this->table_prefix."pg_social_wall_post as w, ".USERS_TABLE." as u	
							WHERE w.post_ID = '".$row['post_parent']."' AND u.user_id = w.user_id
							GROUP BY post_ID";
							$post_parent = $this->db->sql_query($sql);
							$parent = $this->db->sql_fetchrow($post_parent);
							if(isset($parent['post_ID'])) {
								$author_action = 'ha condiviso uno <a href="'.append_sid($this->helper->route("status_page", array("id" => $parent['post_ID']))).'">stato</a>';
								$msg = generate_text_for_display($row['message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $flags);
								$msg .= $this->pg_social_helper->extraText($row['message']);
								$msg .= '<div class="post_parent_cont">';
								if($parent['post_extra'] != "") {
									$photo = $this->photo($parent['post_extra']);
									$msg .= $photo['msg'];
									$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
								} else {
									$allow_bbcode = $this->config['pg_social_bbcode'];
									$allow_urls = $this->config['pg_social_url'];
									$allow_smilies = $this->config['pg_social_smilies'];
									$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);
				
									$msg .= generate_text_for_display($parent['message'], $parent['bbcode_uid'], $parent['bbcode_bitfield'], $flags);
									$msg .= $this->pg_social_helper->extraText($parent['message']);
								}	
								$msg .= '<div class="post_parent_info">';
								$msg .= '<div class="post_parent_author"><a href="'.get_username_string('profile', $parent['user_id'], $parent['username'], $parent['user_colour']).'">'.$parent['username'].'</a></div>';
								$msg .= '<div class="post_parent_date">'.$this->pg_social_helper->time_ago($parent['time']).'</div>';
								$msg .= '</div>';
								$msg .= '</div>';
							}
						} else {
							$author_action = "";
							if($row['post_extra'] != "") {
								$photo = $this->photo($row['post_extra']);
								$msg = $photo['msg'];
								$msg .= '<div class="status_photos">'.$photo['img'].'</div>';
							} else {
								$allow_bbcode = $this->config['pg_social_bbcode'];
								$allow_urls = $this->config['pg_social_url'];
								$allow_smilies = $this->config['pg_social_smilies'];
								$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);
			
								$msg = generate_text_for_display($row['message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $flags);
								$msg .= $this->pg_social_helper->extraText($row['message']);
							}		
						}	
						
						$msg_align = '';
					break;
				}					
				
				$comment = "<span>".$this->pg_social_helper->countAction("comments", $row['post_ID'])."</span> ";
				if($this->pg_social_helper->countAction("comments", $row['post_ID']) == 0 || $this->pg_social_helper->countAction("comments", $row['post_ID']) > 1) {
					$comment .= $this->user->lang('COMMENTS');
				} else {
					$comment .= $this->user->lang('COMMENT');
				}
							
				$this->template->assign_block_vars('post_status', array(			
					'USER_AVATAR'				=> $user_avatar,				
					"POST_STATUS_ID"            => $row['post_ID'],
					"AUTHOR_ACTION"				=> $author_action,
					"AUTHOR_PROFILE"			=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),		
					"AUTHOR_ID"					=> $row['user_id'],
					"AUTHOR_USERNAME"			=> $row['username'],
					"AUTHOR_AVATAR"				=> $this->pg_social_helper->social_avatar($row['user_avatar'], $row['user_avatar_type']),
					"AUTHOR_COLOUR"				=> "#".$row['user_colour'],
					"WALL_ACTION"				=> $wall_action,
					"WALL_PROFILE"				=> get_username_string('profile', $wall['user_id'], $wall['username'], $wall['user_colour']),	
					"WALL_ID"					=> $row['wall_id'],	
					"WALL_USERNAME"				=> $wall['username'],
					"WALL_COLOUR"				=> "#".$wall['user_colour'],
					"POST_TYPE"					=> $row['post_type'],
					"POST_URL"					=> $this->helper->route("status_page", array("id" => $row['post_ID'])),
					"POST_DATE"					=> $this->pg_social_helper->time_ago($row['time']),
					"MESSAGE"					=> $msg,
					"MESSAGE_ALIGN"				=> $msg_align,
					"POST_PRIVACY"				=> $this->user->lang($this->pg_social_helper->social_privacy($row['post_privacy'])),
					"ACTION"					=> $action,
					"LIKE"						=> $this->pg_social_helper->countAction("like", $row['post_ID']),
					"IFLIKE"					=> $this->pg_social_helper->countAction("iflike", $row['post_ID']),
					"COMMENT"					=> $comment,
				));
				
				return $this->helper->render('status.html', "Stai guardando uno stato di ".$row['username']);	
			} else {					
				trigger_error('NO_VIEW_THIS_STATUS');				
			}
		}
	}
}
