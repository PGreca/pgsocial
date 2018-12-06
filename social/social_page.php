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

class social_page
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
	
	public function __construct($template, $user, $helper, $pg_social_helper, $notifyhelper, $social_photo, $social_tag, $social_zebra, $config, $db, $root_path, $php_ext, $table_prefix)
	{
		$this->template					= $template;
		$this->user						= $user;
		$this->helper					= $helper;
		$this->pg_social_helper 		= $pg_social_helper;
		$this->notify 					= $notifyhelper;
		$this->social_photo				= $social_photo;
		$this->social_tag				= $social_tag;
		$this->social_zebra				= $social_zebra;
		$this->config 					= $config;
		$this->db 						= $db;	
	    $this->root_path				= $root_path;	
		$this->php_ext 					= $php_ext;
        $this->table_prefix 			= $table_prefix;
	}
		
	/**
	 * Create new page
	*/
	public function pageCreate($page_name, $page_category = 0)
	{
		$permalink = preg_replace("/[^a-zA-Z]/", "", strtolower(str_replace(' ', '_', $page_name)).rand(0, 1000));
		$sql_arr = array(
			'page_type'				=> 0,
			'page_status'			=> 0,
			'page_founder'			=> $this->user->data['user_id'],
			'page_regdate'			=> time(),
			'page_username'			=> $page_name,
			'page_username_clean'	=> $permalink,
			'page_avatar'			=> '',
			'page_cover'			=> '',
			'page_cover_position'	=> '',
			'page_about'			=> '',
		);
		$sql = "INSERT INTO ".$this->table_prefix.'pg_social_pages'.$this->db->sql_build_array('INSERT', $sql_arr);
		if($this->db->sql_query($sql))
		{
			redirect($this->helper->route('pages_page', array("name" => $permalink)));
		}
		$this->template->assign_vars(array(
			"ACTION"	=> $sql,
		));
		return $this->helper->render('activity_status_action.html', "");
	}
	
	/**
	 * Count likes pages
	*/
	public function user_likePages($user, $page = false)
	{		
		if(isset($page)) 
		{
			$where = " AND page_id = '".$page."'"; 
		}
		else 
		{
			$array = array();
		}
		$sql = "SELECT page_id FROM ".$this->table_prefix."pg_social_pages_like WHERE user_id = '".$user."'".$where;
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result))
		{	
			if(isset($page) && $row['page_id'] != "") 
			{
				$array = $row['page_id']; 
			}
			else 
			{
				$array[] = $row['page_id'];
			}
		}
		if(!isset($array)) $array = 0;
		return $array;
	}
	
	/**
	 * Action like on page
	*/
	public function pagelikeAction($page)
	{
		$sql = "SELECT page_like_ID FROM ".$this->table_prefix."pg_social_pages_like WHERE page_id = '".$page."' AND user_id = '".$this->user->data['user_id']."'";
		$result = $this->db->sql_query($sql);
		$like = $this->db->sql_fetchrow($result);
		
		if($like['page_like_ID'] != "")
		{
			$sql = "DELETE FROM ".$this->table_prefix."pg_social_pages_like WHERE page_id = '".$page."' AND user_id = '".$this->user->data['user_id']."'";
			$action = "dislikepage";
		}
		else
		{
			$sql_arr = array(
				'page_id'			=> $page,
				'user_id'			=> $this->user->data['user_id'],
				'page_like_time'	=> time(),
			);
			$sql = "INSERT INTO ".$this->table_prefix.'pg_social_pages_like'.$this->db->sql_build_array('INSERT', $sql_arr);
			$action = "likepage";
		}		
		$this->db->sql_query($sql);
		$this->template->assign_vars(array(
			"ACTION"	=> $action,
		));
		return $this->helper->render('activity_status_action.html', "");
	}
	
	public function pageLikeif($user, $template, $if = false)
	{
		$sql = "SELECT p.* FROM ".$this->table_prefix."pg_social_pages p
		LEFT JOIN ".$this->table_prefix."pg_social_pages_like l ON p.page_id = l.page_id WHERE l.page_id IS NULL and p.page_status = '1'";
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars($template, array(
				"PAGE_ID"		=> $row['page_id'],
				"AVATAR"		=> $this->pg_social_helper->social_avatar_page($row['page_avatar']),
				"PROFILE_URL"	=> append_sid($this->helper->route('pages_page'), 'u='.$row['page_username_clean']),
				"USERNAME"		=> $row['page_username'],
			
			));
		}		
	}
	
	public function approPages()
	{
		$sql = "SELECT COUNT(page_id) AS count
		FROM ".$this->table_prefix."pg_social_pages WHERE page_status = '0'";
		$result = $this->db->sql_query($sql);
		$count = $this->db->sql_fetchrow($result);
		return $count['count'];
	}
}
