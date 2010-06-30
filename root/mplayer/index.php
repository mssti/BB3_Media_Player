<?php
/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: index.php, v 0.0.4 2009/10/03 03:10:09Z ameisez Exp $
* @copyright: ameisez < ameisez@yahoo.com > http://www.littlegraffyx.com/ameisez
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: ameisez - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=356834
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
**/

/**
* @ignore
**/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$user->add_lang('mods/mplayer');

// Restrictions
$user->data['is_bot'] = false;
bb3_user_access();

$script_version		= '0.0.5';

$root_folder_path	= $phpbb_root_path . 'mplayer/';

$upload_folder_path	= $root_folder_path . 'media';
$skin_folder_path	= $root_folder_path . 'skin';

$path = request_var('path', '');

$folder_options = '';
if (sizeof($folders = bb3_media_get_folders($upload_folder_path)))
{
//	$folder_options = '<option value="-1" ' . ((!$path) ? 'selected="selected"' : '') . ' class="disabled" disabled="disabled" >' . $user->lang['SELECT'] . '</option>';
	$folder_options.= '<option value="">' . $user->lang['BB3_PLAYLIST_ALL'] . '</option>';
	foreach ($folders as $folder)
	{
		$selected = ($folder == $path) ? ' selected="selected"' : '';
		$folder_options .= '<option value="' . $folder . '"' . $selected . '>' . $folder . '</option>';
	}
}

$skin_options = '';
if (sizeof($skins = bb3_media_get_skin($skin_folder_path)))
{
//	$skin_options = '<option value="-1" selected="selected" class="disabled" disabled="disabled" >' . $user->lang['SELECT'] . '</option>';
	$skin_options.= '<option value="">' . $user->lang['BB3_SKIN_BASE'] . '</option>';
	foreach ($skins as $skin)
	{
		$skin_options .= '<option value="' . $skin . '">' . playlist_get_file_name($skin) . '</option>';
	}
}

if ($path)
{
	$upload_folder_path	.= "/$path";
}

page_header($user->lang['BB3_PLAYER_NAME']);

// Assign index specific vars
$template->assign_vars(array(
//	'PLAYLIST'				=> rawurlencode("{$root_folder_path}YouTube_Playlist_XSPF-3.2.php?playlistid=B8D6FA74D3F2629C"), //&image={$root_folder_path}bb3player_logo.png"),
//	'PLAYLIST'				=> rawurlencode("{$root_folder_path}playlist.php?path={$upload_folder_path}"), //&image={$root_folder_path}bb3player_logo.png"),
	'PLAYLIST'				=> "{$root_folder_path}playlist.php?image={$root_folder_path}bb3player_logo.png&path={$upload_folder_path}",
	'BB3_FOLDER_PATH'		=> $root_folder_path,
	'BB3_UPLOAD_PATH'		=> $upload_folder_path,
	'U_ACTION'				=> append_sid("{$root_folder_path}index.$phpEx"),
	'BB3_FOLDER_OPTIONS'	=> $folder_options,
	'BB3_SKIN_OPTIONS'		=> $skin_options,
	'L_BB3_FLASH_NOTE'		=> sprintf($user->lang['BB3_GET_FLASH'], '<a href="http://www.macromedia.com/go/getflashplayer" onclick="window.open(this.href);return false;">', '</a> '),
));

// Add my link to the output page footer into the copyright notes ;)
$user->lang['TRANSLATION_INFO'] = (!empty($user->lang['TRANSLATION_INFO'])) ? $user->lang['TRANSLATION_INFO'] . '<br />' . sprintf($user->lang['BB3_PLAYER_AMEISEZ_LINK'], $script_version) : sprintf($user->lang['BB3_PLAYER_AMEISEZ_LINK'], $script_version);

$template->set_filenames(array(
	'body' => 'mplayer_body.html')
);

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

page_footer();

/**
* Retrieve information about a folder
*
* @param string		$path	 path to analize
* @return array		with  recursive folder in the given path
**/
function bb3_media_get_folders($path)
{
	$contents	= array();

	if ($handle = @opendir($path))
	{
		while (false !== ($file = readdir($handle)))
		{
			$nextpath = $path . '/' . $file;
			if ($file != '.' && $file != '..' && !is_link($nextpath))
			{
				if (is_dir($nextpath))
				{
					$contents[] = $file;
					if (sizeof($nextpath_folder = bb3_media_get_folders($nextpath)))
					{
						foreach ($nextpath_folder as $folder)
						{
							$contents[] = "$file/$folder";
						}
					}
				}
			}
		}
		closedir ($handle);
	}
	return $contents;
}

function bb3_media_get_skin($path)
{
	$contents	= array();

	if ($handle = @opendir($path))
	{
		while (false !== ($file = readdir($handle)))
		{
			$nextpath = $path . '/' . $file;
			if ($file != '.' && $file != '..' && !is_link($nextpath))
			{
				if (!is_dir($nextpath))
				{
					if (playlist_get_file_extension($file) == 'swf')
					{
						$contents[] = $file;
					}
				}
			}
		}
		closedir ($handle);
	}
	return $contents;
}

/**
* Get file name
**/
function playlist_get_file_name($filename)
{
	$filename = basename($filename);
	$filename = substr($filename, 0, strpos($filename, '.'));

	return $filename;
}

/**
* Check User Specific disable option
**/
function bb3_user_access()
{
	global $user, $db;

	// Search for banned users
	$sql = 'SELECT ban_userid
		FROM ' . BANLIST_TABLE . '
		WHERE ban_userid = ' . (int) $user->data['user_id'] . '
			AND ban_exclude <> 1';
	$result = $db->sql_query($sql);
	$user->data['ban_userid'] = $db->sql_fetchfield('ban_userid');
	$db->sql_freeresult($result);

	// Reject anonymous users, banned User and bots
	if ($user->data['user_id'] == ANONYMOUS || $user->data['ban_userid'] > 0 || $user->data['is_bot'])
	{
		global $phpbb_root_path, $phpEx;

		meta_refresh(10, append_sid("{$phpbb_root_path}index.$phpEx"));

		$user->session_kill();
		$user->session_begin();
		$message = $user->lang['BB3_NO_ACCESS'];
		$message = $message . '<br /><br />' . sprintf($user->lang['RETURN_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a> ');
		trigger_error($message);
	}

	return;
}

/**
* Get file extension
**/
function playlist_get_file_extension($filename)
{
	if (strpos($filename, '.') === false)
	{
		return '';
	}
	$filename = explode('.', $filename);

	return array_pop($filename);
}

?>