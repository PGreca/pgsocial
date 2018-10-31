<?php
/**
*
* Social extension for the phpBB Forum Software package.
*
* @copyright (c) 2017 Antonio PGreca (PGreca)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace pgreca\pgsocial\event;

use phpbb\template\template;
use phpbb\user;
use phpbb\db\driver\driver_interface as db_driver;
use phpbb\auth\auth;
use phpbb\request\request;
use phpbb\controller\helper;
use phpbb\config\db;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 *
 * @package pgreca/pgsocial
 */
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\template\template */
	protected $template;
	
	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\config\db */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;
	
	/* @var string phpBB root path */
	protected $root_path;	
	
	/** @var core.php_ext */
	protected $php_ext;

	/** @var string $table_prefix */
	protected $table_prefix;
	
	/** @var ContainerInterface */
	protected $phpbb_container;

	protected $is_startpage = false;
	
	/**
	 * Constructor
	 *
	 * @param template		$template
	 * @param user			$user
	 * @param db_driver		$db
	 * @param auth			$auth
	 * @param request		$request
	 * @param helper		$helper
	 * @param db			$config
	 * @param string		$root_path
	 * @param string		$php_ext
	 * @param $social_helper $social_helper
	 * @param $social_photo $social_photo	
	 * @param $social_zebra $social_zebra	  
	 * @param string		$table_prefix
	 */
	
	public function __construct(template $template, user $user, db_driver $db, auth $auth, request $request,
	helper $helper, db $config, $root_path, $php_ext, $social_helper, $post_status, $social_photo, $social_zebra, $table_prefix, $phpbb_container)
	{
		$this->template				= $template;
		$this->user					= $user;
		$this->db					= $db;
		$this->auth					= $auth;
		$this->request				= $request;
		$this->helper				= $helper;
		$this->config				= $config;
		$this->root_path			= $root_path;
		$this->php_ext				= $php_ext;
		$this->pgsocial_helper		= $social_helper;
		$this->post_status			= $post_status;
		$this->social_photo			= $social_photo;
		$this->social_zebra			= $social_zebra;
        $this->table_prefix 		= $table_prefix;	
		$this->phpbb_container 		= $phpbb_container;
		
		$this->is_phpbb31	= phpbb_version_compare($config['version'], '3.1.0@dev', '>=') && phpbb_version_compare($config['version'], '3.2.0@dev', '<');
		$this->is_phpbb32	= phpbb_version_compare($config['version'], '3.2.0@dev', '>=') && phpbb_version_compare($config['version'], '3.3.0@dev', '<');

		$this->template->assign_vars(array(
			'IS_PHPBB31' => $this->is_phpbb31,
			'IS_PHPBB32' => $this->is_phpbb32,
		));
	}

	/**
	 * Decides what listener to use
	 *
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'								=> 'load_language_on_setup',
			'core.permissions'								=> 'add_permission',			
			'core.viewonline_overwrite_location'			=> 'add_viewonline_location',
			'core.display_forums_modify_sql'				=> 'set_startpage',
			
			'core.memberlist_view_profile'	    			=> 'memberlist_view_profile',
			'core.page_header'								=> 'add_page_links',
			'core.page_footer'								=> 'load',
			
			'core.submit_post_end'							=> 'user_status_post',
			'core.ucp_profile_modify_profile_info'			=> 'user_profile',
			'core.ucp_profile_validate_profile_info'		=> 'user_profile_validate',
			'core.ucp_profile_info_modify_sql_ary'			=> 'user_profile_sql',
			'core.avatar_driver_upload_move_file_before'	=> 'user_avatar_change',
		);
	}
	
	/**
	 * Load language for PG Social Network 
	*/
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'pgreca/pgsocial',
			'lang_set' => 'lang',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}	
	
	/**
	 * Add permissions for PG Social Network
	 *
	 * @param \phpbb\event\data $event The event object
	*/
	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$categories = $event['categories'];

		$categories['pg_social'] = 'ACL_CAT_PG_SOCIAL';

		$permissions['u_page_create'] = array('lang' => 'ACL_U_PAGE_CREATE', 'cat' => 'pg_social');
		$permissions['m_page_manage'] = array('lang' => 'ACL_M_PAGE_MANAGE', 'cat' => 'pg_social');
		$permissions['a_page_manage'] = array('lang' => 'ACL_A_PAGE_MANAGE', 'cat' => 'pg_social');

		$event['categories'] = $categories;
		$event['permissions'] = $permissions;
	}
		
	/**
	 * Remove forumlist from index and replace with Social
	 */
	public function set_startpage($event)
	{
		if($this->user->page['page_name'] == 'index.' . $this->php_ext && !$this->is_startpage && $this->user->data['user_id'] != ANONYMOUS)
		{
			if($this->config['pg_social_index_replace'])
			{
				$this->is_startpage = true;
				$sql_ary = $event['sql_ary'];

				$sql_ary['WHERE'] .= ($sql_ary['WHERE']) ? ' AND ' : '';
				$sql_ary['WHERE'] .= 'f.forum_id = 0';

				$event['sql_ary'] = $sql_ary;
				
				$controller_object = $this->get_startpage_controller();
				if($controller_object)
				{
					$controller_dir = explode('\\', get_class($controller_object));
					$controller_style_dir = 'ext/' . $controller_dir[0] . '/' . $controller_dir[1] . '/styles';
					$this->template->set_style(array($controller_style_dir, 'styles'));

					/** @type \Symfony\Component\HttpFoundation\Response $response */
					$response = call_user_func_array(array($controller_object, "handle"), explode('/', "mp"));
					$response->send();
					exit_handler();
				}
			}
		}
	}
	
	/**
	 * @return object|false
	 */
	protected function get_startpage_controller()
	{
		if($this->phpbb_container->has("pgreca.pgsocial.controller"))
		{
			$controller_object = $this->phpbb_container->get("pgreca.pgsocial.controller");
			$method = "handle";

			if(is_callable(array($controller_object, $method)))
			{
				return $controller_object;
			}
		}
	}
	
	/**
	 * @param \phpbb\event\data $event
	 */
	public function add_viewonline_location(\phpbb\event\data $event)
	{
		if($event['on_page'][1] == 'app' && strrpos($event['row']['session_page'], 'app.' . $this->php_ext . '/forum') === 0)
		{
			$event['location'] = $this->user->lang('FORUM_INDEX');
			$event['location_url'] = $this->phpbb_container->get('controller.helper')->route('forum_page');
		}
	}
	
	/**
	 * New look for memberlist_view_profile
	*/
	public function memberlist_view_profile($event)
	{
		if($this->config['pg_social_profile'])
		{
			$member = $event['member'];
			$user_id = $member['user_id'];
					
			if($user_id == $this->user->data['user_id']) $member['user_action'] = true; else $member['user_action'] = false;
			if($member['user_gender'] == 0) $profile_gender = ""; else $profile_gender = $this->pgsocial_helper->social_gender($member['user_gender']);
			$friends = $this->social_zebra->friendStatus($user_id);
			if($friends['status'] == "PG_SOCIAL_FRIENDS" || $user_id == $this->user->data['user_id']) $member['status_action'] = 1;	 		
					
			$this->template->assign_vars(array(
				'PG_SOCIAL_SIDEBAR_RIGHT'	=> $this->config['pg_social_sidebarRight'],		
				'PG_SOCIAL_PROFILE'			=> $this->config['pg_social_profile'],
				
				'PROFILE_ACTION'			=> $member['user_action'],
				'PROFILE_FRIEND_ACTION'		=> $friends['status'],
				'PROFILE_FRIEND_ACT_ICON'	=> $friends['icon'],	
				'PROFILE_STATUS_ACTION'		=> $member['status_action'],
				
				'PROFILE_ID'				=> $user_id,
				'PROFILE_UPDATE'			=> append_sid($this->root_path."ucp.".$this->php_ext, 'i=ucp_profile&mode=profile_info'),
				'PROFILE_COVER'				=> $this->pgsocial_helper->social_cover($member['user_pg_social_cover']),
				'PROFILE_COVER_POSITION'	=> $member['user_pg_social_cover_position'],
				'PROFILE_AVATAR'			=> $this->pgsocial_helper->social_avatar($member['user_avatar'], $member['user_avatar_type']),	
				'PROFILE_AVATAR_THUMB'		=> $this->pgsocial_helper->social_avatar_thumb($member['user_avatar'], $member['user_avatar_type']),	       
				'PROFILE_AVATAR_UPDATE'     => append_sid($this->root_path."ucp.".$this->php_ext, 'i=profile&mode=avatar'),
				'PROFILE_USERNAME'			=>$member['username'],
				'PROFILE_COLOUR'			=> "#".$member['user_colour'],
				'PROFILE_QUOTE'				=> $member['user_quote'],
				'PROFILE_GENDER'			=> $this->user->lang($profile_gender),
				'PROFILE_COUNT_FRIENDS'		=> $this->social_zebra->countFriends($user_id),
				
				'GALLERY_NAME'				=> $this->social_photo->gallery_info($this->request->variable('gall', ''))['gallery_name'],
				
				'SOCIAL_PROFILE_PATH'		=> $this->helper->route('profile_page'),
				'STATUS_WHERE'				=> 'profile',
			));
			
			$this->post_status->getStatus("profile", $user_id, 0, "profile", "seguel", "");
			$this->social_zebra->getFriends($user_id, "profile", "yes");
			$this->social_photo->getGallery($user_id, "profile");
			if($this->request->variable('gall', '')) $this->social_photo->getPhotos(0, $user_id, $this->request->variable('gall', ''));
		}	
	}
		
	public function load($event)
	{
		$this->template->assign_vars(array(				
			'PROFILE'					=> $this->user->data['user_id'],			
			'PG_SOCIAL_CHAT'			=> $this->config['pg_social_chat_enabled'] ? true : false,	
			'PG_SOCIAL_INDEX_REPLACE'	=> $this->config['pg_social_index_replace'] ? true : false,
			'PG_SOCIAL_INDEX_ACTIVITY'	=> $this->config['pg_social_index_activity'] ? true : false,
		));	
		
		if($this->is_startpage) $this->template->destroy_block_vars('navlinks');
	}
	
	public function add_page_links($event)
	{
		if($this->config['pg_social_index_replace']) $forumnav = $this->helper->route('forum_page');
		$this->template->assign_vars(array(
			'S_PG_SOCIAL_ENABLED' 	=> $this->config['pg_social_enabled'] ? true : false,
			'PG_SOCIAL_COLOR' 		=> $this->config['pg_social_color'],
			'ACTIVITY_PAGE'	     	=> $this->helper->route('profile_page'),	
			'ACTIVITY_PAGE_NAV'	    => $this->helper->route('profile_page'),
			'SOCIAL_FORUM'			=> $forumnav,
		));
		
		if($this->request->is_set('f') && $this->config['pg_social_index_replace'])
		{
			$this->template->alter_block_array('navlinks', array(
				'FORUM_NAME'	=> $this->user->lang('FORUM'),
				'U_VIEW_FORUM' 	=> $forumnav,
			));
		}	
	}	
	
	/**
	* Allow users to change their gender
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function user_profile($event)
	{
		if(DEFINED('IN_ADMIN'))
		{
			$user_quote = $event['user_row']['user_quote'];
			$user_gender = $event['user_row']['user_gender'];
		}
		else
		{
			$user_quote = $this->user->data['user_quote'];
			$user_gender = $this->user->data['user_gender'];
		}
		// Request the user option vars and add them to the data array
		$event['data'] = array_merge($event['data'], array(
			'user_quote'	=> $this->request->variable('user_quote', $user_quote),
			'user_gender'	=> $this->request->variable('user_gender', $user_gender),
		));

		$this->template->assign_vars(array(
			'QUOTE'				=> $user_quote,
			'PROFILE_GENDER'	=> $user_gender,
		));
	}
	
	/**
	* Validate users changes to their gender
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function user_profile_validate($event)
	{
		$array = $event['error'];
		//ensure gender is validated
		$validate_array = array(
			'user_quote'	=> array('string', true, 0, 255),
			'user_gender'	=> array('num', true, 0, 99),
		);
		$error = validate_data($event['data'], $validate_array);
		$event['error'] = array_merge($array, $error);
	}

	/**
	* User changed their gender so update the database
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function user_profile_sql($event)
	{
		$event['sql_ary'] = array_merge($event['sql_ary'], array(
			'user_quote'	=> $event['data']['user_quote'],
			'user_gender'	=> $event['data']['user_gender'],
		));
	}
	
	/** 
	 * Activity for new avatar
	*/
	public function user_avatar_change($event)
	{
		$photo = array();
		$photo['name'] = $event['filedata']['real_filename'];
		$photo['size'] = $event['filedata']['filesize'];
		$photo['tmp_name'] = $event['filedata']['filename'];
		$photo['type'] = $event['filedata']['mimetype'];
		
		$this->social_photo->photoUpload("", $this->user->data['user_id'], "", "avatar", "profile", $photo);
	}
	
	/**
	 * Activity for new post in topic_id
	*/
	public function user_status_post($event)
	{
		$info = $event['data'];
		$this->post_status->addStatus('post', $this->user->data['user_id'], '', 2, 4, $info['topic_id']."#p".$info['post_id']); 
	}		
}
