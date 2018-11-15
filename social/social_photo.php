<?php
/**
*
* Social extension for the phpBB Forum Software package.
*
* @copyright (c) 2017 Antonio PGreca (PGreca)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace pgreca\pgsocial\social;

class social_photo
{
	/* @var \phpbb\template\template */
	protected $template;
	
	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\config\config */
	protected $config;
	
	/* @var string phpBB root path */
	protected $root_path;	
	
	/* @var string phpEx */
	protected $php_ext;		
	
	/**
	* Constructor
	*
	* @param \phpbb\template\template  $template
	* @param \phpbb\config\config $config
	* @param \phpbb\controller\helper		$helper
	* @param \pg_social\\controller\helper $pg_social_helper	
	* @param \phpbb\config\config			$config
	* @param \phpbb\db\driver\driver_interface			$db
	*/
	
	public function __construct($template, $user, $helper, $pg_social_helper, $social_tag, $config, $db, $root_path, $php_ext, $table_prefix)
	{
		$this->template					= $template;
		$this->user						= $user;
		$this->helper 					= $helper;
		$this->pg_social_helper 		= $pg_social_helper;
		$this->social_tag				= $social_tag;
		$this->config 					= $config;
		$this->db 						= $db;
	    $this->root_path 				= $root_path;	
		$this->php_ext 					= $php_ext;
        $this->table_prefix 			= $table_prefix;	
	    $this->pg_social_path 			= './ext/pgreca/pgsocial/images/';	
	}
	
	/**
	 * The Albums of the wall
	*/
	public function getGallery($wall, $where)
	{
		switch($where)
		{
			case 'page':
				$where = 1;
			break;	
			default:
				$where = 0;
			break;
		}
		
		$sql = "SELECT user_id, username, username_clean, user_colour FROM ".USERS_TABLE." WHERE user_id = '".$wall."'";
		$result = $this->db->sql_query($sql);
		$user = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);			
		
		$limit = 6;
		$sql = "SELECT *, 
				(SELECT photo_file 
				FROM ".$this->table_prefix."pg_social_photos as cov 
				WHERE cov.user_id = ph.user_id
					AND ph.gallery_id = cov.gallery_id 
					AND ph.photo_where = '".$where."' 
				ORDER BY photo_time DESC 
				LIMIT 0, 1) as gallery_cover, 
				(SELECT COUNT(*)
				FROM ".$this->table_prefix."pg_social_photos as contt
				WHERE contt.user_id = ph.user_id
					AND ph.gallery_id = contt.gallery_id
					AND ph.photo_where = '".$where."'
				) as count 
			FROM ".$this->table_prefix."pg_social_photos as ph 
			WHERE ph.user_id = '".$wall."' 
				AND ph.photo_where = '".$where."' 
			GROUP BY gallery_id";
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result))
		{	
			switch($row['gallery_id'])
			{
				case 3: 
					$row['gallery_name'] = $this->user->lang('PG_SOCIAL_WALL');				
				break;
				case 1:
					$row['gallery_name'] = $this->user->lang('PG_SOCIAL_AVATAR');
				break;
				case 2:
					$row['gallery_name'] = $this->user->lang('PG_SOCIAL_COVER');
				break;				
			}
			$count_g = $this->user->lang('PHOTO', 1);
			if($row['count'] == 0 || $row['count'] > 1) $count_g = $this->user->lang('PHOTO', 2);
			$this->template->assign_block_vars('social_gallery', array(
				'GALLERY_ID'		=> $row['gallery_id'],
				'GALLERY_URL'		=> get_username_string('profile', $user['user_id'], $user['username'], $user['user_colour'])."&gall=".$row['gallery_id'],
				'GALLERY_NAME'		=> $row['gallery_name'],
				'GALLERY_COUNT'		=> $row['count']." ".$count_g,
				'PHOTO_COVER'		=> $this->pg_social_path."upload/".$row['gallery_cover'],
				'PHOTO_FILE'		=> $row['photo_file'],
			));
		}
		$this->db->sql_freeresult($result);		
	}
	
	/**
	 * Array of gallery
	*/
	public function gallery_info($gallery)
	{		
		switch($gallery)
		{
			case 3: 
				$row['gallery_name'] = $this->user->lang('PG_SOCIAL_WALL');				
			break;
			case 1:
				$row['gallery_name'] = $this->user->lang('PG_SOCIAL_AVATAR');
			break;
			case 2:
				$row['gallery_name'] = $this->user->lang('PG_SOCIAL_COVER');
			break;			
			default:
				$sql = "SELECT * FROM ".$this->table_prefix."pg_social_gallery WHERE gallery_id = '".$gallery."'";
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
			break;
		}
		$return = $row;
		return $return;
	}
	
	
	/**
	 * Array Photos of gallery
	*/
	public function getPhotos($where, $last = "gall", $user, $gall = false)
	{
		$sqlwhere = '';
		$sqllimit = '';
		if($last == "last") 
		{
			$sqllimit = " LIMIT 0, 9"; 
			$block = "last_photos";
		}
		else 
		{		
			$block = 'social_photos';
			$sqlwhere = " AND gallery_id = '".$gall."'"; 	
		}
		$sql = "SELECT * FROM ".$this->table_prefix."pg_social_photos WHERE user_id = '".$user."' AND photo_where = '".$where."'".$sqlwhere." ORDER BY photo_time DESC".$sqllimit;
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars($block, array(
				"SQL"			=> $sql,
				"PHOTO_ID"		=> $row['photo_id'],
				"PHOTO_FILE"	=> $this->pg_social_path."upload/".$row['photo_file'],
			));
		}		
	}
	
	/**
	 * Array or Output of photos
	*/
	public function getPhoto($photo, $template = NULL)
	{
		$allow_bbcode = $this->config['pg_social_bbcode'];
		$allow_urls = $this->config['pg_social_url'];
		$allow_smilies = $this->config['pg_social_smilies'];
		$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);
		
		$photoe = explode('#', $photo);
		$photo = $photoe[0];
		$sql = "SELECT p.*, (SELECT post_ID FROM ".$this->table_prefix."pg_social_wall_post WHERE post_extra = '".$photo."') as post_id FROM ".$this->table_prefix."pg_social_photos AS p WHERE p.photo_id = '".$photo."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		
		if($row['photo_where'] == 0)
		{
			$sql = "SELECT user_id, username, username_clean, user_colour, user_avatar, user_avatar_type, '' as page_founder FROM ".USERS_TABLE." WHERE user_id = '".$row['user_id']."'";
		}
		else
		{
			$sql = "SELECT page_id as user_id, page_username as username, page_username as username_clean, '' as user_colour, page_avatar as user_avatar, '' as user_avatar_type, page_founder FROM ".$this->table_prefix."pg_social_pages WHERE page_id = '".$row['user_id']."'";
		}
		$result = $this->db->sql_query($sql);
		$user = $this->db->sql_fetchrow($result);
		
		if(!$template)
		{
			$row['photo_file'] = generate_board_url()."/ext/pgreca/pgsocial/images/upload/".$row['photo_file'];
			return $row;
		}
		else{
			$comment = "<span>".$this->pg_social_helper->countAction("comments", $row['post_id'])."</span> ";
			if($this->pg_social_helper->countAction("comments", $row['post_id']) == 0 || $this->pg_social_helper->countAction("comments", $row['post_id']) > 1)
			{
				$comment .= $this->user->lang('COMMENT', 2);
			}
			else
			{
				$comment .= $this->user->lang('COMMENT', 1);
			}
			$likes = "<span>".$this->pg_social_helper->countAction("like", $row['post_id'])."</span> ".$this->user->lang('LIKE', 1);
			if($this->pg_social_helper->countAction("like", $row['post_id']) == 0 | $this->pg_social_helper->countAction("like", $row['post_id']) > 1)
			
			$desc = $this->pg_social_helper->noextra(generate_text_for_display($row['photo_desc'], '', '', $flags));
			if(($this->user->data['user_id'] == $user['user_id'] && $row['photo_file'] != $this->user->data['user_pg_social_cover']) || $this->user->data['user_id'] == $user['page_founder']) $photo_action = 1; else $photo_action = 0;	
			if(!array_key_exists('username', $row)) $row['username'] = "";
			if(!array_key_exists('user_colour', $row)) $row['user_colour'] = "";
			($row['username'] && $row['user_colour']) ? $gallery_url = get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour'])."&gall=".$row['gallery_id'] : $gallery_url = '';
			$this->template->assign_block_vars('social_photo', array(
				'PHOTO_ID'					=> $row['photo_id'],
				'PHOTO_FILE'				=> generate_board_url().$this->pg_social_path."upload/".$row['photo_file'],
				'PHOTO_TIME'				=> $this->pg_social_helper->time_ago($row['photo_time']),
				'PHOTO_ACTION'				=> $photo_action,
				'AUTHOR_PROFILE'			=> get_username_string('profile', $user['user_id'], $user['username'], $user['user_colour']),
				'AUTHOR_USERNAME'			=> $user['username'],
				'AUTHOR_COLOUR'				=> '#'.$user['user_colour'],
				'AUTHOR_AVATAR'				=> ($row['photo_where'] == 0 ? $this->pg_social_helper->social_avatar_thumb($user['user_avatar'], $user['user_avatar_type'], $this->user->data['user_avatar_width'], $this->user->data['user_avatar_height']) : '<img src="'.generate_board_url().'/ext/pgreca/pgsocial/images/'.($page['page_avatar'] != "" ? $page_avatar = 'upload/'.$page['page_avatar'] : $page_avatar = 'page_no_avatar.jpg').'" />'),
				'GALLERY_URL'				=> $gallery_url,
				'PHOTO_ALBUM'				=> $this->gallery_info($row['gallery_id'])['gallery_name'],
				'PHOTO_DESC'				=> $desc,
				"LIKE"						=> $likes,
				"IFLIKE"					=> $this->pg_social_helper->countAction("iflike", $row['post_id']),
				"PRE"						=> $this->prenextPhoto($row['photo_id'], 0, $row['photo_where'], false),
				"NEX"						=> $this->prenextPhoto($row['photo_id'], 1, $row['photo_where'], false),
				"COMMENT"					=> $comment,
				'POST_ID'					=> $row['post_id'],
			));
			return $this->helper->render('pg_social_photo.html', '');
		}
	}
	
	/**
	 * Upload new photo
	*/	
	public function photoUpload($where, $who, $msg = null, $type, $lwhere = 'profile', $photo, $itop = null)
	{
		switch($where)
		{
			case 'page':
				$where = 1;
			break;	
			default:
				$where = 0;
			break;
		}
		//let's access these values by using their index position
	    $ImageName 		= str_replace(')','', str_replace('(','', str_replace(' ','-',strtolower($photo['name'])))); 
	    $ImageSize 		= $photo['size']; 
	    $TempSrc	 	= $photo['tmp_name']; 
	    $ImageType	 	= $photo['type']; 
		$imageAlbum 	= $this->pg_social_path.'upload/';

		if(!file_exists($imageAlbum)) mkdir($imageAlbum);
		$BigImageMaxSize 		= 1500; //Image Maximum height or width
		$Quality 				= 100;
		$now = time();
		
		// Random number file, will be added after image name
		$RandomNumber 	= rand(0, 9999999999); 
		switch(strtolower($ImageType))
		{
			case 'image/png':
				$CreatedImage = imagecreatefrompng($TempSrc);
				break;
			case 'image/gif':
				$CreatedImage = imagecreatefromgif($TempSrc);
				break;			
			case 'image/jpeg':			
			   $CreatedImage = imagecreatefromjpeg($TempSrc);
				break;
			
			case 'image/pjpeg':
				$CreatedImage = imagecreatefromjpeg($TempSrc);
				break;
			default:
				trigger_error($this->user->lang['ATTACHED_IMAGE_NOT_IMAGE']);
			break;
		}
		
		//PHP getimagesize() function returns height-width from image file stored in PHP tmp folder.
		//Let's get first two values from image, width and height. list assign values to $CurWidth,$CurHeight
		list($CurWidth,$CurHeight) = getimagesize($TempSrc);
		//Get file extension from Image name, this will be re-added after random name
		$ImageExt = substr($ImageName, strrpos($ImageName, '.'));
		$ImageExt = str_replace('.','',$ImageExt);
		
		//remove extension from filename
		$ImageName 		= preg_replace("/\\.[^.\\s]{3,4}$/", "", $ImageName); 
		
		$NewImageName = $ImageName.'-'.$RandomNumber.'.'.$ImageExt;
		//set the Destination Image
		$DestRandImageName 			= $imageAlbum.$NewImageName; //Name for Big Image
		
		// This function will proportionally resize image 
		function resizeImage($CurWidth,$CurHeight,$MaxSize,$DestFolder,$SrcImage,$Quality,$ImageType)
		{
			//Check if height is differnt than width to resize it
			if($CurWidth <= $CurHeight) $MaxSize = 1500;
			if($CurWidth <= 0 || $CurHeight <= 0) return false;
			
			
			//Construct a proportional size of new image
			if($CurWidth > $MaxSize)
			{
				$ImageScale      	= min($MaxSize/$CurWidth, $MaxSize/$CurHeight); 
				$NewWidth  			= ceil($ImageScale*$CurWidth);
				$NewHeight 			= ceil($ImageScale*$CurHeight);
				$NewCanves 			= imagecreatetruecolor($NewWidth, $NewHeight);
			}
			else
			{
				$NewWidth 			= ceil($CurWidth);
				$NewHeight			= ceil($CurHeight);
				$NewCanves			= imagecreatetruecolor($NewWidth, $NewHeight);
			}
			// Resize Image
			if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
			{
				switch(strtolower($ImageType))
				{
					case 'image/png':
						imagepng($NewCanves,$DestFolder);
						break;
					case 'image/gif':
						imagegif($NewCanves,$DestFolder);
						break;			
					case 'image/jpeg':
						imagejpeg($NewCanves,$DestFolder,$Quality);
						break;			
					case 'image/pjpeg':
						imagejpeg($NewCanves,$DestFolder,$Quality);
						break;
					default:
						return false;
					break;
				}
				//Destroy image, frees memory	
				if(is_resource($NewCanves)){imagedestroy($NewCanves);} 
				return true;
			}
		}
		if(resizeImage($CurWidth,$CurHeight,$BigImageMaxSize,$DestRandImageName,$CreatedImage,$Quality,$ImageType))
		{
			$a = $this->photoQuery($where, $who, $msg, $type, $lwhere, $NewImageName, $wall_id, $now, $itop);
		}
		
		/*$a = $type;
		$this->template->assign_vars(array(
			"ACTION"	=>  $a,
		));*/
		if($type != 'avatar') return $this->helper->render('activity_status_action.html', "");
	}
	
	/**
	 * Upload photo query
	*/
	public function photoQuery($where, $who, $msg, $type, $lwhere, $file, $wall, $time, $itop)
	{
		$user = (int) $this->user->data['user_id'];	
		switch($type)
		{
			case 'avatar':
				$gallery = 1;
			break;			
			case 'cover':
				$gallery = 2;
				switch($lwhere)
				{
					case 'page':
						$sql_cover = "UPDATE ".$this->table_prefix."pg_social_pages SET page_cover = '".$file."', page_cover_position = '".$itop."' WHERE page_id = '".$who."'";
					break;
					case 'profile':
						$sql_cover = "UPDATE ".USERS_TABLE." SET user_pg_social_cover = '".$file."', user_pg_social_cover_position = '".$itop."' WHERE user_id = '".$user."'";
					break;
				}
				$this->db->sql_query($sql_cover);
			break;
			case 'wall':
				$gallery = 3;
			break;
		}
		$sql_arr = array(
			'photo_where'		=> $where,
			'gallery_id'		=> $gallery,
			'user_id'			=> $who,
			'photo_file'		=> $file,
			'photo_time'		=> $time,
			'photo_desc'		=> $msg,
		);
		$sql = "INSERT INTO ".$this->table_prefix."pg_social_photos ".$this->db->sql_build_array('INSERT', $sql_arr);
		if($this->db->sql_query($sql))
		{
			$sql = "SELECT photo_id FROM ".$this->table_prefix."pg_social_photos WHERE photo_file = '".$file."'";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			return $this->add_statusPhoto($where, $who, $user, $gallery, 1, $row['photo_id'], $msg);
		}
	}
	/**
	
	 * New activity for upload photo
	*/
	public function add_statusPhoto($where, $who, $user, $type, $privacy, $photo, $text)
	{
		$allow_bbcode = $this->config['pg_social_bbcode'];
		$allow_urls = $this->config['pg_social_url'];
		$allow_smilies = $this->config['pg_social_smilies'];
		$text = urldecode($text);
		$time = time();
		generate_text_for_storage($text, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);
			
		$text = str_replace('&amp;nbsp;', ' ', $text);
		$sql_arr = array(
			'post_parent'		=> 0,
			'post_where'		=> $where,
			'wall_id'			=> $who,
			'user_id'			=> $user,
			'message'			=> $text,
			'time'				=> $time,
			'post_privacy'		=> $privacy,
			'post_type'			=> $type,
			'post_extra'		=> $photo,
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid,
			'tagged_user'		=> ''
		);
		$sql = "INSERT INTO " . $this->table_prefix . 'pg_social_wall_post' . $this->db->sql_build_array('INSERT', $sql_arr);
		if($this->db->sql_query($sql))
		{	
			$sql = "UPDATE ".$this->table_prefix."pg_social_photos SET photo_desc = '".$text."' WHERE photo_id = '".$photo."'";
			$this->db->sql_query($sql);
			$last_status = "SELECT post_ID FROM ".$this->table_prefix."pg_social_wall_post WHERE time = '".$time."' AND user_id = '".$this->user->data['user_id']."' AND wall_id = '".$wall_id."' ORDER BY time DESC LIMIT 0, 1";
			$last = $this->db->sql_query($last_status);
			$row = $this->db->sql_fetchrow($last);	
			$this->social_tag->addTag($row['post_ID'], $text_clear);
		}	
	}
	
	/**
	 * Delete photo
	*/
	public function deletePhoto($photo)
	{		
		$delphoto = false;
		
		$sql = "SELECT photo_id, photo_file, user_id, photo_where FROM ".$this->table_prefix."pg_social_photos WHERE photo_id = '".$photo."'";
		$query = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($query);
		if($row['photo_where'] == 1) 
		{
			$pagesql = "SELECT page_founder FROM ".$this->table_prefix."pg_social_pages WHERE page_id = '".$row['user_id']."'";
			$pageresult = $this->db->sql_query($pagesql);
			$rowpage = $this->db->sql_fetchrow($pageresult);
			if($rowpage['page_founder'] == $this->user->data['user_id']) $delphoto = true;
		} 
		elseif($row['user_id'] == $this->user->data['user_id'])
		{
			$delphoto = true;
		}
		
		if($delphoto)
		{
			$photo = $row['photo_file'];
			$file = $this->pg_social_path.'upload/'.$photo;
			$deletePhoto = "DELETE FROM ".$this->table_prefix."pg_social_photos WHERE photo_id = '".$row['photo_id']."'";
			if($this->db->sql_query($deletePhoto) && unlink($file))
			{ 	
				$deletePost = "DELETE FROM ".$this->table_prefix."pg_social_wall_post WHERE post_extra = '".$row['photo_id']."'";
				$this->db->sql_query($deletePost);
				$this->template->assign_vars(array(
					"ACTION"	=>  "deleted",
				));
			}
		}
		return $this->helper->render('activity_status_action.html', "");
	}
	
	/**
	 * Pre and next photo
	*/
	public function prenextPhoto($photo, $ord, $where, $template = true)
	{
		if($ord == 0)
		{
			$orde = ">"; 
			$ordn = "ASC"; 
		}
		else
		{
			$orde = "<";
			$ordn = "DESC";
		}
		$photo_info = $this->getPhoto($photo);
		$sql = "SELECT photo_id FROM ".$this->table_prefix."pg_social_photos WHERE photo_id ".$orde." '".$photo_info['photo_id']."' AND photo_where = '".$photo_info['photo_where']."' AND user_id = '".$photo_info['user_id']."' AND gallery_id = '".$photo_info['gallery_id']."' ORDER BY photo_id ".$ordn." LIMIT 0, 1"; 
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
			
		if($template)
		{
			$this->template->assign_vars(array(
				"ACTION"	=> $row['photo_id'],
			));	
			return $this->helper->render('activity_status_action.html', "");
		}
		else
		{
			if($row['photo_id']) $action = true; else $action = false;
			return $action;
		}		
	}
}