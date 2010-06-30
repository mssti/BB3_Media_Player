<?php
/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: playlist.php, v 0.0.4 2009/10/03 03:10:09Z ameisez Exp $
* @copyright: ameisez < ameisez@yahoo.com > http://www.littlegraffyx.com/ameisez
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: ameisez - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=356834
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
**/

/**
* @ignore
* http://www.longtailvideo.com/support/forum/Modules/8879/Autogenerate-playlist-from-folder-#
* http://www.longtailvideo.com/support/forum/JavaScript-Interaction/10514/Multiple-playlists-in-same-player-#msg71070
**/

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// path to the directory you want to scan by default
$url = "mplayer/media";

// path to the default image
$img = "mplayer/bb3player_logo.png";

// search for mp3 and flv files
// Supported formats: See JW FLV Player : http://www.longtailvideo.com/support/faq
$extensions = array(
	'mp3',
//	'mp4',
//	'aac',
//	'jgp',
//	'gif',
//	'png',
	'flv',
//	'swf',
);

define('FORCEDOWNLOAD', true);
/******************************************************************************************************************************************************************************************************
* DO NOT CHANGE ANYTHING AFTER THIS LINE!
******************************************************************************************************************************************************************************************************/
$user->add_lang('mods/mplayer');

$script_name = $user->lang['BB3_PLAYER_NAME'];

// Direct link to the board
$location = generate_board_url();

$action = request_var('action', '', true);
if ($action == 'download')
{
	playlist_download();
}

$image	= request_var('image', $img);
$image	= playlist_fix_path($image, true);

$path	= request_var('path', $url);
// There is a default image in therequered folder ?
if (file_exists("$path/albumart.jpg"))
{
	$image = "$path/albumart.jpg";
}
$path	= playlist_fix_path($path, true);

// Get file content from the request folder
$dir_info = playlist_get_dir_info($path);
// print_r($dir_info);
// die("END");
asort($dir_info);

header('Content-type: text/xml; charset=UTF-8');
header('Cache-Control: private, no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
header('Pragma: no-cache');

print ("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<playlist version=\"1\" xmlns=\"http://xspf.org/ns/0/\">
	<title>$script_name</title>
	<info>$location</info>
	<creator>http://www.littlegraffyx.com/ameisez/</creator>
	<trackList>");

foreach ($dir_info as $item)
{
	print ("
	<track>
		<title>" . $item['name'] . "</title>
		<location>" . $item['file'] . "</location>
		<info>" . $item['download'] . "</info>
		<image>" . (($item['image']) ? $item['image'] : (($image) ? $image : '')) . "</image>
	</track>");
}

print ("
	</trackList>
</playlist>");

/**
* Checks if a path ($path) is absolute or relative
*
* @param string		$path		Path to check
* @param boolean	$absolute	true = absolute | false = relative
* @return string	$path		absolute or relative
**/
function playlist_fix_path($path = '', $absolute = true)
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
* @return array		with files in the given path
**/
function playlist_get_dir_info($path)
{
	global $phpbb_root_path, $phpEx;
	global $extensions, $location, $url;

	$contents	= array();

	// Path must be relative 
	$path = playlist_fix_path($path, false);

	if ($handle = @opendir($path))
	{
		while (false !== ($file = readdir($handle)))
		{
			$nextpath = "$path/$file";
			if ($file != '.' && $file != '..' && !is_link($nextpath))
			{
				if (is_dir($nextpath))
				{
					$contents = array_merge($contents, playlist_get_dir_info($nextpath));
				}
				else if (is_file($nextpath))
				{
					if (in_array(playlist_get_file_extension($nextpath), $extensions))
					{
						$contents_name	= playlist_get_file_name($nextpath);
						$contents_file	= preg_replace(array("#\\\#", "#$phpbb_root_path#"), array("/", "$location/"), $nextpath);
						$contents_ext	= playlist_get_file_extension($nextpath);
						$contents_img	= (file_exists("$path/$contents_name.jpg")) ? playlist_fix_path("$path/$contents_name.jpg", false) : '';

						// Force Download Script
						if (FORCEDOWNLOAD)
						{
							$download_url		= str_replace("$location/", '', $contents_file);
							$contents_download	= append_sid("$location/mplayer/playlist.$phpEx", array('action' => 'download', 'redirect' => urlencode($download_url)));
						}
						else
						{
							$contents_download = $contents_file;
						}

						$contents[$file] = array(
							'image'		=> $contents_img,
							'name'		=> $contents_name,
							'file'		=> $contents_file,
							'extension' => $contents_ext,
							'download'	=> $contents_download,
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
		$ext = playlist_get_file_extension($request_file);

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

?>