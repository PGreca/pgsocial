<?php

/**
 *
 * PGreca Social extension for phpBB.
 *
 * @copyright (c) 2015 pgreca <http://www.livemembersonly.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace pgreca\pg_social\mcp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\pgreca\pg_social\mcp\main_module',
			'title'		=> 'MCP_PG_SOCIAL_TITLE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'page_manage'	=> array(
					'title'	=> 'MCP_PG_SOCIAL_PAGE_MANAGE',
					'auth'	=> 'ext_pgreca/pg_social',
					'cat'	=> array('MCP_PG_SOCIAL_PAGE')
				),
			)
		);
	}
}
