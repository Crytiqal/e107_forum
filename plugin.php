<?php
/*
+ ----------------------------------------------------------------------------+
|     e107 website system
|
|     Copyright (C) 2001-2002 Steve Dunstan (jalist@e107.org)
|     Copyright (C) 2008-2010 e107 Inc (e107.org)
|
|
|     Released under the terms and conditions of the
|     GNU General Public License (http://gnu.org).
|
|     $URL: https://e107.svn.sourceforge.net/svnroot/e107/trunk/e107_0.7/e107_plugins/forum/plugin.php $
|     $Revision: 12178 $
|     $Id: plugin.php 12178 2011-05-02 20:45:40Z e107steved $
|     $Author: e107steved $
+----------------------------------------------------------------------------+
*/

if (!defined('e107_INIT')) { exit; }

include_lan(e_PLUGIN.'forum/languages/'.e_LANGUAGE.'/lan_forum_conf.php');

// Plugin info -------------------------------------------------------------------------------------------------------
$eplug_name 		= 'Forum';
$eplug_version 		= '1.0';
$eplug_author 		= 'e107dev';
$eplug_description 	= 'This plugin is a fully featured Forum system.';
$eplug_compatible 	= 'e107v0.8+';
$eplug_url 		= 'http://e107.org';
$eplug_email 		= '';
$eplug_readme 		= '';
$eplug_latest 		= TRUE; //Show reported threads in admin (use latest.php)
$eplug_status 		= TRUE; //Show post count in admin (use status.php)

// Name of the plugin's folder -------------------------------------------------------------------------------------
$eplug_folder 		= "forum";

// Name of menu item for plugin ----------------------------------------------------------------------------------
$eplug_menu_name 	= "forum";

// Name of the admin configuration file --------------------------------------------------------------------------
$eplug_conffile 	= "forum_admin.php";

// Icon image and caption text ------------------------------------------------------------------------------------
$eplug_icon 		= $eplug_folder."/images/forums_32.png";
$eplug_icon_small 	= $eplug_folder."/images/forums_16.png";
$eplug_caption 		= 'Configure Forum';

// List of preferences -----------------------------------------------------------------------------------------------
/*
$eplug_prefs = array(
	'forum_show_topics' 	=> '1',
	'forum_postfix' 	=> '[more...]',
	'forum_poll' 		=> '0',
	'forum_popular' 	=> '10',
	'forum_track' 		=> '0',
	'forum_eprefix' 	=> '[forum]',
	'forum_enclose' 	=> '1',
	'forum_title' 		=> 'Forums',
	'forum_postspage' 	=> '10',
	'forum_hilightsticky' => '1'
 );
*/
// List of table names -----------------------------------------------------------------------------------------------
$eplug_table_names = array(
	"forum_p",
	"forum_f",
	"forum_t",
	"forum_posts" );

// List of sql requests to create tables -----------------------------------------------------------------------------

$eplug_tables = array(
"CREATE TABLE ".MPREFIX."forum_p (
	forum_parent_id 		int(10) unsigned NOT NULL auto_increment,
	forum_parent_name 		varchar(250) NOT NULL default '',
	forum_parent_description 	text NOT NULL,
	forum_parent_class 		varchar(100) NOT NULL default '',
	forum_parent_postclass 	tinyint(3) unsigned NOT NULL default '0',
	forum_parent_order 		int(10) unsigned NOT NULL default '0',
	PRIMARY KEY  (forum_parent_id)
	) ENGINE=MyISAM AUTO_INCREMENT=1;",

"CREATE TABLE ".MPREFIX."forum_f (
	forum_parent_id			int(10) unsigned NOT NULL default '0',
	forum_forum_id			int(10) unsigned NOT NULL auto_increment,
	forum_forum_sub			int(10) unsigned NOT NULL default '0',
	forum_forum_name		varchar(250) NOT NULL default '',
	forum_forum_description 	text NOT NULL,
	forum_forum_class 		varchar(100) NOT NULL default '',
	forum_forum_postclass 	tinyint(3) unsigned NOT NULL default '0',
	forum_forum_moderators	tinyint(3) unsigned NOT NULL default '0',
	forum_forum_order 		int(10) unsigned NOT NULL default '0',
	PRIMARY KEY  (forum_forum_id),
	KEY forum_parent_id (forum_parent_id)
	) ENGINE=MyISAM AUTO_INCREMENT=1;",

"CREATE TABLE ".MPREFIX."forum_t (
	forum_forum_id			int(10) unsigned NOT NULL default '0',
	forum_thread_id			int(10) unsigned NOT NULL auto_increment,
	forum_thread_name		varchar(250) NOT NULL default '',
	forum_thread_thread		text NOT NULL default '',
	forum_thread_datestamp	int(10) unsigned NOT NULL default '0',
	forum_thread_viewcount	int(10) unsigned NOT NULL default '0',
	forum_thread_postcount	int(10) unsigned NOT NULL default '0',
	forum_thread_lastpost		int(10) unsigned NOT NULL default '0',
	forum_thread_lastuser		int(10) unsigned NOT NULL default '0',
	forum_thread_trackers		varchar(250) NOT NULL default '',
	forum_thread_trackbatch	varchar(250) NOT NULL default '',
	forum_thread_tracklimbo	varchar(250) NOT NULL default '',
	PRIMARY KEY (forum_thread_id),
	KEY forum_forum_id (forum_forum_id),
	KEY forum_thread_datestamp (forum_thread_datestamp)
	) ENGINE=MyISAM AUTO_INCREMENT=1;",

"CREATE TABLE ".MPREFIX."forum_posts (
	forum_thread_id			int(10) unsigned NOT NULL default '0',
	forum_post_id			int(10) unsigned NOT NULL auto_increment,
	forum_post_user			varchar(30) NOT NULL default '',
	forum_post_post			text NOT NULL default '',
	forum_post_datestamp		int(10) unsigned NOT NULL default '0',
	forum_post_reputation		int(10) unsigned NOT NULL default '0',
	forum_post_quoted		int(10) unsigned NOT NULL default '0',	
	PRIMARY KEY (forum_post_id),
	KEY forum_thread_id (forum_thread_id),
	KEY forum_post_datestamp (forum_post_datestamp)
	) ENGINE=MyISAM AUTO_INCREMENT=1;"
);

// Create a link in main menu (yes=TRUE, no=FALSE) -------------------------------------------------------------
$eplug_link = TRUE;
$eplug_link_name = "Forum";
$eplug_link_url = e_PLUGIN.'forum/forum.php';

// Text to display after plugin successfully installed ------------------------------------------------------------------
$eplug_done = 'Your forum is now installed';

$eplug_upgrade_done = 'Forum successfully upgraded, now using version: '.$eplug_version;

/*
$upgrade_alter_tables = array(
"ALTER TABLE ".MPREFIX."forum ADD forum_postclass TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ;"
);

	"ALTER TABLE ".MPREFIX."user ADD( user_forum_oldtimestamp int(10) unsigned NOT NULL default '0', user_forum_newtimestamp int(10) unsigned NOT NULL default '0')";

*/

if (!function_exists('forum_uninstall')) {
	function forum_uninstall() {
		global $sql;
		$sql -> db_Update("user", "user_forums='0'");
	}
}

if (!function_exists('forum_install')) {
	function forum_install() {
		global $sql;
		$sql -> db_Update("user", "user_forum_posts='0'");
	}
}

/*
if (stripos($_SERVER['REQUEST_URI'],'?install') !== false) {
mysql_query("ALTER TABLE ".MPREFIX."user ADD( user_forum_oldtimestamp int(10) unsigned NOT NULL default '0', user_forum_newtimestamp int(10) unsigned NOT NULL default '0', user_forum_newposts int(10) unsigned NOT NULL default '0')") or die(mysql_error());
}


if (stripos($_SERVER['REQUEST_URI'],'?uninstall') !== false) {
mysql_query("ALTER TABLE ".MPREFIX."user DROP user_forum_oldtimestamp, DROP user_forum_newtimestamp, DROP user_forum_newposts") or die(mysql_error());
}
*/


?>