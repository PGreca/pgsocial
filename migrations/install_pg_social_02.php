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

class install_pg_social_02 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v321');
	}
			
	
	// Add chat DB tables and columns
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix.'users' => array(
					'user_about'					=> array('VCHAR:255', ''),
					'user_status_life'				=> array('UINT:1', 0),
					'user_hobbies'					=> array('VCHAR:350', ''),
					'user_favorite_tvseries'		=> array('VCHAR:350', ''),
					'user_favorite_movies'			=> array('VCHAR:350', ''),
					'user_favorite_games'			=> array('VCHAR:350', ''),
					'user_favorite_musics'			=> array('VCHAR:350', ''),
					'user_favorite_books'			=> array('VCHAR:350', ''),
				),
			),
		);
	}	
	
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix.'users' => array(
					'user_about',
					'user_status_life',
					'user_hobbies',
					'user_favorite_tvseries',
					'user_favorite_games',
					'user_favorite_musics',
					'user_favorite_books',
				),
			),
		);
	}	
}