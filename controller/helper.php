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

use \Symfony\Component\DependencyInjection\ContainerInterface;

class helper
{
	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/** @var \pgreca\pgsocial\controller\notifyhelper */
	protected $notifyhelper;

	/* @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\log\log */
	protected $log;

	/** @var ContainerInterface */
	protected $phpbb_container;

	/** @var \phpbb\event\dispatcher */
	protected $dispatcher;

	/* @var string phpBB root path */
	protected $root_path;

	/* @var string phpEx */
	protected $php_ext;

	/** @var string */
	protected $pgsocial_wallpostlike;

	/** @var string */
	protected $pgsocial_wallpostcomment;

	/** @var string */
	protected $pgsocial_photos;

	/** @var string */
	protected $pg_social_path;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth $auth
	 * @param \phpbb\user $user
	 * @param \phpbb\controller\helper $helper
	 * @param \pgreca\pgsocial\controller\notifyhelper $notifyhelper
	 * @param \phpbb\config\config $config
	 * @param \phpbb\db\driver\driver_interface	$db
	 * @param \phpbb\log\log $log
	 * @param ContainerInterface $phpbb_container
	 * @param \phpbb\event\dispatcher $dispatcher
	 * @param string $root_path
	 * @param string $php_ext
	 * @param string $pgsocial_table_wallpostlike
	 * @param string $pgsocial_table_wallpostcomment
	 * @param string $pgsocial_table_photos
	 */
	public function __construct($auth, $user, $helper, $notifyhelper, $config, $db, $log, $phpbb_container, $dispatcher, $root_path, $php_ext, $pgsocial_table_wallpostlike, $pgsocial_table_wallpostcomment, $pgsocial_table_photos)
	{
		$this->auth = $auth;
		$this->user = $user;
		$this->helper = $helper;
		$this->notifyhelper = $notifyhelper;
		$this->config = $config;
		$this->db = $db;
		$this->log = $log;
		$this->phpbb_container = $phpbb_container;
		$this->dispatcher			= $dispatcher;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->pgsocial_wallpostlike	= $pgsocial_table_wallpostlike;
		$this->pgsocial_wallpostcomment = $pgsocial_table_wallpostcomment;
		$this->pgsocial_photos = $pgsocial_table_photos;
		$this->pg_social_path = generate_board_url().'/ext/pgreca/pgsocial';
	}

	/* TIME AGO - FOR ACTIVITY AND MESSAGES CHAT */
	public function time_ago($from, $to = 0)
	{
		$periods = array(
			'SECOND',
			'MINUTE',
			'HOUR',
			'DAY',
			'WEEK',
			'MONTH',
			'YEAR',
			'DECADE',
		);
		$lengths = array(
			'60',
			'60',
			'24',
			'7',
			'4.35',
			'12',
			'10',
		);
		if ($to == 0)
		{
			$to = time();
		}
		if ($to > $from)
		{
			$difference = $to - $from;
			$tense = 'WALL_TIME_AGO';
		}
		else
		{
			$difference = $from - $to;
			$tense = 'WALL_TIME_FROM_NOW';
		}
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++)
		{
			$difference /= $lengths[$j];
		}
		$difference = round($difference);
		$period = $periods[$j];
		if ($difference != 1)
		{
			$period .= 'S';
		}
		if (($to - $from) > ((3600 - 1) / 60))
		{
			return sprintf($this->user->lang[$tense], $difference, $this->user->lang['WALL_TIME_PERIODS'][$period]);
		}
	}

	/* PRIVACY OF ACTIVITY */
	public function social_privacy($privacy)
	{
		switch($privacy)
		{
			case '1':
				$privacySet = 'FRIEND';
			break;
			case '2':
				$privacySet = 'ALL';
			break;
			default:
				$privacySet = 'ONLY_YOU';
			break;
		}
		return $privacySet;
	}

	/* COVER DEFAULT */
	public function social_cover($cover)
	{
		if ($cover == '')
		{
			$cover = $this->pg_social_path.'/images/no_cover.jpg';
		}
		else
		{
			$cover = $this->pg_social_path.'/images/upload/'.$cover;
		}
		return $cover;
	}

	public function social_avatar_page($avatar)
	{
		if ($avatar)
		{
			$avatar = 'upload/'.$avatar;
		}
		else
		{
			$avatar = 'page_no_avatar.jpg';
		}
		$url = generate_board_url().'/ext/pgreca/pgsocial/images/'.$avatar;
		$avatar = '<img class="avatar" src="'.$this->pg_social_path.'/images/transp.gif" style="background-image:url('.$url.')" />';
		return $avatar;
	}

	/* AVATAR THUMB ON SOCIAL */
	public function social_avatar_thumb($avatar, $avatar_type, $avatar_width, $avatar_height)
	{
		$data = array(
			'user_avatar'         => $avatar,
			'user_avatar_type'    => $avatar_type,
			'user_avatar_width'    => $avatar_width,
			'user_avatar_height'    => $avatar_height,
		);
		$core_avatar =  phpbb_get_user_avatar($data);
     	preg_match('#(src=")(.+?)(download|images)#', $core_avatar, $matches);

		if ($matches)
		{
			$core_avatar = preg_replace('#('.$matches[2].')#', $base_url = generate_board_url(). '/', $core_avatar, 1);
		}
		$core_avatar = str_replace('src="', 'src="'.$this->pg_social_path.'/images/transp.gif" style="background-image:url(', str_replace('" alt', ')" alt', preg_replace( '/(width|height)="\d*"\s/', "", $core_avatar)));

		$wall_avatar = '<img src="'.$this->pg_social_path.'/images/no_avatar.jpg" class="avatar" />';

		return ($core_avatar) ? $core_avatar : $wall_avatar;
    }

	/* GENDER OF USER */
	public function social_gender($gender)
	{
		switch($gender)
		{
			case 1:
				$return = 'GENDER_FEMALE';
			break;
			case 2:
				$return = 'GENDER_MALE';
			break;
			default:
				$return = 'GENDER_UNKNOWN';
			break;
		}
		return $return;
	}

	public function social_status_life($status)
	{
		switch($status)
		{
			case 1:
				$return = 'SOCIAL_STATUS_SINGLE';
			break;
			case 2:
				$return = 'SOCIAL_STATUS_ENGAGED';
			break;
			case 3:
				$return = 'SOCIAL_STATUS_MARRIED';
			break;
			case 4:
				$return = 'SOCIAL_STATUS_COMPLICATED';
			break;
			case 5:
				$return = 'SOCIAL_STATUS_RELATIONSHIP';
			break;
			case 0:
			default:
				$return = 'SOCIAL_STATUS_UNKNOW';
			break;
		}
		return $return;
	}

	/* RANK OF USER */
	public function social_rank($rank)
	{
		if ($rank)
		{
			$sql = 'SELECT * FROM '.RANKS_TABLE.' WHERE rank_id = "'.$rank.'"';
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			if ($row)
			{
				if ($row['rank_image'] != '')
				{
					$row['rank_image'] = '<img src="'.generate_board_url().'/images/ranks/'.$row['rank_image'].'" />';
				}
				return $row;
			}
			else
			{
				return false;
			}
		}
	}

	/* AGE OF USER */
	public function social_age($birth)
	{
			list($bday_day, $bday_month, $bday_year) = array_map('intval', explode('-', $birth));
			$now = $this->user->create_datetime();
			$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());

			$diff = $now['mon'] - $bday_month;
			if ($diff == 0)
			{
				$diff = ($now['mday'] - $bday_day < 0) ? 1 : 0;
			}
			else
			{
				$diff = ($diff < 0) ? 1 : 0;
			}
			$age = max(0, (int) ($now['year'] - $bday_year - $diff));
			return $age;
	}

	/* ONLINE STATUS OF USER */
	public function social_status($user)
	{
		$sql = "SELECT MAX(s.session_time) AS session_time, MIN(s.session_viewonline) AS session_viewonline, u.user_allow_viewonline
		FROM ".SESSIONS_TABLE." as s, ".USERS_TABLE." as u
		WHERE s.session_user_id = '".$user."' AND u.user_id = '".$user."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		if ($row['user_allow_viewonline'] == 0)
		{
			$online = 'offline';
		}
		else
		{
			if ($row['session_time'] == '')
			{
				$row['session_time'] = 0;
			}
			if ($row['session_viewonline'] == '')
			{
				$row['session_viewonline'] = 0;
			}
			$update_time = $this->config['load_online_time'] * 60;
			$online = (time() - $update_time < $row['session_time'] && (isset($row['session_viewonline']) || $this->auth->acl_get('u_viewonline'))) ? 'online' : 'offline';
		}

		return $online;
	}

	/* FIX PATCH OF SMILIES */
	public function social_smilies($text)
	{
		$text = str_replace('./../', generate_board_url().'/', $text);
		$text = str_replace('/..', '', $text);
		return $text;
	}

	/* COUNT LIKES OR COMMENTS OF ACTIVITY */
	public function count_action($action, $post)
	{
		switch($action)
		{
			case 'like':
				$sql = "SELECT COUNT(post_like_ID) AS count
				FROM ".$this->pgsocial_wallpostlike."
				WHERE post_ID = '".$post."'";
			break;
			case 'iflike':
				$sql = "SELECT COUNT(post_like_ID) AS count
				FROM ".$this->pgsocial_wallpostlike."
				WHERE post_ID = '".$post."' AND user_id = '".$this->user->data['user_id']."'";
			break;
			case 'comments':
				$sql = "SELECT COUNT(post_comment_ID) AS count
				FROM ".$this->pgsocial_wallpostcomment."
				WHERE post_ID = '".$post."'";
			break;
		}
		$result = $this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult($result);
		return $count;
	}

	/* COUNT PHOTOS OF USER */
	public function countPhotos($user)
	{
		$sql = "SELECT COUNT(photo_id) AS count
		FROM ".$this->pgsocial_photos."
		WHERE user_id = '".$user."'";
		$result = $this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult($result);
		return $count;
	}	

	public function noextra($text,  $tags = '')
	{
		preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
		$tags = array_unique($tags[1]);

		if (is_array($tags) AND count($tags) > 0)
		{
			return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
		}
		else
		{
			return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
		}
	}

	/* ADD LOG OF USER */
	public function log($user, $ip, $action, $id)
	{
		$this->log->add('user', $user, $ip, 'PG_SOCIAL_'.$action.'_LOG', time(), array('reportee_id' => $id));
	}

	public function pgblog_statusArticle($article)
	{
		if ($this->pgblog = $this->phpbb_container->get('pgreca.pgblog.controller'))
		{
			return $this->pgblog->pgblog_article($article);
		}

		return null;
	}

	public function pg_status_type($type, $extra, $msg, $author_action, $wshow, $block_vars)
	{
		$array = array();
		$vars = array('type', 'extra', 'msg', 'author_action', 'wshow', 'block_vars');
		extract($this->dispatcher->trigger_event('pgreca.pgsocial.statustype', compact($vars)));
		$temp = array(
			'type'						=> $type,
			'extra'						=> $extra,
			'msg'							=> $msg,
			'author_action'		=> $author_action,
			'wshow'						=> $wshow,
			'block_vars'			=> $block_vars
		);
		$array = $temp;
		return $array;
	}
	
	/**
	 * Return array for save text in database
	 *
	 * @param string $message
	 * @return array
	 */
	public function pgMessage($message)
	{
		$options = 0;
		$uid = $bitfield = $allow_img = $allow_flash = $allow_quote = $allow_url = $allow_bbcode = '';
		
		if ($message)
		{
			$allow_bbcode = $this->config['pg_social_bbcode'];
			$allow_urls = $this->config['pg_social_url'];
			$allow_smilies = $this->config['pg_social_smilies'];
		
			generate_text_for_storage($message, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies, $allow_img, $allow_flash, $allow_quote, $allow_url);
			$message = str_replace("'", '&#39;', $message);
		}
		
		return array(
			'message'			=> $message,
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid,
			'bbcode_options'	=> $options,
		);
	}
}
