<?php
/**
* @package: phpBB 3.0.6 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: search_playlist.php, v0.0.8 2010/02/24 10:02:24Z ameisez Exp $
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

// Pull the feed
$script_name = $user->lang['BB3_PLAYER_NAME'];

$image		= request_var('image', $bb3media_config_image_path);
$image		= playlist_fix_path($image, true);
$path		= request_var('path', $bb3media_config_media_path);
$file 		= request_var('file', 0);
$keywords	= utf8_normalize_nfc(request_var('keywords', '', true));

// if a file was specified we have to do less cleaning
if ($file)
{

	$keywords	= str_replace(array('\\', '/', "'"), ' ', $keywords . '|');
	$keywords	= array_filter(explode('|', $keywords));
}
else
{
	// We not allow comodins or any kind of special trick
	$keywords	= str_replace(array('|', '$', '(', ')', '*', '%', '+', '-', '\\', '/', "'"), ' ', $keywords . '|');
	$keywords	= array_filter(explode(' ', $keywords));

	foreach ($keywords as $i => $word)
	{
		$clean_word = preg_replace('#^[+\-|"]#', '', $word);

		// check word length
		$clean_len = utf8_strlen(str_replace('*', '', $clean_word));
		if (($clean_len < $word_length['min']) || ($clean_len > $word_length['max']))
		{
			unset($keywords[$i]);
		}
	}

	// We limit the number of allowed keywords
	if (!sizeof($keywords))
	{
	//	trigger_error(sprintf($user->lang['SEARCH_KEYWORDS_EXPLAIN'], $word_length['min'], $word_length['max']));
		$error[$user->lang['BB3_PLAYLIST_ERROR_TITLE']] = array(
			'file'			=> '&type=mp3',
			'title'			=> $script_name . ' ' . $user->lang['BB3_PLAYLIST_ERROR_TITLE'],
			'description'	=> sprintf($user->lang['SEARCH_KEYWORDS_EXPLAIN'], $word_length['min'], $word_length['max'])
		);

		return make_rss_playlist($error, $location, $script_name);
	//	return make_rss_playlist(array(), $location, $script_name);
	}
}

// There is a default image in the requered folder ?
if (file_exists("$path/albumart.jpg"))
{
	$image = "$path/albumart.jpg";
	$image = playlist_fix_path($image, true);
}

$path	= playlist_fix_path($path, true);
$search	= array();
$find	= array();

// Get file content from the request folder
$search = array_merge($search, playlist_get_dir_info($path, $image));

if ($bb3media_config['attach_playlist'])
{
	$search = array_merge($search, playlist_get_attachment_info($extensions, $image));
}

if (sizeof($search))
{
	// Fileds to not search in 
	$skip_value = array('thumb', /* 'title', */ /* 'description', */ 'file', /* 'extension', */ 'date', 'download');
	// Fields to search in 
	//	title + description + extension
	foreach ($search as $items => $item)
	{
		if (bb3_media_search($items, $keywords))
		{
			// string needle found in haystack
			$find[$items] = $item;
			continue;
		}

		foreach ($item as $value => $data)
		{
			if (in_array($value, $skip_value))
			{
				continue;
			}

			if (bb3_media_search($data, $keywords))
			{
				// string needle found in haystack
				$find[$items] = $item;
				break;
			}
		}
	}
}
asort($find);

if ($file)
{
	return make_rss_playlist($find, $location, $script_name);
}

if ($bb3media_config['search_ajax'])
{
	make_rss_playlist2($find, $location, $script_name);
}
else
{
	make_rss_playlist($find, $location, $script_name);
}

function make_rss_playlist2($feeds, $location, $title)
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

function bb3_media_search($haystack, $needle)
{
	if (!is_array($needle))
	{
		$needle = array($needle);
	}
	$haystack	= utf8_normalize_nfc($haystack);

	foreach ($needle as $need)
	{
		if (strpos(strtolower(trim($haystack)), strtolower(trim($need))) !== false /*|| strtolower($haystack) == strtolower($need)*/)
		{
			// string needle found in haystack
			return true;
		}
	}
	return false;
}

?>