<?php
/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: bb3mediaplayer.php, v 0.0.1 2009/09/25 09:09:18Z ameisez Exp $
* @copyright: ameisez < ameisez@yahoo.com > http://www.littlegraffyx.com/ameisez
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
* @author: ameisez - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=356834
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

// Restrict Guest
//if ($user->data['user_id'] == ANONYMOUS)
//	{    
//		login_box('', $user->lang['LOGIN']);
//	}

$root_folder_path	= $phpbb_root_path . 'mplayer/';
$upload_folder_path	= $root_folder_path . 'media';

page_header('BB3 Media Player');

// Assign index specific vars
$template->assign_vars(array(
   'ROOT_FOLDER_PATH'	=> $root_folder_path,
   'UPLOAD_FOLDER'		=> $upload_folder_path,
));

$template->set_filenames(array(
	'body' => 'mplayer_body.html')
);

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

page_footer();

?>