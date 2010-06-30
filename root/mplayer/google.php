<?php

/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: dailymotion.php, v 0.0.4 2009/10/03 03:10:09Z ameisez Exp $
* @copyright: ameisez < ameisez@yahoo.com > http://www.littlegraffyx.com/ameisez
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: ameisez - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=356834
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
**/

/**
* @ignore
* http://www.longtailvideo.com/support/forum/Modules/5752/Frustrated-
**/

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$docid = request_var('docid', '');
if ($docid)
{
	return get_google_link($docid);
}

// default rss video feed:
$rssfeed = request_var('rssfeed', "http://video.google.com/videofeed?type=top100new&num=20&output=rss");
// $rssfeed = "http://video.google.com/videosearch?q=fun&output=rss";

$feeds = array();
if ($rss = file($rssfeed))
{
	foreach ($rss as $line)
	{
		// look for video title
		if (preg_match('/<title>(.*?)<\/title>/', $line, $title_match))
		{
			$title = $title_match[1];
		}
		// look for video image
		if (preg_match('/[^<]*<media:thumbnail url="(.*?)"(.*?)\/>/', $line, $thumb_match))
		{
			$thumb = $thumb_match[1];
		}

		// look for flv video
		if (preg_match('/[^<]*<media:player url="(.*?)"(.*?)\/>/', $line, $link_match))
		{
			$link = $link_match[1];

			if ($link == 'http://video.google.com/')
			{
				continue;
			}
			else
			{
				$link = rawurldecode($link);
				$link = str_replace('http://www.google.com/url?q=', '', $link);

				if ($pos = strpos($link, '&'))
				{
					$link = substr($link, 0, $pos);
				}
				
				if ($pos = strpos($link, 'video.google.com/videoplay'))
				{
					$link = preg_replace('#(.*?)videoplay\?docid=([0-9A-Za-z-_]+)#si', "google.php?docid=$2&amp;type=flv", $link);
				}
				else if ($pos = strpos($link, 'www.dailymotion.com'))
				{
					$videoid = preg_replace('#(.*?)dailymotion.com\/video\/([0-9A-Za-z-_]+)#si', "$2", $link);
					if ($file = @file_get_contents("http://www.dailymotion.com/video/$videoid"));
					{
						if (preg_match('/%7C%7C(.*?)%40%40/', $file, $match))
						{
							$link = urldecode($match[1]) . ".flv";
						}
						unset($file, $videoid);
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
			}

			$feeds[$link]['title'] = (isset($title)) ? $title : $link;
			$feeds[$link]['thumb'] = (isset($thumb)) ? $thumb : '';
		}
	}
}

// generate playlist
header('Content-type: text/xml; charset=UTF-8');
header('Cache-Control: private, no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
header('Pragma: no-cache');

print ("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<playlist version=\"1\" xmlns=\"http://xspf.org/ns/0/\">
	<title>video.google.com</title>
	<info>http://video.google.com/</info>
	<creator>http://www.littlegraffyx.com/ameisez/</creator>
	<trackList>");

while($things = current($feeds))
{
	$link = key($feeds);
	print ("
		<track>
			<title>" . $things['title'] . "</title>
			<location>" . $link . "</location>
			<info>" . $link . "</info>
			<image>" . $things['thumb'] . "</image>
		</track>");

	next($feeds);
}

print ('</trackList>
</playlist>');

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