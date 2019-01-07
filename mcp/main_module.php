<?php

/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2015 pgreca <http://www.livemembersonly.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace pgreca\pgsocial\mcp;

class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	protected $db;
	protected $auth;
	protected $request;
	protected $template;
	protected $user;
	protected $table_prefix;

	// Main function
	function main($id, $mode)
	{
		global $db, $auth, $request, $template, $user, $u_action, $table_prefix;

		$this->db = $db;
		$this->auth = $auth;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->u_action = $u_action;
		$this->table_prefix = $table_prefix;

		$this->tpl_name = 'mcp_pg_social';
		$this->page_title = 'MCP_PG_SOCIAL_MAIN';
		add_form_key('mcp_pg_social');

		switch($mode)
		{
			case 'page_manage':
				if($this->request->is_set_post('submit'))
				{
					if(!check_form_key('mcp_pg_social'))
					{
						trigger_error('FORM_INVALID');
					}

					$sql_arr = array(
						'page_status' => 1
					);
					$sql = 'UPDATE '.$this->table_prefix.'pg_social_pages SET '.$this->db->sql_build_array('UPDATE', $sql_arr).' WHERE page_id IN ("'.implode('", "', $this->request->variable('page_selected', array('' => ''))).'")';
					if($this->db->sql_query($sql))
					{
						$message = $this->user->lang('PREFERENCES_UPDATED').'<br /><br /><a href="'.$this->u_action.'">A</a>';
						trigger_error($message);
					}
				}

				$sql = "SELECT p.*, u.username, u.user_colour FROM ".$this->table_prefix."pg_social_pages as p, ".USERS_TABLE." as u WHERE p.page_founder = u.user_id AND p.page_status = '0'";
				$result = $this->db->sql_query($sql);
				while($row = $this->db->sql_fetchrow($result))
				{
					$this->template->assign_block_vars('pages', array(
						'PAGE_ID'		=> $row['page_id'],
						'PAGE_USERNAME'	=> $row['page_username'],
						'PAGE_FOUNDER'	=> $row['username'],
						'PAGE_FOUNDERL'	=> get_username_string('profile', $row['page_founder'], $row['username'], $row['user_colour']),
						'PAGE_REGDATA'	=> $row['page_regdate'],
					));
				}
				$this->template->assign_vars(array(
					'MCP_PG_SOCIAL_PAGE'	=> 'page_manage',
					'PAGE_MANAGE_ACTION'	=> ($auth->acl_gets('m_page_manage') || $auth->acl_gets('a_page_manage') ? 1 : 0),
				));
			break;
		}
	}

}
