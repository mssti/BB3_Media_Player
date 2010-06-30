<?php
/**
* @package: phpBB 3.0.6 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: index.php, v0.0.8 2010/02/24 10:02:24Z ameisez Exp $
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
$file = request_var('file', '');
$item = request_var('item', 0);
$popup = request_var('pop', '');

// Read available folders - Start
$attach_playlist_enabled		= ($bb3media_config['attach_playlist'] && !$bb3media_config['attach_separate']) ? true : false;
$folder_options					= bb3_folder_select($attach_playlist_enabled, $bb3media_config_media_path, $path);
// Read available folders - End

// Read available skins - Start
$skin_enabled					= ($bb3media_config['skin_enabled']) ? true : false;
$skin_options					= bb3_skin_select($bb3media_config_skin_path, $skin_enabled);
// Read available skins - End

// Youtube plug-in
$youtube_enabled				= (@file_exists("{$bb3media_config_folder_path}yt.swf")) ? true : false;

// Youtube search plug-in - Start
$youtube_search_enabled			= ($bb3media_config['youtube_search']) ? true : false;
$youtube_search					= (@file_exists("{$bb3media_config_folder_path}yousearch.swf") && $youtube_enabled && $youtube_search_enabled) ? true : false;
// Youtube search plug-in - End

// Youtube custom play list - Start
$youtube_myplaylist_enabled		= ($youtube_enabled && $bb3media_config['youtube_myplaylist']) ? true : false;
$youtube_myplaylist				= bb3_custom_playlist_select($youtube_myplaylist_enabled);
// Youtube custom play list - End

// Youtube rss play list - Start
$youtube_playlist_enabled		= ($youtube_enabled && $bb3media_config['youtube_playlist']) ? true : false;
$youtube_playlist				= bb3_youtube_playlist_select($youtube_playlist_enabled);
// Youtube rss play list - End

// Dailymotion rss play list - Start
$dailymotion_playlist_enabled	= ($bb3media_config['dailymotion_playlist']) ? true : false;
$dailymotion_playlist			= bb3_dailymotion_playlist_select($dailymotion_playlist_enabled);
// Dailymotion rss play list - End

// Google rss play list - Start
$google_playlist_enabled		= ($bb3media_config['google_playlist']) ? true : false;
$google_playlist				= bb3_google_playlist_select($google_playlist_enabled);
// Google rss play list - End

// Attach rss play list - Start
$attach_playlist_enabled		= ($bb3media_config['attach_playlist'] && $bb3media_config['attach_separate']) ? true : false;
$attachments_playlist			= bb3_attachment_playlist_select($attach_playlist_enabled, $extensions);
// Attach rss play list - End

if ($path && ($path != 'attach'))
{
	$bb3media_config_media_path	.= "/$path";
}

page_header($user->lang['BB3_PLAYER_NAME']);

// Assign index specific vars
$template->assign_vars(array(
	'U_ACTION'				=> append_sid("{$bb3media_config_folder_path}index.$phpEx"),
	'PHP_EX'				=> $phpEx,
	'VERSION'				=> $bb3media_config['script_version'],

	'L_BB3_FLASH_NOTE'		=> sprintf($user->lang['BB3_GET_FLASH'], '<a href="http://www.macromedia.com/go/getflashplayer" onclick="window.open(this.href);return false;">', '</a> '),

	'BB3_SEARCH'			=> $bb3media_config['search'],
	'BB3_SEARCH_AJAX'		=> $bb3media_config['search_ajax'],
	'L_SEARCH_KEYWORDS_EXPLAIN'	=> sprintf($user->lang['SEARCH_KEYWORDS_EXPLAIN'], $word_length['min'], $word_length['max']),
	'BB3_INIT_ITEM'			=> $item,
	'BB3_INIT_FILE'			=> $file,
	'BB3_POPUP'				=> ($popup) ? true : false,
	
	'BB3_IMAGE_PATH'		=> $bb3media_config_image_path,
	'BB3_FOLDER_PATH'		=> $bb3media_config_folder_path,
	'BB3_UPLOAD_PATH'		=> $bb3media_config_media_path,
	'BB3_FOLDER_OPTIONS'	=> $folder_options,
	'BB3_SKIN_OPTIONS'		=> $skin_options,

//	'PLAYLIST'				=> "{$bb3media_config_folder_path}playlist.$phpEx?image={$bb3media_config_image_path}&path={$bb3media_config_media_path}",

	'YOUTUBE_SEARCH'		=> $youtube_search,
	'YOUTUBE_MYPLAYLIST'	=> ($youtube_myplaylist != "\n") ? $youtube_myplaylist : false,
	'YOUTUBE_PLAYLIST'		=> ($youtube_playlist != "\n") ? $youtube_playlist : false,
	'DAILYMOTION_PLAYLIST'	=> ($dailymotion_playlist != "\n") ? $dailymotion_playlist : false,
	'GOOGLE_PLAYLIST'		=> ($google_playlist != "\n") ? $google_playlist : false,
	'ATTACH_PLAYLIST'		=> ($attachments_playlist != "\n") ? $attachments_playlist : false,
	'ATTACH'				=> ($path == 'attach' && $bb3media_config['attach_playlist'] && !$bb3media_config['attach_separate']) ? true : false,
));

// Add my link to the output page footer into the copyright notes ;)
$user->lang['TRANSLATION_INFO'] = (!empty($user->lang['TRANSLATION_INFO'])) ? $user->lang['TRANSLATION_INFO'] . '<br />' . sprintf($user->lang['BB3_AUTHOR_LINK'], $bb3media_config['script_version']) : sprintf($user->lang['BB3_AUTHOR_LINK'], $bb3media_config['script_version']);

$template->set_filenames(array(
	'body' => 'mplayer_body.html')
);

if (!$popup)
{
	make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
}

page_footer();

?>