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

class pages {
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
	* @param \phpbb\template\template  $template
	* @param \phpbb\user				$user
	*/
	public function __construct($files_factory, $auth, $config, $db, $helper, $request, $pg_social_helper, $notifyhelper, $post_status, $social_zebra, $social_photo, $social_tag, $template, $user, $root_path, $php_ext, $table_prefix) {
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
		$sql = "SELECT * FROM ".$this->table_prefix."pg_social_pages WHERE page_username_clean = '".$name."'";
		$result = $this->db->sql_query($sql);
		$page = $this->db->sql_fetchrow($result);
		
		if($page) {			
			$this->template->assign_block_vars('page', array(
				'PAGE_NAME'		=> $page['page_name'],
			));
		} else {
			$mode = $this->request->variable('mode', '');
			
			switch($mode) {
				case 'page_new':
					return "miao";
				break;
			}
			$this->template->assign_vars(array(
				'PAGES_NEW_URL'		=> append_sid($this->helper->route('pages_page'), 'mode=page_new'),
			));
		}
		return $this->helper->render('pg_social_page.html', $row['username']);	
	}
	
	
}
	