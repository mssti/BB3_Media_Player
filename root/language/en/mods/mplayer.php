<?php
/**
* @package: phpBB 3.0.6 :: Ameisez BB3 Media Player -> root/language/en/mods :: [en][English]
* @version: $Id: index.php, v0.0.8 2010/02/24 10:02:24Z ameisez Exp $
* @copyright: ameisez < ameisez@yahoo.com > http://www.littlegraffyx.com/ameisez
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: ameisez - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=356834
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
* @translator: ameisez - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=356834
**/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// "Page %s of %s" you can (and should) write "Page %1$s of %2$s", this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. "Message %d" is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., "Click %sHERE%s" is fine
// Reference : http://www.phpbb.com/mods/documentation/phpbb-documentation/language/index.php#lang-use-php
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'BB3_PLAYER_NAME'			=> 'BB3 Media Center',
	'BB3_AUTHOR_LINK'			=> '<strong>BB3 Media Player %1$s</strong> Powered by <a href="http://www.littlegraffyx.com/ameisez/" onclick="window.open(this.href);return false;" >AMEISEZ</a> & <a href="http://www.mssti.com/" onclick="window.open(this.href);return false;" >.:: MSSTI ::.</a>',

	'BB3_NO_ACCESS'				=> 'You do not have the required permissions to access to <strong>BB3 Media Player</strong>.<br />Please contact the board administrator for details.',

	// Flash strings
	'BB3_GET_FLASH'				=> '%sGet the Flash Player%s to see this player.',

	'BB3_PLAYLIST_ERROR_TITLE'	=> 'ERROR : ',
	'BB3_PLAYLIST_ERROR_DESC'	=> 'There are no valid items in this play list',
	'BB3_PLAYLIST_NUMBER'		=> 'Number of items in the Current playlist',
	'BB3_PLAYLIST_OPTIONS'		=> 'Available playlist',
	'BB3_PLAYLIST_ALL'			=> 'All',
	'BB3_SKIN_OPTIONS'			=> 'Available skins',
	'BB3_SKIN_BASE'				=> 'Default',
	'BB3_EXPLAYLIST_OPTIONS'	=> 'Available external playlist',
	'BB3_YOUTUBE_SEARCH'		=> 'Search video in Youtube',
	'BB3_YOUTUBE_MYPLAYLIST'	=> 'My Youtube playlist',
	'BB3_YOUTUBE_PLAYLIST'		=> 'Youtube videos',
	'BB3_DAILYMOTION_PLAYLIST'	=> 'Dailymoion videos',
	'BB3_GOOGLE_PLAYLIST'		=> 'Google videos',
	'BB3_ATTACH_PLAYLIST'		=> 'Attachments',
	'BB3_NO_DESCRIPTION'		=> '(No description)',
	'BB3_NO_KEYWORDS'			=> 'You must specify at least one word to search for.',
	'BB3_SEARCH_SHOW'			=> 'Show search',
	'BB3_SEARCH_HIDE'			=> 'Hide search',
	'BB3_PLAY_ME'				=> 'Click to play',

	'SEARCH_KEYWORDS'			=> 'Search for keywords',
	'SEARCH_KEYWORDS_EXPLAIN'	=> 'You must specify at least one word to search for. Each word must consist of at least %d characters and must not contain more than %d characters.Wildcards are not allowed.',


// Playlist title tranlations
	// Multiple Custom Youtube video playlist ID
	'PHPBB3_RELATED'			=> 'phpBB3 related',
	'SONGS'						=> 'Songs',
	'MY_NEPHEWS'				=> 'My nephews',
	'TV_SERIES_LOST'			=> 'TV series lost',
	'FRIKI'						=> 'Friki',
	'UFOS'						=> 'Ufos',
	'FUNNY'						=> 'Funny',
	// Multiple Youtube video playlist
	'RECENTLY_FEATURED'			=> 'Recently Featured',
	'TOP_RATED'					=> 'Top rated',
	'TOP_FAVORITES'				=> 'Top favorites',
	'MOST_VIEWED'				=> 'Most viewed',
	'MOST_RECENT'				=> 'Most Recent',
	'MOST_DISCUSSED'			=> 'Most discussed',
	'MOST_LINKED'				=> 'Most linked',
	'MOST_RESPONDED'			=> 'Most responded',
	'WATCH_ON_MOBILE'			=> 'Mobile Videos',
	// Multiple Dailymotion video playlist
	'RECENT'					=> 'Recent',
	'FEATURED'					=> 'Featured',
	'CREATIVE'					=> 'Creative',
	'ANIMALS'					=> 'Animals',
	// Multiple Youtube video playlist
	'TOP_100_NEW'				=> 'Top 100 new',
	'POPULAR'					=> 'Popular',
	'RECENT'					=> 'Recent',
	'FUNNY'						=> 'Funny',
	'ANIMALS'					=> 'Animals',


// Download
	'BB3_DOWNLOAD_FAIL'			=> 'Download Failed',
	'BB3_DOWNLOAD_ERROR'		=> 'No parameter specified',
	'BB3_DOWNLOAD_NOFILE'		=> 'No file specified to download',
	'BB3_DOWNLOAD_NOVALID'		=> 'Requested file "<em>%1$s</em>" is not available',
	'BB3_DOWNLOAD_NOREAD'		=> 'Cannot read "<em>%1$s</em>" file',
	'BB3_DOWNLOAD_NOFOUND'		=> 'File "<em>%1$s</em>" not found',

// ID3 tags valid for .mp3 files
	'BB3_ID3_TITLE'			=> 'Title',
	'BB3_ID3_ARTIST'		=> 'Artist',
	'BB3_ID3_ALBUM'			=> 'Album',
	'BB3_ID3_GENRE'			=> 'Genre',
	'BB3_ID3_YEAR'			=> 'Year',
	'BB3_ID3_TRACK'			=> 'Track',
	'BB3_ID3_COMMENTS'		=> 'Comments',
	// Genre explain
	'BB3_ID3_TCON'			=> array(
										'Blues',
										'Classic Rock',
										'Country',
										'Dance',
										'Disco',
										'Funk',
										'Grunge',
										'Hip-Hop',
										'Jazz',
										'Metal',
										'New Age',
										'Oldies',
										'Other',
										'Pop',
										'R&B',
										'Rap',
										'Reggae',
										'Rock',
										'Techno',
										'Industrial',
										'Alternative',
										'Ska',
										'Death Metal',
										'Pranks',
										'Soundtrack',
										'Euro-Techno',
										'Ambient',
										'Trip-Hop',
										'Vocal',
										'Jazz+Funk',
										'Fusion',
										'Trance',
										'Classical',
										'Instrumental',
										'Acid',
										'House',
										'Game',
										'Sound Clip',
										'Gospel',
										'Noise',
										'Alternative Rock',
										'Bass',
										'Soul',
										'Punk',
										'Space',
										'Meditative',
										'Instrumental Pop',
										'Instrumental Rock',
										'Ethnic',
										'Gothic',
										'Darkwave',
										'Techno-Industrial',
										'Electronic',
										'Pop-Folk',
										'Eurodance',
										'Dream',
										'Southern Rock',
										'Comedy',
										'Cult',
										'Gangsta',
										'Top 40',
										'Christian Rap',
										'Pop/Funk',
										'Jungle',
										'Native US',
										'Cabaret',
										'New Wave',
										'Psychadelic',
										'Rave',
										'Showtunes',
										'Trailer',
										'Lo-Fi',
										'Tribal',
										'Acid Punk',
										'Acid Jazz',
										'Polka',
										'Retro',
										'Musical',
										'Rock & Roll',
										'Hard Rock',
										'Folk',
										'Folk-Rock',
										'National Folk',
										'Swing',
										'Fast Fusion',
										'Bebob',
										'Latin',
										'Revival',
										'Celtic',
										'Bluegrass',
										'Avantgarde',
										'Gothic Rock',
										'Progressive Rock',
										'Psychedelic Rock',
										'Symphonic Rock',
										'Slow Rock',
										'Big Band',
										'Chorus',
										'Easy Listening',
										'Acoustic',
										'Humour',
										'Speech',
										'Chanson',
										'Opera',
										'Chamber Music',
										'Sonata',
										'Symphony',
										'Booty Bass',
										'Primus',
										'Porn Groove',
										'Satire',
										'Slow Jam',
										'Club',
										'Tango',
										'Samba',
										'Folklore',
										'Ballad',
										'Power Ballad',
										'Rhytmic Soul',
										'Freestyle',
										'Duet',
										'Punk Rock',
										'Drum Solo',
										'Acapella',
										'Euro-House',
										'Dance Hall',
										'Goa',
										'Drum & Bass',
										'Club-House',
										'Hardcore',
										'Terror',
										'Indie',
										'BritPop',
										'Negerpunk',
										'Polsk Punk',
										'Beat',
										'Christian Gangsta',
										'Heavy Metal',
										'Black Metal',
										'Crossover',
										'Contemporary C',
										'Christian Rock',
										'Merengue',
										'Salsa',
										'Thrash Metal',
										'Anime',
										'JPop',
										'SynthPop',
									)

));

?>