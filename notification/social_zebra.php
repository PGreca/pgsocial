<?php
/**
*
* Social extension for the phpBB Forum Software package.
*
* @copyright (c) 2017 Antonio PGreca (PGreca)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace pgreca\pgsocial\notification;

use phpbb\notification\type\base;
/**
*
* @package notifications
*/
class social_zebra extends base
{

	/* @var \phpbb\controller\helper */
	protected $helper;

	protected $pg_social_helper;

	/* @var \phpbb\user */
	protected $user;

	/**
	* Notification Type Boardrules Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\cache\driver\driver_interface $cache
	* @param \phpbb\user $user
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\controller\helper $helper
	* @param \pgreca\pgsocial\controller\helper $pg_social_helper
	* @param string $phpbb_root_path
	* @param string $php_ext
	* @param string $notification_types_table
	* @param string $notifications_table
	* @param string $user_notifications_table
	* @return \phpbb\notification\type\base
	*/
	public function __construct($db, $cache, $user, $auth, $config, $helper, $pg_social_helper, $phpbb_root_path, $php_ext, $notification_types_table, $notifications_table, $user_notifications_table)
	{
		$this->db 						= $db;
		$this->cache 					= $cache;
		$this->user 					= $user;
		$this->auth 					= $auth;
		$this->config 					= $config;
		$this->helper 					= $helper;
		$this->pg_social_helper 		= $pg_social_helper;
		$this->phpbb_root_path 			= $phpbb_root_path;
		$this->php_ext 					= $php_ext;

		$this->notification_types_table = $notification_types_table;
		$this->notifications_table 		= $notifications_table;
		$this->user_notifications_table = $user_notifications_table;
	}

	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'pgreca.pgsocial.notification.type.social_zebra';
	}

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_SOCIAL_ZEBRA',
		'group'	=> 'NOTIFICATION_PG_SOCIAL',
	);

	/**
	* Is this type available to the current user (defines whether or not it will be shown in the UCP Edit notification options)
	*
	* @return bool True/False whether or not this is available to the user
	*/
	public function is_available()
	{
		return true;
	}

	/**
	 * Get item id
	 *
	 * @param $data
	 * @return int
	 * @access public
	 */
	public static function get_item_id($data)
	{
		return $data['status_id'];
	}

	/**
	 * Get item's parent id
	 *
	 * @param $data
	 * @return int
	 * @access public
	 */
	public static function get_item_parent_id($data)
	{
		// No parent
		return $data['status_id'];
	}

	/**
	* Find the users who will receive notifications
	*
	* @param array $data The data for the updated rules
	*
	* @return array
	*/
	public function find_users_for_notification($data, $options = array())
	{
		$users = array();
		$users[$data['user_id']] = $this->notification_manager->get_default_methods();

		return $users;
	}

	/**
	* Users needed to query before this notification can be displayed
	*
	* @return array Array of user_ids
	*/
	public function users_to_query()
	{
		return array();
	}

	/**
	* Get the user's avatar
	*/
	public function get_avatar()
	{
		$sql = "SELECT user_avatar, user_avatar_type FROM ".USERS_TABLE." WHERE user_id = '".$this->get_data('poster_id')."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$user_avatar = $this->pg_social_helper->social_avatar($row['user_avatar'], $row['user_avatar_type']);

		return $user_avatar;
	}

	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		$sql = "SELECT username, user_colour FROM ".USERS_TABLE." WHERE user_id = '".$this->get_data('poster_id')."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		return $this->user->lang('HAS_TAGGED_YOU', '<span style="color:#'.$row['user_colour'].'">'.$row['username'].'</span>');
	}

	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		$sql = "SELECT user_id, username, user_colour FROM ".USERS_TABLE." WHERE user_id = '".$this->get_data('user_id')."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		return $this->helper->route("status_page", array("id" => $this->get_data('status_id')));
	}

	/**
	* Get email template
	*
	* @return string|bool
	*/
	public function get_email_template()
	{
		return false;
	}

	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
      return array();
	}

	/**
   * Get the HTML formatted reference of the notification
   *
   * @return string
   */

	/**
	* Function for preparing the data for insertion in an SQL query
	* (The service handles insertion)
	*
	* @param array $data The data for the updated rules
	* @param array $pre_create_data Data from pre_create_insert_array()
	*
	* @return array Array of data ready to be inserted into the database
	*/
	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('user_id', $data['user_id']);
		$this->set_data('poster_id', $data['poster_id']);
		$this->set_data('status_id', $data['status_id']);
		$this->set_data('status', $data['status']);
		$this->set_data('lang', $data['lang']);

		return parent::create_insert_array($data, $pre_create_data);
	}
}
