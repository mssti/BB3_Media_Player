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

$videoid = request_var('videoid', '');
if ($videoid)
{
	return get_dailymotion_link($videoid);
}

// default rss video feed:
$rssfeed = request_var('rssfeed', "http://www.dailymotion.com/rss/us/featured");

$feeds = array();
if ($rss = file($rssfeed))
{
	foreach ($rss as $line)
	{
		// look for video title
		// title>36 Skaters Make Downhill Neon Video Game with Freebords</title>
		if (preg_match('/<title>(.*?)<\/title>/', $line, $title_match))
		{
			$title = $title_match[1];
		}
		// look for video image
		//<media:thumbnail url="http://ak2.static.dailymotion.com/static/video/884/208/17802488:jpeg_preview_large.jpg?20091005102438" height="240" width="320" />
		if (preg_match('/[^<]*<media:thumbnail url="(.*?)"(.*?)\/>/', $line, $thumb_match))
		{
			$thumb = $thumb_match[1];
		}
		// look for flv video
		// <media:content url="http://www.dailymotion.com/cdn/FLV-80x60/video/xalkhk?auth=1255470709_61afc4ec22d6e6d45a68d1df25a4876e.flv" type="video/x-flv" duration="90" width="80" height="60"/>
		if (preg_match('/[^<]*<media:content url="(.*?).flv"(.*?)\/>/', $line, $feed_match))
		{
			$link = $feed_match[1] . ".flv";

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
	<title>$rssfeed</title>
	<info>$rssfeed</info>
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