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

/**
* Thanks for posts notifications class
* This class handles notifying users when they have been thanked for a post
*/

class social_comments extends \phpbb\notification\type\base
{
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'pgreca.pgsocial.notification.type.social_comments';
	}

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_SOCIAL_COMMENTS',
		'group'	=> 'NOTIFICATION_PG_SOCIAL',
	);
	
	/** @var string */
	protected $notifications_table;

	/** @var \phpbb\user_loader */
	protected $user_loader;

	public function set_notifications_table($notifications_table)
	{
		$this->notifications_table = $notifications_table;
	}
	
	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	
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
		return 0;
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
		$options = array_merge([
			'ignore_users'		=> [],
		], $options);

		$users = [(int) $data['user_id']];
		return $this->check_user_notification_options($users, $options);
	}

	/**
	* Get the user's avatar
	*/
	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('poster_id'));
	}

	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		return $this->language->lang($this->get_data('lang'), $this->user_loader->get_username($this->get_data('poster_id'), 'username'));
	}

	/**
	* Users needed to query before this notification can be displayed
	*
	* @return array Array of user_ids
	*/
	public function users_to_query()
	{
		return array($this->get_data('poster_id'));
	}
	
	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'status/'.$this->item_id);
	}

	/**
	* {inheritDoc}
	*/
	public function get_redirect_url()
	{
		return $this->get_url();
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
		$this->set_data('status_id', $data['status_id']);
		$this->set_data('user_id', $data['user_id']);
		$this->set_data('poster_id', $data['poster_id']);
		$this->set_data('lang', $data['lang']);

		return parent::create_insert_array($data, $pre_create_data);
	}
}
