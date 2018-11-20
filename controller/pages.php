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

class pages
{
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
	* @param \pg_social\social\$social_photo $social_photo	 	
	* @param \pg_social\social\$social_tag $social_tag		
	* @param \pg_social\social\$social_page $social_page	  
	* @param \phpbb\template\template  $template
	* @param \phpbb\user				$user
	*/
	public function __construct($files_factory, $auth, $config, $db, $helper, $request, $pg_social_helper, $notifyhelper, $post_status, $social_zebra, $social_photo, $social_tag, $social_page, $template, $user, $root_path, $php_ext, $table_prefix)
	{
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
		$this->social_photo			= $social_photo;
		$this->social_tag			= $social_tag;
		$this->social_page			= $social_page;
		$this->template				= $template;
		$this->user					= $user;
	    $this->root_path			= $root_path;
		$this->php_ext				= $php_ext;	
        $this->table_prefix 		= $table_prefix;	
	}
	
	/**
	* Profile controller for route /page/{name}
	*
	* @param string		$name
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handlepage($name)
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
			$page_title = $this->user->lang['PAGES'];
	
			$sql = "SELECT p.*, (SELECT COUNT(*) 
							FROM ".$this->table_prefix."pg_social_pages_like as l
							WHERE l.page_id = p.page_id) AS countlike
			FROM ".$this->table_prefix."pg_social_pages as p
			WHERE p.page_username_clean = '".$name."'";
			$result = $this->db->sql_query($sql);
			$page = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			if($page && ($page['page_status'] == 1 || $page['page_founder'] == $this->user->data['user_id'] || $this->auth->acl_get('a_page_manage')))
			{	
				$page_title = $page['page_username'];
				if($page['page_status'] == 0) $page_alert = true; else $page_alert = false;
				
				if($page['page_status'] == 1) $page['page_act'] = true; else $page['page_act'] = false;
				if($page['page_founder'] == $this->user->data['user_id'] && $page['page_status'] == 1) $page['page_action'] = true;
				if($this->social_page->user_likePages($this->user->data['user_id'], $page['page_id']) == $page['page_id']) 
				{
					$page_likeCheck = "like"; 
					$page_likelang = $this->user->lang('LIKE', 2);
				}
				else 
				{
					$page_likeCheck = "dislike"; 
					$page_likelang = $this->user->lang('LIKE', 1); 
				}
					
				$this->template->assign_block_vars('page', array(
					'PAGE_ID'				=> $page['page_id'],
					'PAGE_ALERT'			=> $page_alert,
					'PAGE_ACTION'			=> $page['page_action'],
					'PAGE_AVATAR'			=> $this->pg_social_helper->social_avatar_page($page['page_avatar']),		     
					'PAGE_COVER'			=> $this->pg_social_helper->social_cover($page['page_cover']),
					'PAGE_URL'				=> $this->helper->route('pages_page', array('name' => $page['page_username_clean'])),
					'PAGE_USERNAME'			=> $page['page_username'],
					'PAGE_ABOUT_WE'			=> $page['page_about'],
					'PAGE_REGDATE'			=> $this->pg_social_helper->time_ago($page['page_regdate']),
					
					'PAGE_LIKE'				=> $page['countlike'],
					'PAGE_LIKE_CHECK'		=> $page_likeCheck."page",	
					'PAGE_LIKE_CHECKLANG'	=> $page_likelang,						
				));
				
				$this->template->assign_vars(array(
					'PG_SOCIAL_SIDEBAR_RIGHT'				=> $this->config['pg_social_sidebarRight'],	
					
					'STATUS_WHERE'				=> 'page',
					'PROFILE_ID'				=> $page['page_id'],
					'GALLERY_NAME'				=> $this->social_photo->gallery_info($this->request->variable('gall', ''))['gallery_name'],
				));
				$this->post_status->getStatus('page', $page['page_id'], 0, "all", "seguel", "");
				$this->social_photo->getPhotos(1, "last", $page['page_id']);
				$this->social_photo->getGallery($page['page_id'], "page");
				if($this->request->variable('gall', '')) $this->social_photo->getPhotos(1, "gall", $page['page_id'], $this->request->variable('gall', ''));				
			}
			else
			{				
				$mode = $this->request->variable('mode', '');
				
				switch($mode)
				{
					case 'page_new':
						return $this->social_page->pageCreate($this->request->variable('page_new_name', ''));
					break;
				}
				
				$sql = "SELECT *, (SELECT COUNT(*) FROM ".$this->table_prefix."pg_social_pages_like WHERE ".$this->table_prefix."pg_social_pages.page_id = ".$this->table_prefix."pg_social_pages_like.page_id) AS countlike FROM ".$this->table_prefix."pg_social_pages WHERE page_status = '1'";
				$result = $this->db->sql_query($sql);
				while($pages = $this->db->sql_fetchrow($result))
				{
					if($page['page_avatar'] != "") $page_avatar = 'upload/'.$page['page_avatar']; else $page_avatar = 'page_no_avatar.jpg';
					if($this->social_page->user_likePages($this->user->data['user_id'], $pages['page_id']) == $pages['page_id']) $page_likeCheck = "like"; else $page_likeCheck = "dislike";
					if($this->social_page->user_likePages($this->user->data['user_id'], $pages['page_id']) == $pages['page_id']) 
					{
						$page_likeCheck = "like"; 
						$page_like = $this->user->lang('LIKE', 2);
					}
					else 
					{
						$page_likeCheck = "dislike"; 
						$page_like = $this->user->lang('LIKE', 1); 
					}
					$this->template->assign_block_vars('pages', array(
						'PAGE_ID'				=> $pages['page_id'],
						'PAGE_AVATAR'			=> $this->pg_social_helper->social_avatar_page($pages['page_avatar']),	     
						'PAGE_COVER'			=> $this->pg_social_helper->social_cover($pages['page_cover']),
						'PAGE_COUNT_FOLLOWER'	=> $pages['countlike'],
						'PAGE_USERNAME'			=> $pages['page_username'],
						'PAGE_URL'				=> $this->helper->route('pages_page', array('name' => $pages['page_username_clean'])),
						'PAGE_REGDATE'			=> $page['page_regdate'],
						'PAGE_LIKE'				=> $page_like,
						'PAGE_LIKE_CHECK'		=> $page_likeCheck."page",
					));	
				}
				$this->template->assign_vars(array(
					'PG_SOCIAL_SIDEBAR_RIGHT'				=> $this->config['pg_social_sidebarRight'],	
					'PAGES'									=> true,
					'PAGE_CREATE'							=> $this->auth->acl_get('u_page_create') ? true : false,
					'PAGE_FORM'								=> append_sid($this->helper->route('pages_page'), 'mode=page_new'),
				));			
			}
			return $this->helper->render('pg_social_page.html', $page_title);
		}
	}
}
	