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

class install_pg_social_01 extends \phpbb\db\migration\migration {
	static public function depends_on() {
		return array('\phpbb\db\migration\data\v32x\v321');
	}

	public function update_data() {
		return array(
			array('config.add', array('pg_social_version', '0.1.0-a2')),
			
			array('config.add', array('pg_social_enabled', 1)),			
			array('config.add', array('pg_social_index_replace', 0)),
			
			array('config.add', array('pg_social_sidebarRight', 1)),
			array('config.add', array('pg_social_sidebarRight_friendsRandom', 1)),
			array('config.add', array('pg_social_chat_enabled', 1)),
			
			array('config.add', array('pg_social_smilies', 1)),
			array('config.add', array('pg_social_bbcode', 1)),
			array('config.add', array('pg_social_url', 1)),
			
			array('config.add', array('pg_social_chat_enabledfor', 1)),
			array('config.add', array('pg_social_chat_message_url_enabled', 1)),
			array('config.add', array('pg_social_chat_smilies_enabled', 1)),
			array('module.add', array(
				'acp',
				0,
				'ACP_PG_SOCIAL_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_PG_SOCIAL_TITLE',
				'ACP_WALL_TITLE'
			)),			
			array('module.add', array(
				'acp',
				'ACP_PG_SOCIAL_TITLE',
				array(
					'module_basename'	=> '\pgreca\pg_social\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),	
			array('module.add', array(
				'acp',
				'ACP_PG_SOCIAL_TITLE',
				array(
					'module_basename'	=> '\pgreca\pg_social\acp\main_module',
					'modes'				=> array('social'),
				),
			)),		
			array('module.add', array(
				'acp',
				'ACP_PG_SOCIAL_TITLE',
				array(
					'module_basename'	=> '\pgreca\pg_social\acp\main_module',
					'modes'				=> array('chat'),
				),
			)),
			array('module.add', array(
				'ucp',
				0,
				'UCP_PG_SOCIAL_MAIN',
			)),
			array('module.add', array(
				'ucp',
				'UCP_PG_SOCIAL_MAIN',				
				array(
					'module_basename'    => '\pgreca\pg_social\ucp\main_module',
					'modes'              => array('chat'),
				),
			)),
		);
	}	
	
	// Add chat DB tables and columns
	public function update_schema()	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix.'pg_social_wall_post'	=> array(
					'COLUMNS'		=> array(
						'post_ID'			=> array('UINT:11', null, 'auto_increment', 0),
						'post_parent'		=> array('UINT:11', 0),
						'wall_id'			=> array('UINT:11', 0),
						'user_id'			=> array('UINT:11', 0),
						'message'			=> array('MTEXT_UNI', ''),
						'time'				=> array('UINT:11', 0),
						'post_privacy'		=> array('UINT:1', 0),
						'post_type'			=> array('UINT:2', 0),
						'post_extra'		=> array('VCHAR:255', ''),
						'bbcode_bitfield'	=> array('VCHAR:255', ''),
						'bbcode_uid'		=> array('VCHAR:8', ''),
						'tagged_user'		=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'	=> 'post_ID',
				),
				$this->table_prefix.'pg_social_wall_like'	=> array(
					'COLUMNS'		=> array(
						'post_like_ID'		=> array('UINT:11', null, 'auto_increment', 0),
						'post_ID'			=> array('UINT:11', 0),
						'user_id'			=> array('UINT:11', 0),
						'post_like_time'	=> array('UINT:11', 0),					
					), 
					'PRIMARY_KEY'	=> 'post_like_ID',				
				),
				$this->table_prefix.'pg_social_wall_comment'	=> array(
					'COLUMNS'		=> array(
						'post_comment_ID'	=> array('UINT:11', null, 'auto_increment', 0),
						'post_ID'			=> array('UINT:11', 0),
						'user_id'			=> array('UINT:11', 0),
						'time'				=> array('UINT:11', 0),
						'message'			=> array('MTEXT_UNI', ''),
						'bbcode_bitfield'	=> array('VCHAR:255', ''),
						'bbcode_uid'		=> array('VCHAR:8', ''),
					),
					'PRIMARY_KEY'	=> 'post_comment_ID',
				),
				$this->table_prefix.'pg_social_chat'		=> array(
					'COLUMNS'		=> array(
						'chat_id'			=> array('UINT:11', null, 'auto_increment', 0),
						'user_id'			=> array('UINT:11', 0),
						'chat_text'			=> array('MTEXT_UNI', ''),
						'chat_time'			=> array('UINT:11', 0),
						'chat_member'		=> array('UINT:11', 0),
						'chat_status'		=> array('UINT:11', 0),
						'chat_read'			=> array('UINT:11', 0),
						'bbcode_bitfield'	=> array('VCHAR:255', ''),
						'bbcode_uid'		=> array('VCHAR:8', ''),
					),
					'PRIMARY_KEY'	=> 'chat_id',					
				),
				$this->table_prefix.'pg_social_gallery'		=> array(
					'COLUMNS'		=> array(
						'gallery_id'		=> array('UINT:11', null, 'auto_increment', 0),
						'gallery_name'		=> array('VCHAR:255', ''),
						'user_id'			=> array('UINT:11', 0),
						'gallery_time'		=> array('UINT:11', 0),
					),
					'PRIMARY_KEY'	=> 'gallery_id',
				),
				$this->table_prefix.'pg_social_photos'		=> array(
					'COLUMNS'		=> array(
						'photo_id'			=> array('UINT:11', null, 'auto_increment', 0),
						'gallery_id'		=> array('UINT:11', 0),
						'user_id'			=> array('UINT:11', 0),
						'photo_file'		=> array('VCHAR:255', ''),
						'photo_time'		=> array('UINT:11', 0),
						'photo_desc'		=> array('MTEXT_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'photo_id',
				),
			),
			'add_columns'	=> array(
				$this->table_prefix.'users' => array(
					'user_gender'					=> array('UINT:1', 0),
					'user_pg_social_cover'			=> array('VCHAR:255', ''),
					'user_pg_social_cover_position'	=> array('VCHAR:10', ''),
					'user_quote'					=> array('VCHAR:255', ''),
					'user_signature_replace'		=> array('UINT:1', 0),
					'user_chat_music'				=> array('UINT:1', 1),
					'user_chat_visibility'			=> array('UINT:1', 1)
				),
			),
		);
	}	
	
	public function revert_data() {
		return array(
			array(
				'module.remove', array(
					'acp',
					'ACP_PG_SOCIAL_TITLE',
					array(
						'module_basename'	=> '\pgreca\pg_social\acp\main_module',
					),
				),
				'module.remove', array(
					'ucp',
					'UCP_PG_SOCIAL_MAIN',
					array(
						'module_basename'	=> '\pgreca\pg_social\ucp\main_module',
					)
				),
			),	
			array(
				'custom', array(
					array(
						$this, 'remove_photos'
					)
				)
			),
		);
	}
	
	public function revert_schema() {
		return array(
			'drop_tables'	=> array(
				$this->table_prefix.'pg_social_wall_post',
				$this->table_prefix.'pg_social_wall_like',
				$this->table_prefix.'pg_social_wall_comment',
				$this->table_prefix.'pg_social_chat',
				$this->table_prefix.'pg_social_gallery',
				$this->table_prefix.'pg_social_photos',
			),
			'drop_columns'	=> array(
				$this->table_prefix . 'users' => array(
					'user_pg_social_cover',
					'user_pg_social_cover_position',
					'user_quote',
					'user_chat_music',
					'user_chat_visibility'
				),
			),
		);
	}
	
	public function remove_photos() {
        global $phpbb_root_path;
		$this->RemoveFolderContent($phpbb_root_path. 'ext/pgreca/pg_social/images/upload/');
	}
	  
	function RemoveFolderContent($folder) {
		foreach(glob($folder."/*") as $file) {
			if(is_dir($file)) {
				$this->RemoveFolderContent($file);
				rmdir($file);
			} else {
				unlink($file);
			}
		}
	}	
}