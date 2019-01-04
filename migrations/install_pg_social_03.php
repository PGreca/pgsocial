<?php

/**
 *
 * PG Social extension for phpBB.
 *
 * @copyright (c) 2015 pgreca <http://www.livemembersonly.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace pgreca\pgsocial\migrations;

class install_pg_social_03 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v321');
	}
	
	public function update_schema()
	{
		$sql = "ALTER TABLE ".$this->table_prefix."pg_social_photos CHANGE gallery_id album_id INT(11) UNSIGNED NOT NULL DEFAULT '0'";
		$result = $this->db->sql_query($sql);
		return array(
			'add_columns'	=> array(
				$this->table_prefix.'pg_social_photos' => array(
					'gallery_id'		=> array('UINT:11', 0),
				),
			),
		);
	}	
}