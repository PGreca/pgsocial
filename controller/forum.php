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

class forum
{
	
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
		
	/** @var \phpbb\group\helper */
	protected $group_helper;
	
	/** @var \phpbb\eent\dispatcher */
	protected $dispatcher;

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
	* @param \pg_social\social\$social_page $social_page	 
	* @param \phpbb\template\template  $template
	* @param \phpbb\user				$user
	* @param \phpbb\group\helper     $group_helper 
	* @param \phpbb\event\dispatcher $dispatcher
	*/
	public function __construct($auth, $config, $db, $helper, $request, $pg_social_helper, $notifyhelper, $post_status, $social_zebra, $social_chat, $social_photo, $social_tag, $social_page, $template, $user, $group_helper, $dispatcher, $root_path, $php_ext, $table_prefix)
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
		$this->group_helper			= $group_helper;
		$this->dispatcher			= $dispatcher;
	    $this->root_path			= $root_path;
		$this->php_ext				= $php_ext;	
        $this->table_prefix 		= $table_prefix;	
	}
	
	/**
	* Profile controller for route /social
	*
	* @param string		$name
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle()
	{		
		if($this->user->data['user_id'] == ANONYMOUS || !$this->config['pg_social_index_replace']) redirect($this->root_path);
		
		// @codeCoverageIgnoreStart
		if(!function_exists('display_forums'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		}
		// @codeCoverageIgnoreEnd

		display_forums('', $this->config['load_moderators']);
		
		$order_legend = ($this->config['legend_sort_groupname']) ? 'group_name' : 'group_legend';
		// Grab group details for legend display
		if($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
			$sql = 'SELECT group_id, group_name, group_colour, group_type, group_legend
				FROM ' . GROUPS_TABLE . '
				WHERE group_legend > 0
				ORDER BY ' . $order_legend . ' ASC';
		}
		else
		{
			$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type, g.group_legend
				FROM ' . GROUPS_TABLE . ' g
				LEFT JOIN ' . USER_GROUP_TABLE . ' ug
					ON (
						g.group_id = ug.group_id
						AND ug.user_id = ' . $this->user->data['user_id'] . '
						AND ug.user_pending = 0
					)
				WHERE g.group_legend > 0
					AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . $this->user->data['user_id'] . ')
				ORDER BY g.' . $order_legend . ' ASC';
		}
		$result = $this->db->sql_query($sql);

		$legend = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';
			$group_name = $this->group_helper->get_name($row['group_name']);

			if($row['group_name'] == 'BOTS' || ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile')))
			{
				$legend[] = '<span' . $colour_text . '>' . $group_name . '</span>';
			}
			else
			{
				$legend[] = '<a' . $colour_text . ' href="' . append_sid("{$this->root_path}memberlist.$this->php_ext", 'mode=group&amp;g=' . $row['group_id']) . '">' . $group_name . '</a>';
			}
		}
		$this->db->sql_freeresult($result);

		$legend = implode($this->user->lang['COMMA_SEPARATOR'], $legend);

		// Generate birthday list if required ...
		$show_birthdays = ($this->config['load_birthdays'] && $this->config['allow_birthdays'] && $this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'));

		$birthdays = $birthday_list = array();
		if($show_birthdays)
		{
			$time = $this->user->create_datetime();
			$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

			// Display birthdays of 29th february on 28th february in non-leap-years
			$leap_year_birthdays = '';
			if($now['mday'] == 28 && $now['mon'] == 2 && !$time->format('L'))
			{
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
					AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')',
			);
			
			/**
			* Event to modify the SQL query to get birthdays data
			*
			* @event core.index_modify_birthdays_sql
			* @var	array	now			The assoc array with the 'now' local timestamp data
			* @var	array	sql_ary		The SQL array to get the birthdays data
			* @var	object	time		The user related Datetime object
			* @since 3.1.7-RC1
			*/
			$vars = array('now', 'sql_ary', 'time');
			extract($this->dispatcher->trigger_event('pgreca.pgsocial.core.index_modify_birthdays_sql', compact($vars)));
			
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			foreach ($rows as $row)
			{
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
			
			/**
			* Event to modify the birthdays list
			*
			* @event core.index_modify_birthdays_list
			* @var	array	birthdays		Array with the users birthdays data
			* @var	array	rows			Array with the birthdays SQL query result
			* @since 3.1.7-RC1
			*/
			$vars = array('birthdays', 'rows');
			extract($this->dispatcher->trigger_event('pgreca.pgsocial.core.index_modify_birthdays_list', compact($vars)));

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

			'S_LOGIN_ACTION'			=> append_sid("{$this->root_path}ucp.$this->php_ext", 'mode=login'),
			'U_SEND_PASSWORD'           => ($this->config['email_enable']) ? append_sid("{$this->root_path}ucp.$this->php_ext", 'mode=sendpassword') : '',
			'S_DISPLAY_BIRTHDAY_LIST'	=> $show_birthdays,
			'S_INDEX'					=> true,

			'U_MARK_FORUMS'		=> ($this->user->data['is_registered'] || $this->config['load_anon_lastread']) ? append_sid("{$this->root_path}index.$this->php_ext", 'hash=' . generate_link_hash('global') . '&amp;mark=forums&amp;mark_time=' . time()) : '',
			'U_MCP'				=> ($this->auth->acl_get('m_') || $this->auth->acl_getf_global('m_')) ? append_sid("{$this->root_path}mcp.$this->php_ext", 'i=main&amp;mode=front', true, $this->user->data['user_id']) : '')
		);

		$page_title = ($this->config['board_index_text'] !== '') ? $this->config['board_index_text'] : $this->user->lang['INDEX'];

		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang('FORUM'),
			'U_VIEW_FORUM'	=> $this->helper->route('forum_page'),
		));
		
		/**
		* You can use this event to modify the page title and load data for the index
		*
		* @event core.index_modify_page_title
		* @var	string	page_title		Title of the index page
		* @since 3.1.0-a1
		*/
		$vars = array('page_title');
		extract($this->dispatcher->trigger_event('pgreca.pgsocial.core.index_modify_page_title', compact($vars)));

		// Output page
		page_header($page_title, true);
		$this->post_status->getStatus('all', $this->user->data['user_id'], 0, "all", "", "seguel", "");
		$this->template->set_filenames(array(
			'body' => 'index_body.html')
		);
		
		page_footer();
	}
}
