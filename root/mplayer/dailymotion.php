<?php
/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: dailymotion.php, v 0.0.6 2009/10/14 14:10:09Z ameisez Exp $
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

$videoid = request_var('videoid', '');
if ($videoid)
{
	return get_dailymotion_link($videoid);
}

// default rss video feed:
$feed_link	= request_var('rssfeed', $dailymotion_playlist);
$contents	= bb3_rss_load($feed_link, $dailymotion_playlist_cache);
$result		= xml2array($contents);
$feed_ary	= array();
$feed_title	= $user->lang['BB3_DAILYMOTION_RECENT'];
if (sizeof($result))
{
	$feed_title = $result['rss']['channel']['title']['value'];
	$feed_link	= $result['rss']['channel']['link']['value'];
	$items		= $result['rss']['channel']['item'];
	for ($i = 0, $size = sizeof($items); $i < $size; $i++)
	{
		// before start we need to check if all tags are availables - Start
		if (!isset($items[$i]['title']['value'])
		 || !isset($items[$i]['link']['value'])
		 || !isset($items[$i]['description']['value']) 
		 || !isset($items[$i]['media:group']['media:content'][1]['attr']['url'])
		   )
		{
			continue;
		}
		// before start we need to check if all tags are availables - End

		if (!isset($items[$i]['media:group']['media:content'][1]['attr']['duration']))
		{
			$items[$i]['media:group']['media:content'][1]['attr']['duration']  = '0';
		}

		if (!isset($items[$i]['pubDate']['value']))
		{
			$items[$i]['pubDate']['value'] = '';
		}

		if (!isset($items[$i]['media:thumbnail']['attr']['url']))
		{
			$items[$i]['media:thumbnail']['attr']['url'] = '';
		}

		$feed_ary[$i] = array(
			'title'			=> $items[$i]['title']['value'],
			'link'			=> $items[$i]['link']['value'],
			'description'	=> (string) strip_tags(str_replace(array('<![CDATA[', ']]>', '<br />'), array('', '', "\n"), $items[$i]['description']['value'])),
			'date'			=> $items[$i]['pubDate']['value'],
			'duration'		=> $items[$i]['media:group']['media:content'][1]['attr']['duration'],
			'file'			=> $items[$i]['media:group']['media:content'][1]['attr']['url'],
			'thumb'			=> $items[$i]['media:thumbnail']['attr']['url'],
		);
	}
}
unset($result);

// Pull the feed
make_rss_playlist($feed_ary, $feed_link, $feed_title);
unset($feed_ary);

/**
* Google URI: http://video.google.com/videoplay?docid=7206494026138253535
* 
* MP4:
* call with: http://www.mydomain.com/path/Google_URI.php?docid=7206494026138253535
* JWMP code:
* file': encodeURIComponent('Google_URI.php?docid=7206494026138253535'),
* 'type': 'video',
* 
* FLV:
* call with: http://www.mydomain.com/path/Google_URI.php?docid=7206494026138253535&type=flv
* JWMP code:
* 'file': encodeURIComponent('Google_URI.php?docid=7206494026138253535&type=flv'),
* 'type': 'video',
**/
function get_dailymotion_link($videoid = '', $type = 'flv')
{
	$videoid = ($videoid) ? strval($videoid) : 'xe1ap_cindy-lauper-girls-just-w-have-fun_life';

	if ($file = @file_get_contents("http://www.dailymotion.com/video/$videoid"));
	{
		preg_match('/ value="(.*?)" class="text embed_input"/', $file, $match);

		$uri = urldecode($match[1]) . ".flv";

		header("Location: $uri");
	}
}

?>