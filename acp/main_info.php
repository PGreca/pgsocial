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

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\pgreca\pgsocial\acp\main_module',
			'title'		=> 'ACP_PG_SOCIAL_MAIN',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'settings'	=> array(
					'title' => 'ACP_PG_SOCIAL_GENERAL', 
					'auth' => 'ext_pgreca/pgsocial && acl_a_board', 
					'cat' => array('ACP_PG_SOCIAL_MAIN')
				),
				'social'	=> array(
					'title' => 'ACP_PG_SOCIAL_SETTINGS', 
					'auth' => 'ext_pgreca/pgsocial && acl_a_board', 
					'cat' => array('ACP_PG_SOCIAL_MAIN')
				),
				'chat'	=> array(
					'title' => 'ACP_PG_SOCIAL_CHAT', 
					'auth' => 'ext_pgreca/pgsocial && acl_a_board', 
					'cat' => array('ACP_PG_SOCIAL_MAIN')
				),
				'page_manage'	=> array(
					'title'	=> 'ACP_PG_SOCIAL_PAGE_MANAGE',
					'auth'	=> 'ext_pgreca/pgsocial && acl_a_board',
					'cat'	=> array('ACP_PG_SOCIAL_PAGE')
				),
			)
		);
	}
}
