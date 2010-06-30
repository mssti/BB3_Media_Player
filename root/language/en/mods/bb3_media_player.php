<?php
/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/language/en/mods :: [en][English]
* @version: $Id: bb3mediaplayer.php, v 0.0.1 2009/09/25 09:09:18Z ameisez Exp $
* @copyright: ameisez < ameisez@yahoo.com > (anton) http://www.littlegraffyx.com/ameisez
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
* @author: ameisez - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=356834
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
	'BB3_PLAYER_AMEISEZ_LINK'	=> '<strong>BB3 Media Player %1$s</strong> by <a href="http://www.littlegraffyx.com/ameisez/" onclick="window.open(this.href);return false;" >AMEISEZ</a>',
	'AUTHOR_LINK'				=> 'Powered by: <a href="http://www.littlegraffyx.com/ameisez/" onclick="window.open(this.href);return false;" >Ameisez BB3 Media Player</a>',

	// ID3 tags valid for .mp3 files
	'BB3PLAYER_ID3_TITLE'			=> 'Title',
	'BB3PLAYER_ID3_ARTIST'			=> 'Artist',
	'BB3PLAYER_ID3_ALBUM'			=> 'Album',
	'BB3PLAYER_ID3_GENRE'			=> 'Genre',
	'BB3PLAYER_ID3_YEAR'			=> 'Year',
	'BB3PLAYER_ID3_TRACK'			=> 'Track',
	'BB3PLAYER_ID3_COMMENTS'		=> 'Comments',
	// Genre explain
	'BB3PLAYER_ID3_TCON'			=> array(
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