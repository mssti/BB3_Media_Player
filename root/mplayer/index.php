<?php
/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: index.php, v 0.0.6 2009/10/14 14:10:09Z ameisez Exp $
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
include('functions_mplayer.' . $phpEx);

$action = request_var('action', '', true);
if ($action == 'download')
{
	playlist_download();
}

$path = request_var('path', '');

// Read available folders - Start
$folder_options = '';
if (sizeof($folders = bb3_media_get_folders($bb3media_config_media_path)))
{
	$folder_options.= '<option value="">' . $user->lang['BB3_PLAYLIST_ALL'] . '</option>';
	foreach ($folders as $folder)
	{
		$selected = ($folder == $path) ? ' selected="selected"' : '';
		$folder_options .= '<option value="' . $folder . '"' . $selected . '>' . $folder . '</option>';
	}
}
// Read available folders - End

// Read available skins - Start
$skin_options = '';
if (sizeof($skins = bb3_media_get_skin($bb3media_config_skin_path)))
{
	$skin_options.= '<option value="">' . $user->lang['BB3_SKIN_BASE'] . '</option>';
	foreach ($skins as $skin)
	{
		$skin_options .= '<option value="' . $skin . '">' . playlist_get_file_name($skin) . '</option>';
	}
}
// Read available skins - Start

if ($path)
{
	$bb3media_config_media_path	.= "/$path";
}

page_header($user->lang['BB3_PLAYER_NAME']);

// Assign index specific vars
$template->assign_vars(array(
	'U_ACTION'				=> append_sid("{$bb3media_config_folder_path}index.$phpEx"),
	'PHP_EX'				=> $phpEx,

	'L_BB3_FLASH_NOTE'		=> sprintf($user->lang['BB3_GET_FLASH'], '<a href="http://www.macromedia.com/go/getflashplayer" onclick="window.open(this.href);return false;">', '</a> '),

	'BB3_IMAGE_PATH'		=> $bb3media_config_image_path,
	'BB3_FOLDER_PATH'		=> $bb3media_config_folder_path,
	'BB3_UPLOAD_PATH'		=> $bb3media_config_media_path,
	'BB3_FOLDER_OPTIONS'	=> $folder_options,
	'BB3_SKIN_OPTIONS'		=> $skin_options,

	'PLAYLIST'				=> "{$bb3media_config_folder_path}playlist.$phpEx?image={$bb3media_config_image_path}&path={$bb3media_config_media_path}",
	'YOUTUBE_SEARCH'		=> $youtube_search,
	'YOUTUBE_MYPLAYLIST'	=> $youtube_myplaylist,
	'YOUTUBE_PLAYLIST'		=> $youtube_playlist,
	'DAILYMOTION_PLAYLIST'	=> $dailymotion_playlist,
	'GOOGLE_PLAYLIST'		=> $google_playlist,
));

// Add my link to the output page footer into the copyright notes ;)
$user->lang['TRANSLATION_INFO'] = (!empty($user->lang['TRANSLATION_INFO'])) ? $user->lang['TRANSLATION_INFO'] . '<br />' . sprintf($user->lang['BB3_PLAYER_AMEISEZ_LINK'], $bb3media_config['script_version']) : sprintf($user->lang['BB3_PLAYER_AMEISEZ_LINK'], $bb3media_config['script_version']);

$template->set_filenames(array(
	'body' => 'mplayer_body.html')
);

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

page_footer();

?>