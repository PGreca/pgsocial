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

class helper
{
	/* @var \phpbb\auth\auth */
	protected $auth;
	
	/* @var \phpbb\user */
	protected $user;
	
	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\config\config */
	protected $config;
	
	/* @var \phpbb\log\log */
	protected $log;
	
	/* @var string phpBB root path */
	protected $root_path;	
	
	/* @var string phpEx */
	protected $php_ext;	

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth			$auth
	* @param \phpbb\user				$user	 
	* @param \phpbb\controller\helper		$helper	
	* @param \pg_social\\controller\helper $pg_social_helper	
	* @param \phpbb\config\config			$config	
	* @param \phpbb\db\driver\driver_interface	$db	 
	* @param \phpbb\log\log              $log
	*/
	
	public function __construct($auth, $user, $helper, $notifyhelper, $config, $db, $log, $root_path, $php_ext, $table_prefix)
	{
		$this->auth = $auth;
	    $this->user = $user;
		$this->helper = $helper;
		$this->notifyhelper = $notifyhelper;
		$this->config = $config;
		$this->db = $db;
		$this->log = $log;
	    $this->root_path = $root_path;	
		$this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;
	    $this->pg_social_path = $this->root_path.'/ext/pgreca/pgsocial';	
	}
	
	/* TIME AGO - FOR ACTIVITY AND MESSAGES CHAT */
	public function time_ago($from, $to = 0)
	{
		$periods = array(
			"SECOND",
			"MINUTE",
			"HOUR",
			"DAY",
			"WEEK",
			"MONTH",
			"YEAR",
			"DECADE",
		);
		$lengths = array(
			"60",
			"60",
			"24",
			"7",
			"4.35",
			"12",
			"10",
		);
		if($to == 0)
		{
			$to = time();
		}

		if($to > $from)
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
		if($difference != 1)
		{
			$period .= "S";
		}

		return sprintf($this->user->lang[$tense], $difference, $this->user->lang['WALL_TIME_PERIODS'][$period]);
	} 	
	
	/* PRIVACY OF ACTIVITY */
	public function social_privacy($privacy)
	{
		switch($privacy)
		{
			case '1':
				$privacySet = "FRIENDS";
			break;
			case '2':
				$privacySet = "ALL";
			break;
			default:
				$privacySet = "ONLY_YOU";
			break;
		}
		return $privacySet;
	}
	
	/* COVER DEFAULT */
	public function social_cover($cover)
	{
		if($cover == "")
		{
			$cover = $this->pg_social_path."/images/no_cover.jpg"; 
		}
		else
		{
			$cover = $this->pg_social_path."/images/upload/".$cover; 
		}
		return $cover;		
	}
	
	/* AVATAR DEFAULT ON SOCIAL */
	public function social_avatar($avatar, $avatar_type)
	{
		$data = array(
			"user_avatar"         => $avatar,
			"user_avatar_type"    => $avatar_type,
		);
			
		$core_avatar =  phpbb_get_user_avatar($data);
     	preg_match('#(src=")(.+?)(download|images)#', $core_avatar, $matches);
		 
		if($matches)
		{		
			$core_avatar = preg_replace('#('.$matches[2].')#', $base_url = generate_board_url(). '/', $core_avatar, 1);
		}
      
		$wall_avatar = '<img src="'.$this->pg_social_path.'/images/no_avatar.jpg" class="avatar" />';
		return ($core_avatar) ? $core_avatar : $wall_avatar;
    }	
	
	/* AVATAR THUMB ON SOCIAL */
	public function social_avatar_thumb($avatar, $avatar_type)
	{
		$data = array(
			"user_avatar"         => $avatar,
			"user_avatar_type"    => $avatar_type,
		);
			
		$core_avatar =  phpbb_get_user_avatar($data);
     	preg_match('#(src=")(.+?)(download|images)#', $core_avatar, $matches);
		 
		$core_avatar = str_replace('" alt', ')" src="'.$this->pg_social_path.'/images/transp.gif" alt', str_replace("src=\"", 'style="background-image:url(', $core_avatar));
		 
		if($matches)
		{		
			$core_avatar = preg_replace('#('.$matches[2].')#', $base_url = generate_board_url(). '/', $core_avatar, 1);
		}
      
		$wall_avatar = '<img src="'.$this->pg_social_path.'/images/no_avatar.jpg" class="avatar" />';
		return ($core_avatar) ? $core_avatar : $wall_avatar;
    }	
	
	/* GENDER OF USER */
	public function social_gender($gender)
	{
		switch($gender)
		{
			case 1:
				$return = "GENDER_FEMALE";
			break;
			case 2:
				$return = "GENDER_MALE";
			break;
			default: 
				$return = "GENDER_UNKNOWN";
			break;
		}
		return $return;
	}
	
	/* RANK OF USER */
	public function social_rank($rank)
	{
		if($rank)
		{
			$sql = "SELECT * FROM ".RANKS_TABLE." WHERE rank_id = '".$rank."'";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			
			$row['rank_image'] = '<img src="'.generate_board_url().'/images/ranks/'.$row['rank_image'].'" />';
			if($row['rank_image']) return $row;
		}
		else
		{
			return false;
		}
	}
	
	/* AGE OF USER */
	public function social_age($birth)
	{
			list($bday_day, $bday_month, $bday_year) = array_map('intval', explode('-', $birth));
			$now = $this->user->create_datetime();
			$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());

			$diff = $now['mon'] - $bday_month;
			if($diff == 0) $diff = ($now['mday'] - $bday_day < 0) ? 1 : 0; else $diff = ($diff < 0) ? 1 : 0;

			$age = max(0, (int) ($now['year'] - $bday_year - $diff));
			return $age;
	}
	
	/* ONLINE STATUS OF USER */
	public function social_status($user)
	{
		$sql = "SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
		FROM ".SESSIONS_TABLE." as s
		WHERE session_user_id = '".$user."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		
		
		if($row['session_time'] == '') $row['session_time'] = 0;
		if($row['session_viewonline'] == '') $row['session_viewonline'] = 0;
		$update_time = $this->config['load_online_time'] * 60;
		$online = (time() - $update_time < $row['session_time'] && ((isset($row['session_viewonline']) && $row['session_viewonline']) || $this->auth->acl_get('u_viewonline'))) ? "online" : "offline";
			
		return $online;		
	}
	
	/* FIX PATCH OF SMILIES */
	public function social_smilies($text)
	{
		$text = str_replace("./../", generate_board_url()."/", $text);
		$text = str_replace("/..", "", $text);
		return $text;		
	}
	
	/* COUNT LIKES OR COMMENTS OF ACTIVITY */
	public function countAction($action, $post)
	{
		$user_id = (int) $this->user->data['user_id'];
		switch($action)
		{
			case 'like':
				$sql = "SELECT COUNT(post_like_ID) AS count
				FROM ".$this->table_prefix."pg_social_wall_like
				WHERE post_ID = '".$post."'";
			break;
			case 'iflike':
				$sql = "SELECT COUNT(post_like_ID) AS count
				FROM ".$this->table_prefix."pg_social_wall_like
				WHERE post_ID = '".$post."' AND user_id = '".$user_id."'";
			break;
			case 'comments':
				$sql = "SELECT COUNT(post_comment_ID) AS count 
				FROM ".$this->table_prefix."pg_social_wall_comment
				WHERE post_ID = '".$post."'";
			break;
		}
		$result = $this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('count');
		$return = $count;
		return $return;
	}
	
	/* EXTRA OF ACTIVITY */
	public function extraText($text)
	{
		$a = $this->youtube($text);
		return $a;
	}
	
	/* PLAYER YOUTUBE FOR ACTIVITY OR MESSAGES CHAT */
	public function youtube($text)
	{                        
		if(strstr($text, 'youtube.com/watch?v=') !== false)
		{
			$domain = strstr($text, 'youtube.com/watch?v=');
			$domain = str_replace("youtube.com/watch?v=", "", $domain);
			$domain = explode('&', $domain);
			$youtube = '<p class="post_status_youtube"><iframe src="https://www.youtube.com/embed/'.$domain[0].'" allowfullscreen></iframe>
			</p>';
			if($youtube) return $youtube;
		}
	}
	
	/* ADD LOG OF USER */
	public function log($user, $ip, $action, $id)
	{
		$this->log->add('user', $user, $ip, 'PG_SOCIAL_'.$action.'_LOG', time(), array($id));
	}

}
?>