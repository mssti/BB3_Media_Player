<?php
/**
* @package: phpBB 3.0.5 :: Ameisez BB3 Media Player -> root/mplayer
* @version: $Id: bb3mediaplayer.php, v 0.0.1 2009/09/25 09:09:18Z ameisez Exp $
* @copyright: ameisez < ameisez@yahoo.com > http://www.littlegraffyx.com/ameisez
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License 
* @author: ameisez - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=356834
**/
//
//
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

// Some oft used variables
$safe_mode		= (@ini_get('safe_mode') == '1' || strtolower(@ini_get('safe_mode')) === 'on') ? true : false;
$file_uploads	= (@ini_get('file_uploads') == '1' || strtolower(@ini_get('file_uploads')) === 'on') ? true : false;
$user->add_lang( array('acp/common', 'mods/bb3_media_player') );

/**
* You can change the following options:
**/
page_header('BB3 Media Player');
$template->set_filenames(array(
	'body' => 'mplayer_body.html')
);

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
page_footer();
?>