<?php
/**
 *
 * PG Social extension for phpBB.
 *
 * @copyright (c) 2015 pgreca <http://www.livemembersonly.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace pgreca\pg_social\migrations;

class install_pg_social_02 extends \phpbb\db\migration\migration {
	static public function depends_on() {
		return array('\pgreca\pg_social\migrations\install_pg_social_01');
	}

	public function update_data() {
		return array(
		);
	}	
	
	public function update_schema()	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix.'pg_social_pages_like'	=> array(
					'COLUMNS'		=> array(
						'page_like_ID'		=> array('UINT:11', null, 'auto_increment', 0),
						'user_id'			=> array('UINT:11', 0),
						'page_id'			=> array('UINT:10', 0),
						'page_like_time'	=> array('UINT:11', 0),					
					), 
					'PRIMARY_KEY'	=> 'page_like_ID',				
				),			
			),
			'add_columns'	=> array(
				$this->table_prefix.'pg_social_wall_post'		=> array(
					'post_where'			=> array('UINT', 0),
				),
			),
		);
	}
	
	public function revert_schema() {
		return array(
			'drop_tables'	=> array(
				$this->table_prefix.'pg_social_pages_like',
			),
		);
	}
}