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

class main_info {
	function module() {
		return array(
			'filename'	=> '\pgreca\pg_social\acp\main_module',
			'title'		=> 'ACP_PG_SOCIAL_TITLE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'settings'	=> array(
					'title' => 'ACP_PG_SOCIAL_GENERAL', 
					'auth' => 'ext_pgreca/pg_social && acl_a_board', 
					'cat' => array('ACP_PG_SOCIAL_TITLE')
				),
				'social'	=> array(
					'title' => 'ACP_PG_SOCIAL_SETTINGS', 
					'auth' => 'ext_pgreca/pg_social && acl_a_board', 
					'cat' => array('ACP_PG_SOCIAL_TITLE')
				),
				'chat'	=> array(
					'title' => 'ACP_PG_SOCIAL_CHAT', 
					'auth' => 'ext_pgreca/pg_social && acl_a_board', 
					'cat' => array('ACP_PG_SOCIAL_TITLE')
				)
			)
		);
	}
}
