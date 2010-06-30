<?php
/**
* @package: phpBB 3.0.6 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: functions_mplayer.php, v0.0.8 2010/02/24 10:02:24Z ameisez Exp $
* @copyright: ameisez < ameisez@yahoo.com > http://www.littlegraffyx.com/ameisez
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: ameisez - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=356834
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
**/

/**
* @ignore
* http://www.longtailvideo.com/support/forum/Modules/8879/Autogenerate-playlist-from-folder-#
* http://www.longtailvideo.com/support/forum/JavaScript-Interaction/10514/Multiple-playlists-in-same-player-#msg71070
* http://www.longtailvideo.com/support/forum/Modules/5752/Frustrated-
**/
/**
* @todo 
* ACP module
**/
if (!defined('IN_PHPBB'))
{
	exit;
}

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

// Add this mod language strings
$user->add_lang(array('mods/mplayer'));

$bb3media_config = array(
	'script_version'		=> '0.0.8',

/* MPLAYER PATH, FILENAME AND LOCATION SETTINGS */
	// Default script folder name
	'root_name'				=> 'mplayer',

	// Default media folder name
	'media_name'			=> 'media',

	// Default skin folder name
	'skin_name'				=> 'skin',

	// Enable skin selector ?
	'skin_enabled'			=> true,

	// Default script image name
	'image_name'			=> 'bb3player_logo.png',

/* MPLAYER BEHAVIOR SETTINGS */
	// Force download link ?
	'force_Download'		=> true,

	// Foce SAFEMODE ? (false = do not use cache, do not check url)
	'safe_mode'				=> false,

	// Default cache duration in hours, if safe_mode is enabled
	'cache_ttl'				=> 24,

/* MPLAYER PLAYLIST SETTINGS */
	// Enable xml files
	'xml_playlist'			=> true,

	// Enable search ?
	'search'				=> true,

	// Enable search trough ajax ?
	'search_ajax'			=> true,

	// Enable attach playlist
	'attach_playlist'		=> true,
	// Display separate attach playlist
	'attach_separate'		=> false,

	// Enable youtube search plug-in
	'youtube_search'		=> true,

	// Enable youtube myplaylist
	'youtube_myplaylist'	=> true,

	// Multiple Custom Youtube video playlist ID
	'youtube_myplaylist_id'	=> array(
		'phpbb3_related'	=> "http://gdata.youtube.com/feeds/api/videos?vq=phpbb3",
		'Funny'				=> 'http://gdata.youtube.com/feeds/api/playlists/B43DE27FC1AEFDFA',
		'cheezburger'		=> 'http://gdata.youtube.com/feeds/api/users/cheezburger/uploads',
	),

	// Enable youtube playlist
	'youtube_playlist'		=> true,

	// Multiple Youtube video playlist
	'youtube_playlist_id'	=> array(
		'recently_featured'	=> 'http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured',
		'top_rated'			=> 'http://gdata.youtube.com/feeds/api/standardfeeds/top_rated',
		'top_favorites'		=> 'http://gdata.youtube.com/feeds/api/standardfeeds/top_favorites',
		'most_viewed'		=> 'http://gdata.youtube.com/feeds/api/standardfeeds/most_viewed',
		'most_recent'		=> 'http://gdata.youtube.com/feeds/api/standardfeeds/most_recent',
		'most_discussed'	=> 'http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed',
		'most_linked'		=> 'http://gdata.youtube.com/feeds/api/standardfeeds/most_linked',
		'most_responded'	=> 'http://gdata.youtube.com/feeds/api/standardfeeds/most_responded',
		'watch_on_mobile'	=> 'http://gdata.youtube.com/feeds/api/standardfeeds/watch_on_mobile',
	),

	// Enable Dailymotion playlist
	'dailymotion_playlist'		=> true,

	// Multiple Dailymotion video playlist
	'dailymotion_playlist_id'	=> array(
		'Recent'			=> "http://www.dailymotion.com/rss",
		'Creative'			=> "http://www.dailymotion.com/rss/creative",
		'Featured'			=> "http://www.dailymotion.com/rss/featured",
		'Animals'			=> "http://www.dailymotion.com/rss/featured/channel/animals",
	),

	// Enable google playlist
	'google_playlist'		=> true,

	// Multiple Google video playlist
	'google_playlist_id'	=> array(
		'top_100_new'		=> "http://video.google.com/videofeed?type=top100new",
		'popular'			=> "http://video.google.com/videofeed?type=popular",
		'Recent'			=> "http://video.google.com/videosearch?q=recent",
		'Funny'				=> "http://video.google.com/videosearch?q=funny",
		'Animals'			=> "http://video.google.com/videosearch?q=animals",
	),

/* MPLAYER LOCAL SEARCH SETTINGS */
	// search for mp3 and flv files : Supported formats: See JW FLV Player : http://www.longtailvideo.com/support/faq
	'extensions'			=> array(
		'mp3',
	//	'mp4',
	//	'aac',
	//	'jgp',
	//	'gif',
	//	'png',
		'flv',
	//	'swf',
	)
);
/******************************************************************************************************************************************************************************************************
* DO NOT CHANGE ANYTHING AFTER THIS LINE!
******************************************************************************************************************************************************************************************************/

// Restrictions - Start
bb3_user_access();
// Restrictions - End

// Direct link to the board
$location	 = generate_board_url();
// Determine board url - we may need it later
// $location = (defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? $location : $phpbb_root_path;

$extensions	 = $bb3media_config['extensions'];
/**
*  Some fix and checks
**/
// Path to this script
$bb3media_config_folder_path	= $phpbb_root_path . $bb3media_config['root_name'];
if (substr($bb3media_config_folder_path, -1, 1) != '/')
{
	$bb3media_config_folder_path .= '/';
}

// Path to the default image
$bb3media_config_image_path		= $bb3media_config_folder_path . $bb3media_config['image_name'];

// path to the directory you want to scan for media files, by default
$bb3media_config_media_path		= $bb3media_config_folder_path . $bb3media_config['media_name'];

// Path to the skin folder
$bb3media_config_skin_path		= $bb3media_config_folder_path . $bb3media_config['skin_name'];

// Select which method we'll use
if (basename($config['search_type']) == 'fulltext_native')
{
	$word_length = array('min' => $config['fulltext_native_min_chars'], 'max' => $config['fulltext_native_max_chars']);
}
else
{
	$word_length = array('min' => $config['fulltext_mysql_min_word_len'], 'max' => $config['fulltext_mysql_max_word_len']);
}

define('FORCEDOWNLOAD', ($bb3media_config['force_Download']) ? true : false);
define('BB3_SAFEMODE', ($bb3media_config['safe_mode']) ? true : false);

/*****************************************************************************************************************************************************************************************************/
/* Common functions                                                                                                                                                                                  */
/*****************************************************************************************************************************************************************************************************/

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

		$user->session_kill();
		$user->session_begin();

		meta_refresh(10, append_sid("{$phpbb_root_path}index.$phpEx"));
		$message = $user->lang['BB3_NO_ACCESS'] . '<br /><br />' . sprintf($user->lang['RETURN_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a> ');
		trigger_error($message);
	}
	return;
}

/**
* Read available folders
*
* @param bool		$attach_playlist_enabled
* @param string		$bb3media_config_media_path
* @param string		$path
* @return html selector
**/
function bb3_folder_select($attach_playlist_enabled, $bb3media_config_media_path, $path)
{
	global $user;

	$folder_options = "\n";

	if (sizeof($folders = bb3_media_get_folders($bb3media_config_media_path)))
	{
		$folder_options.= '<option value="">' . $user->lang['BB3_PLAYLIST_ALL'] . '</option>' . "\n";
		foreach ($folders as $folder)
		{
			$selected = ($folder == $path) ? ' selected="selected"' : '';
			$folder_options .= '<option value="' . $folder . '"' . $selected . '>' . $folder . '</option>' . "\n";
		}
	}

	if ($attach_playlist_enabled)
	{
		$selected = ($path == 'attach') ? ' selected="selected"' : '';
		$folder_options	 .= '<option value="attach"' . $selected . '>' . $user->lang['ATTACHMENTS'] . '</option>' . "\n";
	}
	return $folder_options;
}

/**
* Retrieve information about a folder
*
* @param string		$path	 path to analize
* @return array		with recursive folder in the given path
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
					// Check if the path contains media files
					if (sizeof($dir_info = playlist_get_dir_info($nextpath, '', true)))
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
		}
		closedir ($handle);
	}
	return $contents;
}

/**
* Read available skins
*
* @param string		$bb3media_config_skin_path
* @return html selector
**/
function bb3_skin_select($bb3media_config_skin_path, $skin_enabled = true)
{
	$skin_options = '';

	if ($skin_enabled)
	{
		global  $user;

		if (sizeof($skins = bb3_media_get_skin($bb3media_config_skin_path)))
		{
			$skin_options.= '<option value="">' . $user->lang['BB3_SKIN_BASE'] . '</option>';
			foreach ($skins as $skin)
			{
				$skin_options .= '<option value="' . $skin . '">' . bb3media_get_file_name($skin) . '</option>';
			}
		}
	}
	return $skin_options;
}

/**
* Read available skins
*
* @param string		$path
* @return array
**/
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
					if (bb3media_get_file_extension($file) == 'swf')
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
*
* @param string		$filename
* @return string
**/
function bb3media_get_file_name($filename)
{
	$filename = basename($filename);
	$filename = substr($filename, 0, strpos($filename, '.'));

	return $filename;
}

/**
 * Get file extension
 *
 * @param string	$filename
 * @return string
 */
function bb3media_get_file_extension($filename)
{
	if (strpos($filename, '.') === false)
	{
		return '';
	}
	$filename = explode('.', $filename);

	return array_pop($filename);
}

/**
* Checks if a path ($path) is absolute or relative
*
* @param string		$path		Path to check
* @param boolean	$absolute	true = absolute | false = relative
* @return string	$path		absolute or relative
**/
function bb3media_fix_path($path = '', $absolute = true)
{
	global $phpbb_root_path, $location;

	if ($absolute)
	{
		if (strpos($path, $phpbb_root_path) === false)
		{
			$path = "$location/" . $path;
		}
		else
		{
			$path = str_replace($phpbb_root_path, "$location/", $path);
		}
	}
	else
	{
		if (strpos($path, $phpbb_root_path) === false)
		{
			$path = str_replace("$location/", $phpbb_root_path, $path);
		}
	}

	// Strip / from the end
	if (substr($path, -1, 1) == '/')
	{
		$path = substr($path, 0, -1);
	}

	return $path;
}

/**
* Retrieve information about a folder
*
* @param string		$path	 path to analize
* @param string		$default_image
* @param bool		$recursive
* @return array
**/
function playlist_get_dir_info($path, $default_image, $recursive = false)
{
	global $phpbb_root_path, $phpEx, $user;
	global $extensions, $location, $bb3media_config;

	$contents	= array();

	// Path must be relative 
	$path = bb3media_fix_path($path, false);

	if ($handle = @opendir($path))
	{
		while (false !== ($file = readdir($handle)))
		{
			$nextpath = "$path/$file";

			if ($bb3media_config['xml_playlist'] && bb3media_get_file_extension($file) == 'xml')
			{
				if (!$recursive)
				{
					$videoxml	= bb3_rss_load($nextpath, 'testing xml playlist');
					print_r($videoxml);
					die();
					break;
				}
				else
				{
					$contents[$file] = array();
				}
			}
			else if (!$bb3media_config['xml_playlist'] && bb3media_get_file_extension($file) == 'xml')
			{
				if ($recursive)
				{
					closedir ($handle);
					return array();
				}
			}

			if ($file != '.' && $file != '..' && !is_link($nextpath))
			{
				if (is_dir($nextpath))
				{
					$contents = array_merge($contents, playlist_get_dir_info($nextpath, $default_image, true));
				}
				else if (is_file($nextpath))
				{
					if (in_array(bb3media_get_file_extension($nextpath), $extensions))
					{
						$contents_name	= bb3media_get_file_name($nextpath);
						$contents_file	= preg_replace(array("#\\\#", "#$phpbb_root_path#"), array("/", "$location/"), $nextpath);
						$contents_ext	= bb3media_get_file_extension($nextpath);
						$contents_img	= (file_exists("$path/$contents_name.jpg")) ? bb3media_fix_path("$path/$contents_name.jpg", true) : $default_image;
						$contents_desc	= ''; // "($contents_ext) ";

						// Force Download Script
						if (FORCEDOWNLOAD)
						{
							$download_url		= str_replace("$location/", '', $contents_file);
							$contents_download	= append_sid("$location/mplayer/index.$phpEx", array('action' => 'download', 'redirect' => urlencode($download_url)));
						}
						else
						{
							$contents_download = $contents_file;
						}

						// Check for ID3
						if ($contents_ext == 'mp3' && in_array($contents_ext, $extensions))
						{
							$myId3 = new ID3($nextpath);
							if ($myId3->getInfo())
							{
								$Id3_title	= $myId3->getTitle();
								$Id3_artist	= $myId3->getArtist();
								$Id3_album	= $myId3->getAlbum();
								$Id3_gender	= $myId3->getGender();
								$Id3_year	= $myId3->getYear();
								$Id3_track	= $myId3->getTrack();

								$contents_desc.= ($Id3_title)  ? ' ' . $user->lang['BB3_ID3_TITLE'] . ':' . $Id3_title : '';
								$contents_desc.= ($Id3_artist) ? ' ' . $user->lang['BB3_ID3_ARTIST'] . ':' . $Id3_artist : '';
								$contents_desc.= ($Id3_album)  ? ' ' . $user->lang['BB3_ID3_ALBUM'] . ':' . $Id3_album : '';
								$contents_desc.= ($Id3_gender) ? ' ' . $user->lang['BB3_ID3_GENRE'] . ':' . $Id3_gender : '';
								$contents_desc.= ($Id3_year)   ? ' ' . $user->lang['BB3_ID3_YEAR'] . ':' . $Id3_year : '';
								$contents_desc.= ($Id3_track)  ? ' ' . $user->lang['BB3_ID3_TRACK'] . ':' . $Id3_track : '';
							}
						}

						$contents[$file] = array(
							'thumb'			=> $contents_img,
							'title'			=> $contents_name,
							'description'	=> $contents_desc,
							'file'			=> $contents_file,
							'extension' 	=> $contents_ext,
							'date'			=> bb3_date2822(false, @filemtime($nextpath)),
							'download'		=> $contents_download,
						);
					}
				}
			}
		}
		closedir ($handle);
	}

	return $contents;
}

/**
* Forcing a Download Dialog
*
* Code from :
* 	http://apptools.com/phptools/force-download.php
* 	http://www.codingforums.com/archive/index.php/t-168760.html
**/
function playlist_download()
{
	global $user, $phpbb_root_path;

	$error			= array();
	$request_file	= '';
	$request_folder	= '';
	$user_folder	= '';
	$user_files		= array();

	// did we get a parameter telling us what file to download?
	$request = request_var('redirect', '');

	if(!$request)
	{
		$error[] = $user->lang['BB3_DOWNLOAD_ERROR'];
	}
	else
	{
		// Get a clear file name
		$request_file	= basename($request);

		if(!$request_file)
		{
			$error[] = $user->lang['BB3_DOWNLOAD_NOFILE'];
		}

		// Get a clear folder name
		$request_folder	= reverse_strrchr($request, '/', true);

		// Built an valid path to the required folder
		$user_folder	= $phpbb_root_path . $request_folder;

		// Retrieve information about a folder
		$dir_info		= playlist_get_dir_info($user_folder);
		foreach ($dir_info as $files => $file)
		{
			$user_files[] = $files;
		}

		// Check if the file already exist 
		if (!in_array($request_file, $user_files))
		{
			$error[] = sprintf($user->lang['BB3_DOWNLOAD_NOVALID'], $request_file);
		}
	}

	// if we didn't get any errors above
	if (!sizeof($error))
	{
		// Built an valid path to the required file
		$file = $user_folder . $request_file;

		// Get the extension
		$ext = bb3media_get_file_extension($request_file);

		// If the file exists
		if (@file_exists($file))
		{
			// If file is readable
			if (@is_readable($file))
			{
				$real_filename = basename($request);

				if (@ob_get_length())
				{
					@ob_end_clean();
				}

				// We force application/octetstream for all files
				$mimetype = (strpos(strtolower($user->browser), 'msie') !== false || strpos(strtolower($user->browser), 'opera') !== false) ? 'application/octetstream' : 'application/octet-stream';

				// Now send the File Contents to the Browser
				$size = @filesize($file);

				/**
				* Send out the Headers - Start
				**/
				// leave blank to avoid IE errors
				header('Cache-Control: ');

				// leave blank to avoid IE errors
				header('Pragma: ');

				header('Content-Type: ' . $mimetype);

				$is_ie8 = (strpos(strtolower($user->browser), 'msie 8.0') !== false);
				if ($is_ie8)
				{
					header('X-Content-Type-Options: nosniff');
				}

				// Do not set Content-Disposition to inline please, it is a security measure for users using the Internet Explorer.
				$user_agent = (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : '';
				if (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Safari') !== false || strpos($user_agent, 'Konqueror') !== false)
				{
					header('Content-Disposition: attachment; ' . "filename=" . rawurlencode($real_filename));
				}
				else
				{	// follow the RFC for extended filename for the rest
					header('Content-Disposition: attachment; ' . "filename*=UTF-8''" . rawurlencode($real_filename));
				}

				if ($size)
				{
					header("Content-Length: $size");
				}

				readfile($file);
				flush();
				exit;
			}
			else
			{
				// file is not readable
				$error[] =  sprintf($user->lang['BB3_DOWNLOAD_NOREAD'], $request_file);
			}
		}
		else
		{
			// the file does not exist
			$error[] =  sprintf($user->lang['BB3_DOWNLOAD_NOFOUND'], $request_file);
		}
	}

	$msg_title	= $user->lang['BB3_DOWNLOAD_FAIL'];
	$msg_title	= (!isset($msg_title)) ? $user->lang['GENERAL_ERROR'] : ((!empty($user->lang[$msg_title])) ? $user->lang[$msg_title] : $msg_title);
	$msg_text	= (sizeof($error)) ? implode('<br />', $error) : '';

	// if all went well, the exit above will prevent anything below from showing otherwise, we'll display an error message we created above
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">';
	echo '<head>';
	echo '	<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
	echo '	<title>' . $msg_title . '</title>';
	echo '<style type="text/css">' . "\n" . '/* <![CDATA[ */' . "\n";
	echo '* { margin: 0; padding: 0; } html { font-size: 100%; height: 100%; margin-bottom: 1px; background-color: #E4EDF0; } body { font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif; color: #536482; background: #E4EDF0; font-size: 62.5%; margin: 0; } ';
	echo 'a:link, a:active, a:visited { color: #006699; text-decoration: none; } a:hover { color: #DD6900; text-decoration: underline; } ';
	echo '#wrap { padding: 0 20px 15px 20px; min-width: 615px; } #page-header { text-align: right; height: 40px; } #page-footer { clear: both; font-size: 1em; text-align: center; } ';
	echo '.panel { margin: 4px 0; background-color: #FFFFFF; border: solid 1px  #A9B8C2; } ';
	echo '#errorpage #page-header a { font-weight: bold; line-height: 6em; } #errorpage #content { padding: 10px; } #errorpage #content h1 { line-height: 1.2em; margin-bottom: 0; color: #DF075C; } ';
	echo '#errorpage #content div { margin-top: 20px; margin-bottom: 5px; border-bottom: 1px solid #CCCCCC; padding-bottom: 5px; color: #333333; font: bold 1.2em "Lucida Grande", Arial, Helvetica, sans-serif; text-decoration: none; line-height: 120%; text-align: left; } ';
	echo "\n" . '/* ]]> */' . "\n";
	echo '</style>';
	echo '</head>';
	echo '<body id="errorpage">';
	echo '<div id="wrap">';
	echo '	<div id="content">';
	echo '		<h1>' . $msg_title . '</h1>';
	echo '		<div>' . $msg_text . '</div>';
	echo '	</div>';
	echo '</body>';
	echo '</html>';

	exit_handler();
	exit;

}

/** 
* @source : http://us2.php.net/manual/en/function.strrchr.php#64157
* @param string		$haystack	string to search in
* @param string		$needle		string to search for
* @param bool		$trail		 use $trail to include needle chars including and past last needle
* @return string	Return everything up to last instance of needle
**/
function reverse_strrchr($haystack, $needle, $trail = 0)
{
    return strrpos($haystack, $needle) ? substr($haystack, 0, strrpos($haystack, $needle) + $trail) : false;
}

/**
* Enter description here...
*
* @param unknown_type $rss_url
* @param unknown_type $rss_name
* @param unknown_type $rss_ttl
* @return unknown
**/
function bb3_rss_load($rss_url, $rss_name = '', $rss_ttl = 0)
{
	global $cache, $phpEx;

	if (!$rss_ttl)
	{
		global $bb3media_config;
		$rss_ttl = ((60 * 60) * (int) $bb3media_config['cache_ttl']); // 24 hours ( 60 second * 60 minutes * 24 hours = 86400 seconds )
	}

	$rss_contents = '';
	// Foce SAFEMODE ? (false = do not use cache, do not check url)
	if (BB3_SAFEMODE && $rss_name)
	{
		// Is there an exist "copy" of the feed ?
		if ($rss_contents = $cache->get($rss_name))
		{
			// Need to check if it is valid yet
			$rss_cachename	= $cache->cache_dir . "data{$rss_name}.$phpEx";
			$rss_filetime = @filemtime($rss_cachename);

			if (($rss_filetime + $rss_ttl) < time())
			{
				$rss_contents = '';
			}
		}
	}

	// If not, or the "copy" was old, recreate it
	if (!$rss_contents)
	{
		$rss_contents = @file_get_contents($rss_url);

		// Recreate the cache if we need it
		if ($rss_contents && (BB3_SAFEMODE && $rss_name))
		{
			$cache->destroy($rss_name);		
			$cache->put($rss_name, $rss_contents, $rss_ttl);
		}
	}
/**
	// Finally check the feed fontent 
	if (!$rss_contents)
	{
		trigger_error('Unable to access/read “Google fees”.');
	}
**/
	return $rss_contents;
}

/**
* Retrieve contents from remotely stored file
*
* xml2array() will convert the given XML text to an array in the XML structure.
* Link: http://www.bin-co.com/php/scripts/xml2array/
**/
function xml2array($contents, $get_attributes=1,  $encoding = 'UTF-8')
{
	if (!$contents) return array();

	if (!function_exists('xml_parser_create'))
	{
		return array();
	}

	$parser = xml_parser_create($encoding);
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $contents, $xml_values);
	xml_parser_free($parser);

	if (!$xml_values)
	{
		return;
	}

	$xml_array	 = array();
	$parents	 = array();
	$opened_tags = array();
	$arr		 = array();
	$current	 = &$xml_array;

	foreach ($xml_values as $data)
	{
		unset ($attributes, $value);
		extract($data);
		$result = '';
		if ($get_attributes)
		{
			$result = array();
			if (isset($value))
			{
				$result['value'] = $value;
			}
			if (isset($attributes))
			{
				foreach ($attributes as $attr => $val)
				{
					if ($get_attributes == 1)
					{
						$result['attr'][$attr] = $val;
					}
				}
			}
		}
		else if (isset($value))
		{
			$result = $value;
		}

		if ($type == "open")
		{
			$parent[$level-1] = &$current;
			if (!is_array($current) or (!in_array($tag, array_keys($current))))
			{
				$current[$tag] = $result;
				$current = &$current[$tag];
			}
			else
			{
				if (isset($current[$tag][0]))
				{
					array_push($current[$tag], $result);
				}
				else
				{
					$current[$tag] = array($current[$tag], $result);
				}

				$last = count($current[$tag]) - 1;
				$current = &$current[$tag][$last];
			}
		}
		else if ($type == "complete")
		{
			if (!isset($current[$tag]))
			{
				$current[$tag] = $result;
			}
			else
			{
				if ((is_array($current[$tag]) and $get_attributes == 0) or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1))
				{
					array_push($current[$tag], $result);
				}
				else
				{
					$current[$tag] = array($current[$tag],$result);
				}
			}
		}
		else if ($type == 'close')
		{
			$current = &$parent[$level-1];
		}
	}

	return($xml_array);
}

/**
* Build a xml within result
*
* @param array $feeds
* @param string $location
* @param string $title
**/
function make_rss_playlist($feeds, $location, $title)
{
	// generate playlist
	header('Content-type: text/xml; charset=UTF-8');
	header('Cache-Control: private, no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	header('Expires: 0');
	header('Pragma: no-cache');

	print ('<rss version="2.0" 
		xmlns:media="' . $location . '"
		xmlns:jwplayer="http://developer.longtailvideo.com/trac/wiki/FlashFormats">
		<channel>
			<title>' . $title . '</title>');

	if (sizeof($feeds))
	{
		foreach ($feeds as $items => $item)
		{
			$item['link']		= (isset($item['download']))	? $item['download'] : (isset($item['link']) ? $item['link'] : $location);
			$item['description']= (isset($item['description']))	? $item['description'] : $user->lang['BB3_NO_DESCRIPTION'];
			$item['date']		= (isset($item['date'])    )	? $item['date'] : bb3_date2822();
			$item['duration']	= (isset($item['duration']))	? $item['duration'] : '0';
			print ('
				<item>
					<title>' . $item['title'] . '</title>
					<link>' . $item['link'] . '</link>
					<description>' . $item['description'] . '</description>
					<pubDate>' . $item['date'] . '</pubDate>
					<media:content url="' . $item['file'] . '" duration="' . $item['duration'] . '" />
					<media:thumbnail url="' . $item['thumb'] . '" />
				</item>');
		}
	}
	else
	{
		global $user;

			print ('
				<item>
					<title>' . $title . ' ' . $user->lang['BB3_PLAYLIST_ERROR_TITLE'] . '</title>
					<description>' . $user->lang['BB3_PLAYLIST_ERROR_DESC'] . '</description>
					<media:content url="' . $location . '&amp;type=flv" duration=""/>
				</item>');
	}

	print ('
		</channel>
	</rss>');
}

/**
* Build a a table within result for being used trugh Ajax
*
* @param array $feeds
* @param string $location
* @param string $title
**/
function make_ajax_playlist($feeds, $location, $title)
{
	global $user;

	$xml = '<table cellspacing="1" cellpadding="0" width="100%" border="0">' . "\n";

	$number = 1;
	if (sizeof($feeds))
	{
		foreach ($feeds as $items => $item)
		{
			$xml.= '<tr><td class="h"><a href="' . $item['title'] . '" onclick="javascript:playlist_change(\'' . $item['title'] . '\'); return false;" title="' . $user->lang['BB3_PLAY_ME'] . '" alt="' . $user->lang['BB3_PLAY_ME'] . '" >' . $item['title'] . '</a></td></tr>' . "\n";
		//	$xml.= '<tr><td class="h"><a href="' . $item['file'] . '" onclick="javascript:playlist_change(this.href); return false;" title="' . $user->lang['BB3_PLAY_ME'] . '" alt="' . $user->lang['BB3_PLAY_ME'] . '" >' . $item['title'] . '</a></td></tr>' . "\n";

			$xml.= '<tr class="col' . (($number & 1) ? '1' : '2') . '"><td>';
			
			if (isset($item['thumb']))
			{
				$xml.= '<img src="' . $item['thumb'] . '" alt="" title="" />';
			}

			if (isset($item['description']) && $item['description'] != '')
			{
				$xml.= $item['description'] . '<br />';
			}

			if (isset($item['extension']) && $item['extension'] != '')
			{
				$xml.= $item['extension'] . '<br />';
			}

			if (isset($item['date']))
			{
				$xml.= $item['date'] . '<br />';
			}

			$xml.= '</td></tr>' . "\n";

			$number++;
		}
	}
	else
	{
		$xml.= '<tr><td class="h">' . $title . ' ' . $user->lang['BB3_PLAYLIST_ERROR_TITLE'] . '</td></tr>' . "\n";
		$xml.= '<tr class="col1"><td>' . $user->lang['BB3_PLAYLIST_ERROR_DESC'] . '</td></tr>' . "\n";
	}

	$xml.= '</table>' . "\n";

	print_r($xml);
}

/**
* Check for string inside a string
*
* @param string $haystack
* @param array $needle
* @return bolean
**/
function bb3_media_search($haystack, $needle)
{
	if (!is_array($needle))
	{
		$needle = array($needle);
	}
	$haystack	= utf8_normalize_nfc($haystack);

	foreach ($needle as $need)
	{
		if (strpos(utf8_strtolower(trim($haystack)), utf8_strtolower(trim($need))) !== false /*|| strtolower($haystack) == strtolower($need)*/)
		{
			// string needle found in haystack
			return true;
		}
	}
	return false;
}

/**
* Get date in RFC2822 format
*
* @param $forced	bool 	force time to 0 
* @param $timestamp	integer	the time
* @param $timezone	integer	the time zone
* 
* @return string	string	date in RFC2822 format
* Code based off : 
* 	http://cyber.law.harvard.edu/rss/rss.html#requiredChannelElements
* 	http://www.faqs.org/rfcs/rfc2822 3.3
**/
function bb3_date2822($forced = false, $timestamp = 0, $timezone = 0)
{
	global $config;

	// Local differential hours+min. (HHMM) ( ("+" / "-") 4DIGIT ); 
	$timezone  = ($timezone) ? $timezone   : $config['board_timezone'];
	$timezone  = $timezone + $config['board_dst'];
	$timezone  = ($timezone > 0) ? '+' . $timezone : $timezone;
	$tz = $tzhour = $tzminutes = '';

	$matches = array();
	if (preg_match('/^([\-+])?([0-9]+)?(\.)?([0-9]+)?$/', $timezone, $matches))
	{
		$tz			= isset($matches[1] ) ? $matches[1] : $tz;
		$tzhour		= isset($matches[2] ) ? str_pad($matches[2], 2, "0", STR_PAD_LEFT) : $tzhour;
		$tzminutes	= isset($matches[4] ) ? (($matches[4] == '75') ? '45' : '30') : '00';
		$timezone	= $tz . $tzhour . $tzminutes;
	}
	$timezone  = ((int) $timezone == 0) ? 'GMT' : $timezone;

	$date_time = ($timestamp) ? $timestamp : time();
	$date_time = ($forced) ? date('D, d M Y 00:00:00', $date_time) : date('D, d M Y H:i:s', $date_time);

	return $date_time . ' ' . $timezone;
}

/**
* Youtube custom play list
* 	http://code.google.com/intl/es-AR/apis/youtube/developers_guide_protocol.html
*
* @param unknown_type $bb3media_config_media_path
**/
function bb3_custom_playlist_select($youtube_enabled = true)
{
	global $bb3media_config, $template, $user;

	$youtube_myplaylist	= "\n";
	if ($youtube_enabled)
	{
		if (!is_array($bb3media_config['youtube_myplaylist_id']))
		{
			$bb3media_config['youtube_myplaylist_id'] = array('' => $bb3media_config['youtube_myplaylist_id']);
		}

		foreach ($bb3media_config['youtube_myplaylist_id'] as $myplaylist_name => $myplaylist_id)
		{
			if (external_url_exists($myplaylist_id))
			{
				$template->assign_block_vars('myyoutube', array(
					'PLAYLIST_NAME'	=> $myplaylist_name,
					'PLAYLIST_URL'	=> $myplaylist_id . ((strpos($myplaylist_id, '?') !== false) ? '&' : '?') . "max-results=50",
				));
				$myplaylist_lang_name = (isset($user->lang[strtoupper($myplaylist_name)])) ? $user->lang[strtoupper($myplaylist_name)] : str_replace('_', ' ', $myplaylist_name);
				$youtube_myplaylist .= '<option value="' . $myplaylist_name . '">' . $user->lang['BB3_YOUTUBE_MYPLAYLIST'] . ': ' . $myplaylist_lang_name . '</option>' . "\n";
			}
		}
	}

	return $youtube_myplaylist;
}

/**
* Youtube play list
*
* @param bool	$youtube_enabled
* @return html selector
**/
function bb3_youtube_playlist_select($youtube_enabled = true)
{
	global $bb3media_config, $template, $user;

	$youtube_playlist = "\n";
	if ($youtube_enabled)
	{
		if (!is_array($bb3media_config['youtube_playlist_id']))
		{
			$bb3media_config['youtube_playlist_id'] = array('' => $bb3media_config['youtube_playlist_id']);
		}

		foreach ($bb3media_config['youtube_playlist_id'] as $playlist_name => $playlist_url)
		{
			$playlist		= ($playlist_url) ? $playlist_url : '';
		//	$playlist		= (external_url_exists($playlist)) ? $playlist : '';
			if (external_url_exists($playlist))
			{
				$template->assign_block_vars('youtube', array(
					'PLAYLIST_NAME'	=> $playlist_name,
					'PLAYLIST_URL'	=> $playlist_url,
				));
				$playlist_lang_name = (isset($user->lang[strtoupper($playlist_name)])) ? $user->lang[strtoupper($playlist_name)] : str_replace('_', ' ', $playlist_name);
				$youtube_playlist .= '<option value="' . $playlist_name . '">' . $user->lang['BB3_YOUTUBE_PLAYLIST'] . ': ' . $playlist_lang_name . '</option>' . "\n";
			}
		}
	}

	return $youtube_playlist;
}

/**
* Dailymotion play list
* @param bool	$dailymotion_enabled
* @return html selector
**/
function bb3_dailymotion_playlist_select($dailymotion_enabled = true)
{
	global $bb3media_config, $template, $user;

	$dailymotion_playlist = "\n";

	if ($dailymotion_enabled)
	{
		if (!is_array($bb3media_config['dailymotion_playlist_id']))
		{
			$bb3media_config['dailymotion_playlist_id'] = array('' => $bb3media_config['dailymotion_playlist_id']);
		}

		foreach ($bb3media_config['dailymotion_playlist_id'] as $playlist_name => $playlist_url)
		{
			if (external_url_exists($playlist_url))
			{
				$template->assign_block_vars('dailymotion', array(
					'PLAYLIST_NAME'	=> $playlist_name,
					'PLAYLIST_URL'	=> urlencode(str_replace('http://www.dailymotion.com/rss', '', $playlist_url)),
				));
				$playlist_lang_name = (isset($user->lang[strtoupper($playlist_name)])) ? $user->lang[strtoupper($playlist_name)] : str_replace('_', ' ', $playlist_name);
				$dailymotion_playlist .= '<option value="' . $playlist_name . '">' . $user->lang['BB3_DAILYMOTION_PLAYLIST'] . ': ' . $playlist_lang_name . '</option>' . "\n";
			}
		}
	}

	return $dailymotion_playlist;
}

/**
* Google play list
* @param bool	$google_enabled
* @return html selector
**/
function bb3_google_playlist_select($google_enabled = true)
{
	global $bb3media_config, $template, $user;

	$google_playlist = "\n";

	if ($google_enabled)
	{
		if (!is_array($bb3media_config['google_playlist_id']))
		{
			$bb3media_config['google_playlist_id'] = array('' => $bb3media_config['google_playlist_id']);
		}

		foreach ($bb3media_config['google_playlist_id'] as $playlist_name => $playlist_url)
		{
			if (external_url_exists($playlist_url))
			{
				$template->assign_block_vars('google', array(
					'PLAYLIST_NAME'	=> $playlist_name,
					'PLAYLIST_URL'	=> urlencode(str_replace('http://video.google.com/', '', $playlist_url)),
				));
				$playlist_lang_name = (isset($user->lang[strtoupper($playlist_name)])) ? $user->lang[strtoupper($playlist_name)] : str_replace('_', ' ', $playlist_name);
				$google_playlist .= '<option value="' . $playlist_name . '">' . $user->lang['BB3_GOOGLE_PLAYLIST'] . ': ' . $playlist_lang_name . '</option>' . "\n";
			}
		}
	}

	return $google_playlist;
}

/**
* Attach play list separate selector
*
* @param bool $attach_enabled
* @param unknown_type $extensions
* @return html selector
**/
function bb3_attachment_playlist_select($attach_enabled = true, $extensions)
{
	global $db, $template, $user;
	global $phpbb_root_path, $phpEx, $location;

	$attach_myplaylist = "\n";

	if ($attach_enabled)
	{
		// Collect post and topic ids for later use if we need to touch remaining entries (if resync is enabled)
		$sql = 'SELECT attach_id, physical_filename, real_filename, attach_comment, extension, filetime, post_msg_id
				FROM ' . ATTACHMENTS_TABLE . '
				WHERE ' . $db->sql_in_set('extension', $extensions) . '
				ORDER BY filetime DESC, post_msg_id ASC';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('attach', array(
				'PLAYLIST_NAME'	=> $row['real_filename'],
				'PLAYLIST_URL'	=> append_sid($location . '/download/file.' . $phpEx, array('id' => $row['attach_id'], 'type' => $row['extension']), true, ''),
			));
			$attach_myplaylist .= '<option value="' . $row['real_filename'] . '">' . $row['real_filename'] . '</option>' . "\n";
		}
		$db->sql_freeresult($result);
	}

	return $attach_myplaylist;
}

/**
* Attach play list within the Available playlist selector
*
* @param array		$extensions
* @param string		$default_image
* @return array
**/
function playlist_get_attachment_info($extensions, $default_image)
{
	global $db, $template, $user;
	global $phpbb_root_path, $phpEx, $location;

	$contents	= array();

	// There is a default image in therequered folder ?
	if (file_exists($phpbb_root_path . "files/albumart.jpg"))
	{
		$default_image = $location . "/files/albumart.jpg";
	}

	// Collect post and topic ids for later use if we need to touch remaining entries (if resync is enabled)
	$sql = 'SELECT attach_id, physical_filename, real_filename, attach_comment, extension, filetime, post_msg_id, filetime
			FROM ' . ATTACHMENTS_TABLE . '
			WHERE ' . $db->sql_in_set('extension', $extensions) . '
			ORDER BY filetime DESC, post_msg_id ASC';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$contents_desc = '';
/**
		if ($row['extension'] == 'mp3' && in_array($row['extension'], $extensions))
		{
			$path = $location . '/files/' . $row['physical_filename'];
			$myId3 = new ID3($path);
			if ($myId3->getInfo())
			{
				$Id3_title	= $myId3->getTitle();
				$Id3_artist	= $myId3->getArtist();
				$Id3_album	= $myId3->getAlbum();
				$Id3_gender	= $myId3->getGender();
				$Id3_year	= $myId3->getYear();
				$Id3_track	= $myId3->getTrack();

				$contents_desc.= ($Id3_title)  ? ' ' . $user->lang['BB3_ID3_TITLE'] . ':' . $Id3_title : '';
				$contents_desc.= ($Id3_artist) ? ' ' . $user->lang['BB3_ID3_ARTIST'] . ':' . $Id3_artist : '';
				$contents_desc.= ($Id3_album)  ? ' ' . $user->lang['BB3_ID3_ALBUM'] . ':' . $Id3_album : '';
				$contents_desc.= ($Id3_gender) ? ' ' . $user->lang['BB3_ID3_GENRE'] . ':' . $Id3_gender : '';
				$contents_desc.= ($Id3_year)   ? ' ' . $user->lang['BB3_ID3_YEAR'] . ':' . $Id3_year : '';
				$contents_desc.= ($Id3_track)  ? ' ' . $user->lang['BB3_ID3_TRACK'] . ':' . $Id3_track : '';
			}
		}
**/
		$contents[$row['real_filename']] = array(
			'thumb'			=> $default_image,
			'title'			=> $row['real_filename'],
			'description'	=> $row['attach_comment'] . $contents_desc ,
			'file'			=> append_sid($location . '/download/file.' . $phpEx, array('id' => $row['attach_id'], 'type' => $row['extension']), true, ''),
			'extension' 	=> $row['extension'],
			'date'			=> bb3_date2822(false, $row['filetime']),
			'download'		=> append_sid($location . '/download/file.' . $phpEx, array('id' => $row['attach_id'], 'type' => $row['extension']), true, ''),
		);
	}
	$db->sql_freeresult($result);

	return $contents;
}

function external_url_exists($url)
{
	if (!BB3_SAFEMODE)
	{
		return true;
	}
	
	if ($headers_ary = @get_headers($url))
	{
		if (isset($headers_ary[0]) && stripos($headers_ary[0], '200 OK') !== false)
		{
			return true;
		}
	}

	return false;
/**
	if (!BB3_SAFEMODE)
	{
		$file = bb3_rss_load($url);
		if (!$file)
		{
			return false;
		}
	}
	return true;
**/
}

function bb3_get_remote_file($host, $filename, $port = 80, $errno = 0, $errstr = '', $timeout = 10)
{
	global $user;

	$file_info = '';
	if ($fsock = @fsockopen($host, $port, $errno, $errstr, $timeout))
	{
		@fputs($fsock, "GET $filename HTTP/1.1\r\n");
		@fputs($fsock, "HOST: $host\r\n");
		@fputs($fsock, "Connection: close\r\n\r\n");

		$get_info = false;

		while (!@feof($fsock))
		{
			if ($get_info)
			{
				$file_info .= @fread($fsock, 1024);
			}
			else
			{
				$line = @fgets($fsock, 1024);

				if ($line == "\r\n")
				{
					$get_info = true;
				}
				else if (stripos($line, '404 not found') !== false)
				{
					return false;
				}
				else if (stripos($line, '403 Forbidden') !== false)
				{
					return false;
				}
			}
		}
		@fclose($fsock);

		return $file_info;
	}

	return false;
}

/***********************************************************
* Class:       ID3
* Version:     1.0
* Date:        Janeiro 2004
* Author:      Tadeu F. Oliveira
* Contact:     tadeu_fo@yahoo.com.br
* Use:         Extract ID3 Tag information from mp3 files
***********************************************************
Exemple

    require('error.inc.php');
    $nome_arq  = 'Blind Guardian - Bright Eyes.mp3';
	 $myId3 = new ID3($nome_arq);
	 if ($myId3->getInfo()){
         echo('<HTML>');
         echo('<a href= "'.$nome_arq.'">Clique para baixar: </a><br>');
         echo('<table border=1>
               <tr>
                  <td><strong>Artista</strong></td>
                  <td><strong>Titulo</strong></font></div></td>
                  <td><strong>Trilha</strong></font></div></td>
                  <td><strong>Album/Ano</strong></font></div></td>
                  <td><strong>G&ecirc;nero</strong></font></div></td>
                  <td><strong>Coment&aacute;rios</strong></font></div></td>
               </tr>
               <tr>
                  <td>'. $myId3->getArtist() . '&nbsp</td>
                  <td>'. $myId3->getTitle()  . '&nbsp</td>
                  <td>'. $myId3->getTrack()  . '&nbsp</td>
                  <td>'. $myId3->getAlbum()  . '/'.$myId3->getYear().'&nbsp</td>
                  <td>'. $myId3->getGender() . '&nbsp</td>
                  <td>'. $myId3->tags['COMM']. '&nbsp</td>
               </tr>
            </table>');
         echo('</HTML>');
   	}else{
    	echo($errors[$myId3->last_error]);
   }

*/
class ID3
{
	var $file_name	= '';	//full path to the file, the sugestion is that this path should be a relative path
	var $tags;				//array with ID3 tags extracted from the file
	var $last_error	= 0;	//keep the number of the last error ocurred
	var $tags_count = 0;	// the number of elements at the tags array

	/*********************/
	/**private functions**/
	/*********************/
	function hex2bin($data)
	{
		//thankz for the one who wrote this function, If iknew your name I would say it here
		$len = strlen($data);
		for( $i = 0; $i < $len; $i += 2)
		{
			$newdata .= pack("C",hexdec(substr($data,$i,2)));
		}
		return $newdata;
	}

	function get_frame_size($fourBytes)
	{
		$tamanho[0]	= str_pad(base_convert(substr($fourBytes,0,2),16,2),7,0,STR_PAD_LEFT);
		$tamanho[1]	= str_pad(base_convert(substr($fourBytes,2,2),16,2),7,0,STR_PAD_LEFT);
		$tamanho[2]	= str_pad(base_convert(substr($fourBytes,4,2),16,2),7,0,STR_PAD_LEFT);
		$tamanho[3]	= str_pad(base_convert(substr($fourBytes,6,2),16,2),7,0,STR_PAD_LEFT);
		$total		= $tamanho[0] . $tamanho[1] . $tamanho[2] . $tamanho[3];
		$tamanho[0]	= substr($total,0,8);
		$tamanho[1]	= substr($total,8,8);
		$tamanho[2]	= substr($total,16,8);
		$tamanho[3]	= substr($total,24,8);
		$total		= $tamanho[0] . $tamanho[1] . $tamanho[2] . $tamanho[3];
		$total		= base_convert($total,2,10);

		return $total;
	}

	function extractTags($text,&$tags)
	{
		$size = -1;	//inicializando diferente de zero para n�o sair do while
		while ((strlen($text) != 0) and ($size != 0))
		{
			//while there are tags to read and they have a meaning
			$ID		= substr($text,0,4);
			$aux	= substr($text,4,4);
			$aux	= bin2hex($aux);
			$size	= $this->get_frame_size($aux);
			$flags	= substr($text,8,2);
			$info	= substr($text,11,$size-1);

			if ($size != 0)
			{
				$tags[$ID] = $info;
				$this->tags_count++;
			}
			$text = substr($text,10+$size,strlen($text));
		}
	}

	/********************/
	/**public functions**/
	/********************/
	
	/**Constructor**/
	function ID3($file_name)
	{
		$this->file_name = $file_name;
		$this->last_error = 0;
	}

	/**
	* Read the file and put the TAGS
	* content on $this->tags array
	**/
	function getInfo()
	{
	//	die("this->file_name=(".$this->file_name.")");
		if ($this->file_name != '')
		{
			$mp3	= @fopen($this->file_name, "r");
			$header	= @fread($mp3,10);
		//	die("header=($header)");

			if (!$header)
			{
				$this->last_error = 2;
			//	die('!header');
				return false;
			}

			if (substr($header, 0, 3) != "ID3")
			{
				$this->last_error = 3;
			//	die('!ID3');
				return false;
			}

			$header		= bin2hex($header);
			$version	= base_convert(substr($header,6,2),16,10).".".base_convert(substr($header,8,2),16,10);
			$flags		= base_convert(substr($header,10,2),16,2);
			$flags		= str_pad($flags,8,0,STR_PAD_LEFT);

			if ($flags[7] == 1)
			{
			//	echo('with Unsynchronisation<br>');
			}
			if ($flags[6] == 1)
			{
			//	echo('with Extended header<br>');
			}
			if ($flags[5] == 1)
			{
			//	Esperimental tag
				$this->last_error = 4;
				return false;
			//	die();
			}

			$total	= $this->get_frame_size(substr($header,12,8));
			$text	= @fread($mp3,$total);
			fclose($mp3);
			$this->extractTags($text,$this->tags);
		}
		//	file not set
		else
		{
			$this->last_error = 1;
			return false;
		//	die();
		}

		return true;
	}

	/**
	* PUBLIC Functions to get information from the ID3 tag
	**/
	function getArtist()
	{
		if (array_key_exists('TPE1',$this->tags))
		{
			return $this->tags['TPE1'];
		}
		else
		{
			$this->last_error = 5;
			return false;
		}
	}

	function getTrack()
	{
		if (array_key_exists('TRCK',$this->tags))
		{
			return $this->tags['TRCK'];
		}
		else
		{
			$this->last_error = 5;
			return false;
		}
	}

	function getTitle()
	{
		if (array_key_exists('TIT2',$this->tags))
		{
			return $this->tags['TIT2'];
		}
		else
		{
			$this->last_error = 5;
			return false;
		}
	}

	function getAlbum()
	{
		if (array_key_exists('TALB',$this->tags))
		{
			return $this->tags['TALB'];
		}
		else
		{
			$this->last_error = 5;
			return false;
		}
	}

	function getYear()
	{
		if (array_key_exists('TYER',$this->tags))
		{
			return $this->tags['TYER'];
		}
		else
		{
			$this->last_error = 5;
			return false;
		}
	}

	function getGender()
	{
		if (array_key_exists('TCON',$this->tags))
		{
			global $user;

			return $user->lang['BB3_ID3_TCON'][ord(trim($this->tags['TCON']))];
		//	return $this->tags['TCON'];
		}
		else
		{
			$this->last_error = 5;
			return false;
		}
	}

	function getComments()
	{
		if (array_key_exists('COMM',$this->tags))
		{
			return $this->tags['COMM'];
		}
		else
		{
			$this->last_error = 5;
			return false;
		}
	}
}

?>