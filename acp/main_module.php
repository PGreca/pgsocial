<?php

/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2015 pgreca <http://www.livemembersonly.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace pgreca\pg_social\acp;

class main_module {
	var $u_action;

	function main($id, $mode) {
		global $db, $user, $auth, $template, $cache, $request, $helper;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$this->tpl_name = 'pg_social_body';
		$this->page_title = $user->lang('ACP_PG_SOCIAL_TITLE');
		add_form_key('pgreca/pg_social');
		
		switch ($mode) {
			case 'settings':
				if ($request->is_set_post('submit')) {
					if (!check_form_key('pgreca/pg_social')) {
						trigger_error('FORM_INVALID');
					}
					
					$config->set('pg_social_enabled', $request->variable('pg_social_enabled', 0));
					$config->set('pg_social_index_replace', $request->variable('pg_social_index_replace', 0));
					$config->set('pg_social_sidebarRight', $request->variable('pg_social_sidebarRight', 0));
					$config->set('pg_social_sidebarRight_friendsRandom', $request->variable('pg_social_sidebarRight_friendsRandom', 0));
					
					$config->set('pg_social_chat_enabled', $request->variable('pg_social_chat_enabled', 0));
					
					if($request->variable('pg_social_sidebarRight', 0) == 0) $config->set('pg_social_sidebarRight_friendsRandom', 0);
					trigger_error($user->lang('ACP_PG_SOCIAL_SETTING_SAVED') . adm_back_link($this->u_action));
				}		
				
				$template->assign_vars(array(
					'PG_SOCIAL_PAGE_MAIN'						=> true,
					'PG_SOCIAL_ENABLED'							=> $config['pg_social_enabled'],
					'PG_SOCIAL_INDEX_REPLACE'							=> $config['pg_social_index_replace'],
					'PG_SOCIAL_SIDEBAR_RIGHT'					=> $config['pg_social_sidebarRight'],
					'PG_SOCIAL_SIDEBAR_RIGHT_FRIENDSRANDOM'		=> $config['pg_social_sidebarRight_friendsRandom'],
					
					'PG_SOCIAL_CHAT'							=> $config['pg_social_chat_enabled'],
					
					'PG_SOCIAL_VERSION'							=> $config['pg_social_version'],
				));
			break;
			case 'social':
				if($request->is_set_post('submit')) {
					if (!check_form_key('pgreca/pg_social')) {
						trigger_error('FORM_INVALID');
					}					
					
					$config->set('pg_social_smilies', $request->variable('pg_social_smilies', 0));
					$config->set('pg_social_bbcode', $request->variable('pg_social_bbcode', 0));
					$config->set('pg_social_url', $request->variable('pg_social_url', 0));
					
					trigger_error($user->lang('ACP_PG_SOCIAL_SETTING_SAVED').adm_back_link($this->u_action));
				}
				$template->assign_vars(array(
					'PG_SOCIAL_PAGE_SOCIAL'						=> true,
					'PG_SOCIAL_SMILIES'							=> $config['pg_social_smilies'],
					'PG_SOCIAL_BBCODE'							=> $config['pg_social_bbcode'],
					'PG_SOCIAL_URL'								=> $config['pg_social_url'],
				));				
			break;
			case 'chat':
				if($request->is_set_post('submit')) {
					if (!check_form_key('pgreca/pg_social')) {
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
		}
	}
}