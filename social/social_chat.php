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

class social_chat
{
	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/** @var \pgreca\pgsocial\controller\helper */
	protected $pg_social_helper;

	/** @var \pgreca\pgsocial\social\social_zebra */
	protected $social_zebra;

	/* @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var string phpBB root path */
	protected $root_path;

	/** @var string */
	protected $pgsocial_chat;

	/**
	 * Constructor
	 *
	 * @param \phpbb\template\template				$template				Template object
	 * @param \phpbb\user							$user					uUser object
	 * @param \phpbb\controller\helper				$helper					Controller helper object
	 * @param \phpbb\config\config					$config					Config object
	 * @param \phpbb\db\driver\driver_interface		$db						Database object
	 * @param \pgreca\pgsocial\controller\helper	$pg_social_helper		PG Social helper
	 * @param \pgreca\pgsocial\social\social_zebra	$social_zebra			PG Social zebra
	 * @param string								$root_path				phpBB root path
	 * @param string								$pgsocial_table_chat	PG Social chat table
	 */
	public function __construct(
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\pgreca\pgsocial\controller\helper $pg_social_helper,
		social_zebra $social_zebra,
		$root_path,
		$pgsocial_table_chat
	)
	{
		$this->template			= $template;
		$this->user				= $user;
		$this->helper			= $helper;
		$this->config 			= $config;
		$this->db 				= $db;
		$this->pg_social_helper = $pg_social_helper;
		$this->social_zebra		= $social_zebra;
		$this->root_path		= $root_path;
		$this->pgsocial_chat 	= $pgsocial_table_chat;
	}

	/**
	 * @param string	$setting
	 * @param			$value		@todo Unused?
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function chat_setting($setting, $value)
	{
		$set = '';

		switch ($setting)
		{
			case 'pgsocial_setting_hide':
				$set = 'user_allow_viewonline';
			break;

			case 'pgsocial_setting_audio':
				$set = 'user_chat_music';
			break;
		}

		if (!empty($set))
		{
			$sql = 'UPDATE ' . USERS_TABLE . '
				SET ' . $set . ' = NOT ' . $set .  '
				WHERE user_id = ' . (int) $this->user->data['user_id'];
			$this->db->sql_query($sql);
		}

		$this->template->assign_vars(array(
										 'ACTION'	=> $set,
									 ));

		return $this->helper->render('activity_status_action.html', '');
	}

	/**
	 * Play audio notify for new message on chat
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function pgsocial_chat_check()
	{
		$sound = null;

		$message_check = $this->message_check();

		if (!empty($message_check) && !empty($this->user->data['user_chat_music']))
		{
			$sound = 'sound';
		}

		$this->template->assign_vars(array(
										 'ACTION'	=> $sound,
									 ));

		return $this->helper->render('activity_status_action.html', '');
	}

	/**
	 * Return people online on chat
	 *
	 * @param int	$person
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getchat_people($person)
	{
		if ($person != "")
		{
			$expression = $this->db->sql_like_expression('LIKE ' . $this->db->get_any_char() . $this->db->sql_escape($person) . $this->db->get_any_char());
			$sql_where = 'AND (u.username_clean ' . $expression . ' OR u.username ' . $expression . ')';
		}
		else
		{
			$sql_where = '';
		}

		$sql = 'SELECT user_id, username, username_clean, user_colour,
						user_avatar, user_avatar_type, user_avatar_width, user_avatar_height
				FROM ' . USERS_TABLE . '
				WHERE ' . $this->db->sql_in_set('user_type', array(USER_NORMAL, USER_FOUNDER)) . '
					AND user_id <> ' . (int) $this->user->data['user_id'] . $sql_where;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$zebra_row = $this->social_zebra->friend_status($row['user_id']);

			if (!empty($zebra_row) && $zebra_row['status'] === 'PG_SOCIAL_FRIENDS')
			{
				$this->template->assign_block_vars('pg_social_chat', array(
					'USER_ID'			=> $row['user_id'],
					'USER_USERNAME'		=> $row['username'],
					'USER_COLOR'		=> $row['user_colour'],
					'USER_AVATAR'		=> $this->pg_social_helper->social_avatar_thumb($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']),
					'USER_PROFILE'		=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),
					'USER_STATUS'		=> $this->pg_social_helper->social_status($row['user_id']),
					'USER_TMSG'			=> $this->message_check((int) $row['user_id']),
				));
			}
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars(array(
										 'CHAT_LOGIN'	=> $this->user->data['user_allow_viewonline'] ? false : true,
									 ));

		return $this->helper->render('pg_social_chatpeople.html', '');
	}

	/**
	 * Return info of user's chat
	 *
	 * @param int		$person
	 * @param string	$read
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getchat_person($person, $read)
	{
		$sql = 'SELECT user_id, username, username_clean, user_colour, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . (int) $person;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$this->template->assign_block_vars('pg_social_chat_person', array(
			'PROFILE_ID'						=> $row['user_id'],
			'PROFILE_URL'						=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),
			'PROFILE_STATUS'					=> $this->pg_social_helper->social_status($row['user_id']),
			'PROFILE_USERNAME'					=> $row['username'],
			'PROFILE_COLOUR'					=> "#".$row['user_colour'],
			'PROFILE_AVATAR'					=> $this->pg_social_helper->social_avatar_thumb($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']),
		));

		if ($read == 'read')
		{
			$this->message_read($person);
		}

		$this->getchat_message($person, 'seguel', 0);

		return $this->helper->render('pg_social_chatperson.html', '');
	}


	/**
	 * Message of chat
	 *
	 * @param int		$person
	 * @param string	$type
	 * @param int		$lastmessage
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getchat_message($person, $type, $lastmessage)
	{
		$this->message_read((int) $person);

		$sql_ary = array(
			'SELECT'	=> 'c.*, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height',

			'FROM'		=> array(
				$this->pgsocial_chat	=> 'c',
				USERS_TABLE				=> 'u',
			),

			'WHERE'		=> 'c.user_id = u.user_id
					AND (
						(c.user_id = ' . (int) $person . ' AND c.chat_member = ' . (int) $this->user->data['user_id'] . ')
						OR (c.user_id = ' . (int) $this->user->data['user_id'] . ' AND c.chat_member = ' . (int) $person . ')
					)',

			'ORDER_BY'	=> 'c.chat_time ' . ((empty($lastmessage) || ($type === 'prequel')) ? 'DESC' : 'ASC'),
		);

		if (!empty($lastmessage))
		{
			$sql_ary['WHERE'] .= ' AND c.chat_id ' . (($type === 'prequel') ? '<' : '>') . ' ' . (int) $lastmessage;
		}

		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query_limit($sql, 20);
		while($row = $this->db->sql_fetchrow($result))
		{
			$ifright = ($row['user_id'] == $this->user->data['user_id']) ? 1 : 0; //$this->message_read();

			$allow_bbcode = false; //$this->config['pg_social_bbcode'];
			$allow_urls = false; //$this->config['pg_social_url'];
			$allow_smilies = false; //$this->config['pg_social_smilies'];
			$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);

			$msg = generate_text_for_display(str_rot13($row['chat_text']), $row['bbcode_uid'], $row['bbcode_bitfield'], $flags);
			$msg .= $this->pg_social_helper->extra_text($row['chat_text']);

			// @todo Unused?
			$sound = (($person == $row['user_id']) && !empty($lastmessage) && ($this->user->data['user_chat_music'] == 1) && ($type ==='sequal')) ? 1 : 0;

			$this->template->assign_block_vars('pg_social_chat_message', array(
				'MESSAGE_ID'	=> $row['chat_id'],
				'IFRIGHT'		=> $ifright,
				'AVATAR'		=> $this->pg_social_helper->social_avatar_thumb($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']),
				'MESSAGE'		=> $msg,
				'TIME'			=> $row['chat_time'],
				'TIME_AGO'		=> $this->pg_social_helper->time_ago($row['chat_time']),
			));
		}
		$this->db->sql_freeresult($result);

		return $this->helper->render('pg_social_chatmessage.html', '');
	}

	/**
	 * Send new message on chat
	 *
	 * @param int		$person
	 * @param string	$message
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function message_send($person, $message)
	{
		$allow_bbcode = false; //$this->config['pg_social_bbcode'];
		$allow_urls = $this->config['pg_social_chat_message_url_enabled'];
		$allow_smilies = $this->config['pg_social_chat_smilies_enabled'];

		$message = urldecode($message);
		generate_text_for_storage($message, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);
		$message = str_replace('&amp;nbsp;', ' ', $message);

		$sql_arr = array(
			'user_id'			=> $this->user->data['user_id'],
			'chat_text'			=> str_rot13($message),
			'chat_time'			=> time(),
			'chat_member'		=> $person,
			'chat_status'		=> 0,
			'chat_read'			=> 0,
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid
		);

		$sql = 'INSERT INTO ' . $this->pgsocial_chat . ' '. $this->db->sql_build_array('INSERT', $sql_arr);
		$this->db->sql_query($sql);

		$this->template->assign_vars(array(
										 'ACTION'	=> 'message_send',
									 ));

		return $this->helper->render('activity_status_action.html', "ah");
	}

	/**
	 * Mark read message on chat's user
	 *
	 * @param int		$person
	 * @return void
	 */
	public function message_read($person)
	{
		$sql = 'UPDATE ' . $this->pgsocial_chat . ' SET ' . $this->db->sql_build_array('UPDATE', array(
				'chat_status'	=> true,
				'chat_read'		=> true,
			)) . '
				WHERE chat_member = ' . (int) $this->user->data['user_id'] . '
					AND user_id = ' . (int) $person;
		$this->db->sql_query($sql);
	}


	/**
	 * Check if has new message on chat
	 *
	 * @param int|null $user
	 * @return int
	 */
	public function message_check($user = null)
	{
		if (!empty($user))
		{
			$sql_where = 'chat_status = 1 AND chat_read = 0 AND user_id = ' . (int) $user;
		}
		else
		{
			$sql_where = 'chat_status = 0';
		}

		$message = 0;

		$sql = 'SELECT chat_id, user_id
				FROM ' . $this->pgsocial_chat . '
				WHERE chat_member = ' . (int) $this->user->data['user_id']. '
					AND ' . $sql_where . '
				ORDER BY chat_time DESC';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$message++;

			if (empty($user))
			{
				$sql = 'UPDATE ' . $this->pgsocial_chat . '
						SET chat_status = 1
						WHERE chat_id = ' . (int) $row['chat_id'] . '
							AND chat_member = ' . (int) $this->user->data['user_id'];
				$this->db->sql_query($sql);
			}
		}
		$this->db->sql_freeresult($result);

		return (int) $message;
	}
}
