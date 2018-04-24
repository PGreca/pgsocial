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

class install_pg_social_03 extends \phpbb\db\migration\migration {
	static public function depends_on() {
		return array('\pgreca\pg_social\migrations\install_pg_social_02');
	}

	public function update_data() {
		return array(
			
			array('config.update', array('pg_social_version', '0.1.0-a3')),
		);
	}	
	
	public function update_schema()	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix.'pg_social_pages'	=> array(
					'COLUMNS'		=> array(
						'page_id'					=> array('UINT:10', null, 'auto_increment', 0),
						'page_type'					=> array('TINT:2', 0),
						'page_founder'				=> array('UINT:10', 0),
						'page_regdate'				=> array('UINT:11', 0),
						'page_username'				=> array('VCHAR:255', ''),
						'page_username_clean'		=> array('VCHAR:255', ''),
						'page_avatar'				=> array('VCHAR:255', ''),
						'page_avatar_width'			=> array('USINT', 0),
						'page_avatar_height'		=> array('USINT', 0),
						'page_cover'				=> array('VCHAR:255', ''),
						'page_cover_position'		=> array('VCHAR:10', ''),
					),
					'PRIMARY_KEY'	=> 'page_id',
				),
			)
		);
	}
	
	public function revert_schema() {
	}
}
