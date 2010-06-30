<?php
/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: google.php, v 0.0.6 2009/10/14 14:10:09Z ameisez Exp $
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

$docid = request_var('docid', '');
if ($docid)
{
	return get_google_link($docid);
}

// default rss video feed:
$feed_link	= request_var('rssfeed', $google_playlist);
$contents	= bb3_rss_load($feed_link, $google_playlist_cache);
$result		= xml2array($contents);
$feed_ary	= array();
$feed_title	= $user->lang['BB3_GOOGLE_RECENT'];
if (sizeof($result))
{
	$feed_title = $result['rss']['channel']['title']['value'];
	$feed_link	= $result['rss']['channel']['link']['value'];
	$items		= $result['rss']['channel']['item'];
	for ($i = 0, $size = sizeof($items); $i < $size; $i++)
	{
		// before start we need to check if all tags are availables - Start
		if (!isset($items[$i]['title'])
		 || !isset($items[$i]['link']['value'])
		 || !isset($items[$i]['description']['value'])
		 || !isset($items[$i]['media:group']['media:player']['attr']['url'])
		   )
		{
			continue;
		}
		// before start we need to check if all tags are availables - End
		if (isset($items[$i]['media:group']['media:description']['value']))
		{
			$items[$i]['title'] = $items[$i]['media:group']['media:description']['value'];
		}
		
		if (!isset($items[$i]['media:group']['media:content']))
		{
			$items[$i]['media:group']['media:content']['attr']['duration']  = '0';
		}

		if (isset($items[$i]['media:group']['media:content'][1]))
		{
			$items[$i]['media:group']['media:content'] = $items[$i]['media:group']['media:content'][1];
		}

		if (!isset($items[$i]['pubDate']['value']))
		{
			$items[$i]['pubDate']['value'] = '';
		}

		if (!isset($items[$i]['media:thumbnail']['attr']['url']))
		{
			$items[$i]['media:thumbnail']['attr']['url'] = '';
		}

		if ($pos = strpos($items[$i]['link']['value'], 'rutube.ru'))
		{
			$newlink = (isset($items[$i]['media:group']['media:content']['attr']['url'])) ? $items[$i]['media:group']['media:content']['attr']['url'] : ((isset($items[$i]['enclosure']['attr']['url'])) ? $items[$i]['enclosure']['attr']['url'] : '');
			if ($newlink)
			{
				$items[$i]['media:group']['media:player']['attr']['url'] = $newlink . ".iflv";
			}
		}

		$feed_ary[$i] = array(
			'title'			=> htmlentities(google_fix_title($items[$i]['title'])),
			'link'			=> google_fix_link($items[$i]['link']['value'], false),
			'description'	=> (string) strip_tags(str_replace(array('<![CDATA[', ']]>', '<br />'), array('', '', "\n"), $items[$i]['description']['value'])),
			'date'			=> $items[$i]['pubDate']['value'],
			'duration'		=> $items[$i]['media:group']['media:content']['attr']['duration'],
			'file'			=> google_fix_link($items[$i]['media:group']['media:player']['attr']['url'], true),
			'thumb'			=> $items[$i]['media:group']['media:thumbnail']['attr']['url'],
		);
	}
}
unset($result);

// Pull the feed
make_rss_playlist($feed_ary, google_fix_link($feed_link), $feed_title);
unset($feed_ary);

function google_fix_title($titles)
{
	if (is_array($titles))
	{
		$tmp_title = '';
		foreach ($titles as $values => $datas)
		{
			if (is_array($datas))
			{
				$tmp_title .= google_fix_title($datas);
			}
			else
			{
				$tmp_title .= $datas;
			}
		}
		$titles = $tmp_title;
	}

	return $titles;
}

/**
 * Enter description here...
 *
 * @param unknown_type $link
 * @param unknown_type $force
 * @return unknown
 */
function google_fix_link($link, $force = false)
{
	$link = rawurldecode($link);
	$link = str_replace('http://www.google.com/url?q=', '', $link);

	if ($pos = strpos($link, '&'))
	{
		$link = substr($link, 0, $pos);
	}

	if ($force)
	{
		$host = $filename = $videoid = $videohtml = $videoxml = '';
		
		if ($pos = strpos($link, 'video.google.com/videoplay'))
		{
			$link = preg_replace('#(.*?)videoplay\?docid=([0-9A-Za-z-_]+)#si', "google.php?docid=$2&amp;type=flv", $link);
		}
		else if ($pos = strpos($link, 'dailymotion.com'))
		{
			$videoid = preg_replace('#(.*?)dailymotion.com\/video\/([0-9A-Za-z-_]+)#si', "$2", $link);
			$host = 'www.dailymotion.com';
			$filename = "/video/$videoid";
			if (($videohtml = bb3_get_remote_file($host, $filename)) !== false)
			{
				if (preg_match('/%7C%7C(.*?)%40%40/', $videohtml, $match))
				{
					$link = urldecode($match[1]) . ".flv";
				}
			}
		}
		else if ($pos = strpos($link, 'vimeo.com'))
		{
			$videoid = preg_replace('#(.*?)vimeo.com/(^|clip:)?(.*?)#si', "$3", $link);
			$host = 'www.vimeo.com';
			$filename = "/moogaloop/load/clip:$videoid";
			if (($videoxml = bb3_get_remote_file($host, $filename)) !== false)
			{
				if (preg_match('|<request_signature>(.*)</request_signature>|i', $videoxml, $sig) && preg_match('|<request_signature_expires>(.*)</request_signature_expires>|i', $videoxml, $sigexp))
				{
					$link = "http://www.vimeo.com/moogaloop/play/clip:{$videoid}/{$sig[1]}/{$sigexp[1]}/?q=sd&amp;type=flv";
				}
			}
		}
		else if ($pos = strpos($link, 'metacafe.com'))
		{
			$host = 'www.metacafe.com';
			$filename = str_replace(array('http://', 'www.metacafe.com'), array('', ''), $link);
			if (($videohtml = bb3_get_remote_file($host, $filename)) !== false)
			{
				if (preg_match('/mediaURL":"(.*?)"/si', $videohtml, $mediaURL))
				{
					if ($pos = strpos($mediaURL[1], 'akvideos.metacafe.com'))
					{
						$link = urldecode($mediaURL[1]);
					}
				}
			}
		}
		else
		{
			// Strip / from the end
			if (substr($link, -1, 1) == '/')
			{
				$link = substr($link, 0, -1);
			}
		//	$link .= ".flv&amp;type=flv";
		}

		unset($host, $filename, $videoid, $videohtml, $videoxml);

/**
* @todo 
*
* 		//  http://www.wat.tv/video/the-game-feat-lil-wayne-my-wwxb_w9x4_.html
*		//	http://www.stupidvideos.com/video/stunts/tennis_court_fun_1
* 
*		//	http://embed.break.com/650236
*		//	http://pvideos.5min.com/955612/95561104.flv
*		//	http://www.myvideo.de/watch/2003681/Fun
*		//	http://www.kidstube.com/play.php?vid=12446
*		//	http://www.mytopclip.com/play.php?vid=20974
*		//	http://www.video75.com/vd.php?i=k7q9gx
*		//	http://www.break.com/usercontent/2009/5/daisy-has-fun-in-her-bedroom-726429.html
*		//	http://www.howcast.com/videos/1115-How-To-Make-Fun-Toys-aka-Do-Try-This-At-Home-Episode-9-Toyland
*		//	http://www.vidzshare.net/play.php?vid=3254
*		//	http://www.videojug.com/player?type=interview&id=11aaaf45-2abc-bed7-40d4-ff0008c96c2f
*		//	http://www.veoh.com/browse/videos/category/educational/watch/v247126Grh69dX8
*		//	http://www.videojug.com/player?type=interview&id=11aaaf45-2abc-bed7-40d4-ff0008c96c2f
* 		//	http://www.pp2g.tv/vYXt-Ync_.aspx
*@done
*		//	http://www.metacafe.com/fplayer/2191999/the_most_fun.swf
* 		//	http://vimeo.com/moogaloop.swf?clip_id=6983810
*		//	http://rutube.ru/tracks/642238.html
*
**/
	}
	return $link;
}

/**
* Google URI: http://video.google.com/videoplay?docid=7206494026138253535
* 
* MP4:
* call with: http://www.mydomain.com/path/google.php?docid=7206494026138253535
* JWMP code:
* file': encodeURIComponent('Google_URI.php?docid=7206494026138253535'),
* 'type': 'video',
* 
* FLV:
* call with: http://www.mydomain.com/path/google.php?docid=7206494026138253535&type=flv
* JWMP code:
* 'file': encodeURIComponent('google.php?docid=7206494026138253535&type=flv'),
* 'type': 'video',
**/
function get_google_link($docid = '', $type = 'flv')
{
	$docid = ($docid) ? strval($docid) : '8010793559435491074';

	if ($file = @file_get_contents("http://video.google.com/videoplay?docid=$docid"));
	{
		if ($type == 'flv')
		{
			preg_match("/googleplayer.swf\?videoUrl\\\\x3d(.*?)\\\\x26/", $file, $match);
		}
		else
		{
			preg_match('/right-click <a href="(.*?)">this link/', $file, $match);
		}
		$uri = urldecode($match[1]);

		header("Location: $uri");
	}
}

?>