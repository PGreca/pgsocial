<?php

/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2015 pgreca <http://www.livemembersonly.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace pgreca\pgsocial\ucp;

class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	// Main function
	public function main($id, $mode)
	{
		global $db, $auth, $request, $template, $user, $u_action;

		$this->db = $db;
		$this->auth = $auth;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->u_action = $u_action;

		$this->tpl_name = 'ucp_pg_social';
		$this->page_title = 'UCP_PG_SOCIAL_MAIN';
		add_form_key('ucp_pgsocial');

		switch($mode)
		{
			case 'chat':
				if($this->request->is_set_post('submit'))
				{
					if(!check_form_key('ucp_pgsocial'))
					{
						trigger_error('FORM_INVALID');
					}
					$sql_arr = array(
						'user_signature_replace'		=> $this->request->variable('signature_status', false),
						'user_chat_music'    			=> $this->request->variable('chat_sound', false),
					);

					$sql = 'UPDATE '.USERS_TABLE.' SET '.$this->db->sql_build_array('UPDATE', $sql_arr).' WHERE user_id = '.(int) $this->user->data['user_id'];
					$this->db->sql_query($sql);
					$message = $this->user->lang('PREFERENCES_UPDATED').'<br /><br />'.sprintf($this->user->lang('RETURN_UCP'), '<a href="'.$this->u_action.'">', '</a>');
					trigger_error($message);
				}

				$this->template->assign_vars(array(
					'UCP_PG_SOCIAL_PAGE'						=> 'chat',
					'UCP_PG_SOCIAL_SIGNATURE_STATUS'			=> $this->user->data['user_signature_replace'],
					'UCP_PG_SOCIAL_CHAT_SOUND'					=> $this->user->data['user_chat_music'],
					'S_UCP_ACTION'         						=> $u_action,
				));
			break;
		}
	}

}
