<?php
/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: index.php, v 0.0.7 2009/10/16 16:10:09Z ameisez Exp $
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
$folder_options			= bb3_folder_select($bb3media_config_media_path, $path);
// Read available folders - End

// Read available skins - Start
$skin_options			= bb3_skin_select($bb3media_config_skin_path);
// Read available skins - Start

// Youtube plug-in
$youtube_enabled		= (file_exists("{$bb3media_config_folder_path}yt.swf")) ? true : false;

// Youtube search plug-in
$youtube_search			= (file_exists("{$bb3media_config_folder_path}yousearch.swf") && $youtube_enabled) ? true : false;

// Youtube custom play list - Start
$youtube_myplaylist		= bb3_custom_playlist_select($youtube_enabled);
// Youtube custom play list - End

// Youtube rss play list - Start
$youtube_playlist		= bb3_youtube_playlist_select($youtube_enabled);
// Youtube rss play list - End

// Dailymotion rss play list - Start
$dailymotion_playlist	= bb3_dailymotion_playlist_select();
// Dailymotion rss play list - End

// Google rss play list - Start
//$google_playlist		= ''; (external_url_exists($bb3media_config['google_playlist'])) ? $bb3media_config['google_playlist'] : '';
$google_playlist		= bb3_google_playlist_select();
// Google rss play list - End

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

//	'PLAYLIST'				=> "{$bb3media_config_folder_path}playlist.$phpEx?image={$bb3media_config_image_path}&path={$bb3media_config_media_path}",
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