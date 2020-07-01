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

class install_pg_social_01 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v321');
	}

	public function update_data()
	{
		$data = array(
			array('config.add', array('pg_social_version', '0.6.8b')),

			array('config.add', array('pg_social_enabled', 1)),
			array('config.add', array('pg_social_index_replace', 0)),
			array('config.add', array('pg_social_index_activity', 0)),
			array('config.add', array('pg_social_color', 0)),
			array('config.add', array('pg_social_profile', 1)),

			array('config.add', array('pg_social_sidebarRight', 1)),
			array('config.add', array('pg_social_sidebarRight_friendsRandom', 1)),
			array('config.add', array('pg_social_block_posts_last', 1)),
			array('config.add', array('pg_social_chat_enabled', 1)),

			array('config.add', array('pg_social_smilies', 1)),
			array('config.add', array('pg_social_bbcode', 1)),
			array('config.add', array('pg_social_url', 1)),

			array('config.add', array('pg_social_galleryLimit', 5)),
			array('config.add', array('pg_social_photoLimit', 5)),

			array('config.add', array('pg_social_chat_enabledfor', 1)),
			array('config.add', array('pg_social_chat_message_url_enabled', 1)),
			array('config.add', array('pg_social_chat_smilies_enabled', 1)),

			array('permission.add', array('u_page_create')),
			array('permission.add', array('m_page_manage')),
			array('permission.add', array('m_status_manage')),
			array('permission.add', array('a_page_manage')),
			array('permission.add', array('a_status_manage')),

			array('module.add', array(
				'acp',
				0,
				'ACP_PG_SOCIAL_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_PG_SOCIAL_TITLE',
				'ACP_PG_SOCIAL_MAIN'
			)),
			array('module.add', array(
				'acp',
				'ACP_PG_SOCIAL_MAIN',
				array(
					'module_basename'	=> '\pgreca\pgsocial\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_PG_SOCIAL_MAIN',
				array(
					'module_basename'	=> '\pgreca\pgsocial\acp\main_module',
					'modes'				=> array('social'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_PG_SOCIAL_MAIN',
				array(
					'module_basename'	=> '\pgreca\pgsocial\acp\main_module',
					'modes'				=> array('chat'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_PG_SOCIAL_TITLE',
				'ACP_PG_SOCIAL_PAGE'
			)),
			array('module.add', array(
				'acp',
				'ACP_PG_SOCIAL_PAGE',
				array(
					'module_basename'	=> '\pgreca\pgsocial\acp\main_module',
					'modes'				=> array('page_manage'),
				),
			)),
			array('module.add', array(
				'mcp',
				0,
				'MCP_PG_SOCIAL_TITLE'
			)),
			array('module.add', array(
				'mcp',
				'MCP_PG_SOCIAL_TITLE',
				'MCP_PG_SOCIAL_MAIN'
			)),
			array('module.add', array(
				'mcp',
				'MCP_PG_SOCIAL_MAIN',
				array(
					'module_basename'	=> '\pgreca\pgsocial\mcp\main_module',
					'modes'				=> array('page_manage'),
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
					'module_basename'    => '\pgreca\pgsocial\ucp\main_module',
					'modes'              => array('chat'),
				),
			)),
		);
		if ($this->role_exists('ROLE_USER_STANDARD'))
		{
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_page_create', true));
		}
		if ($this->role_exists('ROLE_USER_FULL'))
		{
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_page_create', true));
		}
		if ($this->role_exists('ROLE_MOD_STANDARD'))
		{
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_page_manage', true));
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_status_manage', false));
		}
		if ($this->role_exists('ROLE_MOD_FULL'))
		{
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_page_manage', true));
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_status_manage', false));
		}
		if ($this->role_exists('ROLE_ADMIN_STANDARD'))
		{
			$data[] = array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_page_manage'));
			$data[] = array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_status_manage', false));
		}
		if ($this->role_exists('ROLE_ADMIN_FULL'))
		{
			$data[] = array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_page_manage', true));
			$data[] = array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_status_manage', false));
		}
		return $data;
	}

	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix.'pg_social_wall_post'	=> array(
					'COLUMNS'			=> array(
						'post_ID'					=> array('UINT:11', null, 'auto_increment', 0),
						'post_parent'			=> array('UINT:11', 0),
						'post_where'			=> array('UINT:1', 0),
						'wall_id'					=> array('UINT:11', 0),
						'user_id'					=> array('UINT:11', 0),
						'message'					=> array('MTEXT_UNI', ''),
						'time'						=> array('UINT:11', 0),
						'post_privacy'		=> array('UINT:1', 0),
						'post_type'				=> array('UINT:2', 0),
						'post_extra'			=> array('VCHAR:255', ''),
						'bbcode_bitfield'	=> array('VCHAR:255', ''),
						'bbcode_uid'			=> array('VCHAR:8', ''),
						'bbcode_options'		=> array('BOOL', '7'),
						'tagged_user'			=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'	=> 'post_ID',
				),	
				$this->table_prefix.'pg_social_wall_like'	=> array(
					'COLUMNS'			=> array(
						'post_like_ID'		=> array('UINT:11', null, 'auto_increment', 0),
						'post_ID'					=> array('UINT:11', 0),
						'user_id'					=> array('UINT:11', 0),
						'post_like_time'	=> array('UINT:11', 0),
					),
					'PRIMARY_KEY'	=> 'post_like_ID',
				),
				$this->table_prefix.'pg_social_wall_comment'	=> array(
					'COLUMNS'			=> array(
						'post_comment_ID'	=> array('UINT:11', null, 'auto_increment', 0),
						'post_ID'					=> array('UINT:11', 0),
						'user_id'					=> array('UINT:11', 0),
						'time'						=> array('UINT:11', 0),
						'message'					=> array('MTEXT_UNI', ''),
						'bbcode_bitfield'	=> array('VCHAR:255', ''),
						'bbcode_uid'			=> array('VCHAR:8', ''),
						'bbcode_options'		=> array('BOOL', '7'),
					),
					'PRIMARY_KEY'	=> 'post_comment_ID',
				),
				$this->table_prefix.'pg_social_chat'		=> array(
					'COLUMNS'			=> array(
						'chat_id'					=> array('UINT:11', null, 'auto_increment', 0),
						'user_id'					=> array('UINT:11', 0),
						'message'				=> array('MTEXT_UNI', ''),
						'chat_time'				=> array('UINT:11', 0),
						'chat_member'			=> array('UINT:11', 0),
						'chat_status'			=> array('UINT:11', 0),
						'chat_read'				=> array('UINT:11', 0),
						'bbcode_bitfield'	=> array('VCHAR:255', ''),
						'bbcode_uid'			=> array('VCHAR:8', ''),
						'bbcode_options'		=> array('BOOL', '7'),	
					),
					'PRIMARY_KEY'	=> 'chat_id',
				),
				$this->table_prefix.'pg_social_gallery'		=> array(
					'COLUMNS'			=> array(
						'gallery_id'			=> array('UINT:11', null, 'auto_increment', 0),
						'gallery_name'		=> array('VCHAR:255', ''),
						'user_id'					=> array('UINT:11', 0),
						'gallery_time'		=> array('UINT:11', 0),
						'gallery_privacy'	=> array('UINT:1', 0),
					),
					'PRIMARY_KEY'	=> 'gallery_id',
				),
				$this->table_prefix.'pg_social_photos'		=> array(
					'COLUMNS'			=> array(
						'photo_id'				=> array('UINT:11', null, 'auto_increment', 0),
						'photo_where'			=> array('UINT:1', 0),
						'gallery_id'			=> array('UINT:11', 0),
						'album_id'				=> array('UINT:11', 0),
						'user_id'					=> array('UINT:11', 0),
						'photo_file'			=> array('VCHAR:255', ''),
						'photo_time'			=> array('UINT:11', 0),
						'photo_privacy'		=> array('UINT:1', 0),
					),
					'PRIMARY_KEY'	=> 'photo_id',
				),
				$this->table_prefix.'pg_social_pages'	=> array(
					'COLUMNS'		=> array(
						'page_id'					=> array('UINT:10', null, 'auto_increment', 0),
						'page_type'				=> array('TINT:2', 0),
						'page_status'			=> array('TINT:1', 0),
						'page_founder'		=> array('UINT:10', 0),
						'page_regdate'		=> array('UINT:11', 0),
						'page_username'		=> array('VCHAR:255', ''),
						'page_username_clean'		=> array('VCHAR:255', ''),
						'page_avatar'			=> array('VCHAR:255', ''),
						'page_cover'			=> array('VCHAR:255', ''),
						'page_cover_position'		=> array('VCHAR:10', ''),
						'page_about'			=> array('MTEXT_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'page_id',
				),
				$this->table_prefix.'pg_social_pages_like'	=> array(
					'COLUMNS'		=> array(
						'page_like_ID'		=> array('UINT:11', null, 'auto_increment', 0),
						'user_id'					=> array('UINT:11', 0),
						'page_id'					=> array('UINT:10', 0),
						'page_like_time'	=> array('UINT:11', 0),
					),
					'PRIMARY_KEY'	=> 'page_like_ID',
				),
			),
			'add_columns'	=> array(
				$this->table_prefix.'users' => array(
					'user_gender'										=> array('UINT:1', 0),
					'user_about'										=> array('VCHAR:255', ''),
					'user_pg_social_cover'					=> array('VCHAR:255', ''),
					'user_pg_social_cover_position'	=> array('VCHAR:10', ''),
					'user_quote'										=> array('VCHAR:255', ''),
					'user_signature_replace'				=> array('UINT:1', 0),
					'user_chat_music'								=> array('UINT:1', 1),
					'user_chat_visibility'					=> array('UINT:1', 1)
				),
				$this->table_prefix.'zebra'		=> array(
					'approval'		=> array('UINT', 0),
				),
			),
		);
	}

	public function revert_data()
	{
		return array(
			array(
				'module.remove', array(
					'acp',
					'ACP_PG_SOCIAL_TITLE',
					array(
						'module_basename'	=> '\pgreca\pgsocial\acp\main_module',
					),
				),
				'module.remove', array(
					'acp',
					'ACP_PG_SOCIAL_PAGE',
					array(
						'module_basename'	=> '\pgreca\pgsocial\acp\main_module',
					),
				),
				'module.remove', array(
					'acp',
					'ACP_PG_SOCIAL_MAIN',
					array(
						'module_basename'	=> '\pgreca\pgsocial\acp\main_module',
					),
				),
				'module.remove', array(
					'ucp',
					'UCP_PG_SOCIAL_MAIN',
					array(
						'module_basename'	=> '\pgreca\pgsocial\ucp\main_module',
					)
				),
				'module.remove', array(
					'mcp',
					'MCP_PG_SOCIAL_PAGE',
					array(
						'module_basename'	=> '\pgreca\pgsocial\mcp\main_module',
					),
				),
				'module.remove', array(
					'mcp',
					'MCP_PG_SOCIAL_TITLE',
					array(
						'module_basename'	=> '\pgreca\pgsocial\mcp\main_module',
					),
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

	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array(
				$this->table_prefix.'pg_social_wall_post',
				$this->table_prefix.'pg_social_wall_like',
				$this->table_prefix.'pg_social_wall_comment',
				$this->table_prefix.'pg_social_chat',
				$this->table_prefix.'pg_social_gallery',
				$this->table_prefix.'pg_social_photos',
				$this->table_prefix.'pg_social_pages',
				$this->table_prefix.'pg_social_pages_like',
			),
			'drop_columns'	=> array(
				$this->table_prefix . 'users' => array(
					'user_gender',
					'user_about',
					'user_pg_social_cover',
					'user_pg_social_cover_position',
					'user_quote',
					'user_chat_music',
					'user_chat_visibility'
				),
				$this->table_prefix.'zebra'		=> array(
					'approval',
				),
			),
		);
	}

	public function remove_photos()
	{
        global $phpbb_root_path;
		$this->RemoveFolderContent($phpbb_root_path. 'ext/pgreca/pgsocial/images/upload/');
	}

	function RemoveFolderContent($folder)
	{
		foreach(glob($folder.'/*') as $file)
		{
			if (is_dir($file))
			{
				$this->RemoveFolderContent($file);
				rmdir($file);
			}
			else
			{
				unlink($file);
			}
		}
	}

	/**
	 * Custom function query permission roles
	 *
	 * @param string $role
	 * @return bool
	 * @access public
	*/
	private function role_exists($role)
	{
		$sql = 'SELECT role_id FROM ' . ACL_ROLES_TABLE . ' WHERE role_name = "' . $this->db->sql_escape($role) . '"';
		$result = $this->db->sql_query_limit($sql, 1);
		$role_id = $this->db->sql_fetchfield('role_id');
		$this->db->sql_freeresult($result);

		return (bool) $role_id;
	}
}
