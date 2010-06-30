<?php
/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: index.php, v 0.0.4 2009/10/03 03:10:09Z ameisez Exp $
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

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$user->add_lang('mods/bb3_media_player');

// Restrict Guest
//if ($user->data['user_id'] == ANONYMOUS)
//	{    
//		login_box('', $user->lang['LOGIN']);
//	}

$script_version		= '0.0.4';

$root_folder_path	= $phpbb_root_path . 'mplayer/';

$upload_folder_path	= $root_folder_path . 'media';
$path = request_var('path', '');

$folder_options = '';
if (sizeof($folders = bb3_media_get_folders($upload_folder_path)))
{
	$folder_options = '<option value="" ' . ((!$path) ? 'selected="selected"' : '') . '>' . $user->lang['BB3_PLAYLIST_ALL'] . '</option>';
	foreach ($folders as $folder)
	{
		$selected = ($folder == $path) ? ' selected="selected"' : '';
		$folder_options .= '<option value="' . $folder . '"' . $selected . '>' . $folder . '</option>';
	}
}

if ($path)
{
	$upload_folder_path	.= "/$path";
}

page_header($user->lang['BB3_PLAYER_NAME']);

// Assign index specific vars
$template->assign_vars(array(
	'BB3_FOLDER_PATH'		=> $root_folder_path,
	'BB3_UPLOAD_PATH'		=> $upload_folder_path,
	'U_ACTION'				=> append_sid("{$root_folder_path}index.$phpEx"),
	'BB3_FOLDER_OPTIONS'	=> $folder_options,

));

// Add my link to the output page footer into the copyright notes ;)
$user->lang['TRANSLATION_INFO'] = (!empty($user->lang['TRANSLATION_INFO'])) ? $user->lang['TRANSLATION_INFO'] . '<br />' . sprintf($user->lang['BB3_PLAYER_AMEISEZ_LINK'], $script_version) : sprintf($user->lang['BB3_PLAYER_AMEISEZ_LINK'], $script_version);

$template->set_filenames(array(
	'body' => 'mplayer_body.html')
);

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

page_footer();

/**
* Retrieve information about a folder
*
* @param string		$path	 path to analize
* @return array		with  recursive folder in the given path
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
		closedir ($handle);
	}

	return $contents;
}

?>