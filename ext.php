<?php

/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2018 pgreca <http://www.pgreca.it>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace pgreca\pgsocial;

/**
* Extension class for custom enable/disable/purge actions
*/
class ext extends \phpbb\extension\base
{
	const PG_SOCIAL_VERSION = '0.6.8b';

	public function is_enableable()
	{
		if (!class_exists('phpbb\mediaembed\ext'))
		{
			$this->container->get('user')->add_lang_ext('pgreca/pgsocial', 'ext_enable_error');
			trigger_error($this->container->get('user')->lang['PG_NOTICE'], E_USER_WARNING);
		}

		if (!$this->container->get('ext.manager')->is_enabled('phpbb/mediaembed'))
		{
			$this->container->get('ext.manager')->enable('phpbb/mediaembed');
		}

		return class_exists('phpbb\mediaembed\ext');
	}

	/**
	* Single enable step that installs any included migrations
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	function enable_step($old_state)
	{
		switch($old_state)
		{
			case '': // Empty means nothing has run yet
				// Enable board rules notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->enable_notifications('notification.type.social_status');
				$phpbb_notifications->enable_notifications('notification.type.social_comments');
				$phpbb_notifications->enable_notifications('notification.type.social_likes');
				$phpbb_notifications->enable_notifications('notification.type.social_tag');
				$phpbb_notifications->enable_notifications('notification.type.social_zebra');
				return 'notifications';
			break;
			default:
				// Run parent enable step method
				return parent::enable_step($old_state);
			break;
		}
	}

	/**
	* Single disable step that does nothing
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	function disable_step($old_state)
	{
		switch ($old_state){
			case '': // Empty means nothing has run yet
				// Disable board rules notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->disable_notifications('notification.type.social_status');
				$phpbb_notifications->disable_notifications('notification.type.social_comments');
				$phpbb_notifications->disable_notifications('notification.type.social_likes');
				$phpbb_notifications->disable_notifications('notification.type.social_tag');
				$phpbb_notifications->disable_notifications('notification.type.social_zebra');
				return 'notifications';
			break;
			default:
				// Run parent disable step method
				return parent::disable_step($old_state);
			break;
		}
	}

	/**
	* Single purge step that reverts any included and installed migrations
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				/**
				* @todo Remove this try/catch condition once purge_notifications is fixed
				* in the core to work with disabled extensions without fatal errors.
				* https://tracker.phpbb.com/browse/PHPBB3-12435
				*/
				try
				{
					// Purge board rules notifications
					$phpbb_notifications = $this->container->get('notification_manager');
					$phpbb_notifications->purge_notifications('notification.type.social_status');
					$phpbb_notifications->purge_notifications('notification.type.social_comments');
					$phpbb_notifications->purge_notifications('notification.type.social_likes');
					$phpbb_notifications->purge_notifications('notification.type.social_tag');
					$phpbb_notifications->purge_notifications('notification.type.social_zebra');
				}
				catch(\phpbb\notification\exception $e)
				{
					// continue
				}
				return 'notifications';
			break;
			default:
				// Run parent purge step method
				return parent::purge_step($old_state);
			break;
		}
	}
}
