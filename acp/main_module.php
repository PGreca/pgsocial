<?php

/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2015 pgreca <http://www.livemembersonly.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace pgreca\pgsocial\acp;

class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	function main($id, $mode){
		global $phpbb_container, $db, $user, $auth, $template, $cache, $request, $helper;
		global $config, $phpbb_root_path, $phpEx, $table_prefix;

		$this->tpl_name = 'pg_social_body';
		$this->page_title = $user->lang('ACP_PG_SOCIAL_TITLE');
		add_form_key('pgreca/pgsocial');

		$template->assign_vars(array(
			'PG_SOCIAL_VERSION'							=> $config['pg_social_version'],
		));

		switch($mode)
		{
			case 'settings':
				if($request->is_set_post('submit'))
				{
					if(!check_form_key('pgreca/pgsocial'))
					{
						trigger_error('FORM_INVALID');
					}

					$config->set('pg_social_enabled', $request->variable('pg_social_enabled', 0));
					$config->set('pg_social_color', $request->variable('pg_social_color', 0));
					$config->set('pg_social_index_replace', $request->variable('pg_social_index_replace', 0));
					$config->set('pg_social_index_activity', $request->variable('pg_social_index_activity', 0));
					$config->set('pg_social_profile', $request->variable('pg_social_profile', 0));
					$config->set('pg_social_sidebarRight', $request->variable('pg_social_sidebarRight', 0));
					$config->set('pg_social_sidebarRight_friendsRandom', $request->variable('pg_social_sidebarRight_friendsRandom', 0));
					$config->set('pg_social_block_posts_last', $request->variable('pg_social_block_posts_last', 0));

					$config->set('pg_social_chat_enabled', $request->variable('pg_social_chat_enabled', 0));

					if($request->variable('pg_social_sidebarRight', 0) == 0)
					{
						$config->set('pg_social_sidebarRight_friendsRandom', 0);
						$config->set('pg_social_block_posts_last', 0);
					}
					trigger_error($user->lang('ACP_PG_SOCIAL_SETTING_SAVED') . adm_back_link($this->u_action));
				}

				$template->assign_vars(array(
					'PG_SOCIAL_PAGE_MAIN'						=> true,
					'PG_SOCIAL_ENABLED'							=> $config['pg_social_enabled'],
					'PG_SOCIAL_COLOR'							=> $config['pg_social_color'],
					'PG_SOCIAL_INDEX_REPLACE'					=> $config['pg_social_index_replace'],
					'PG_SOCIAL_INDEX_ACTIVITY'					=> $config['pg_social_index_activity'],
					'PG_SOCIAL_PROFILE'							=> $config['pg_social_profile'],
					'PG_SOCIAL_SIDEBAR_RIGHT'					=> $config['pg_social_sidebarRight'],
					'PG_SOCIAL_SIDEBAR_RIGHT_FRIENDSRANDOM'		=> $config['pg_social_sidebarRight_friendsRandom'],
					'PG_SOCIAL_SIDEBAR_RIGHT_LAST_POST'			=> $config['pg_social_block_posts_last'],

					'PG_SOCIAL_CHAT'							=> $config['pg_social_chat_enabled'],
				));
			break;
			case 'social':
				if($request->is_set_post('submit'))
				{
					if (!check_form_key('pgreca/pgsocial'))
					{
						trigger_error('FORM_INVALID');
					}

					$config->set('pg_social_smilies', $request->variable('pg_social_smilies', 0));
					$config->set('pg_social_bbcode', $request->variable('pg_social_bbcode', 0));
					$config->set('pg_social_url', $request->variable('pg_social_url', 0));
					$config->set('pg_social_galleryLimit', $request->variable('pg_social_galleryLimit', ''));
					$config->set('pg_social_photoLimit', $request->variable('pg_social_photoLimit', ''));

					trigger_error($user->lang('ACP_PG_SOCIAL_SETTING_SAVED').adm_back_link($this->u_action));
				}
				$template->assign_vars(array(
					'PG_SOCIAL_PAGE_SOCIAL'						=> true,
					'PG_SOCIAL_SMILIES'							=> $config['pg_social_smilies'],
					'PG_SOCIAL_BBCODE'							=> $config['pg_social_bbcode'],
					'PG_SOCIAL_URL'								=> $config['pg_social_url'],
					'PG_SOCIAL_GALLERY_LIMIT'					=> $config['pg_social_galleryLimit'],
					'PG_SOCIAL_PHOTO_LIMIT'						=> $config['pg_social_photoLimit'],
				));
			break;
			case 'chat':
				if($request->is_set_post('submit')){
					if (!check_form_key('pgreca/pgsocial'))
					{
						trigger_error('FORM_INVALID');
					}

					$config->set('pg_social_chat_message_bbcode_enabled', $request->variable('pg_social_chat_message_bbcode_enabled', 0));
					$config->set('pg_social_chat_message_url_enabled', $request->variable('pg_social_chat_message_url_enabled', 0));

					trigger_error($user->lang('ACP_PG_SOCIAL_SETTING_SAVED') . adm_back_link($this->u_action));
				}
				$template->assign_vars(array(
					'PG_SOCIAL_PAGE_CHAT'						=> true,
					'PG_SOCIAL_CHAT_MESSAGE_BBCODE_ENABLED'		=> $config['pg_social_chat_message_bbcode_enabled'],
					'PG_SOCIAL_CHAT_MESSAGE_URL_ENABLED'		=> $config['pg_social_chat_message_url_enabled'],

				));
			break;
			case 'page_manage':
				if($request->is_set_post('submit'))
				{
					if(!check_form_key('pgreca/pgsocial'))
					{
						trigger_error('FORM_INVALID');
					}
					$sql_arr = array(
						'page_status' => 1
					);
					$sql = 'UPDATE '.$table_prefix.'pg_social_pages SET '.$db->sql_build_array('UPDATE', $sql_arr).' WHERE page_id IN ("'.implode('", "', $request->variable('page_selected', array('' => ''))).'")';
					if($db->sql_query($sql))
					{
						trigger_error($user->lang('ACP_PG_SOCIAL_SETTING_SAVED') . adm_back_link($this->u_action));
					}
				}
				else 
				{
					$pgsocial_helper = $phpbb_container->get('pgreca.pgsocial.helper');
					$sql = 'SELECT p.*, u.username, u.user_colour FROM '.$table_prefix.'pg_social_pages as p, '.USERS_TABLE.' as u WHERE p.page_founder = u.user_id AND p.page_status = "0"';
					$result = $db->sql_query($sql);
					while($row = $db->sql_fetchrow($result))
					{
						$template->assign_block_vars('pages', array(
							'PAGE_ID'				=> $row['page_id'],
							'PAGE_USERNAME'			=> $row['page_username'],
							'PAGE_FOUNDER'			=> $row['username'],
							'PAGE_FOUNDERL'			=> get_username_string('profile', $row['page_founder'], $row['username'], $row['user_colour']),
							'PAGE_REGDATA'			=> $pgsocial_helper->time_ago($row['page_regdate']),
						));
					}
					$db->sql_freeresult($result);
					$template->assign_vars(array(
						'PG_SOCIAL_PAGE_PAGE_MANAGE'						=> true,
						'PAGE_MANAGE_ACTION'								=> ($auth->acl_gets('a_page_manage') ? 1 : 0),
					));
				}
			break;
		}
	}
}
