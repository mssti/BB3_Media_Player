<?php
/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: playlist.php, v 0.0.7 2009/10/16 16:10:09Z ameisez Exp $
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

$image	= request_var('image', $bb3media_config_image_path);
$image	= playlist_fix_path($image, true);

$path	= request_var('path', $bb3media_config_media_path);

// There is a default image in therequered folder ?
if (file_exists("$path/albumart.jpg"))
{
	$image = "$path/albumart.jpg";
}
$path	= playlist_fix_path($path, true);

// Get file content from the request folder
$dir_info = playlist_get_dir_info($path, $image);

// Order by file
asort($dir_info);

// Pull the feed
$script_name = $user->lang['BB3_PLAYER_NAME'];

make_rss_playlist($dir_info, $location, $script_name);

?>